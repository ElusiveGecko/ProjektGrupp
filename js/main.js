$(document).ready(function() {
    let currentPage = 1;
    const defaultResultsPerPage = 10;

    function fetchAdverts(category = 0, searchTerm = '', page = 1, resultsPerPage = defaultResultsPerPage) {
        $.post('handle_requests.php', { action: 'fetch', category: category, searchTerm: searchTerm, page: page, resultsPerPage: resultsPerPage }, function(data) {
            const adverts = JSON.parse(data);
            let advertsHtml = '';
            adverts.forEach(advert => {
                advertsHtml += `
                    <div class="advert">
                        <h3>${advert.title}</h3>
                        <img src="../${advert.image}" alt="${advert.title}" class="uploaded-image">
                        <p>${advert.description}</p>
                        <small>Posted by: ${advert.username} (${advert.address}) in ${advert.categoryName} on ${advert.created_at}</small>
                    </div>
                `;
            });
            $('#adverts').html(advertsHtml);
            updatePagination(page);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching adverts:', textStatus, errorThrown);
        });
    }

    function updatePagination(currentPage) {
        const category = $('#category-filter').val();
        const searchTerm = $('#search-term').val();

        $.post('handle_requests.php', { action: 'count', category: category, searchTerm: searchTerm }, function(data) {
            const totalResults = parseInt(JSON.parse(data).total);
            const totalPages = (totalResults % defaultResultsPerPage === 0 ? totalResults / defaultResultsPerPage : Math.floor(totalResults / defaultResultsPerPage) + 1);

            let paginationHtml = '';

            for (let i = 1; i <= totalPages; i++) {
                paginationHtml += `<button class="page-btn${i === currentPage ? ' active' : ''}" data-page="${i}">${i}</button>`;
            }
            $('#pagination').html(paginationHtml);

            $('.page-btn').on('click', function() {
                const page = $(this).data('page');
                currentPage = page;
                fetchAdverts(category, searchTerm, page);
                window.scrollTo(0, 0);
            });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching total results:', textStatus, errorThrown);
        });
    }

    $('#advert-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'publish');
        $.ajax({
            url: 'handle_requests.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    fetchAdverts();
                    $('#advert-form')[0].reset();
                } else {
                    alert(res.message); // Display error message
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error publishing advert:', textStatus, errorThrown);
            }
        });
    });

    $('#category-filter').on('change', function() {
        const category = $(this).val();
        fetchAdverts(category);
    });

    $('#search-button').on('click', function() {
        const category = $('#category-filter').val();
        const searchTerm = $('#search-term').val();
        fetchAdverts(category, searchTerm);
    });

    fetchAdverts();
});
