<?php

$host = 'localhost';
$dbname = 'db_gamer';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if ID is provided and numeric
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        // Get the image filename before deleting
        $stmt = $pdo->prepare("SELECT image FROM gamerrob WHERE id = ?");
        $stmt->execute([$id]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete image file from server
        if ($game && !empty($game['image'])) {
            $imagePath = 'uploads/' . $game['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete the game from the database
        $stmt = $pdo->prepare("DELETE FROM gamerrob WHERE id = ?");
        $stmt->execute([$id]);

        // Redirect back after delete
        header("Location: lab2.php");
        exit;
    } else {
        die("Invalid request. ID must be provided.");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
