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


    $('.ban-btn').on('click', function() {
        const userID = $(this).data('id');
        $.ajax({
            url: 'admin.php',
            type: 'POST',
            data: { toggleBan: true, userID: userID },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    reloadUserRow(userID);
                } else {
                    showMessage(data.message, false);
                }
            }
        });
    });

    // reloads data for row after ban unban
    function reloadUserRow(userID) {
        $.ajax({
            url: 'admin.php',
            type: 'POST',
            data: { getUserDetails: true, userID: userID },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const user = data.user;
                    const userRow = $('#user-' + userID);
                    userRow.find('td:nth-child(3)').text(user.is_banned === 'true' ? 'Banned' : 'Active');
                    userRow.find('.ban-btn').text(user.is_banned === 'true' ? 'Unban' : 'Ban');
                    showMessage("User status updated successfully.", true);
                } else {
                    showMessage(data.message, false);
                }
            }
        });
    }


    function showMessage(message, isSuccess) {
        const messageDiv = $('#message');
        messageDiv.text(message);
        messageDiv.css('background-color', isSuccess ? '#4CAF50' : '#f44336');
        messageDiv.fadeIn().delay(3000).fadeOut();
    }
});
