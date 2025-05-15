<?php
session_start();
require_once 'includes/db_connection.php';

// Replace with your real keys
$siteKey = '6LcWhjkrAAAAAIdl1zycaK1fz6bnZmS7Dj5_Rw19';
$secretKey = '6LcWhjkrAAAAALUINFy1qdHm1Hte-GGOJVDNRNyz';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'signup') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        $recaptcha = $_POST['g-recaptcha-response'];

        // Validate CAPTCHA
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha");
        $captchaSuccess = json_decode($verify);

        if (!$captchaSuccess->success) {
            $error = "Please verify you are not a robot.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
            $error = "Password must be at least 8 characters with uppercase, lowercase, and special characters.";
        } else {
            $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $error = "This email is already registered.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $id, $name, $email, $hashedPassword, $role, $status);

                if ($stmt->execute()) {
                    $success = "Sign-up successful! You may now log in.";
                } else {
                    $error = "Error: Could not register user. " . $conn->error;
                }
            }
        }
    }

    if ($_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'Active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Invalid email or inactive account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup & Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            background-image: url('./assets/diagonal-striped-brick-1920x1080.png');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #004080;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #004080;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #002d66;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .success { color: green; }
        .error { color: red; }
        #password-feedback li { color: red; }
        #password-feedback li.valid { color: green; }
        #password-strength {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (!empty($success)): ?>
        <h1>Login</h1>
        <div class="message success"><?= $success ?></div>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    <?php else: ?>
        <h1>Sign Up</h1>
        <?php if (!empty($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="action" value="signup">
            <div class="form-group">
                <label>User ID</label>
                <input type="text" name="id" required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email (Username)</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" required oninput="checkPasswordStrength()">
                <div id="password-strength"></div>
                <ul id="password-feedback">
                    <li id="length">At least 8 characters</li>
                    <li id="lower">At least one lowercase letter</li>
                    <li id="upper">At least one uppercase letter</li>
                    <li id="special">At least one special character</li>
                </ul>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="Admin">Admin</option>
                    <option value="Billing Processor">Billing Processor</option>
                    <option value="Manager">Manager</option>
                    <option value="Customer">Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="g-recaptcha" data-sitekey="<?= $siteKey ?>"></div>
            <br>
            <button type="submit" class="btn">Sign Up</button>
        </form>
    <?php endif; ?>
</div>

<script>
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const feedback = {
        length: document.getElementById('length'),
        lower: document.getElementById('lower'),
        upper: document.getElementById('upper'),
        special: document.getElementById('special')
    };
    const rules = {
        length: password.length >= 8,
        lower: /[a-z]/.test(password),
        upper: /[A-Z]/.test(password),
        special: /[\W_]/.test(password)
    };
    let passed = 0;
    for (const key in rules) {
        if (rules[key]) {
            feedback[key].classList.add('valid');
            passed++;
        } else {
            feedback[key].classList.remove('valid');
        }
    }
    const strength = document.getElementById('password-strength');
    if (!password) {
        strength.textContent = '';
    } else if (passed <= 2) {
        strength.textContent = 'Weak password';
        strength.style.color = 'red';
    } else if (passed === 3) {
        strength.textContent = 'Moderate password';
        strength.style.color = 'orange';
    } else {
        strength.textContent = 'Strong password';
        strength.style.color = 'green';
    }
}
</script>
</body>
</html>
