<?php
// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the auth.php file to access authentication functions
require_once 'auth.php'; // Adjust the path if necessary

// Database connection parameters
$host = 'localhost';
$db = 'user_auth';
$user = 'root'; // Your database username
$pass = '';     // Your database password

// Create a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !hasRole($_SESSION['user_id'], 'manager')) {
    header("Location: login.php");
    exit();
}

// Handle new user creation
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_user'])) {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $newRole = $_POST['role'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $phoneNo = $_POST['phone_no'];
    $idProofNumber = $_POST['id_proof_number'];
    $experience = $_POST['experience'];

    // Prepare and execute the SQL statement to insert the new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role, first_name, last_name, phone_no, id_proof_number, experience) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssssss", $newUsername, $newEmail, $newPassword, $newRole, $firstName, $lastName, $phoneNo, $idProofNumber, $experience);
    if ($stmt->execute()) {
        $message = "New user created successfully.";
    } else {
        $message = "Error creating user: " . $stmt->error;
    }

    $stmt->close();
}

// Handle edit request
$user = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("User  not found.");
    }
}

// Handle user update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id = $_POST['edit_id'];
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $newRole = $_POST['role'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $phoneNo = $_POST['phone_no'];
    $idProofNumber = $_POST['id_proof_number'];
    $experience = $_POST['experience'];

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=?, first_name=?, last_name=?, phone_no=?, id_proof_number=?, experience=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $newUsername, $newEmail, $newRole, $firstName, $lastName, $phoneNo, $idProofNumber, $experience, $id);
    if ($stmt->execute()) {
        $message = "User  updated successfully.";
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit;
    } else {
        $message = "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); exit;
    } else {
        $message = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all users for display
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-4"><center>User  Management</center></h2>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
   

    <h3><?php echo $user ? 'Edit User' : 'Create User'; ?></h3>
    <form method="POST" action="">
        <?php if ($user): ?>
            <input type="hidden" name="edit_id" value="<?php echo $user['id']; ?>">
        <?php endif; ?>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo $user ? htmlspecialchars($user['first_name']) : ''; ?>" required placeholder="First Name">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo $user ? htmlspecialchars($user['last_name']) : ''; ?>" required placeholder="Last Name">
        </div>
        <div class="form-group">
            <label for="phone_no">Phone Number:</label>
            <input type="text" name="phone_no" class="form-control" value="<?php echo $user ? htmlspecialchars($user['phone_no']) : ''; ?>" required placeholder="Phone Number">
        </div>
        <div class="form-group">
            <label for="id_proof_number">ID Proof Number:</label>
            <input type="text" name="id_proof_number" class="form-control" value="<?php echo $user ? htmlspecialchars($user['id_proof_number']) : ''; ?>" required placeholder="ID Proof Number">
        </div>
        <div class="form-group">
            <label for="experience">Experience:</label>
            <input type="text" name="experience" class="form-control" value="<?php echo $user ? htmlspecialchars($user['experience']) : ''; ?>" required placeholder="Experience">
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" value="<?php echo $user ? htmlspecialchars($user['username']) : ''; ?>" required placeholder="Username">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>" required placeholder="Email">
        </div>
        <div class="form-group">
        <label for="password">Password:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" class="form-control" required placeholder="Password">
            </div>
        <div class="form-group">
            <label for="role">Select Role:</label>
            <select name="role" class="form-control" required>
                <option value="driver" <?php echo ($user && $user['role'] == 'driver') ? 'selected' : ''; ?>>Driver</option>
                <option value="conductor" <?php echo ($user && $user['role'] == 'conductor') ? 'selected' : ''; ?>>Conductor</option>
                <option value="manager" <?php echo ($user && $user['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                <option value ="otheruser" <?php echo ($user && $user['role'] == 'otheruser') ? 'selected' : ''; ?>>OtherUser </option>
            </select>
        </div>
        <button type="submit" name="<?php echo $user ? 'update_user' : 'create_user'; ?>" class="btn btn-primary">
            <?php echo $user ? 'Update User' : 'Create User'; ?>
        </button>
    </form>


    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <h3 class="mt-5"><center>User List</center></h3>
    <table class="table table-striped">
        <thead>
            <tr>
                
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone Number</th>
                <th>ID Proof Number</th>
                <th>Experience</th>
                <th>Actions</th>
            </tr>
        </thead>
      <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
         
            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
            <td><?php echo htmlspecialchars($row['id_proof_number']); ?></td>
            <td><?php echo htmlspecialchars($row['experience']); ?></td>
            <td>
                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
    </table>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>