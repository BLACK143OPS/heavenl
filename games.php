<?php
$host = 'localhost';
$dbname = 'db_gamer';
$username = 'root';
$password = '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM gamerrob WHERE 1";

    if ($search) {
        $query .= " AND (name LIKE :search OR category LIKE :search)";
    }

    if ($categoryFilter) {
        $query .= " AND category = :category";
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);

    if ($search) {
        $stmt->bindValue(':search', "%$search%");
    }

    if ($categoryFilter) {
        $stmt->bindValue(':category', $categoryFilter);
    }

    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categoriesStmt = $pdo->query("SELECT DISTINCT category FROM gamerrob");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Games - Heavenly Games Store</title>
    <link rel="icon" href="original.png" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Trebuchet MS', sans-serif;
            background: url("adce9e5f66922faf24a16da02da74167.gif") center/cover fixed no-repeat;
            backdrop-filter: blur(30px);
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #ffffff;
        }
        header, footer {
            width: 100%;
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 1rem;
        }
        header img, footer img {
            width: 90px;
            height: 90px;
        }
        header h1 {
            font-size: 40px;
            text-shadow: 0 0 10px cyan, 0 0 20px cyan;
        }
        nav {
            display: flex;
            justify-content: center;
            background-color: #333;
            padding: 10px 0;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        nav a {
            color: #fff;
            padding: 12px 20px;
            margin: 0 15px;
            text-decoration: none;
            border-radius: 20px;
            background-color: #555;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
        }
        nav a:hover {
            background-color: #777;
            transform: scale(1.1);
        }
        .search-wrapper {
            text-align: center;
            margin: 30px 0;
        }
        .search-form {
            display: inline-flex;
            align-items: center;
            background-color: #fff;
            border: 2px solid #4CAF50;
            border-radius: 50px;
            padding: 5px 10px;
            transition: box-shadow 0.3s ease;
        }
        .search-form:hover {
            box-shadow: 0 0 10px rgba(0, 128, 0, 0.4);
        }
        .search-input {
            border: none;
            outline: none;
            padding: 10px 15px;
            border-radius: 50px;
            font-size: 16px;
            width: 250px;
            transition: width 0.4s ease-in-out;
        }
        .search-input:focus {
            width: 350px;
        }
        .search-button {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #4CAF50;
            padding: 8px;
            transition: color 0.3s ease;
        }
        .search-button:hover {
            color: #388e3c;
        }
        select[name="category"] {
            background: linear-gradient(145deg, #1a1a1a, #333);
            color: #00ffff;
            padding: 10px 15px;
            border-radius: 12px;
            border: 2px solid #00ffff;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        select[name="category"]:hover {
            background: #222;
            color: #fff;
            border-color: #4CAF50;
        }
        .category-box {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
            width: 95%;
            max-width: 1400px;
            margin-bottom: 50px;
        }
        .thumbnail-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            border: 2px solid #00ffff33;
            width: 100%;
            border-radius: 10px;
        }
        .thumbnail {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: #222;
            border: 2px solid #00ffff33;
            height: 250px;
            text-decoration: none;
        }
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }
        .thumbnail:hover img {
            transform: scale(1.1);
        }
        .description {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            color: #00ffff;
            text-align: center;
            font-weight: bold;
            padding: 8px;
            font-size: 14px;
            line-height: 1.4em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .thumbnail:hover .description {
            opacity: 1;
        }
        footer {
            padding: 1rem 0 2rem;
        }
        footer p {
            margin-top: 10px;
        }
        footer a {
            color: #00ffff;
            text-decoration: none;
            font-weight: bold;
        }
        footer a:hover {
            color: #ffcc00;
        }
        @media (max-width: 768px) {
            .search-input {
                width: 80%;
            }
        }
        .gallery {
            perspective: 1000px;
            display: flex;
            justify-content: center;
            padding: 50px 80px;
        }
        .gallery-container {
            width: 400px;
            height: 400px;
            position: relative;
            transform-style: preserve-3d;
            animation: rotate 8s infinite linear;
        }
        .gallery-image {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
        }
        .gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .gallery-image:nth-child(1) {
            transform: rotateY(0deg);
        }
        .gallery-image:nth-child(2) {
            transform: rotateY(90deg);
        }
        .gallery-image:nth-child(3) {
            transform: rotateY(180deg);
        }
        .gallery-image:nth-child(4) {
            transform: rotateY(270deg);
        }
        @keyframes rotate {
            0% {
                transform: rotateY(0deg);
            }
            100% {
                transform: rotateY(360deg);
            }
        }
          .gallery {
            perspective: 1000px;
            display: flex;
            justify-content: center;
            padding: 50px 80px;
        }

        .gallery-container {
            width: 400px;
            height: 400px;
            position: relative;
            transform-style: preserve-3d;
            animation: rotate 8s infinite linear;
        }

        .gallery-image {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
        }

        .gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .gallery-image:nth-child(1) {
            transform: rotateY(0deg);
        }

        .gallery-image:nth-child(2) {
            transform: rotateY(90deg);
        }

        .gallery-image:nth-child(3) {
            transform: rotateY(180deg);
        }

        .gallery-image:nth-child(4) {
            transform: rotateY(270deg);
        }

        @keyframes rotate {
            0% {
                transform: rotateY(0deg);
            }

            100% {
                transform: rotateY(360deg);
            }
        }
    </style>
</head>
<body>

<header>
    <img src="original.png" alt="Heavenly Games Store Logo" />
    <h1>Heavenly Games Store</h1>
</header>
  

<nav>
    <a href="home.php"><i class="fas fa-home"></i> Home</a>
    <a href="games.php"><i class="fas fa-gamepad"></i> Games</a>
    <a href="about-us.html"><i class="fas fa-info-circle"></i> About Us</a>
</nav>
  <center>
        <div class="gallery">
            <div class="gallery-container">
                <div class="gallery-image"><img src="breath-of-the-wild-box-art.jpg" alt="image 1"></div>
                <div class="gallery-image"><img src="Call-of-Duty-Modern-Warfare-II-7645-1653423060.jpg" alt="image 2"></div>
                <div class="gallery-image"><img src="OIP (2).jpeg" alt="image 3"></div>
                <div class="gallery-image"><img src="OIP (4).jpeg" alt="image 4"></div>
            </div>
        </div>
    </center>
<center>
    <div class="search-wrapper">
        <form method="GET" class="search-form" action="games.php">
            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Search Games..."
                value="<?= htmlspecialchars($search) ?>"
                autocomplete="off"
            />
            <select name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category']) ?>" <?= ($categoryFilter == $cat['category']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="search-button" title="Search">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</center>

<div class="category-box">
    <?php 
    $groupedGames = [];
    foreach ($games as $game) {
        $groupedGames[$game['category']][] = $game;
    }

    foreach ($groupedGames as $category => $categoryGames): ?>
        <div style="width: 100%;">
            <h3 style="text-align:center; text-transform:uppercase; color:#00ffff; text-shadow: 0 0 5px #00ffff;">
                <?= htmlspecialchars($category) ?> Games
            </h3>
            <div class="thumbnail-gallery">
                <?php foreach ($categoryGames as $game): ?>
                    <a href="game-details.php?id=<?= $game['id'] ?>" class="thumbnail">
                        <img src="uploads/<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['name']) ?>" />
                        <div class="description">
    <?= htmlspecialchars($game['name']) ?><br>
    <small><?= htmlspecialchars($game['category']) ?></small><br>
    $<?= number_format($game['price'], 2) ?><br>
    Quantity: <?= (int)$game['quantity'] ?>
</div>

                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<footer>
    <img src="original.png" alt="Heavenly Games Store Logo" />
    <p>© 2023 Heavenly Games Store</p>
    <p>
        Created by <a href="https://github.com/yourgithub" target="_blank" rel="noopener noreferrer">Your Name</a>
    </p>
</footer>

</body>
</html>
