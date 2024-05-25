<?php
session_start();
$db = new SQLite3("../grupp.db");
$validationError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptchaSecret = 'INSERT_API_KEY'; // Remove or replace this key for security reasons
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA response
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptchaData = [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse,
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($recaptchaData),
        ],
    ];
    $context = stream_context_create($options);
    $verify = file_get_contents($recaptchaUrl, false, $context);
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success == false) {
        echo "<script>alert('reCAPTCHA verification failed. Redirecting...');</script>";
        echo "<script>setTimeout(function() { window.location.href = '../index.php'; }, 2000);</script>";
        exit();
    }

    $username = strtolower(test_input($_POST["username"]));
    $email = strtolower(test_input($_POST["email"]));
    $password = test_input($_POST["password"]);

    if (empty($username) || empty($email) || empty($password)) {
        exit();
    }

    if (!preg_match("/.{4,}$/", $username) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/", $password)) {
        $validationError = true;
    }

    $checkUsernameQuery = "SELECT * FROM Users WHERE username = :username";
    $stmt = $db->prepare($checkUsernameQuery);
    $stmt->bindValue(':username', $username);
    $result = $stmt->execute();
    if ($result->fetchArray(SQLITE3_ASSOC)) {
        $validationError = true;
    }

    $checkEmailQuery = "SELECT * FROM Users WHERE email = :email";
    $stmt = $db->prepare($checkEmailQuery);
    $stmt->bindValue(':email', $email);
    $result = $stmt->execute();
    if ($result->fetchArray(SQLITE3_ASSOC)) {
        $validationError = true;
    }

    if (!$validationError) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $db->prepare($insertQuery);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
        $stmt->execute();
        $newUserID = $db->lastInsertRowID();

        $_SESSION['userID'] = $newUserID;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $db->close();
        header("Location: ../php/main.php");
        exit();
    }

    $db->close();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
