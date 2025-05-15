<?php
session_start();
require_once '../includes/db_connection.php';

// Fetch all support tickets
$query_all_tickets = "SELECT ticket_id, email, inquiry, status, response, created_at FROM inquiry ORDER BY created_at DESC";
$result_all_tickets = $conn->query($query_all_tickets);

// Update status and response if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['status'], $_POST['response'])) {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'] === 'Resolved' ? 'Resolved' : 'Pending';
    $response = $_POST['response'];

    $update_query = "UPDATE inquiry SET status = ?, response = ? WHERE ticket_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sss", $status, $response, $ticket_id);

    if ($stmt->execute()) {
        $success = "Status and response updated successfully!";
    } else {
        $error = "Error updating status and response: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
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

        .form-container {
            text-align: center;
            margin-top: 20px;
        }

        .form-container select,
        .form-container textarea {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 200px;
            margin: 5px;
        }

        .form-container button {
            padding: 5px 15px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #003d80;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="customer_service.php" class="back-arrow">&larr;</a>
        <h1>Electricity Billing System</h1>
        <h2>Support Tickets</h2>
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
                    <th>Ticket ID</th>
                    <th>Email</th>
                    <th>Inquiry</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Response</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_all_tickets->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['ticket_id']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['inquiry']; ?></td>
                        <td>
                            <span class="status <?php echo $row['status'] === 'Resolved' ? 'status-resolved' : ''; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['response']; ?></td>
                        <td>
                            <form method="POST" action="" class="form-container">
                                <input type="hidden" name="ticket_id" value="<?php echo $row['ticket_id']; ?>">
                                <select name="status" required>
                                    <option value="Pending" <?php echo $row['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Resolved" <?php echo $row['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                                <textarea name="response" rows="4" placeholder="Enter your response..."><?php echo $row['response']; ?></textarea>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
