<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

/**
 * OTP Functions for Electricity Billing System
 * This file contains functions for generating, sending, and verifying OTPs
 */

/**
 * Generates a random OTP of specified length
 * 
 * @param int $length Length of the OTP (default: 6)
 * @return string The generated OTP
 */
function generateOTP($length = 6) {
    // Generate a random OTP with specified length
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

/**
 * Stores the OTP in the database
 * 
 * @param string $email User's email
 * @param string $otp The generated OTP
 * @param int $expiry Expiry time in seconds (default: 300 seconds = 5 minutes)
 * @return bool True if OTP stored successfully, false otherwise
 */
function storeOTP($email, $otp, $expiry = 300) {
    global $conn;
    
    // Delete any existing OTPs for this user
    $delete_query = "DELETE FROM otp WHERE email = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("s", $email);
    $delete_stmt->execute();
    
    // Calculate expiry timestamp
    $expiry_time = date('Y-m-d H:i:s', time() + $expiry);
    
    // Insert new OTP
    $insert_query = "INSERT INTO otp (email, otp_code, expiry) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("sss", $email, $otp, $expiry_time);
    
    return $insert_stmt->execute();
}

/**
 * Sends OTP to user's email
 * 
 * @param string $email User's email
 * @param string $otp The generated OTP
 * @return bool True if email sent successfully, false otherwise
 */
function sendOTPEmail($email, $otp) {
    $subject = "Your OTP for Electricity Billing System";
    $message = "Hello,\n\n";
    $message .= "Your One-Time Password (OTP) for the Electricity Billing System is: $otp\n\n";
    $message .= "This OTP is valid for 5 minutes.\n\n";
    $message .= "If you did not request this OTP, please ignore this email or contact support.\n\n";
    $message .= "Regards,\nElectricity Billing System Team";
    $headers = "From: noreply@electricitybilling.com";
    
    return mail($email, $subject, $message, $headers);
}

/**
 * Verifies the OTP provided by the user
 * 
 * @param string $email User's email
 * @param string $otp The OTP to verify
 * @return bool True if OTP is valid, false otherwise
 */
function verifyOTP($email, $otp) {
    global $conn;
    
    $query = "SELECT * FROM otp WHERE email = ? AND otp_code = ? AND expiry > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        // Delete the OTP after successful verification
        $delete_query = "DELETE FROM otp WHERE email = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("s", $email);
        $delete_stmt->execute();
        
        return true;
    }
    
    return false;
}

/**
 * Sends a test OTP via email
 * Useful for development and testing when email service is not configured
 * 
 * @param string $email User's email
 * @param string $otp The generated OTP
 * @return void
 */
function debugOTP($email, $otp) {
    // For development purposes, you might want to log the OTP instead of sending an email
    error_log("OTP for $email: $otp");
    // This would appear in your server's error log for testing
}