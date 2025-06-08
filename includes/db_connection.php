<?php

/**
 * Database Connection for Electricity Billing System
 * This file establishes connection to the MySQL database
 */

// Database configuration
$db_host = 'your-rds-endpoint';
$db_user = 'admin';
$db_pass = 'Admin12345!'; 
$db_name = 'billing_system';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4
$conn->set_charset("utf8mb4");
