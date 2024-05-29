<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new SQLite3("../grupp.db");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteAdvert'])) {
    $advertID = intval($_POST['advertID']);
    $stmt = $db->prepare('DELETE FROM Adverts WHERE id = :id');
    $stmt->bindValue(':id', $advertID, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Advert deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete advert."]);
    }
    exit(); // Make sure to exit after sending the response
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggleBan'])) {
    $userID = intval($_POST['userID']);

    // Update is_banned status if it's currently false else unbanned
    $stmt = $db->prepare('UPDATE Users SET is_banned = (CASE WHEN is_banned = "false" THEN "true" ELSE "false" END) WHERE userID = :userID');
    $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
    if ($stmt->execute() && $db->changes() > 0) {
        echo json_encode(["success" => true, "message" => "User status changed successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to ban/unban user"]);
    }
    exit(); // Make sure to exit after sending the response
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['getUserDetails'])) {
    $userID = intval($_POST['userID']);
    $stmt = $db->prepare('SELECT userID, username, is_banned FROM Users WHERE userID = :userID');
    $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
    $result = $stmt->execute();
    if ($user = $result->fetchArray(SQLITE3_ASSOC)) {
        echo json_encode(["success" => true, "user" => $user]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
    exit();
}



$adverts = $db->query('SELECT * FROM Adverts');
$users = $db->query('SELECT * FROM Users');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/admin.js" defer></script>
</head>
<body>
    <div class="header-container">
        <nav class="header">
            <h1 id="header-logo">Projektgrupp 4</h1>
            <div class="header-btns">
                <a href="logout.php" class="header-link header-btn">Logout</a>
                <a href="main.php" class="header-link header-btn">Back to Main</a>
                <a href="admin.php" class="header-link header-btn">Admin Page</a>
            </div>
        </nav>
    </div>
    <div class="admin-container">
        <h1>Admin Page</h1>
        <h2>All Adverts</h2>
        <div id="message" class="message"></div> <!-- Message container -->
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php while ($advert = $adverts->fetchArray(SQLITE3_ASSOC)): ?>
                <tr id="advert-<?php echo $advert['id']; ?>">
                    <td><?php echo htmlspecialchars($advert['id']); ?></td>
                    <td><?php echo htmlspecialchars($advert['title']); ?></td>
                    <td><?php echo htmlspecialchars($advert['description']); ?></td>
                    <td>
                        <button class="delete-btn" data-id="<?php echo $advert['id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <h2>All Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($user = $users->fetchArray(SQLITE3_ASSOC)): ?>
                <tr id="user-<?php echo $user['userID']; ?>">
                    <td><?php echo htmlspecialchars($user['userID']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo $user['is_banned'] === 'true' ? 'Banned' : 'Active'; ?></td>
                    <td>
                        <button class="ban-btn" data-id="<?php echo $user['userID']; ?>" data-banned="<?php echo $user['is_banned']; ?>">
                            <?php echo $user['is_banned'] === 'true' ? 'Unban' : 'Ban'; ?>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
    </div>
</body>
</html>
