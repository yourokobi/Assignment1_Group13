<?php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/fpdf/fpdf.php';

// Handle report generation requests
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
    <title>Generate Report</title>
</head>
<body>
    <h2>Select a Report to Generate</h2>
    <form method="POST" action="">
        <select name="report_type" required>
            <option value="Customer Payments">Customer Payments</option>
            <option value="Usage Data">Usage Data</option>
            <option value="Transaction History">Transaction History</option>
            <option value="Outstanding Bill">Outstanding Bill</option>
        </select>
        <button type="submit">Generate Report</button>
    </form>
</body>
</html>
