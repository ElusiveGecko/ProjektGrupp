document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form-id');
    const registerForm = document.getElementById('register-form-id');
    const loginUsernameInput = document.getElementById('login-username');
    const loginPasswordInput = document.getElementById('login-password');
    const usernameInput = document.getElementById('register-username');
    const emailInput = document.getElementById('register-email');
    const passwordInput = document.getElementById('register-password');
    const passwordConfInput = document.getElementById('register-password-conf');

    function showError(input, message) {
        const formValidation = input.parentElement;
        formValidation.className = 'form-validation error';
        const errorMessage = formValidation.querySelector('p');
        errorMessage.innerText = message;
        console.log(`Error: ${message}`); // Debugging statement
    }

    function showValid(input){
        const formValidation = input.parentElement;
        formValidation.className = 'form-validation valid';
        console.log(`Valid: ${input.name}`); // Debugging statement
    }

    function checkRequired(inputArr){
        inputArr.forEach(function(input){
            if(input.value.trim() === '') {
                showError(input, `${getFieldName(input)} is required`);
            }
        });
    }

    function checkEmail(input) {
        const emailRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
        if(!emailRegex.test(input.value)){
            showError(input, "Invalid email");
        }
    }

    function checkPassword(pass) {
        const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
        if(!passwordRegex.test(pass.value)) {
            showError(pass, "Invalid password");
        } else {
            showValid(pass);
        }
    }

    function checkConfPassword(pass, conf) {
        if(pass.value !== conf.value) {
            showError(conf, "Passwords do not match");
        } else if (conf.value.length > 0) {
            showValid(conf);
        } else {
            showError(conf, "Password confirmation is required");
        }
    }

    function getFieldName(input){
        return input.name.charAt(0).toUpperCase() + input.name.slice(1);  
    }

    const formValidations = document.querySelectorAll('.form-validation');

    registerForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevent default form submission
        console.log("Register form submitted"); // Debugging statement

        checkRequired([usernameInput, emailInput, passwordInput, passwordConfInput]);
        checkEmail(emailInput);
        checkPassword(passwordInput);
        checkConfPassword(passwordInput, passwordConfInput);

        let hasErrors = false;
        formValidations.forEach(formValidation => {
            if (formValidation.classList.contains('error')) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            console.log("Form has errors"); // Debugging statement
            return;
        }

        const recaptchaResponse = grecaptcha.getResponse();
        if (recaptchaResponse.length === 0) {
            alert("Please complete the captcha");
            return;
        }

        console.log("Captcha response:", recaptchaResponse); // Debugging statement

        const formData = new FormData(registerForm);
        formData.append('g-recaptcha-response', recaptchaResponse);

        fetch('php/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response data:", data); // Debugging statement
            if (data.status === 'success') {
                alert('Registration successful');
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevent default form submission
        console.log("Login form submitted"); // Debugging statement

        showValid(loginUsernameInput);
        showValid(loginPasswordInput);
        checkRequired([loginUsernameInput, loginPasswordInput]);
        let hasErrors = false;
        formValidations.forEach(formValidation => {
            if (formValidation.classList.contains('error')) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            console.log("Form has errors"); // Debugging statement
            return;
        }

        loginUser(loginForm, showError, loginUsernameInput, loginPasswordInput);
    });

    usernameInput.addEventListener('input', (e) => {
        const username = usernameInput.value.trim();
        if (username !== '') {
            checkUsernameAvailability(username, showError, showValid, usernameInput);
        } else {
            showError(usernameInput, "Username is required");
        }
    });

    emailInput.addEventListener('input', (e) => {
        const email = emailInput.value.trim();
        if (email !== '') {
            checkEmailAvailability(email, showError, showValid, emailInput);
        } else {
            showError(emailInput, "Email is required");
        }
    });

    passwordInput.addEventListener('input', (e) => {
        const password = passwordInput.value.trim();
        if (password !== '') {
            checkPassword(passwordInput);
        } else {
            showError(passwordInput, "Password is required");
        }
    });

    passwordConfInput.addEventListener('input', (e) => {
        if (passwordConfInput.value !== '') {
            checkConfPassword(passwordInput, passwordConfInput);
        } else {
            showError(passwordConfInput, "Password confirmation is required");
        }
    });

    // Uncommented Google Maps API Initialization code
    // function initAutocomplete() {
    //     var addressInput = document.getElementById('address-input');
    //     var autocomplete = new google.maps.places.Autocomplete(addressInput);
    // }
    // google.maps.event.addDomListener(window, 'load', initAutocomplete);
});
