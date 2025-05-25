<?php
// Database connection
$host = 'localhost';
$dbname = 'db_gamer';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get categories for the dropdown
    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM gamerrob");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Search functionality
    $search = $_GET['search'] ?? '';
    $categoryFilter = $_GET['category'] ?? '';

    // Build the SQL query
    $sql = "SELECT * FROM gamerrob WHERE 1";

    if (!empty($search)) {
        $sql .= " AND (name LIKE :search OR description LIKE :search OR category LIKE :search)";
    }

    if (!empty($categoryFilter)) {
        $sql .= " AND LOWER(category) = LOWER(:category)";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);

    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%");
    }

    if (!empty($categoryFilter)) {
        $stmt->bindValue(':category', $categoryFilter);
    }

    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & View Games | Heavenly Games Store</title>
    <link rel="icon" href="original.png" type="image/x-icon" />
    <style>
        body { font-family: Arial, sans-serif; background-color: #eef; margin: 0; padding: 0; }
        header { background-color: #333; color: white; padding: 20px; text-align: center; }
        .container { width: 80%; max-width: 800px; margin: 20px auto; }
        .upload-form { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        input, textarea, select, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { background-color: #4CAF50; color: white; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .game { display: flex; background-color: #f9f9f9; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 15px; }
        .game img { width: 120px; height: auto; margin-right: 20px; border-radius: 8px; }
        .game-details { flex: 1; }
        .edit-btn, .delete-btn { display: inline-block; padding: 8px 12px; border-radius: 4px; color: white; text-decoration: none; font-weight: bold; margin-top: 10px; margin-right: 8px; }
        .edit-btn { background-color: #007bff; }
        .delete-btn { background-color: #dc3545; }
        .edit-btn:hover { background-color: #0056b3; }
        .delete-btn:hover { background-color: #c82333; }
        .search-wrapper { text-align: center; margin: 30px 0; }
        .search-form { display: inline-flex; align-items: center; background-color: #fff; border: 2px solid #4CAF50; border-radius: 50px; padding: 5px 10px; }
        .search-input { border: none; outline: none; padding: 10px 15px; border-radius: 50px; font-size: 16px; width: 250px; }
        .search-button { background-color: #333; border: none; font-size: 18px; cursor: pointer; color: #4CAF50; padding: 8px; }
        footer { background-color: #333; color: white; padding: 10px; text-align: center; }
        footer a { color: #00ffff; text-decoration: none; }
        footer a:hover { color: #ffcc00; }
    </style>
</head>
<body>

<header>
    <h1>Heavenly Games Store</h1>
</header>

<div class="container">
    <!-- Game Upload Form -->
    <div class="upload-form">
        <h2>Upload New Game</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Game Name" required>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="RPG" <?= $categoryFilter === 'RPG' ? 'selected' : '' ?>>RPG</option>
                <option value="Sport" <?= $categoryFilter === 'Sport' ? 'selected' : '' ?>>Sport</option>
                <?php foreach ($categories as $category): ?>
                    <?php if (strtolower($category['category']) !== 'rpg' && strtolower($category['category']) !== 'sport'): ?>
                        <option value="<?= htmlspecialchars($category['category']) ?>" <?= $categoryFilter === $category['category'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <textarea name="description" placeholder="Game Description" required></textarea>
            <input type="number" step="0.01" name="price" placeholder="Price ($)" required>
            <input type="file" name="image" required>
            <input type="number" name="quantity" placeholder="Quantity in Stock" min="0" required>
            <button type="submit">Upload Game</button>
        </form>
    </div>

    <!-- Search Form -->
    <div class="search-wrapper">
        <form method="get" action="" class="search-form">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search games..." class="search-input">
            <button type="submit" class="search-button">🔍</button>
        </form>
    </div>

    <!-- Category Filter -->
    <div class="search-wrapper">
        <form method="get" action="" class="search-form">
            <select name="category" onchange="this.form.submit()">
                <option value="">Filter by Category</option>
                <option value="RPG" <?= $categoryFilter === 'RPG' ? 'selected' : '' ?>>RPG</option>
                <option value="Sport" <?= $categoryFilter === 'Sport' ? 'selected' : '' ?>>Sport</option>
                <?php foreach ($categories as $category): ?>
                    <?php if (strtolower($category['category']) !== 'rpg' && strtolower($category['category']) !== 'sport'): ?>
                        <option value="<?= htmlspecialchars($category['category']) ?>" <?= $categoryFilter === $category['category'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <h2>Uploaded Games</h2>
    <?php if (!empty($games)): ?>
        <?php foreach ($games as $game): ?>
            <div class="game">
                <img src="uploads/<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['name']) ?>">
                <div class="game-details">
                    <h3><?= htmlspecialchars($game['name']) ?></h3>
                    <p><strong>Category:</strong> <?= htmlspecialchars($game['category']) ?></p>
                    <p><?= htmlspecialchars($game['description']) ?></p>
                    <strong>$<?= number_format($game['price'], 2) ?></strong><br>
                    <a href="edit_game.php?id=<?= $game['id'] ?>" class="edit-btn">Edit</a>
                    <a href="delete_game.php?id=<?= $game['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this game?');">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No games uploaded<?= !empty($search) ? ' matching your search' : '' ?> yet.</p>
    <?php endif; ?>
</div>

<footer>
    &copy; <?= date('Y') ?> Heavenly Games Store | Powered by <a href="https://openai.com/chatgpt" target="_blank">ChatGPT</a>
</footer>

</body>
</html>
