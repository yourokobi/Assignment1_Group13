<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Ensure correct path -->
    <style>
        .rounded-box {
            border: 2px solid #0056b3;
            border-radius: 15px;
            padding: 20px;
            background-color: #ffffff;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .rounded-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 16px;
            margin: 10px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .rounded-btn:hover {
            background-color: #003d80;
        }

        .action-group {
            text-align: center;
        }

        .action-card {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 15px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 10px;
            width: 250px;
        }

        .action-card h3 {
            margin-bottom: 10px;
        }

        .card-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Electricity Billing System</h1>
        <h2>Admin Dashboard</h2>
    </div>

    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="user_management.php">User Management</a></li>
        <li><a href="customer_service.php">Customer Service</a></li>
        <li><a href="system_maintenance.php">System Maintenance</a></li>
        <li><a href="generate_report.php">Reports & Analytics</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>

    <div class="content">
        <div class="rounded-box">
            <h1>Welcome, Admin!</h1>
            <p>Use the buttons below to navigate through the system:</p>
            <div class="action-group">
                <a href="user_management.php" class="rounded-btn">Manage Users</a>
                <a href="customer_service.php" class="rounded-btn">Customer Service</a>
                <a href="system_maintenance.php" class="rounded-btn">System Maintenance</a>
                <a href="generate_report.php" class="rounded-btn">Reports & Analytics</a>
            </div>
        </div>

        <div class="rounded-box">
            <h2>Quick Actions</h2>
            <div class="card-container">
                <div class="action-card">
                    <h3>View Users</h3>
                    <p>Quickly access and edit user data.</p>
                    <a href="user_management.php" class="rounded-btn">Go</a>
                </div>
                <div class="action-card">
                    <h3>Pending Requests</h3>
                    <p>Check pending customer service inquiries.</p>
                    <a href="customer_service.php" class="rounded-btn">Go</a>
                </div>
                <div class="action-card">
                    <h3>System Logs</h3>
                    <p>View and clear system logs as necessary.</p>
                    <a href="system_maintenance.php" class="rounded-btn">Go</a>
                </div>
                <div class="action-card">
                    <h3>Generate Reports</h3>
                    <p>Create detailed system and user activity reports.</p>
                    <a href="generate_report.php" class="rounded-btn">Go</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
