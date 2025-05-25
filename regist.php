<?php
session_start();

$host = 'localhost';
$dbname = 'db_gamer';
$dbUser = 'root';
$dbPass = '';

try {
    // Connect to database with PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: register.html');
        exit;
    }

    // Get and sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        die('Please fill in both username and password.');
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        die("Username already taken. <a href='index.html'>Try a different one</a>");
    }

    // Handle image upload
    $userImagePath = null;
    if (isset($_FILES['userimage']) && $_FILES['userimage']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['userimage']['type'];
        
        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            die("Invalid image type. Allowed types: JPG, PNG, GIF.");
        }

        // Check for upload errors
        if ($_FILES['userimage']['error'] !== UPLOAD_ERR_OK) {
            die("Error uploading image.");
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Securely generate unique filename
        $ext = strtolower(pathinfo($_FILES['userimage']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowedExts)) {
            die("Invalid file extension.");
        }

        $uniqueName = uniqid('userimg_', true) . '.' . $ext;
        $targetFile = $uploadDir . $uniqueName;

        if (!move_uploaded_file($_FILES['userimage']['tmp_name'], $targetFile)) {
            die("Failed to move uploaded image.");
        }

        // Store relative path for database
        $userImagePath = 'uploads/' . $uniqueName;
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare insert query based on presence of image
    if ($userImagePath) {
        $insertQuery = "INSERT INTO users (username, password, userimage) VALUES (?, ?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$username, $hashedPassword, $userImagePath]);
    } else {
        $insertQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$username, $hashedPassword]);
    }

    // Registration successful, log user in
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;

    // Redirect to home page
    header('Location: home.php');
    exit;

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
