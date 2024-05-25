<?php
session_start();
header('Content-Type: application/json'); // Ensure the response is JSON
$username = $email = $password = "";
$db = new SQLite3("../grupp.db");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $searchResult = $db->prepare("SELECT * FROM Users WHERE username = :username");
    $searchResult->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $searchResult->execute();

    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $stored_hashed_password = $row['password'];
        if (password_verify($password, $stored_hashed_password)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['userID'] = $row['userID'];
<<<<<<< HEAD:php/login.php
            echo json_encode(array("status" => "success"));
=======
            $_SESSION['email'] = $row['email'];
            header("Location: main.php");
            exit();


>>>>>>> 627d42e19b974eaa807060d461a0fdb960483974:login.php
        } else {
            echo json_encode(array("status" => "error", "type" => "password", "message" => "Password does not match username"));
        }
    } else {
        echo json_encode(array("status" => "error", "type" => "username", "message" => "Username does not exist"));
    }
    $searchResult->close();
    $db->close();
    exit();
}
?>
