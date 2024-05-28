<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

$db = new SQLite3("../grupp.db");

$categories = $db->query("SELECT * FROM Categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <script src="../js/main.js" defer></script>
</head>
<body>
    <div class="header-container">
        <nav class="header">
            <h1 id="header-logo">Projektgrupp 4</h1>
            <div class="header-btns">
                <a href="logout.php" class="header-link"><div class="header-btns"><button class="login-module-btn" id="header-btns">Logout</button></div></a>
                <a href="user.php" class="header-link"><div class="header-btns"><button class="nav-btn" id="main-user-btn">My Adverts</button></div></a>
            </div>
        </nav>
    </div>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Post a New Advert</h2>
        <form id="advert-form" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="title" placeholder="Advert Title" required>
            </div>
            <div class="form-group">
                <input type="text" id="address-input" name="address" placeholder="Address" required>
            </div>
            <div class="form-group">
                <textarea name="description" placeholder="Advert Description" required></textarea>
            </div>
            <div class="form-group">
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php while ($category = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="file" name="image" accept="image/*" required>
            </div>
            <input type="submit" value="Publish Advert">
        </form>

        <h2>Adverts</h2>
        <div class="form-group">
            <input type="text" id="search-term" placeholder="Search...">
            <button id="search-button">Search</button>
        </div>
        <div class="form-group">
            <select id="category-filter">
                <option value="0">All Categories</option>
                <?php
                $categories->reset();
                while ($category = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="adverts" id="adverts"></div>
        <div id="pagination" class="pagination"></div>
    </div>
</body>
</html>
