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

// Fetch the existing duty details
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM duty_assignments WHERE id = $id");
    $duty = $result->fetch_assoc();
}

// Handle duty update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_duty"])) {
    $id = (int)$_POST['id'];
    $crew_id = (int)$_POST['crew_id'];
    $vehicle_id = (int)$_POST['vehicle_id'];
    $route_id = (int)$_POST['route_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $trip_number = (int)$_POST['trip_number'];
    $trip_type = htmlspecialchars($_POST['trip_type']);

    $stmt = $conn->prepare("UPDATE duty_assignments SET crew_id=?, vehicle_id=?, route_id=?, start_time=?, end_time=?, trip_type=?, trip_number=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("iiisssii", $crew_id, $vehicle_id, $route_id, $start_time, $end_time, $trip_type, $trip_number, $id);
        $stmt->execute();
        echo "<script>alert('Duty updated successfully.'); window.location.href='duties.php';</script>";
        $stmt->close();
    } else {
        echo "Error updating duty: " . $conn->error;
    }
}

// Fetch crews, vehicles, and routes for selection
$crews = $conn->query("SELECT * FROM crews")->fetch_all(MYSQLI_ASSOC);
$vehicles = $conn->query("SELECT * FROM vehicles")->fetch_all(MYSQLI_ASSOC);
$routes = $conn->query("SELECT * FROM routes")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Duty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-4">Edit Duty Assignment</h3>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $duty['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Select Crew:</label>
                            <select name="crew_id" class="form-select" required>
                                <?php foreach ($crews as $crew) { ?>
                                    <option value="<?= $crew['id'] ?>" <?= $crew['id'] == $duty['crew_id'] ? 'selected' : '' ?>><?= $crew['crew_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Vehicle:</label>
                            <select name="vehicle_id" class="form-select" required>
                                <?php foreach ($vehicles as $vehicle) { ?>
                                    <option value="<?= $vehicle['id'] ?>" <?= $vehicle['id'] == $duty['vehicle_id'] ? 'selected' : '' ?>><?= $vehicle['vehicle_number'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Route:</label>
                            <select name="route_id" class="form-select" required>
                                <?php foreach ($routes as $route) { ?>
                                    <option value="<?= $route['route_id'] ?>" <?= $route['route_id'] == $duty['route_id'] ? 'selected' : '' ?>><?= $route['route_number'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Time:</label>
                            <input type="datetime-local" name="start_time" class="form-control" value="<?= $duty['start_time'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Time:</label>
                            <input type="datetime-local" name="end_time" class="form-control" value="<?= $duty['end_time'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trip Number:</label>
                            <input type="number" name="trip_number" class="form-control" value="<?= $duty['trip_number'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trip Type:</label>
                            <select name="trip_type" class="form-select" required>
                                <option value="up" <?= $duty['trip_type'] == 'up' ? 'selected' : '' ?>>Up</option>
                                <option value="down" <?= $duty['trip_type'] == 'down' ? 'selected' : '' ?>>Down</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="update_duty" class="btn btn-primary">Update Duty</button>
                            <a href="duties.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

