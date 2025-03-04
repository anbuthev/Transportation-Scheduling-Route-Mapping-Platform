<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "user_auth";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's assigned duties
$dutyQuery = $conn->prepare("
    SELECT d.id, c.crew_name, v.vehicle_number, r.route_number, d.start_time, d.end_time, d.trip_type, d.trip_number
    FROM duty_assignments d
    JOIN crews c ON d.crew_id = c.id
    JOIN vehicles v ON d.vehicle_id = v.id
    JOIN routes r ON d.route_id = r.route_id
    WHERE c.crew_member1 = ? OR c.crew_member2 = ?
");
$dutyQuery->bind_param("ii", $user_id, $user_id);
$dutyQuery->execute();
$userDuties = $dutyQuery->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assigned Duties</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; }
        h2, h3 { color: #333; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="mt-3">My Assigned Duties</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        

        <?php if (!empty($userDuties)) { ?>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Crew</th>
                        <th>Vehicle</th>
                        <th>Route</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Trip Type</th>
                        <th>Trip Number</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    <?php 
    date_default_timezone_set('Asia/Kolkata'); // Set the timezone
    $currentTime = date("Y-m-d H:i:s"); // Get the current timestamp

    foreach ($userDuties as $duty) { 
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
        </tr>
    <?php } ?>
</tbody>
     </table>
        <?php } else { ?>
            <div class="alert alert-info" role="alert">
                You have no assigned duties at the moment.
            </div>
        <?php } ?>
    </div>

</body>
</html>
