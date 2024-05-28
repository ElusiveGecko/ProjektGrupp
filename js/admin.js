$(document).ready(function() {
    $('.delete-btn').on('click', function() {
        const advertID = $(this).data('id');
        $.ajax({
            url: 'admin.php',
            type: 'POST',
            data: { deleteAdvert: true, advertID: advertID },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    $('#advert-' + advertID).remove();
                    showMessage(data.message, true);
                } else {
                    showMessage(data.message, false);
                }
            }
        });
    });

    function showMessage(message, isSuccess) {
        const messageDiv = $('#message');
        messageDiv.text(message);
        messageDiv.css('background-color', isSuccess ? '#4CAF50' : '#f44336');
        messageDiv.fadeIn().delay(3000).fadeOut();
    }
});
