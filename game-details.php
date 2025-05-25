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
        die("Invalid request.");
    }

    $gameId = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM gamerrob WHERE id = ?");
    $stmt->execute([$gameId]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        die("Game not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($game['name']) ?> - Game Details</title>
<link rel="icon" href="original.png" type="image/x-icon" />
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
/>
<style>
    body {
        margin: 0;
        font-family: 'Trebuchet MS', sans-serif;
        color: #fff;
        background: url('uploads/<?= htmlspecialchars($game['image']) ?>') center/cover fixed no-repeat;
        backdrop-filter: blur(10px);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
    }

    .container {
        background-color: rgba(0, 0, 0, 0.7);
        padding: 30px;
        border-radius: 15px;
        max-width: 800px;
        width: 100%;
        box-shadow: 0 0 25px #00ffff55;
    }

    h1 {
        font-size: 42px;
        text-align: center;
        color: #00ffff;
        text-shadow: 0 0 10px #00ffff;
        margin-bottom: 20px;
    }

    .game-image {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 0 15px #00ffff55;
    }

    .info {
        margin-top: 25px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .info-block {
        background-color: rgba(255, 255, 255, 0.05);
        padding: 15px 20px;
        border-left: 5px solid #00ffff;
        border-radius: 8px;
        box-shadow: 0 0 10px #00ffff22;
    }

    .info-block i {
        margin-right: 10px;
        color: #00ffff;
    }

    .info-block strong {
        color: #00ffff;
    }

    .btns {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .btn {
        padding: 12px 25px;
        background-color: #00ffff;
        color: #000;
        text-decoration: none;
        font-weight: bold;
        border-radius: 6px;
        transition: 0.3s;
        text-align: center;
    }

    .btn:hover {
        background-color: #00cccc;
        transform: scale(1.05);
    }

    @media (max-width: 600px) {
        .btns {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
</head>
<body>

<div class="container">
    <h1><?= htmlspecialchars($game['name']) ?></h1>
    <img src="uploads/<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['name']) ?>" class="game-image">

    <div class="info">
        <div class="info-block">
            <i class="fas fa-tag"></i>
            <strong>Price:</strong> $<?= number_format($game['price'], 2) ?>
        </div>
        <div class="info-block">
            <i class="fas fa-info-circle"></i>
            <strong>Description:</strong><br><?= nl2br(htmlspecialchars($game['description'])) ?>
        </div>
        <div class="info-block">
            <i class="fas fa-calendar-alt"></i>
            <strong>Release Date:</strong> <?= htmlspecialchars($game['created_at']) ?>
        </div>
        <div class="info-block">
            <i class="fas fa-box"></i>
            <strong>Available Quantity:</strong> <?= htmlspecialchars($game['quantity']) ?>
        </div>

        <?php if ($game['quantity'] > 0): ?>
            <a href="add_to_cart.php?id=<?= htmlspecialchars($game['id']) ?>" class="btn">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </a>
        <?php else: ?>
            <button class="btn" disabled>Out of Stock</button>
        <?php endif; ?>
    </div>

    <div class="btns">
        <a href="games.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Games</a>
    </div>
</div>

</body>
</html>
