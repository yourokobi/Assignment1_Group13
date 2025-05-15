<?php
session_start();
require_once '../includes/db_connection.php'; // Ensure this connects to your database

if (!isset($_GET['id'])) {
    die("User ID is required.");
}

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $query = "UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $name, $email, $role, $status, $id);

    if ($stmt->execute()) {
        $success = "User updated successfully!";
    } else {
        $error = "Error: Could not update user. " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            text-align: center;
            color: #0056b3;
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
            width: 100%;
            padding: 10px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d80;
        }

        .message {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="user_management.php" class="back-arrow">&larr;</a>
        <h1>Edit User</h1>
        <?php if (isset($success)): ?>
            <div class="message success"> <?php echo $success; ?> </div>
        <?php elseif (isset($error)): ?>
            <div class="message error"> <?php echo $error; ?> </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="id">User ID</label>
                <input type="text" id="id" name="id" value="<?php echo $user['id']; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin" <?php echo $user['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Billing Processor" <?php echo $user['role'] === 'Billing Processor' ? 'selected' : ''; ?>>Billing Processor</option>
                    <option value="Manager" <?php echo $user['role'] === 'Manager' ? 'selected' : ''; ?>>Manager</option>
                    <option value="Customer" <?php echo $user['role'] === 'Customer' ? 'selected' : ''; ?>>Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Active" <?php echo $user['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $user['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn">Update User</button>
        </form>
    </div>
</body>
</html>
