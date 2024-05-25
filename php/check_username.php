<?php
header('Content-Type: application/json'); // Ensure the response is JSON
function checkUsernameAvailability($username) {
    $db = new SQLite3("../grupp.db");
    $usernameQuery = "SELECT * FROM Users WHERE username = :username";
    $stmt = $db->prepare($usernameQuery);
    $stmt->bindValue(':username', $username);
    $result = $stmt->execute();

    if ($result->fetchArray(SQLITE3_ASSOC)) {
        $db->close();
        return ["status" => "error", "message" => "Username already exists"];
    } else if (!preg_match("/.{4,}$/", $username)) {
        $db->close();
        return ["status" => "error", "message" => "Username must contain at least 4 characters"];
    } else {
        $db->close();
        return ["status" => "success", "message" => "Username available"];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = strtolower($_POST["username"]);
    echo json_encode(checkUsernameAvailability($username));
    exit();
}
?>
