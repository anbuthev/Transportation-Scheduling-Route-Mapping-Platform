<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'user_auth'); // Update with your credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding or updating a vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_number = $conn->real_escape_string($_POST['vehicle_number']);
    $capacity = $conn->real_escape_string($_POST['capacity']);
    $make_year = $conn->real_escape_string($_POST['make_year']);
    $model_type = $conn->real_escape_string($_POST['model_type']);
    $due_for_next_service = $conn->real_escape_string($_POST['due_for_next_service']);
    $due_for_insurance = $conn->real_escape_string($_POST['due_for_insurance']);
    $due_for_fc = $conn->real_escape_string($_POST['due_for_fc']);

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update vehicle logic
        $id = $conn->real_escape_string($_POST['edit_id']);
        $sql = "UPDATE vehicles SET vehicle_number='$vehicle_number', capacity='$capacity', make_year='$make_year', model_type='$model_type', due_for_next_service='$due_for_next_service', due_for_insurance='$due_for_insurance', due_for_fc='$due_for_fc' WHERE id='$id'";
    } else {
        // Add vehicle logic
        $sql = "INSERT INTO vehicles (vehicle_number, capacity, make_year, model_type, due_for_next_service, due_for_insurance, due_for_fc) VALUES ('$vehicle_number', '$capacity', '$make_year', '$model_type', '$due_for_next_service', '$due_for_insurance', '$due_for_fc')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}

// Handle edit request
$vehicle = null;
if (isset($_GET['edit'])) {
    $id = $conn->real_escape_string($_GET['edit']);
    $sql = "SELECT * FROM vehicles WHERE id='$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $vehicle = $result->fetch_assoc();
    } else {
        die("Vehicle not found.");
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM vehicles WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit;
    }
}

// Retrieve vehicle list
$sql = "SELECT * FROM vehicles";
$result = $conn->query($sql);
$vehicles = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        h2 {
            margin-bottom: 20px;
        }
        .table {
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><center>Vehicle Management System</center></h2>
        
        <!-- Back Button -->
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

        <!-- Vehicle Form -->
        <form id="vehicleForm" method="POST">
            <input type="hidden" id="edit_id" name="edit_id" value="<?php echo isset($vehicle) ? $vehicle['id'] : ''; ?>">
            
            <div class="form-group">
                <label for="vehicle_number">Vehicle Number</label>
                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" value="<?php echo isset($vehicle) ? $vehicle['vehicle_number'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo isset($vehicle) ? $vehicle['capacity'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="make_year">Make Year</label>
                <input type="number" class="form-control" id="make_year" name="make_year" value="<?php echo isset($vehicle) ? $vehicle['make_year'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="model_type">Model Type</label>
                <input type="text" class="form-control" id="model_type" name="model_type" value="<?php echo isset($vehicle) ? $vehicle['model_type'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="due_for_next_service">Due for Next Service</label>
                <input type="date" class="form-control" id="due_for_next_service" name="due_for_next_service" value="<?php echo isset($vehicle) ? $vehicle['due_for_next_service'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="due_for_insurance">Due for Insurance</label>
                <input type="date" class="form-control" id="due_for_insurance" name="due_for_insurance" value="<?php echo isset($vehicle) ? $vehicle['due_for_insurance'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="due_for_fc">Due for FC</label>
                <input type="date" class="form-control" id="due_for_fc" name="due_for_fc" value="<?php echo isset($vehicle) ? $vehicle['due_for_fc'] : ''; ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php echo isset($vehicle) ? 'Update Vehicle' : 'Add Vehicle'; ?></button>
        </form>

        <hr>

        <!-- Vehicle List -->
        <h3><center>Vehicle List</center></h3>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Vehicle Number</th>
                    <th>Capacity</th>
                    <th>Make Year</th>
                    <th>Model Type</th>
                    <th>Next Service</th>
                    <th>Insurance</th>
                    <th>Fitness Certificate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $v): ?>
                <tr>
                    <td><?php echo $v['id']; ?></td>
                    <td><?php echo $v['vehicle_number']; ?></td>
                    <td><?php echo $v['capacity']; ?></td>
                    <td><?php echo $v['make_year']; ?></td>
                    <td><?php echo $v['model_type']; ?></td>
                    <td><?php echo $v['due_for_next_service']; ?></td>
                    <td><?php echo $v['due_for_insurance']; ?></td>
                    <td><?php echo $v['due_for_fc']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $v['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?php echo $v['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
