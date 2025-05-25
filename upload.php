<?php
// Database connection
$host = 'localhost';
$dbname = 'db_gamer';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        // Sanitize and retrieve form data
        $name = htmlspecialchars(trim($_POST['name']));
        $category = htmlspecialchars(trim($_POST['category']));
        $description = htmlspecialchars(trim($_POST['description']));
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);  // <-- new: get quantity as int
        
        // File details
        $fileType = $_FILES['image']['type'];
        $allowedTypes = ['image/jpeg', 'image/jpg','image/webp', 'image/png', 'image/gif'];

        // Validate the file type and size
        if (!in_array($fileType, $allowedTypes)) {
            die("Only JPEG, PNG, or GIF images are allowed.");
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            die("File size is too large (Max 5MB).");
        } else {
            // Generate a unique filename to prevent overwriting
            $filename = uniqid() . '-' . basename($_FILES['image']['name']);
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . $filename;

            // Create the uploads directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Attempt to move the uploaded file to the desired location
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                // Insert game information into the database including quantity
                $stmt = $pdo->prepare("INSERT INTO gamerrob (name, category, description, price, quantity, image) VALUES (:name, :category, :description, :price, :quantity, :image)");
                $stmt->execute([
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'price' => $price,
                    'quantity' => $quantity,   // <-- bind quantity
                    'image' => $filename
                ]);

                // Redirect to lab2.php after successful upload
                header("Location: lab2.php");
                exit;
            } else {
                die("Error uploading the image.");
            }
        }
    } else {
        die("No file uploaded.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
