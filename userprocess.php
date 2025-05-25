<?php
$host = 'localhost';
$dbname = 'db_gameracc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $birthdate = htmlspecialchars(trim($_POST['birthdate']));
        $usernameInput = htmlspecialchars(trim($_POST['username']));
        $passwordInput = htmlspecialchars(trim($_POST['password']));

        // Check if any field is empty
        if (
            empty($first_name) || empty($last_name) || empty($email) ||
            empty($birthdate) || empty($usernameInput) || empty($passwordInput)
        ) {
            echo "<script>
                    alert('All fields are required.');
                    window.location.href = 'registerform.html';
                  </script>";
            exit;
        }

        // Hash the password securely
        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);

        // Insert into the user table
        $stmt = $pdo->prepare("INSERT INTO user (first_name, last_name, username, email, password, birthdate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $usernameInput, $email, $hashedPassword, $birthdate]);

        echo "<script>
                alert('Registration successful!');
                window.location.href = 'registerform.html';
              </script>";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
