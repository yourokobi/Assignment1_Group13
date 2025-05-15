<?php
session_start();
require_once '../includes/db_connection.php'; // Ensure this connects to your database

if (!isset($_GET['id'])) {
    die("User ID is required.");
}

$id = $_GET['id'];

// Attempt to delete the user from the database
$query = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    $success = "User deleted successfully!";
} else {
    $error = "Error: Could not delete user. " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Ensure correct path -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../assets/fabric--plaid--1920x1080.png'); /* Correct the path */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 400px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #d9534f;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d80;
        }

        .btn-back {
            background-color: #6c757d;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .message {
            margin: 20px 0;
            font-size: 18px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete User</h1>
        <?php if (isset($success)): ?>
            <div class="message success"> <?php echo $success; ?> </div>
            <a href="user_management.php" class="btn">Back to User Management</a>
        <?php elseif (isset($error)): ?>
            <div class="message error"> <?php echo $error; ?> </div>
            <a href="user_management.php" class="btn btn-back">Back to User Management</a>
        <?php endif; ?>
    </div>
</body>
</html>
