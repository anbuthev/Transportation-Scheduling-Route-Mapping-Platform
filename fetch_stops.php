<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$route_id = $_GET['route_id'] ?? null;
if (!$route_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT stop_name, stop_latitude, stop_longitude FROM intermediate_stops WHERE route_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();

$stops = [];
while ($row = $result->fetch_assoc()) {
    $stops[] = $row;
}

echo json_encode($stops);
?>
