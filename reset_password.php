<?php
session_start(); // Start the session

// Database connection
$host = 'localhost';
$db = 'user_auth';
$user = 'root'; // Update with your actual username
$pass = '';     // Update with your actual password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $username = $_SESSION['username']; // Retrieve username from session

    // Validate input
    if (empty($new_password) || empty($confirm_password)) {
        header("Location: forgot_password.php?error=All fields are required");
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("Location: forgot_password.php?error=Passwords do not match");
        exit();
    }

    // Hash the new password
    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->bind_param("ss", $new_password_hash, $username);
    if ($stmt->execute()) {
        header("Location: login.php?success=Password reset successfully. Please log in.");
    } else {
        header("Location: forgot_password.php?error=Failed to reset password");
    }
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Secure Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Background Styling */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('https://source.unsplash.com/1600x900/?city,road,transport') no-repeat center center/cover;
            position: relative;
        }

        /* Dark Overlay */
        .background-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        /* Form Container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Heading */
        h2 {
            margin-bottom: 15px;
            color: #003366;
            font-weight: 700;
        }

        p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Form Fields */
        .form-group {
            position: relative;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ced4da;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        /* Password Toggle Icon */
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        .toggle-password:hover {
            color: #333;
        }

        /* Reset Button */
        .btn {
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary {
            background: #003366;
            border: none;
        }

        .btn-primary:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        /* Error & Success Messages */
        .alert {
            padding: 10px;
            margin-top: 15px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

    </style>
</head>
<body>

    <div class="background-overlay"></div> <!-- Dark Overlay -->

    <div class="container">
        <h2><i class="fas fa-key"></i> Reset Password</h2>
        <p class="text-muted">Enter a new password for your account.</p>

        <form action="reset_password.php" method="POST">
            <div class="form-group">
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required>
            
              
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
               
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-lock"></i> Reset Password</button>
        </form>

        <!-- Display Error or Success Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success mt-3" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="login.php" class="text-primary"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            let input = document.getElementById(id);
            let icon = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>
