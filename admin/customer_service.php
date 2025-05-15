<?php
session_start();
require_once '../includes/db_connection.php';

// Fetch recent queries
$inquiry_recent = "SELECT * FROM inquiry ORDER BY created_at DESC LIMIT 5";
$result_recent = $conn->query($inquiry_recent);  // Corrected here

// Fetch notification history
$inquiry_notifications = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5";
$result_notifications = $conn->query($inquiry_notifications);  // Corrected here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service</title>
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Ensure correct path -->
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

        .card-container {
            display: flex;
            gap: 20px;
        }

        .card {
            flex: 1;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            margin-top: 0;
        }

        .inquiry-item, .notification-item {
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 15px;
            color: white;
            background-color: #ffc107; /* Pending status */
        }

        .status-resolved {
            background-color: #28a745; /* Resolved status */
        }

        .notification-item small {
            color: gray;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .btn:hover {
            background-color: #003d80;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="admin_dashboard.php" class="back-arrow">&larr;</a>
        <h1>Electricity Billing System</h1>
        <h2>Customer Service</h2>
    </div>
    <div class="container">
        <div class="card-container">
            <div class="card">
                <h2>Recent Queries</h2>
                <?php while ($row = $result_recent->fetch_assoc()): ?>
                    <div class="inquiry-item">
                        <span class="status <?php echo $row['status'] === 'Resolved' ? 'status-resolved' : ''; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                        <p><strong>Support Ticket #<?php echo $row['ticket_id']; ?></strong></p>
                        <p><?php echo $row['inquiry']; ?></p>
                    </div>
                <?php endwhile; ?>
                <div class="btn-container">
                    <a href="support_tickets.php" class="btn">View All Support Tickets</a>
                </div>
            </div>
            <div class="card">
                <h2>Notification History</h2>
                <?php while ($row = $result_notifications->fetch_assoc()): ?>
                    <div class="notification-item">
                        <p><strong><?php echo $row['title']; ?></strong></p>
                        <p><?php echo $row['description']; ?></p>
                        <small><?php echo $row['created_at']; ?></small>
                    </div>
                <?php endwhile; ?>
                <div class="btn-container">
                    <a href="payment_notifications.php" class="btn">Manage Payment Notifications</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
