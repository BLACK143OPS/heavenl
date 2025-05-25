<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: form.html');
    exit;
}

// Assume profile image path is stored in session, e.g., "uploads/imagename.jpg"
$profile_img = (!empty($_SESSION['profile_img']) && file_exists($_SESSION['profile_img']))
    ? $_SESSION['profile_img']
    : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Heavenly Gamer E-commerce Site</title>
    <link rel="icon" href="original.png" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Trebuchet MS', Arial, sans-serif;
            background-image: url("adce9e5f66922faf24a16da02da74167.gif");
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            backdrop-filter: blur(30px);
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #ffffff;
        }

        header {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.7);
            width: 100%;
            text-align: center;
            z-index: 9999;
            position: relative;
        }

        header .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        header img.logo {
            width: 90px;
            height: 90px;
        }

        header h1 {
            color: white;
            font-size: 40px;
            text-shadow: 0 0 10px cyan, 0 0 20px cyan;
            margin: 0 20px;
            flex-grow: 1;
            text-align: left;
        }

        header .profile {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #00ffff;
            font-weight: bold;
            font-size: 16px;
            user-select: none;
        }

        header .profile img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid #00ffff;
            object-fit: cover;
            box-shadow:
                0 0 6px #00ffff,
                0 0 12px #00ffffaa,
                0 0 20px #00ffffcc;
            background-color: #111;
            transition: box-shadow 0.3s ease;
        }

        header .profile img:hover {
            box-shadow:
                0 0 8px #66ffff,
                0 0 16px #66ffffcc,
                0 0 24px #66ffffdd;
            cursor: pointer;
        }

        header .profile .no-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background-color: #222;
            border: 2px solid #00ffff;
            box-shadow:
                0 0 6px #00ffff,
                0 0 12px #00ffffaa,
                0 0 20px #00ffffcc;
        }

        header .profile a {
            color: #00ffff;
            text-decoration: none;
            transition: color 0.3s;
        }

        header .profile a:hover {
            color: #66ffff;
        }

        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            background-color: #333;
            position: sticky;
            top: 0;
            width: 100%;
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

        .container {
            display: flex;
            justify-content: center;
            padding: 30px 20px;
            max-width: 1200px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .box {
            width: 100%;
            max-width: 450px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        .box h2 {
            font-size: 30px;
            border: white 2px solid;
            padding: 5px 14px 5px;
            border-radius: 10px;
            background: #000;
            color: #fff;
            margin-bottom: 80px;
            margin-top: 70px;
            font-weight: bold;
            text-transform: uppercase;
            text-shadow: 0 0 10px cyan, 0 0 20px cyan;
            text-align: center;
        }

        .box p {
            color: #ddd;
            font-size: 16px;
            width: 400px;
            line-height: 1.6;
            text-align: justify;
            padding: 10px;
            background: rgba(200, 148, 161, 0.8);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            margin: 0 auto;
        }

        #outerbox {
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin: 0 auto;
        }

        #sliderbox {
            position: relative;
            display: flex;
            width: 100%;
            height: 510px;
            animation: slider 40s infinite linear;
        }

        #sliderbox img {
            width: 100%;
            height: 100%;
        }

        @keyframes slider {
            0%,
            10% {
                transform: translateX(0);
            }

            12.5%,
            22.5% {
                transform: translateX(-100%);
            }

            25%,
            35% {
                transform: translateX(-200%);
            }

            37.5%,
            47.5% {
                transform: translateX(-300%);
            }

            50%,
            60% {
                transform: translateX(-400%);
            }

            62.5%,
            72.5% {
                transform: translateX(-500%);
            }

            75%,
            85% {
                transform: translateX(-600%);
            }

            87.5%,
            97.5% {
                transform: translateX(-700%);
            }

            100% {
                transform: translateX(-800%);
            }
        }

        footer {
            background-color: #000;
            color: white;
            text-align: center;
            padding: 20px 0;
            width: 100%;
        }

        footer a {
            color: #00ffff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #ffcc00;
        }

        footer img {
            width: 60px;
            height: 60px;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <div style="display:flex; align-items:center;">
                <img src="original.png" alt="Heavenly Games Store Logo" class="logo" />
                <h1>Heavenly Games Store</h1>
            </div>
            <div class="profile">
                <?php if ($profile_img): ?>
                    <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profile Image" />
                <?php else: ?>
                    <div class="no-image"></div>
                <?php endif; ?>
                <span>Hello, <?= htmlspecialchars($_SESSION['username']) ?></span> |
                <a href="logout.php" style="color: #ffd700;">Logout</a>
            </div>
        </div>
    </header>

    <nav>
        <a href="home.php"><i class="fas fa-home"></i> Home</a>
        <a href="games.php"><i class="fas fa-gamepad"></i> Games</a>
        <a href="about-us.html"><i class="fas fa-info-circle"></i> About Us</a>
    </nav>

    <div class="container">
        <div class="box">
            <h2>Welcome to Heavenly Games Store</h2>
            <center>
                <p>Heavenly Games Store is your gateway to the best gaming experience. Explore action-packed
                    adventures, immersive RPGs, and a vast selection of indie games! We offer high-quality titles
                    that suit every gamer’s preference. Let’s dive into the world of gaming!</p>
            </center>
        </div>

        <div class="box">
            <div id="outerbox">
                <div id="sliderbox">
                    <img src="FICKGFAJDOUSMMU.webp" alt="Game 1" />
                    <img src="amoguslandscape_2560x1440-3fac17e8bb45d81ec9b2c24655758075.jpeg" alt="Game 2" />
                    <img src="cod-black-ops-6-vault-edition.webp" alt="Game 3" />
                    <img src="06f9b422-9489-42bc-bea0-00fdcda498b8.jpg" alt="Game 4" />
                    <img src="forza-horizon-5-button-fin-1629830068379.jpg" alt="Game 5" />
                    <img src="305785-The_Texas_Chain_Saw_Massacre-movies-horror.jpg" alt="Game 6" />
                    <img src="OIP (5).jpeg" alt="Game 7" />
                    <img src="ghost-of-tsushima.jpg" alt="Game 8" />
                    <img src="hades_3277133b.jpg" alt="Game 9" />
                </div>
            </div>
        </div>
    </div>

    <footer>
        <img src="original.png" alt="Heavenly Games Logo" />
        <p>© 2024 Heavenly Games Store. All Rights Reserved.</p>
        <a href="contact.html">Contact Us</a>
    </footer>
</body>

</html>
