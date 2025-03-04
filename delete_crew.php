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

// Handle duty deletion
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM crews WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>alert('Crew deleted successfully.'); window.location.href='crewing.php';</script>";
        } else {
            echo "<script>alert('Error deleting Crew.'); window.location.href='crewing.php';</script>";
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>