<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "user_auth";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Handle Duty Assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["assign_duty"])) {
    $crew_id = (int)$_POST['crew_id'];
    $vehicle_id = (int)$_POST['vehicle_id'];
    $route_id = (int)$_POST['route_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $trip_number = (int)$_POST['trip_number'];
    $trip_type = htmlspecialchars($_POST['trip_type']);

    $stmt = $conn->prepare("INSERT INTO duty_assignments (crew_id, vehicle_id, route_id, start_time, end_time, trip_type, trip_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiisssi", $crew_id, $vehicle_id, $route_id, $start_time, $end_time, $trip_type, $trip_number);
        $stmt->execute();
        echo "<script>alert('Duty Assigned successfully.')</script>";
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
// Fetch Users & Roles for Crew Selection
$userQuery = $conn->query("SELECT id, first_name, last_name ,role FROM users");
$users = $userQuery->fetch_all(MYSQLI_ASSOC);

// Assuming roles are stored in a 'roles' table
$roleQuery = $conn->query("SELECT id, role FROM users");
$roles = $roleQuery->fetch_all(MYSQLI_ASSOC);

// Fetch Crews
$crewQuery = $conn->query("SELECT * FROM crews");
$crews = $crewQuery->fetch_all(MYSQLI_ASSOC);

// Fetch Vehicles
$vehicleQuery = $conn->query("SELECT * FROM vehicles");
$vehicles = $vehicleQuery->fetch_all(MYSQLI_ASSOC);

// Fetch Routes
$routeQuery = $conn->query("SELECT * FROM routes");
$routes = $routeQuery->fetch_all(MYSQLI_ASSOC);


// Fetch Assigned Duties
$dutyQuery = $conn->query("SELECT duty_assignments.id, crews.crew_name, vehicles.vehicle_number, routes.route_number, start_time, end_time, trip_type, trip_number 
                           FROM duty_assignments 
                           JOIN crews ON duty_assignments.crew_id = crews.id 
                           JOIN vehicles ON duty_assignments.vehicle_id = vehicles.id
                           JOIN routes ON duty_assignments.route_id = routes.route_id");
$duties = $dutyQuery->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duty Assignment System</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        .card {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .form-control, .btn {
            border-radius: 8px;
        }
        h3 {
            font-weight: bold;
            color: #343a40;
        }
        .btn {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Duty Management</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>

        <!-- Duty Assignment Form -->
        <div class="card p-4">
            <h3 class="text-center mb-3">Assign Duty</h3>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Select Crew:</label>
                    <select name="crew_id" class="form-control" required>
                        <?php foreach ($crews as $crew) { ?>
                            <option value="<?= $crew['id'] ?>"><?= $crew['crew_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Vehicle:</label>
                    <select name="vehicle_id" class="form-control" required>
                        <?php foreach ($vehicles as $vehicle) { ?>
                            <option value="<?= $vehicle['id'] ?>"><?= $vehicle['vehicle_number'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Route:</label>
                    <select name="route_id" class="form-control" required>
                        <?php foreach ($routes as $route) { ?>
                            <option value="<?= $route['route_id'] ?>"><?= $route['route_number'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Start Time:</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">End Time:</label>
                    <input type="datetime-local" name="end_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trip Number:</label>
                    <input type="number" name="trip_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trip Type:</label>
                    <select name="trip_type" class="form-control" required>
                        <option value="up">Up</option>
                        <option value="down">Down</option>
                    </select>
                </div>

                <button type="submit" name="assign_duty" class="btn btn-success w-100">
                    <i class="bi bi-plus-circle"></i> Assign Duty
                </button>
            </form>
        </div>

        <!-- Duty Assignments Table -->
        <div class="card mt-4 p-4">
            <h3 class="text-center mb-3">Current Duty Assignments</h3>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Crew</th>
                        <th>Vehicle</th>
                        <th>Route</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Trip Type</th>
                        <th>Trip Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                        
                    </tr>
                </thead>
                <tbody>
    <?php 
    date_default_timezone_set('Asia/Kolkata'); // Set the timezone
    $currentTime = date("Y-m-d H:i:s"); // Get the current timestamp
    

    foreach ($duties as $duty) { 
        $startTime = $duty['start_time'];
        $endTime = $duty['end_time'];

        // Determine the status
        if ($currentTime < $startTime) {
            $status = "Upcoming";
            $statusClass = "text-primary";
        } elseif ($currentTime >= $startTime && $currentTime <= $endTime) {
            $status = "Ongoing";
            $statusClass = "text-success";
        } else {
            $status = "Completed";
            $statusClass = "text-danger";
        }
    ?>
        <tr>
            <td><?= htmlspecialchars($duty['crew_name']) ?></td>
            <td><?= htmlspecialchars($duty['vehicle_number']) ?></td>
            <td><?= htmlspecialchars($duty['route_number']) ?></td>
            <td><?= htmlspecialchars($startTime) ?></td>
            <td><?= htmlspecialchars($endTime) ?></td>
            <td><?= htmlspecialchars($duty['trip_type']) ?></td>
            <td><?= htmlspecialchars($duty['trip_number']) ?></td>
            <td class="<?= $statusClass ?>"><strong><?= $status ?></strong></td>
            <td>
                                <a href="edit_duty.php?id=<?= $duty['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="delete_duty.php?id=<?= $duty['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this duty?');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
        </tr>
    <?php } ?>
</tbody>

            </table>
        </div>

    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</body>
</html>

