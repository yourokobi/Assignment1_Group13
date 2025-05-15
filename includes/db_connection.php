<?php

/**
 * Database Connection for Electricity Billing System
 * This file establishes connection to the MySQL database
 */

// Database configuration
$db_host = 'localhost';
$db_user = 'root';  // Default XAMPP username
$db_pass = '';      // Default XAMPP password (empty)
$db_name = 'billing_system';  // Your database name

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4
$conn->set_charset("utf8mb4");