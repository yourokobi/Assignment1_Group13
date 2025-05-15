<?php
session_start();

// Check if we have email in the session (meaning user passed first login step)
if (!isset($_SESSION['temp_email'])) {
    header("Location: login.php");
    exit();
}

// If OTP verification form was submitted via POST from this page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_otp') {
    // Form was submitted here, redirect to login.php to handle the verification logic
    // This keeps all our authentication logic in one place
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - Electricity Billing System</title>
    <link rel="stylesheet" href="assets/styles.css"> <!-- Ensure correct path -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('assets/diagonal-striped-brick-1920x1080.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        .container {
            width: 400px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        h1 {
            color: #004080;
            font-size: 32px;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif;
        }
        
        h2 {
            color: #004080;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            text-align: center;
            letter-spacing: 0.5em;
            font-size: 18px;
        }

        .form-group input:focus {
            border-color: #004080;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 255, 0.2);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #004080;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #003366;
        }

        .message {
            text-align: center;
            margin-top: 20px;
        }

        .error {
            color: red;
        }
        
        .resend {
            margin-top: 20px;
            text-align: center;
        }
        
        .resend a {
            color: #004080;
            text-decoration: none;
            font-size: 14px;
        }
        
        .resend a:hover {
            text-decoration: underline;
        }
        
        .email-info {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            color: #333;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .timer {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .back-btn {
            background-color: #ccc;
            margin-top: 10px;
        }
        
        .back-btn:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Electricity Billing System</h1>
        <h2>OTP Verification</h2>
        
        <?php if (isset($error)): ?>
            <div class="message error"> <?php echo $error; ?> </div>
        <?php endif; ?>
        
        <div class="email-info">
            An OTP has been sent to: <strong><?php echo htmlspecialchars($_SESSION['temp_email']); ?></strong>
        </div>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="otp">Enter 6-Digit OTP</label>
                <input type="text" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}" 
                       required autocomplete="off" inputmode="numeric">
            </div>
            
            <button type="submit" name="action" value="verify_otp" class="btn">Verify OTP</button>
            <a href="login.php" class="btn back-btn">Back to Login</a>
            
            <div class="timer" id="countdown">
                OTP expires in: <span id="timer">05:00</span>
            </div>
        </form>
        
        <div class="resend">
            <a href="resend_otp.php" id="resendLink" style="display: none;">Didn't receive OTP? Resend</a>
        </div>
    </div>
    
    <script>
        // OTP expiry countdown timer
        let timeLeft = 300; // 5 minutes in seconds
        const countdownEl = document.getElementById('timer');
        const resendLink = document.getElementById('resendLink');
        
        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            countdownEl.innerHTML = `${minutes}:${seconds}`;
            
            if (timeLeft === 0) {
                clearInterval(countdownTimer);
                countdownEl.innerHTML = "Expired";
                resendLink.style.display = 'block';
            }
            timeLeft--;
        }
        
        // Run once immediately, then every second
        updateCountdown();
        const countdownTimer = setInterval(updateCountdown, 1000);
        
        // Show resend link after 30 seconds anyway
        setTimeout(() => {
            resendLink.style.display = 'block';
        }, 30000);
    </script>
</body>
</html>