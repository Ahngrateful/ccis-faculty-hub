<?php
// Start session
session_start();

// Database connection
require_once("dbconn.php");

// Initialize messages
$error_message = "";
$success_message = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Honeypot check
    if (!empty($_POST['website'])) {
        // Treat this as a bot submission
        $error_message = "Bot detected. Submission blocked.";
    } else {
        // Safely get inputs
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($password)) {
            $error_message = "Email and password are required.";
        } else {
            // Check if user exists and is an admin (role_id = 1)
            $query = "SELECT * FROM faculty WHERE email = ? AND role_id = '1' LIMIT 1";
            $stmt = mysqli_prepare($conn, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) === 1) {
                    $user = mysqli_fetch_assoc($result);

                    // Verify password using 'password_hash' column
                    if (password_verify($password, $user['password_hash'])) {
                        // Set session variables
                        $_SESSION['faculty_id'] = $user['id'];
                        $_SESSION['faculty_name'] = $user['name'];
                        $_SESSION['faculty_email'] = $user['email'];
                        $_SESSION['faculty_logged_in'] = true;

                        // Redirect to dashboard
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $error_message = "Invalid password.";
                    }
                } else {
                    $error_message = "No faculty account found with this email.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Database error: Failed to prepare statement.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CCIS - Faculty Hub Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: url("../assets/umak-bg.png");
            background-size: cover;
        }

        /* Centering the login panel */
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Login Panel */
        .login-container {
            background-color: rgba(0, 64, 0, 0.7);
            /* Dark green transparent background with 70% opacity */
            border-radius: 8px;
            width: 700px;
            position: relative;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
            display: flex;
            /* Use flexbox to create the split layout */
        }

        /* Logo Section (Left Side) */
        .logo-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-section img {
            max-width: 100%;
            height: auto;
        }

        /* Form Section (Right Side) */
        .form-section {
            flex: 1;
            padding: 40px 30px;
        }

        /* Header Text */
        .login-form h2 {
            color: white;
            font-size: 20px;
            font-weight: 500;
            text-align: left;
            margin-bottom: 30px;
        }

        /* Input Groups */
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 270px;
            padding: 10px 10px 10px 35px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-right: 10px;
        }

        .input-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #444;
            font-size: 14px;
        }

        /* Login Button */
        .login-btn {
            background-color: #ffd000;
            color: black;
            font-weight: bold;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* Forgot Password */
        .forgot-password {
            margin-top: 10px;
            text-align: left;
        }

        .forgot-password a {
            color: #ffd000;
            font-size: 13px;
            text-decoration: none;
            font-style: italic;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(255, 0, 0, 0.2);
        }

        .alert-success {
            background-color: rgba(0, 255, 0, 0.1);
            color: #75d979;
            border: 1px solid rgba(0, 255, 0, 0.2);
        }
    </style>
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Left side with logo -->
            <div class="logo-section">
                <img src="../assets/CCIS-Logo-Official.png" alt="CCIS Logo">
            </div>

            <!-- Right side with login form -->
            <div class="form-section">
                <div class="login-form">
                    <h2>Log in to CCIS - Faculty Hub</h2>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div style="display:none;">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="email" placeholder="Email" required>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="login-btn">Log in</button>
                        <div class="forgot-password">
                            <a href="forgot_password.php">Forgot Password?</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>

</html>