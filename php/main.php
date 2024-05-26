<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

$db = new SQLite3("../grupp.db");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'publish' && isset($_POST['title'], $_POST['description'], $_POST['category'], $_POST['address'])) {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $categoryID = intval($_POST['category']);
        $address = htmlspecialchars($_POST['address']);
        $userID = $_SESSION['userID'];

        // Handle file upload
        $target_dir = "../uploads/";  // Corrected path to point to the root uploads directory
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;
        $image = "";

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            echo "File is not an image.";
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
            echo "Sorry, file already exists.";
        }

        // Check file size
        if ($_FILES["image"]["size"] > 2000000) {
            $uploadOk = 0;
            echo "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = "uploads/" . basename($_FILES["image"]["name"]);  // Store relative path for image display
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        if ($uploadOk == 1) {
            $stmt = $db->prepare("INSERT INTO Adverts (userID, title, description, categoryID, address, image) VALUES (:userID, :title, :description, :categoryID, :address, :image)");
            $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
            $stmt->bindValue(':title', $title, SQLITE3_TEXT);
            $stmt->bindValue(':description', $description, SQLITE3_TEXT);
            $stmt->bindValue(':categoryID', $categoryID, SQLITE3_INTEGER);
            $stmt->bindValue(':address', $address, SQLITE3_TEXT);
            $stmt->bindValue(':image', $image, SQLITE3_TEXT);
            if ($stmt->execute()) {
                echo "Advert published successfully.";
            } else {
                echo "Failed to publish advert.";
            }
        }
        exit();
    }

    if ($_POST['action'] == 'fetch') {
        $categoryFilter = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $searchTerm = isset($_POST['searchTerm']) ? htmlspecialchars($_POST['searchTerm']) : '';


        $query = "SELECT Adverts.*, Users.username, Categories.name as categoryName FROM Adverts JOIN Users ON Adverts.userID = Users.userID JOIN Categories ON Adverts.categoryID = Categories.id";
        // Adds catg filter in database select if category is selected
        if ($categoryFilter > 0) {
            $query .= " WHERE Adverts.categoryID = :categoryID";
        }
        // adds search term to title in database select if search term is not empty.  (consider an OR to search the advert text also)
        if (!empty($searchTerm)) {
            $query .= " AND Adverts.title LIKE :searchTerm";
        }
        $query .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($query);
        //binding
        if ($categoryFilter > 0) {
            $stmt->bindValue(':categoryID', $categoryFilter, SQLITE3_INTEGER);
        }
         if (!empty($searchTerm)) {
             $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', SQLITE3_TEXT);
         }

        $adverts = $stmt->execute();
        $result = [];
        while ($row = $adverts->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $row;
        }
        echo json_encode($result);

        exit();

    }
}

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
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script> <!-- Replace YOUR_API_KEY with your actual Google Maps API key -->
</head>
<body>
    <div class="header-container">
        <nav class="header">
            <h1 id="header-logo">Blocket-ish</h1>
            <div class="header-btns">
                <a href="logout.php" class="header-link"><div class="header-btns"><button class="login-module-btn" id="header-btns">Logout</button></div></a>
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
    </div>

    <script>
        $(document).ready(function() {
        function fetchAdverts(category = 0, searchTerm = '') {
            $.post('main.php', { action: 'fetch', category: category, searchTerm: searchTerm }, function(data) {
                const adverts = JSON.parse(data);
                let advertsHtml = '';
                adverts.forEach(advert => {
                    advertsHtml += `
                        <div class="advert">
                            <h3>${advert.title}</h3>
                            <img src="../${advert.image}" alt="${advert.title}" class="uploaded-image">  <!-- Corrected path to display image -->
                            <p>${advert.description}</p>
                            <small>Posted by: ${advert.username} (${advert.address}) in ${advert.categoryName} on ${advert.created_at}</small>
                        </div>
                    `;
                });
                $('#adverts').html(advertsHtml);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching adverts:', textStatus, errorThrown);
            });
        }

            $('#advert-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'publish');
                $.ajax({
                    url: 'main.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Advert published:', response);
                        fetchAdverts();
                        $('#advert-form')[0].reset();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error publishing advert:', textStatus, errorThrown);
                    }
                });
            });

            $('#category-filter').on('change', function() {
                const category = $(this).val();
                fetchAdverts(category);
            });
            //calls the fetchAdverts on new search term being entered
            $('#search-button').on('click', function() {
                const category = $('#category-filter').val();
                const searchTerm = $('#search-term').val();
                fetchAdverts(category, searchTerm);
            });

            fetchAdverts();
        });
    </script>
</body>
</html>
