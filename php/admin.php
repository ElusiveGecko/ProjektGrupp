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

$adverts = $db->query('SELECT * FROM Adverts');
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
    </div>
</body>
</html>
