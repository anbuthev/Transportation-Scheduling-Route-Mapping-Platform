<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the auth.php file
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

$userId = $_SESSION['user_id'];
$role = getUserRole($userId, $conn); // Call the function to get the role
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Background Image (Transport Theme) */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            background: url('https://source.unsplash.com/1600x900/?transport,bus,roads') no-repeat center center/cover;
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

        /* Card Container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Navbar */
        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            background: #003366;
            padding: 10px;
        }

        .navbar-brand {
            color: #ffcc00 !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        h1 {
            color: #003366;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .role-message {
            font-size: 1.2rem;
            font-weight: 500;
            color: #495057;
            margin-bottom: 20px;
        }

        /* Transport-Themed Buttons */
        .btn-group-custom {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .btn-custom {
            width: 180px;
            padding: 12px;
            border-radius: 10px;
            font-size: 1rem;
        }

        .btn-primary { background: #003366; border: none; }
        .btn-success { background: #28a745; border: none; }
        .btn-warning { background: #ffcc00; color: #000; border: none; }
        .btn-danger { background: #dc3545; border: none; }

        .btn i {
            margin-right: 8px;
        }

    </style>
</head>
<body>

    <div class="background-overlay"></div> <!-- Overlay for better readability -->

    <nav class="navbar navbar-dark">
    <a href="view_profile.php" class="btn btn-info btn-custom"><i class="fas fa-user"></i> View Profile</a>
        <span class="navbar-brand mx-auto"><i class="fas fa-road"></i>Transport Scheduling Route Mapping Platform  </span>
        
        <a href="logout.php" class="btn btn-danger btn-custom"><i class="fas fa-sign-out-alt"></i> Logout</a>

    </nav>
<br>   <br> <br>   <br> <br>   <br> <br>   <br> 
    <div class="container">
        <h1><i class="fas fa-bus"></i> R.V.R TRANSPORTS</h1>

        <div class="alert alert-info">
            <div class="role-message">
                <?php
                if ($role === 'manager') {
                    echo "You are logged in as a <strong>Manager</strong>.";
                } elseif ($role === 'driver') {
                    echo "You are logged in as a <strong>Driver</strong>.";
                } elseif ($role === 'conductor') {
                    echo "You are logged in as a <strong>Conductor</strong>.";
                } else {
                    echo "You are logged in as a <strong>User</strong>.";
                }
                ?>
            </div>
        </div>

        <div class="btn-group-custom">
            <?php
            if ($role === 'manager') {
                echo '<a href="user_management.php" class="btn btn-primary btn-custom"><i class="fas fa-users"></i> User Management</a>';
                echo '<a href="vehicle_management.php" class="btn btn-success btn-custom"><i class="fas fa-bus"></i> Vehicle Management</a>';
                echo '<a href="view_profile.php" class="btn btn-info btn-custom"><i class="fas fa-user"></i> View Profile</a>';
                echo '<a href="router.php" class="btn btn-warning btn-custom"><i class="fas fa-route"></i> Route Management</a>';
                echo '<a href="view_route.php" class="btn btn-secondary btn-custom"><i class="fas fa-map"></i> View Routes</a>';
                echo '<a href="crewing.php" class="btn btn-warning btn-custom"><i class="fas fa-tasks"></i> Crew Management</a>';
                echo '<a href="duties.php" class="btn btn-primary btn-custom"><i class="fa fa-folder-open"></i>  Duty Management</a>';
                echo '<a href="report.php" class="btn btn-info btn-custom"><i class="fa fa-terminal"></i> View Report</a>';
            } elseif ($role === 'driver') {
               
                echo '<a href="view_duty.php" class="btn btn-success btn-custom"><i class="fas fa-clock"></i> View Duty</a>';
                echo '<a href="view_route.php" class="btn btn-primary btn-custom"><i class="fas fa-map"></i> View Routes</a>';
            } elseif ($role === 'conductor') {
              
                echo '<a href="view_duty.php" class="btn btn-warning btn-custom"><i class="fas fa-tasks"></i> View Duty</a>';
                echo '<a href="view_route.php" class="btn btn-primary btn-custom"><i class="fas fa-map"></i> View Routes</a>';
            } else {
              echo '<a href="view_route.php" class="btn btn-primary btn-custom"><i class="fas fa-map"></i> View Routes</a>';
            }
            ?>
        </div>

  
    </div>

</body>
</html>


