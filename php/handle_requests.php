<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}

$db = new SQLite3("../grupp.db");

function sendJsonResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'publish' && isset($_POST['title'], $_POST['description'], $_POST['category'], $_POST['address'])) {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $categoryID = intval($_POST['category']);
        $address = htmlspecialchars($_POST['address']);
        $userID = $_SESSION['userID'];

        // Validate description length
        if (strlen($description) > 500) {
            sendJsonResponse('error', 'Description is too long. Maximum length is 500 characters.');
        }

        // Handle file upload
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;
        $image = "";

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            sendJsonResponse('error', 'File is not an image.');
        }

        if (file_exists($target_file)) {
            sendJsonResponse('error', 'Sorry, file already exists.');
        }

        if ($_FILES["image"]["size"] > 2000000) {
            sendJsonResponse('error', 'Sorry, your file is too large.');
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            sendJsonResponse('error', 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = "uploads/" . basename($_FILES["image"]["name"]);
        } else {
            sendJsonResponse('error', 'Sorry, there was an error uploading your file.');
        }

        $stmt = $db->prepare("INSERT INTO Adverts (userID, title, description, categoryID, address, image) VALUES (:userID, :title, :description, :categoryID, :address, :image)");
        $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':categoryID', $categoryID, SQLITE3_INTEGER);
        $stmt->bindValue(':address', $address, SQLITE3_TEXT);
        $stmt->bindValue(':image', $image, SQLITE3_TEXT);
        if ($stmt->execute()) {
            sendJsonResponse('success', 'Advert published successfully.');
        } else {
            sendJsonResponse('error', 'Failed to publish advert.');
        }
    }

    if ($_POST['action'] == 'fetch') {
        $categoryFilter = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $searchTerm = isset($_POST['searchTerm']) ? htmlspecialchars($_POST['searchTerm']) : '';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $resultsPerPage = isset($_POST['resultsPerPage']) ? intval($_POST['resultsPerPage']) : 10;
        $offset = ($page - 1) * $resultsPerPage;

        $query = "SELECT Adverts.*, Users.username, Categories.name as categoryName FROM Adverts JOIN Users ON Adverts.userID = Users.userID JOIN Categories ON Adverts.categoryID = Categories.id";
        if ($categoryFilter > 0) {
            $query .= " WHERE Adverts.categoryID = :categoryID";
        }
        if (!empty($searchTerm)) {
            $query .= " AND Adverts.title LIKE :searchTerm";
        }
        $query .= " ORDER BY created_at DESC LIMIT :resultsPerPage OFFSET :offset";

        $stmt = $db->prepare($query);
        if ($categoryFilter > 0) {
            $stmt->bindValue(':categoryID', $categoryFilter, SQLITE3_INTEGER);
        }
        if (!empty($searchTerm)) {
            $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', SQLITE3_TEXT);
        }
        $stmt->bindValue(':resultsPerPage', $resultsPerPage, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);

        $adverts = $stmt->execute();
        $result = [];
        while ($row = $adverts->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $row;
        }
        echo json_encode($result);

        exit();
    }

    if ($_POST['action'] == 'count') {
        $categoryFilter = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $searchTerm = isset($_POST['searchTerm']) ? htmlspecialchars($_POST['searchTerm']) : '';

        $query = "SELECT COUNT(*) as total 
                  FROM Adverts 
                  JOIN Users ON Adverts.userID = Users.userID 
                  JOIN Categories ON Adverts.categoryID = Categories.id ";

        if ($categoryFilter > 0) {
            $query .= " AND Adverts.categoryID = :categoryID";
        }

        if (!empty($searchTerm)) {
            $query .= " AND Adverts.title LIKE :searchTerm";
        }

        $stmt = $db->prepare($query);

        if ($categoryFilter > 0) {
            $stmt->bindValue(':categoryID', $categoryFilter, SQLITE3_INTEGER);
        }

        if (!empty($searchTerm)) {
            $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', SQLITE3_TEXT);
        }

        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        echo json_encode($result);
        exit();
    }
}
?>
