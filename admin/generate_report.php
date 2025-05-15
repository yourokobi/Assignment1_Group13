<?php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/fpdf/fpdf.php';

// Fetch the analytics data based on user selection
$analytics_data = [];
$labels = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['analytics_type'])) {
    $analytics_type = $_POST['analytics_type'];

    if ($analytics_type === 'Daily Statistics') {
        // Fetch daily statistics (e.g., number of transactions per day)
        $query = "SELECT DATE(created_at) as date, COUNT(*) as count FROM transactions GROUP BY DATE(created_at)";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $analytics_data[] = $row;
            $labels[] = $row['date'];
            $data[] = $row['count'];
        }
    } elseif ($analytics_type === 'Monthly Statistics') {
        // Fetch monthly statistics (e.g., total usage per month)
        $query = "SELECT MONTH(date) as month, SUM(total_usage) as total_usage FROM usage_data GROUP BY MONTH(date)";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $analytics_data[] = $row;
            $labels[] = $row['month'];
            $data[] = $row['total_usage'];
        }
    } elseif ($analytics_type === 'Yearly Statistics') {
        // Fetch yearly statistics (e.g., total outstanding balance per year)
        $query = "SELECT YEAR(date) as year, SUM(outstanding_balance) as total_outstanding FROM outstanding GROUP BY YEAR(date)";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $analytics_data[] = $row;
            $labels[] = $row['year'];
            $data[] = $row['total_outstanding'];
        }
    }
}

// Handle report generation requests for the main page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_type'])) {
    $report_type = $_POST['report_type'];

    // Generate Customer Payments Report
    if ($report_type === 'Customer Payments') {
        $query = "SELECT id, customer_id, payment_date, outstanding FROM customer_payments";
        $result = $conn->query($query);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Customer Payments Report', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 10, 'ID', 1);
        $pdf->Cell(60, 10, 'Customer ID', 1);
        $pdf->Cell(50, 10, 'Payment Date', 1);
        $pdf->Cell(40, 10, 'Outstanding', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['id'], 1);
            $pdf->Cell(60, 10, $row['customer_id'], 1);
            $pdf->Cell(50, 10, $row['payment_date'], 1);
            $pdf->Cell(40, 10, $row['outstanding'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'Customer_Payments_Report.pdf');
        exit();
    }

    // Generate Usage Data Report
    if ($report_type === 'Usage Data') {
        $query = "SELECT id, customer_id, start_reading, end_reading, total_usage, date FROM usage_data";
        $result = $conn->query($query);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Usage Data Report', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 10, 'ID', 1);
        $pdf->Cell(60, 10, 'Customer ID', 1);
        $pdf->Cell(40, 10, 'Start Reading', 1);
        $pdf->Cell(40, 10, 'End Reading', 1);
        $pdf->Cell(40, 10, 'Total Usage', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['id'], 1);
            $pdf->Cell(60, 10, $row['customer_id'], 1);
            $pdf->Cell(40, 10, $row['start_reading'], 1);
            $pdf->Cell(40, 10, $row['end_reading'], 1);
            $pdf->Cell(40, 10, $row['total_usage'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'Usage_Data_Report.pdf');
        exit();
    }

    // Generate Transaction History Report
    if ($report_type === 'Transaction History') {
        $query = "SELECT id, customer_id, amount, created_at FROM transactions";
        $result = $conn->query($query);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Transaction History Report', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, 'ID', 1);
        $pdf->Cell(50, 10, 'Customer ID', 1);
        $pdf->Cell(50, 10, 'Amount', 1);
        $pdf->Cell(50, 10, 'Created At', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(40, 10, $row['id'], 1);
            $pdf->Cell(50, 10, $row['customer_id'], 1);
            $pdf->Cell(50, 10, $row['amount'], 1);
            $pdf->Cell(50, 10, $row['created_at'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'Transaction_History_Report.pdf');
        exit();
    }

    // Generate Outstanding Bill Report
    if ($report_type === 'Outstanding Bill') {
        $query = "SELECT id, customer_id, date, current_month_bill, outstanding_balance FROM outstanding";
        $result = $conn->query($query);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Outstanding Bill Report', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(15, 10, 'ID', 1);
        $pdf->Cell(45, 10, 'Customer ID', 1);
        $pdf->Cell(35, 10, 'Date', 1);
        $pdf->Cell(40, 10, 'Current Month Bill', 1);
        $pdf->Cell(40, 10, 'Outstanding Balance', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(15, 10, $row['id'], 1);
            $pdf->Cell(45, 10, $row['customer_id'], 1);
            $pdf->Cell(35, 10, $row['date'], 1);
            $pdf->Cell(40, 10, $row['current_month_bill'], 1);
            $pdf->Cell(40, 10, $row['outstanding_balance'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'Outstanding_Bill_Report.pdf');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .header {
            background-color: #004080;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
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

        .card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 20px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d80;
        }

        .analytics-chart {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="admin_dashboard.php" class="back-arrow">&larr;</a>
        <h1>Electricity Billing System</h1>
        <h2>Reports & Analytics</h2>
    </div>

    <div class="container">
        <!-- Generate Reports Section -->
        <div class="card">
            <h2>Generate Reports</h2>
            <form method="POST" action="">
                <select name="report_type" required>
                    <option value="Customer Payments">Customer Payments</option>
                    <option value="Usage Data">Usage Data</option>
                    <option value="Transaction History">Transaction History</option>
                    <option value="Outstanding Bill">Outstanding Bill</option>
                </select>
                <button type="submit" class="btn">Generate Report</button>
            </form>
        </div>

        <!-- Analytics Overview Section -->
        <div class="card">
            <h2>Analytics Overview</h2>
            <form method="POST" action="">
                <select name="analytics_type" required>
                    <option value="Daily Statistics">Daily Statistics</option>
                    <option value="Monthly Statistics">Monthly Statistics</option>
                    <option value="Yearly Statistics">Yearly Statistics</option>
                </select>
                <button type="submit" class="btn">Show Analytics</button>
            </form>
            <div class="analytics-chart">
                <canvas id="analyticsChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('analyticsChart').getContext('2d');

        // Fetch the PHP data and populate the chart dynamically
        const labels = <?php echo json_encode($labels); ?>;
        const data = <?php echo json_encode($data); ?>;

        // Initialize Chart.js with the fetched data
        const analyticsChart = new Chart(ctx, {
            type: 'bar', // You can change this to 'line', 'pie', etc.
            data: {
                labels: labels, // Dates or months or years
                datasets: [{
                    label: 'Transactions or Data',
                    data: data, // Count of transactions or total usage or outstanding
                    backgroundColor: 'rgba(0, 123, 255, 0.5)', // Color of the bars
                    borderColor: 'rgba(0, 123, 255, 1)', // Border color
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
