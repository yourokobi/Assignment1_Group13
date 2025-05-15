<?php
session_start();
require_once '../includes/db_connection.php';

// Fetch all payment notifications in ascending order by ID
$query_notifications = "SELECT id, title, description, status, created_at FROM notifications ORDER BY CAST(id AS UNSIGNED) ASC";
$result_notifications = $conn->query($query_notifications);

// Handle notification action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];
    $action = $_POST['action'];
    $current_status_query = "SELECT status FROM notifications WHERE id = ?";
    $stmt_status = $conn->prepare($current_status_query);
    $stmt_status->bind_param("i", $notification_id);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();
    $status_row = $result_status->fetch_assoc();
    
    $current_status = $status_row['status'];
    
    if ($action === 'send_reconnection') {
        // If the current status is not already 'Reconnected', update it to 'Reconnected'
        if ($current_status !== 'Reconnected') {
            $update_query = "UPDATE notifications SET status = 'Reconnected' WHERE id = ?";
        }
    } elseif ($action === 'send_disconnection') {
        // If the current status is not already 'Disconnected', update it to 'Disconnected'
        if ($current_status !== 'Disconnected') {
            $update_query = "UPDATE notifications SET status = 'Disconnected' WHERE id = ?";
        }
    }

    // Execute the update if a valid action was determined
    if (isset($update_query)) {
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $notification_id);

        if ($stmt->execute()) {
            $success = "Notification status updated successfully!";
        } else {
            $error = "Error updating notification: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification History</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .header {
            background-color: #004080;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            margin: 0;
        }

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            color: white;
            text-decoration: none;
        }

        .back-arrow:hover {
            color: #ffc107;
        }

        .container {
            margin: 20px auto;
            width: 90%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #004080;
            color: white;
        }

        .btn {
            padding: 5px 15px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d80;
        }

        .form-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="customer_service.php" class="back-arrow">&larr;</a>
        <h1>Electricity Billing System</h1>
        <h2>Notification History</h2>
    </div>
    <div class="container">
        <?php if (isset($success)): ?>
            <p style="color: green; text-align: center;"> <?php echo $success; ?> </p>
        <?php elseif (isset($error)): ?>
            <p style="color: red; text-align: center;"> <?php echo $error; ?> </p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_notifications->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <form method="POST" action="" class="form-container">
                                <input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="send_disconnection" class="btn">Send Disconnection</button>
                                <button type="submit" name="action" value="send_reconnection" class="btn">Send Reconnection</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
