<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

  body {
    font-family: 'Montserrat', sans-serif;
    background: #121212;
    color: #eee;
    margin: 0;
    padding: 40px 20px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  h1 {
    color: #00e5ff;
    font-weight: 600;
    font-size: 2.5rem;
    margin-bottom: 30px;
    text-shadow: 0 0 8px #00e5ffaa;
  }

  .cart-container {
    width: 100%;
    max-width: 900px;
  }

  /* Card style for each cart item */
  .cart-item {
    display: flex;
    background: #1e1e1e;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0, 229, 255, 0.25);
    overflow: hidden;
    transition: transform 0.3s ease;
  }
  .cart-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 229, 255, 0.45);
  }

  .item-image {
    flex: 0 0 120px;
    height: 120px;
    overflow: hidden;
  }
  .item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .item-details {
    flex: 1;
    padding: 15px 25px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .item-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #00e5ff;
    margin-bottom: 8px;
    text-shadow: 0 0 5px #00e5ffaa;
  }

  .item-description {
    font-size: 0.9rem;
    color: #ccc;
    margin-bottom: 12px;
    line-height: 1.3;
    max-height: 3.9em; /* approx 3 lines */
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .item-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1rem;
    color: #ddd;
  }

  .price {
    font-weight: 600;
    color: #00e5ff;
    font-size: 1.1rem;
  }

  .quantity {
    background: #007a8a;
    padding: 5px 14px;
    border-radius: 20px;
    font-weight: 600;
    color: #e0f7fa;
    user-select: none;
  }

  /* Message box */
  .message {
    background-color: #00bcd4;
    color: #000;
    padding: 15px 25px;
    margin-bottom: 30px;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 0 15px #00bcd4aa;
    text-align: center;
  }

  /* Back button */
  .back-link {
    display: inline-block;
    margin-top: 30px;
    padding: 12px 40px;
    background: #00e5ff;
    color: #000;
    text-decoration: none;
    font-weight: 700;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0, 229, 255, 0.6);
    transition: background 0.3s ease;
  }
  .back-link:hover {
    background: #00a3b5;
    box-shadow: 0 8px 20px rgba(0, 163, 181, 0.8);
  }

  /* Empty cart message */
  .empty-msg {
    font-size: 1.2rem;
    font-weight: 600;
    color: #666;
    margin-top: 40px;
    text-align: center;
  }

  /* Responsive */
  @media (max-width: 600px) {
    .cart-item {
      flex-direction: column;
      height: auto;
    }
    .item-image {
      flex: none;
      height: 200px;
    }
    .item-details {
      padding: 15px 20px;
    }
  }
</style>
</head>
<body>

<h1>Your Cart</h1>

<div class="cart-container">

<?php if (isset($_GET['message'])): ?>
    <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
<?php endif; ?>

<?php if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])): ?>
    <div class="empty-msg">Your cart is empty.</div>
<?php else: ?>
    <?php foreach ($_SESSION['cart'] as $gameId => $item): ?>
        <?php
            if (!is_array($item)) continue;

            $name = htmlspecialchars($item['name'] ?? 'Unknown');
            $price = number_format(floatval($item['price'] ?? 0), 2);
            $quantity = intval($item['quantity'] ?? 0);
            $image = htmlspecialchars($item['image'] ?? 'no-image.png');
            $description = htmlspecialchars($item['description'] ?? '');
        ?>
        <div class="cart-item">
            <div class="item-image">
                <img src="uploads/<?= $image ?>" alt="<?= $name ?>" loading="lazy" />
            </div>
            <div class="item-details">
                <div class="item-name"><?= $name ?></div>
                <div class="item-description"><?= $description ?></div>
                <div class="item-meta">
                    <div class="price">$<?= $price ?></div>
                    <div class="quantity">Qty: <?= $quantity ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>

<a href="games.php" class="back-link">← Back to Games</a>

</body>
</html>
