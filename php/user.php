<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

$db = new SQLite3("../grupp.db");

$userID = $_SESSION['userID'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'delete' && isset($_POST['advertID'])) {
        $advertID = intval($_POST['advertID']);
        $stmt = $db->prepare("DELETE FROM Adverts WHERE id = :advertID AND userID = :userID");
        $stmt->bindValue(':advertID', $advertID, SQLITE3_INTEGER);
        $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
        $stmt->execute();
        echo json_encode(["success" => true]);
        exit();
    }
}

$adverts = $db->query("SELECT Adverts.*, Categories.name as categoryName FROM Adverts JOIN Categories ON Adverts.categoryID = Categories.id WHERE userID = $userID");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Adverts</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script> <!-- Replace YOUR_API_KEY with your actual Google Maps API key -->
</head>
<body>
    <div class="header-container">
        <nav class="header">
            <h1 id="header-logo">Projektgrupp 4</h1>
            <div class="header-links">
                <a href="logout.php" class="header-link header-btn">Logout</a>
                <a href="main.php" class="header-link header-btn">Back to Main</a>
            </div>
        </nav>
    </div>

    <div class="container">
        <h1>Your Adverts</h1>
        <div class="adverts" id="adverts">
            <?php while ($advert = $adverts->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="advert" data-id="<?php echo $advert['id']; ?>">
                    <h3><?php echo htmlspecialchars($advert['title']); ?></h3>
                    <img src="../<?php echo htmlspecialchars($advert['image']); ?>" alt="<?php echo htmlspecialchars($advert['title']); ?>" class="uploaded-image">
                    <p><?php echo htmlspecialchars($advert['description']); ?></p>
                    
                    <small>Posted in <?php echo htmlspecialchars($advert['categoryName']); ?> on <?php echo htmlspecialchars($advert['created_at']); ?></small>
                    <button class="delete-btn" data-id="<?php echo $advert['id']; ?>">Delete</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                const advertID = $(this).data('id');
                $.post('user.php', { action: 'delete', advertID: advertID }, function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $(`.advert[data-id="${advertID}"]`).remove();
                    } else {
                        alert('Failed to delete advert.');
                    }
                });
            });
        });
    </script>
</body>
</html>
