<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$route_number = $_GET['route_number'] ?? '';

$sql = "SELECT * FROM routes WHERE 1=1";

if (!empty($route_number)) {
    $sql .= " AND route_number = '" . $conn->real_escape_string($route_number) . "'";
}

$result = $conn->query($sql);
$routes = [];
while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}

echo json_encode($routes);
?>
