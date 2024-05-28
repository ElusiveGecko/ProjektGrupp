<?php session_start(); 
$_SESSION['userID'] = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="js/module_utility.js" defer></script>
    <script src="js/form_validation.js" defer></script>
    <script src="js/ajax_requests.js" defer></script>
</head>

<body>

    <div class="header-container">
        <nav class="header">
            <h1 id="header-logo">Projektgrupp 4</h1>
            <div class="header-links">
                <a href="#" class="header-link header-btn login-module-btn">Login</a>
            </div>
        </nav>
    </div>


    <div class="home">
        <div class="home-container">
            <div class="home-content">
                <div class="home-text">
                    <h1>Your #1 online marketplace</h1>
                    <p>For all of your buying and selling needs!</p>
                </div>
                <div class="btn-container">
                    <button class="register-module-btn" id="account-btn">Get Started Today!</button>
                </div>
            </div>
            <div class="home-img-container"><img src="images/home_img.svg" id="home-img"></div>
        </div>
    </div>

    <div class="login-module" id="login-module-id">
    <div class="module-content">
        <span class="login-close-btn">&times;</span>
        <div class="login-header">
            <h2>Welcome Back!</h2>
            <p>Please enter your credentials to log in.</p>
        </div>
        <form action="php/login.php" method="POST" class="login-form" id="login-form-id">
            <div class="form-validation">
                <input type="text" class="module-input" id="login-username" name="username" placeholder="Enter your username">
                <p>Error Message</p>
            </div>
            <div class="form-validation">
                <input type="password" class="module-input" id="login-password" name="password" placeholder="Enter your password">
                <p>Error Message</p>
            </div>
            <input type="submit" class="login-input-btn" value="Login">
        </form>
    </div>
</div>


    <div class="register-module" id="register-module-id">
        <div class="module-content">
            <span class="register-close-btn">&times;</span>
            <form action="php/register.php" method="POST" class="register-form" id="register-form-id">
                <div class="form-validation">
                    <input type="text" class="module-input" id="register-username" name="username" placeholder="Enter your username">
                    <p>Error Message</p>
                </div>
                <div class="form-validation">
                    <input type="text" class="module-input" id="register-email" name="email" placeholder="Enter your email">
                    <p>Error Message</p>
                </div>
                <div class="form-validation">
                    <input type="password" class="module-input" id="register-password" name="password" placeholder="Enter your password">
                    <p>Error Message</p>
                </div>
                <div class="form-validation">
                    <input type="password" class="module-input" id="register-password-conf" name="password-confirm" placeholder="Confirm your password">
                    <p>Error Message</p>
                </div>
                <div class="g-recaptcha" data-sitekey="6LcETuYpAAAAAJQSYwpU2nkdbGc_8hTF740s3QYu"></div>
                <input type="submit" class="register-input-btn" value="Sign Up">
            </form>
        </div>
    </div>
</body>
</html>
