<?php
session_start();
require_once 'includes/db_connection.php'; // Database connection
require_once 'includes/otp_functions.php'; // Include OTP functions

// Check if the user has an active session with temporary email
if (!isset($_SESSION['temp_email'])) {
    // Redirect to login if there's no active authentication process
    header("Location: login.php");
    exit();
}

// Get the email from session
$email = $_SESSION['temp_email'];

// Generate a new OTP
$otp = generateOTP();

// Store the OTP in the database
if (storeOTP($email, $otp)) {
    // Send the OTP via email
    sendOTPEmail($email, $otp);
    // For development purposes
    debugOTP($email, $otp);
    
    // Set success message
    $_SESSION['otp_message'] = "A new OTP has been sent to your email.";
} else {
    // Set error message
    $_SESSION['otp_error'] = "Failed to generate a new OTP. Please try again.";
}

// Redirect back to OTP verification page
header("Location: verify_otp.php");
exit();