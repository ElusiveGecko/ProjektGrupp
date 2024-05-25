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
    }

    function showValid(input){
        const formValidation = input.parentElement;
        formValidation.className = 'form-validation valid';
    }

    function checkRequired(inputArr){
        inputArr.forEach(function(input){
            if(input.value.trim() === '') {
                showError(input, `${getFieldName(input)} is required`);
            }
        })
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
            showValid(pass)
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
        checkRequired([usernameInput, emailInput, passwordInput, passwordConfInput]);
        checkEmail(emailInput);
        checkPassword(passwordInput);
        checkConfPassword(passwordInput, passwordConfInput);

        formValidations.forEach(formValidation => {
            if (formValidation.classList.contains('error')) {
                e.preventDefault();
            }
        });
    });

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevent default form submission
        checkRequired([loginUsernameInput, loginPasswordInput]);
        const formValidations = document.querySelectorAll('.form-validation');
        formValidations.forEach(formValidation => {
            if (formValidation.classList.contains('error')) {
                e.preventDefault();
                return;
            }
        });
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
