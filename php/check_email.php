<?php
header('Content-Type: application/json'); // Ensure the response is JSON
function checkEmailAvailability($email) {
    $db = new SQLite3("../grupp.db");
    $emailQuery = "SELECT * FROM Users WHERE email = :email";
    $stmt = $db->prepare($emailQuery);
    $stmt->bindValue(':email', $email);
    $result = $stmt->execute();

    if ($result->fetchArray(SQLITE3_ASSOC)) {
        $db->close();
        return ["status" => "error", "message" => "Email already has an account"];
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $db->close();
        return ["status" => "error", "message" => "Invalid email"];
    } else {
        $db->close();
        return ["status" => "success", "message" => "Email available"];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    echo json_encode(checkEmailAvailability($email));
}
?>
