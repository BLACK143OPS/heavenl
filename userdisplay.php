<?php
$host = 'localhost';
$dbname = 'db_gameracc';
$username = 'root';
$password = '';

$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($search) {
        $parts = preg_split('/\s+/', trim($search));
        if (count($parts) >= 2) {
            $first = $parts[0];
            $last = $parts[1];
            $stmt = $pdo->prepare("SELECT * FROM user WHERE first_name LIKE :first AND last_name LIKE :last");
            $stmt->execute([
                'first' => "%$first%",
                'last' => "%$last%"
            ]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM user WHERE first_name LIKE :search OR last_name LIKE :search");
            $stmt->execute(['search' => "%$search%"]);
        }
    } else {
        $stmt = $pdo->query("SELECT * FROM user");
    }

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Registered Users</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #eef2f3; padding: 20px; }
            table { width:100%; margin: 0 auto; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #007BFF; color: white; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .center-btn {
                display: block;
                width: 80%;
                margin: 20px auto;
                padding: 15px;
                text-align: center;
                background-color: #007BFF;
                color: white;
                border: none;
                font-size: 16px;
                cursor: pointer;
                text-decoration: none;
                border-radius: 5px; 
            }
            .center-btn:hover {
                background-color: #0056b3; 
            }
            .container {
                width: 100%;
                text-align: center;
                margin-bottom: 20px;
            }
            .search-bar input {
                padding: 10px;
                width: 1150px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            .search-bar button {
                padding: 10px;
                border: 1px solid #ccc;
                background-color: #007BFF;
                color: white;
                border-radius: 5px;
                cursor: pointer;
            }
            .search-bar button:hover {
                background-color: #0056b3;
            }
            .action-btn {
                padding: 6px 12px;
                text-decoration: none;
                border-radius: 4px;
                font-size: 14px;
                font-weight: bold;
                color: white;
                display: inline-block;
                text-align: center;
            }
            .edit-btn {
                background-color: #28a745;
            }
            .edit-btn:hover {
                background-color: #218838;
            }
            .delete-btn {
                background-color: #dc3545;
            }
            .delete-btn:hover {
                background-color: #c82333;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <form method='GET' class='search-bar'>
                <input type='text' name='search' placeholder='Search by First or Last Name...' value='" . htmlspecialchars($search) . "'>
                <button type='submit'>Search</button>
            </form>
        </div>

        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Birthdate</th>
                <th>Registered At</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>";

    if ($results) {
        foreach ($results as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['first_name']) . "</td>
                    <td>" . htmlspecialchars($row['last_name']) . "</td>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['birthdate']) . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                    <td><a href='edit_user.php?id=" . $row['id'] . "' class='action-btn edit-btn'>Edit</a></td>
                    <td><a href='delete_user.php?id=" . $row['id'] . "' class='action-btn delete-btn' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a></td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No users found.</td></tr>";
    }

    echo "</table>
        <br><a href='registerform.html' class='center-btn'>Go Back to Registration Form</a>
    </body>
    </html>";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
