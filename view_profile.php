<?php
// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the auth.php file to access authentication functions
require_once 'auth.php'; // Adjust the path if necessary

// Database connection parameters
$host = 'localhost';
$db = 'user_auth';
$user = 'root'; // Your database username
$pass = '';     // Your database password

// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user information from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, role, first_name, last_name, phone_no, id_proof_number, experience FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User  not found.");
}

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .profile-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(189, 13, 13, 0.1);
            max-width: 600px;
            margin: auto;
            text-align: center;
        }
        .profile-container h2 {
            color: #007bff;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .profile-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .profile-table th, .profile-table td {
            padding: 12px;
            border: 1px solid#b24919;
            text-align: left;
        }
        .profile-table th {
            background-color: #007bff;
            color: #fff;
            font-weight: 500;
        }
        .profile-table td {
            background-color:rgb(235, 239, 242);
            font-weight: 400;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>User Profile</h2>
    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    <table class="profile-table">
        
        <tr>
            <td>First Name</td>
            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
        </tr>
        <tr>
            <td>Username</td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
        <tr>
            <td>Role</td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
        </tr>
        <tr>
            <td>Phone Number</td>
            <td><?php echo htmlspecialchars($user['phone_no']); ?></td>
        </tr>
        <tr>
            <td>ID Proof Number</td>
            <td><?php echo htmlspecialchars($user['id_proof_number']); ?></td>
        </tr>
        <tr>
            <td>Experience</td>
            <td><?php echo htmlspecialchars($user['experience']); ?></td>
        </tr>
    </table>

    
</div>

</body>
</html>
