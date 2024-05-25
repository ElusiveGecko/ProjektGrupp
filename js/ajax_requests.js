function checkUsernameAvailability(username, showError, showValid, usernameInput) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/check_username.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'error') {
                showError(usernameInput, response.message);
            } else {
                showValid(usernameInput);
            }
        } else {
            console.error("Request failed. Status: " + xhr.status);
        }
    };
    xhr.send(`username=${encodeURIComponent(username)}`);
}

function checkEmailAvailability(email, showError, showValid, emailInput) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/check_email.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'error') {
                showError(emailInput, response.message);
            } else {
                showValid(emailInput);
            }
        } else {
            console.error("Request failed. Status: " + xhr.status);
        }
    };
    xhr.send(`email=${encodeURIComponent(email)}`);
}

function loginUser(form, showError, loginUsernameInput, loginPasswordInput) {
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/login.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                window.location.href = 'php/main.php';
            } else {
                if (response.type === 'password') {
                    showError(loginPasswordInput, response.message);
                } else if (response.type === 'username') {
                    showError(loginUsernameInput, response.message);
                }
            }
        } else {
            console.error("Request failed. Status: " + xhr.status);
        }
    };
    xhr.send(new URLSearchParams(formData));
}
