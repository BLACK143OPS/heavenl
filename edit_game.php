<?php
$host = 'localhost';
$dbname = 'db_gamer';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch available categories (static list)
    $categories = ['Action', 'Adventure', 'RPG', 'FPS', 'TPS', 'Racing', 'Survival', 'Sports'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = floatval($_POST['price']);
        $category = $_POST['category'];
        $quantity = intval($_POST['quantity']);  // <-- added quantity
        $newImage = $_FILES['image'];

        if ($newImage['size'] > 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileExtension = pathinfo($newImage['name'], PATHINFO_EXTENSION);
            if (!in_array($newImage['type'], $allowedTypes)) {
                die("Invalid image type.");
            }
            if ($newImage['size'] > 5 * 1024 * 1024) {
                die("Image too large (Max 5MB).");
            }

            // Sanitize the filename
            $filename = uniqid() . '-' . basename($newImage['name']);
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . $filename;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($newImage['tmp_name'], $uploadFile)) {
                // Update including new image and quantity
                $stmt = $pdo->prepare("UPDATE gamerrob SET name = ?, description = ?, price = ?, category = ?, quantity = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $category, $quantity, $filename, $id]);
            } else {
                die("Error uploading new image.");
            }
        } else {
            // Update without image, but with quantity
            $stmt = $pdo->prepare("UPDATE gamerrob SET name = ?, description = ?, price = ?, category = ?, quantity = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category, $quantity, $id]);
        }

        header("Location: lab2.php");
        exit();
    }

    if (!isset($_GET['id'])) {
        die("Missing game ID.");
    }

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM gamerrob WHERE id = ?");
    $stmt->execute([$id]);
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
    <meta charset="UTF-8">
    <title>Edit Game | Heavenly Games Store</title>
    <link rel="icon" href="original.png" type="image/x-icon" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        input, textarea, button, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        img {
            max-width: 100px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        footer {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    <h1>Edit Game</h1>
</header>

<div class="container">
    <form method="post" action="edit_game.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($game['id']) ?>">
        
        <label>Game Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($game['name']) ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($game['description']) ?></textarea>

        <label>Price ($):</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($game['price']) ?>" required>

        <label>Category:</label>
        <select name="category" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>" <?= $cat === $game['category'] ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>

        <label>Quantity in Stock:</label>
        <input type="number" name="quantity" min="0" value="<?= htmlspecialchars($game['quantity']) ?>" required>

        <label>Current Image:</label><br>
        <img src="uploads/<?= htmlspecialchars($game['image']) ?>" alt="Current Image"><br>

        <label>New Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Update Game</button>
    </form>
</div>

<footer>
    <p>© 2024 Heavenly Games Store</p>
</footer>

</body>
</html>
