<?php
session_start();
require_once '../includes/db_connection.php';

// Fetch the last backup information
$query_backup = "SELECT * FROM backups ORDER BY created_at DESC LIMIT 1";
$result_backup = $conn->query($query_backup);
$last_backup = $result_backup->fetch_assoc();

// Fetch system logs
$query_logs = "SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 5";
$result_logs = $conn->query($query_logs);

// Handle system backup action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'initialize_backup') {
        // Simulate backup process
        $backup_status = "Successful";
        $created_at = date('Y-m-d H:i:s');

        $insert_backup = "INSERT INTO backups (status, created_at) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_backup);
        $stmt->bind_param("ss", $backup_status, $created_at);
        $stmt->execute();

        $success = "System backup initialized successfully.";
    } elseif ($_POST['action'] === 'clear_logs') {
        // Clear system logs
        $delete_logs = "DELETE FROM system_logs";
        if ($conn->query($delete_logs)) {
            $success = "System logs cleared successfully.";
        } else {
            $error = "Error clearing system logs: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
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

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d80;
        }

        .btn-red {
            background-color: #d9534f;
        }

        .btn-red:hover {
            background-color: #c9302c;
        }

        .log-item {
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="admin_dashboard.php" class="back-arrow">&larr;</a>
        <h1>Electricity Billing System</h1>
        <h2>System Maintenance</h2>
    </div>
    <div class="container">
        <div class="card">
            <h2>System Backup</h2>
            <form method="POST" action="">
                <button type="submit" name="action" value="initialize_backup" class="btn">Initialize Backup</button>
            </form>
            <div class="log-item">
                <p>Last backup: <?php echo $last_backup ? $last_backup['created_at'] : 'No backups yet'; ?></p>
                <p>Status: <?php echo $last_backup ? $last_backup['status'] : 'N/A'; ?></p>
            </div>
        </div>
        <div class="card">
            <h2>System Logs</h2>
            <form method="POST" action="">
                <button type="submit" name="action" value="clear_logs" class="btn btn-red">Clear System Logs</button>
            </form>
            <?php while ($log = $result_logs->fetch_assoc()): ?>
                <div class="log-item">
                    <p>[<?php echo $log['created_at']; ?>] <?php echo $log['message']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php if (isset($success)): ?>
        <script>alert("<?php echo $success; ?>");</script>
    <?php elseif (isset($error)): ?>
        <script>alert("<?php echo $error; ?>");</script>
    <?php endif; ?>
</body>
</html>
