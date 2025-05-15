<?php
session_start();
require_once '../includes/db_connection.php'; // Ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $role = $_POST['role'];
    $status = $_POST['status'];

    $query = "INSERT INTO users (id, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $id, $name, $email, $password, $role, $status);

    if ($stmt->execute()) {
        $success = "User added successfully!";
    } else {
        $error = "Error: Could not add user. " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
        }

        .back-arrow {
            display: inline-block;
            margin-bottom: 10px;
            font-size: 24px;
            color: #0056b3;
            text-decoration: none;
        }

        .back-arrow:hover {
            color: #003d80;
        }

        h1 {
            color: #0056b3;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 10px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #003d80;
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
        <a href="user_management.php" class="back-arrow">&larr;</a>
        <h1>Add User</h1>
        <?php if (isset($success)): ?>
            <div class="message success"> <?php echo $success; ?> </div>
        <?php elseif (isset($error)): ?>
            <div class="message error"> <?php echo $error; ?> </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="id">User ID</label>
                <input type="text" id="id" name="id" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin">Admin</option>
                    <option value="Billing Processor">Billing Processor</option>
                    <option value="Manager">Manager</option>
                    <option value="Customer">Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn">Add User</button>
        </form>
    </div>
</body>
</html>
