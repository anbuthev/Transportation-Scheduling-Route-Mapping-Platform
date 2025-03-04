<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

/**
 * Function to check if a user has a specific role
 * 
 * @param int $userId
 * @param string $role
 * @return bool
 */
function hasRole($userId, $role) {
    global $conn; // Use the global connection

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userRole);
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    return $userRole === $role;
}

/**
 * Function to get the role of a user
 * 
 * @param int $userId
 * @return string|null
 */
function getUserrole($userId) { // Corrected function name
    global $conn; // Use the global connection

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    return $role;
}

// Close the database connection when you're done with all operations
// You can call this at the end of your script or in a separate cleanup function
// $conn->close();
?>