<?php
session_start();

// Database connection (You'll need to replace with your database connection)
$host = 'localhost';
$db = 'user_auth';
$user = 'root';
$pass = '';

$conn = new mysqli('localhost', 'username', 'password', 'user_auth');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $conn->real_escape_string($data['id']);

    $sql = "DELETE FROM vehicles WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}
$conn->close();
?>