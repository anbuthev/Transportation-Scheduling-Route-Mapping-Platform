<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'user_auth');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user count by role
$userRolesQuery = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$userRolesResult = $conn->query($userRolesQuery);
$userRoles = [];
while ($row = $userRolesResult->fetch_assoc()) {
    $userRoles[$row['role']] = $row['count'];
}

// Fetch vehicle statistics
$vehicleStatsQuery = "SELECT COUNT(*) as total, 
                      SUM(due_for_next_service < CURDATE()) as service_due, 
                      SUM(due_for_insurance < CURDATE()) as insurance_due, 
                      SUM(due_for_fc < CURDATE()) as fc_due 
                      FROM vehicles";
$vehicleStats = $conn->query($vehicleStatsQuery)->fetch_assoc();

// Fetch route statistics
$routeStatsQuery = "SELECT COUNT(*) as total_routes, SUM(total_distance) as total_distance FROM routes";
$routeStats = $conn->query($routeStatsQuery)->fetch_assoc();

// Fetch duty statistics
$dutyStatsQuery = "SELECT COUNT(*) as total_duties FROM duty_assignments";
$dutyStats = $conn->query($dutyStatsQuery)->fetch_assoc();

// Fetch average distance per route
$avgDistanceQuery = "SELECT AVG(total_distance) as avg_distance FROM routes";
$avgDistance = $conn->query($avgDistanceQuery)->fetch_assoc();

// Fetch total completed trips
$completedTripsQuery = "SELECT COUNT(*) as total_trips FROM duty_assignments WHERE end_time < NOW()";
$completedTrips = $conn->query($completedTripsQuery)->fetch_assoc();

// Fetch crew performance report
$crewPerformanceQuery = "SELECT c.crew_name, COUNT(d.id) as completed_duties 
                         FROM duty_assignments d
                         JOIN crews c ON d.crew_id = c.id
                         WHERE d.end_time < NOW()
                         GROUP BY c.crew_name";
$crewPerformanceResult = $conn->query($crewPerformanceQuery);
$crewPerformance = [];
while ($row = $crewPerformanceResult->fetch_assoc()) {
    $crewPerformance[$row['crew_name']] = $row['completed_duties'];
}
//Fetch route efficiency report
$routeEfficiencyQuery = "SELECT r.route_number, COUNT(d.id) as total_trips, SUM(r.total_distance) as total_distance 
                         FROM duty_assignments d
                         JOIN routes r ON d.route_id = r.route_id
                         GROUP BY r.route_number";
$routeEfficiencyResult = $conn->query($routeEfficiencyQuery);
$routeEfficiency = [];
while ($row = $routeEfficiencyResult->fetch_assoc()) {
    $routeEfficiency[$row['route_number']] = [
        'total_trips' => $row['total_trips'],
        'total_distance' => $row['total_distance']
    ];
}

// Fetch crew workload balance
$crewWorkloadQuery = "SELECT c.crew_name, COUNT(d.id) as assigned_duties 
                       FROM duty_assignments d
                       JOIN crews c ON d.crew_id = c.id
                       GROUP BY c.crew_name";
$crewWorkloadResult = $conn->query($crewWorkloadQuery);
$crewWorkload = [];
while ($row = $crewWorkloadResult->fetch_assoc()) {
    $crewWorkload[$row['crew_name']] = $row['assigned_duties'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Reports & Analytics</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    
    <div class="row">
        <!-- User Statistics -->
        <div class="col-md-6">
            <canvas id="userChart"></canvas>
        </div>
        
        <!-- Vehicle Statistics -->
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item">Total Vehicles: <?php echo $vehicleStats['total']; ?></li>
                <li class="list-group-item text-danger">Service Due: <?php echo $vehicleStats['service_due']; ?></li>
                <li class="list-group-item text-warning">Insurance Due: <?php echo $vehicleStats['insurance_due']; ?></li>
                <li class="list-group-item text-info">FC Due: <?php echo $vehicleStats['fc_due']; ?></li>
            </ul>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Route Statistics -->
        <div class="col-md-6">
            <div class="alert alert-primary">Total Routes: <?php echo $routeStats['total_routes']; ?></div>
            <div class="alert alert-secondary">Total Distance Covered: <?php echo $routeStats['total_distance']; ?> km</div>
            <div class="alert alert-info">Average Distance per Route: <?php echo round($avgDistance['avg_distance'], 2); ?> km</div>
        </div>
        
        <!-- Duty Statistics -->
        <div class="col-md-6">
            <div class="alert alert-success">Total Duties Assigned: <?php echo $dutyStats['total_duties']; ?></div>
            <div class="alert alert-dark">Total Completed Trips: <?php echo $completedTrips['total_trips']; ?></div>
        </div>
    </div>
    
    <!-- Crew Performance Report -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h3 class="text-center">Crew Performance Report</h3>
            <canvas id="crewChart"></canvas>
        </div>
    </div>
    <!-- Route Efficiency Report -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h3 class="text-center">Route Efficiency Report</h3>
            <canvas id="routeEfficiencyChart"></canvas>
        </div>
    </div>
    
    <!-- Crew Workload Balance -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h3 class="text-center">Crew Workload Balance</h3>
            <canvas id="crewWorkloadChart"></canvas>
        </div>
    </div>
</div>

<script>
const userCtx = document.getElementById('userChart').getContext('2d');
const userData = {
    labels: <?php echo json_encode(array_keys($userRoles)); ?>,
    datasets: [{
        label: 'Users by Role',
        data: <?php echo json_encode(array_values($userRoles)); ?>,
        backgroundColor: ['blue', 'red', 'green', 'purple'],
    }]
};
new Chart(userCtx, { type: 'pie', data: userData });

const crewCtx = document.getElementById('crewChart').getContext('2d');
const crewData = {
    labels: <?php echo json_encode(array_keys($crewPerformance)); ?>,
    datasets: [{
        label: 'Completed Duties',
        data: <?php echo json_encode(array_values($crewPerformance)); ?>,
        backgroundColor: 'orange',
    }]
};
new Chart(crewCtx, { type: 'bar', data: crewData });

const routeCtx = document.getElementById('routeEfficiencyChart').getContext('2d');
const routeData = {
    labels: <?php echo json_encode(array_keys($routeEfficiency)); ?>,
    datasets: [{
        label: 'Total Trips',
        data: <?php echo json_encode(array_column($routeEfficiency, 'total_trips')); ?>,
        backgroundColor: 'blue',
    }, {
        label: 'Total Distance (km)',
        data: <?php echo json_encode(array_column($routeEfficiency, 'total_distance')); ?>,
        backgroundColor: 'green',
    }]
};
new Chart(routeCtx, { type: 'bar', data: routeData });

const crewWorkloadCtx = document.getElementById('crewWorkloadChart').getContext('2d');
const crewWorkloadData = {
    labels: <?php echo json_encode(array_keys($crewWorkload)); ?>,
    datasets: [{
        label: 'Assigned Duties',
        data: <?php echo json_encode(array_values($crewWorkload)); ?>,
        backgroundColor: 'orange',
    }]
};
new Chart(crewWorkloadCtx, { type: 'bar', data: crewWorkloadData });

const crewPerformanceCtx = document.getElementById('crewPerformanceChart').getContext('2d');
const crewPerformanceData = {
    labels: <?php echo json_encode(array_keys($crewPerformance)); ?>,
    datasets: [{
        label: 'Completed Duties',
        data: <?php echo json_encode(array_values($crewPerformance)); ?>,
        backgroundColor: 'purple',
    }]
};
new Chart(crewPerformanceCtx, { type: 'bar', data: crewPerformanceData });
</script>
</script>
</body>
</html>
