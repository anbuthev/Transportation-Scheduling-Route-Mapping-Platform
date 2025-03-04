<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the auth.php file
require_once 'auth.php'; 

// Database connection parameters
$host = 'localhost';
$db = 'user_auth';
$user = 'root'; 
$pass = ''; 

// Create a database connection
$conn = new mysqli($host, $user, $pass, $db); 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and has the manager role
if (!isset($_SESSION['user_id']) || !hasRole($_SESSION['user_id'], 'manager')) {
    header("Location: login.php");
    exit();
}

// Check if the vehicle ID is provided
if (!isset($_GET['id'])) {
    die("Vehicle ID not specified.");
}

$vehicleId = intval($_GET['id']); // Get the vehicle ID from the URL

// Retrieve the vehicle's current information
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Vehicle not found.");
}

$vehicle = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating vehicle information
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_number = $_POST['vehicle_number'];
    $capacity = $_POST['capacity'];
    $make_year = $_POST['make_year'];
    $model_type = $_POST['model_type'];
    $due_for_next_service = $_POST['due_for_next_service'];
    $due_for_insurance = $_POST['due_for_insurance'];
    $due_for_fc = $_POST['due_for_fc'];

    // Prepare and execute the SQL statement to update the vehicle
    $stmt = $conn->prepare("UPDATE vehicles SET vehicle_number = ?, capacity = ?, make_year = ?, model_type = ?, due_for_next_service = ?, due_for_insurance = ?, due_for_fc = ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sisssssi", $vehicle_number, $capacity, $make_year, $model_type, $due_for_next_service, $due_for_insurance, $due_for_fc, $vehicleId);
    if ($stmt->execute()) {
        $message = "Vehicle updated successfully.";
        header("Location: vehicle_management.php"); // Redirect to vehicle management page
        exit;
    } else {
        $message = "Error updating vehicle: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection at the end
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .container {
            margin-top: 30px;
        }
        h2 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .message {
            color: #d9534f;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Vehicle</h2>

        <?php if ($message) echo "<p class='message'>" . htmlspecialchars($message) . "</p>"; ?>

        <form method="POST" action="">
            <input type="text" name="vehicle_number" value="<?php echo htmlspecialchars($vehicle['vehicle_number']); ?>" required placeholder="Vehicle Number">
            <input type="number" name="capacity" value="<?php echo htmlspecialchars($vehicle['capacity']); ?>" required placeholder="Capacity">
            <input type="number" name="make_year" value="<?php echo htmlspecialchars($vehicle['make_year']); ?>" required placeholder="Make Year">
            <select name="model_type" required>
                <option value="BS4" <?php echo ($vehicle['model_type'] == 'BS4') ? 'selected' : ''; ?>>BS4</option>
                <option value="BS6" <?php echo ($vehicle['model_type'] == 'BS6') ? 'selected' : ''; ?>>BS6</option>
            </select>
            <input type="date" name="due_for_next_service" value="<?php echo htmlspecialchars($vehicle['due_for_next_service']); ?>" required placeholder="Due for Next Service">
            <input type="date" name="due_for_insurance" value="<?php echo htmlspecialchars($vehicle['due_for_insurance']); ?>" required placeholder="Due for Insurance">
            <input type="date" name="due_for_fc" value="<?php echo htmlspecialchars($vehicle['due_for_fc']); ?>" required placeholder="Due for FC">
            <button type="submit">Update Vehicle</button>
        </form>

        <a href="vehicle_management.php" class="btn btn-secondary mt-3">Back to Vehicle Management</a>
    </div>
</body>
</html>