<?php
session_start();
header('Content-Type: application/json'); // Ensure the response is JSON

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    $db = new SQLite3("../grupp.db");
    $validationError = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $recaptchaSecret = 'YOUR-API-KEY-HERE'; // Remove or replace this key for security reasons
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
            $response['message'] = 'reCAPTCHA verification failed';
            echo json_encode($response);
            exit();
        }

        $username = strtolower(test_input($_POST["username"]));
        $email = strtolower(test_input($_POST["email"]));
        $password = test_input($_POST["password"]);

        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'All fields are required';
            echo json_encode($response);
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
            $response['message'] = 'Username already exists';
            echo json_encode($response);
            exit();
        }

        $checkEmailQuery = "SELECT * FROM Users WHERE email = :email";
        $stmt = $db->prepare($checkEmailQuery);
        $stmt->bindValue(':email', $email);
        $result = $stmt->execute();
        if ($result->fetchArray(SQLITE3_ASSOC)) {
            $response['message'] = 'Email already exists';
            echo json_encode($response);
            exit();
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

            $response['status'] = 'success';
            $response['message'] = 'User registered successfully';
            echo json_encode($response);
            $db->close();
            exit();
        } else {
            $response['message'] = 'Validation error';
        }
    } else {
        $response['message'] = 'Invalid request method';
    }

    $db->close();
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
