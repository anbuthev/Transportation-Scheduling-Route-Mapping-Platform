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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Validate input
    if (empty($username) || empty($email)) {
        header("Location: forgot_password.php?error=All fields are required");
        exit();
    }

    // Check if the username and email exist
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        header("Location: forgot_password.php?error=Invalid username or email");
        exit();
    }

    // If the username and email match, show the reset password form
    $_SESSION['username'] = $username; // Store username in session for later use
    header("Location: reset_password.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Transport Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Background Image */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('https://source.unsplash.com/1600x900/?road,bus,city') no-repeat center center/cover;
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
            margin-bottom: 20px;
            color: #003366;
            font-weight: 700;
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 15px;
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

        /* Verify Button */
        .btn {
            background: #003366;
            border: none;
            padding: 12px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            font-size: 1rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
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
        <h2><i class="fas fa-key"></i> Forgot Password?</h2>
        <p class="text-muted">Enter your details to reset your password.</p>

        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <button type="submit" class="btn btn-block"><i class="fas fa-check-circle"></i> Verify</button>
        </form>

        <!-- Display Error or Success Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="login.php" class="text-primary"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

</body>
</html>
