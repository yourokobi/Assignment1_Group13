<?php
/**
 * Enhanced Session Security for Electricity Billing System
 * This file contains session security measures and functions
 */

/**
 * Enhances session security with various protections
 */
function enhanceSessionSecurity() {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1); // Prevents JavaScript access to session cookie
    ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies
    
    // Set secure session cookie parameters if using HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1); // Only sends the cookie over HTTPS
        ini_set('session.cookie_samesite', 'Strict'); // Prevents CSRF attacks
    }
    
    // Regenerate session ID to prevent session fixation attacks
    if (!isset($_SESSION['session_created'])) {
        session_regenerate_id(true);
        $_SESSION['session_created'] = time();
    } else {
        // Regenerate session ID periodically (every 30 minutes)
        $session_age = time() - $_SESSION['session_created'];
        if ($session_age > 1800) {
            session_regenerate_id(true);
            $_SESSION['session_created'] = time();
        }
    }
    
    // Bind session to IP address to prevent session hijacking
    if (!isset($_SESSION['client_ip'])) {
        $_SESSION['client_ip'] = $_SERVER['REMOTE_ADDR'];
    } else if ($_SESSION['client_ip'] !== $_SERVER['REMOTE_ADDR']) {
        // IP address changed - potential session hijacking
        // Clear session and redirect to login
        session_unset();
        session_destroy();
        header("Location: login.php?error=security");
        exit();
    }
    
    // Bind session to user agent to prevent session hijacking
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    } else if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // User agent changed - potential session hijacking
        session_unset();
        session_destroy();
        header("Location: login.php?error=security");
        exit();
    }
    
    // Set session absolute timeout (8 hours)
    if (isset($_SESSION['session_created']) && (time() - $_SESSION['session_created'] > 28800)) {
        // Session expired - force relogin
        session_unset();
        session_destroy();
        header("Location: login.php?error=timeout");
        exit();
    }
    
    // Set session idle timeout (30 minutes)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // Session idle timeout
        session_unset();
        session_destroy();
        header("Location: login.php?error=idle");
        exit();
    }
    
    // Update last activity time stamp
    $_SESSION['last_activity'] = time();
}

/**
 * Validates CSRF token to protect against cross-site request forgery attacks
 * 
 * @param string $token The token from the form
 * @return bool True if token is valid, false otherwise
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Generates a new CSRF token and stores in session
 * 
 * @return string The generated CSRF token
 */
function generateCSRFToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

/**
 * Logs security events for monitoring
 *