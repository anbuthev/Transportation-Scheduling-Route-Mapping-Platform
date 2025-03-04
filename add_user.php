<?php
session_start();

// Database connection
// ... (Your database connection code)

// Check if the user is logged in and an admin
if (!isset($_SESSION['user_id']) || !hasRole($_SESSION['user_id'], 'admin', $conn)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    if ($stmt->execute()) {
        echo "<p>User added successfully!</p>";
    } else {
        echo "<p>Error adding user: " . $stmt->error . "</p>";
    }
}
?>

<h2>Add New User</h2>
<form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" name="username" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <br>
    <label for="role">Role:</label>
    <select name="role">
        <option value="admin">Admin</option>
        <option value="driver">Driver</option>
        <option value="conductor">Conductor</option>
        <option value="user">User </option>
    </select>
    <br>
    <input type="submit" value="Add User">
</form>