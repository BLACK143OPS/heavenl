<?php
session_start();

$host = 'localhost';
$dbname = 'db_gamer';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: form.html');
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        die('Please fill in both username and password. <a href="form.html">Try again</a>');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        // Store profile image filename or fallback to default image
        $_SESSION['profile_img'] = !empty($user['userimage']) ? $user['userimage'] : 'default-user.png';

        header('Location: home.php');
        exit;
    } else {
        echo "Invalid username or password. <a href='form.html'>Try again</a>";
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
