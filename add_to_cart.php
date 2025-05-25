<?php
session_start();

$host = 'localhost';
$dbname = 'db_gamer';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid game ID.");
    }

    $gameId = (int)$_GET['id'];

    // Fetch game from DB
    $stmt = $pdo->prepare("SELECT * FROM gamerrob WHERE id = ?");
    $stmt->execute([$gameId]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        die("Game not found.");
    }

    if ($game['quantity'] <= 0) {
        die("Sorry, this game is out of stock.");
    }

    // Initialize cart session if needed
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update item in cart
    if (!isset($_SESSION['cart'][$gameId])) {
        // Add new item to cart with quantity 1
        $_SESSION['cart'][$gameId] = [
            'name' => $game['name'],
            'price' => $game['price'],
            'quantity' => 1,
            'image' => $game['image']
        ];

        // Update DB stock (subtract 1)
        $newStock = $game['quantity'] - 1;
        $updateStmt = $pdo->prepare("UPDATE gamerrob SET quantity = ? WHERE id = ?");
        $updateStmt->execute([$newStock, $gameId]);

    } else {
        // Check if adding one more exceeds stock
        if ($_SESSION['cart'][$gameId]['quantity'] < $game['quantity']) {
            // Increase cart quantity by 1
            $_SESSION['cart'][$gameId]['quantity']++;

            // Update DB stock (subtract 1)
            $newStock = $game['quantity'] - 1;
            $updateStmt = $pdo->prepare("UPDATE gamerrob SET quantity = ? WHERE id = ?");
            $updateStmt->execute([$newStock, $gameId]);
        } else {
            die("Cannot add more, stock limit reached.");
        }
    }

    header("Location: cart.php?message=Added to cart successfully!");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
