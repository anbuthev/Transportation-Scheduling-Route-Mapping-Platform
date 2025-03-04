<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if route_id is provided
if (!isset($_GET['route_id'])) {
    die("Invalid request: Route ID is required.");
}

$route_id = $_GET['route_id'];

// Delete query (CASCADE should be enabled for intermediate stops to delete automatically)
$sql = "DELETE FROM routes WHERE route_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $route_id);

if ($stmt->execute()) {
    echo "<script>alert('Route deleted successfully!'); window.location.href='rts.php';</script>";
} else {
    echo "<script>alert('Error deleting route. Please try again.');</script>";
}

$conn->close();
?>
