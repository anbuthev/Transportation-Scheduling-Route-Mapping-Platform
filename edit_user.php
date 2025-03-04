<?php
session_start();
require_once 'auth.php'; 

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

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !hasRole($_SESSION['user_id'], 'manager')) {
    header("Location: login.php");
    exit();
}

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    die("User  ID not specified.");
}

$userId = intval($_GET['id']); // Get the user ID from the URL

// Retrieve the user's current information
$stmt = $conn->prepare("SELECT username, email, password_hash, role, first_name, last_name, phone_no, id_proof_number, experience FROM users WHERE id = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User  not found.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating user information
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newRole = $_POST['role'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $phoneNo = $_POST['phone_no'];
    $idProofNumber = $_POST['id_proof_number'];
    $experience = $_POST['experience'];

    // Check if a new password is provided
    $newPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password_hash'];

    // Prepare and execute the SQL statement to update the user
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password_hash = ?, role = ?, first_name = ?, last_name = ?, phone_no = ?, id_proof_number = ?, experience = ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssssssi", $newUsername, $newEmail, $newPassword, $newRole, $firstName, $lastName, $phoneNo, $idProofNumber, $experience, $userId);
    if ($stmt->execute()) {
        $message = "User  updated successfully.";
    } else {
        $message = "Error updating user: " . $stmt->error;
    }

    $stmt->close();
}

// Display the edit user form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .message {
            color: #d9534f;
            text-align: center;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #5bc0de;
        }
        a:hover {
            text-decoration: underline;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Edit User</h2>

    <?php if ($message) echo "<p class='message'>" . htmlspecialchars($message) . "</p>"; ?>

    <form method="POST" action="">
        <input type="text" name="first_name" value ="<?php echo htmlspecialchars($user['first_name']); ?>" required placeholder="First Name">
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required placeholder="Last Name">
        <input type="text" name="phone_no" value="<?php echo htmlspecialchars($user['phone_no']); ?>" required placeholder="Phone Number">
        <input type="text" name="id_proof_number" value="<?php echo htmlspecialchars($user['id_proof_number']); ?>" required placeholder="ID Proof Number">
        <input type="text" name="experience" value="<?php echo htmlspecialchars($user['experience']); ?>" required placeholder="Experience">
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required placeholder="Username">
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="Email">
        <div class="password-container">
            <input type="password" id="password" name="password" required placeholder="Password">
        </div>
        <label for="role">Select Role:</label>
        <select name="role" required>
            <option value="driver" <?php echo ($user['role'] == 'driver') ? 'selected' : ''; ?>>Driver</option>
            <option value="conductor" <?php echo ($user['role'] == 'conductor') ? 'selected' : ''; ?>>Conductor</option>
            <option value="manager" <?php echo ($user['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
            <option value="otheruser" <?php echo ($user['role'] == 'otheruser') ? 'selected' : ''; ?>>Other User</option>
        </select>
        <button type="submit">Update User</button>
    </form>

    <a href="user_management.php">Back to User Management</a>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordType = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', passwordType);
        }
    </script>
</body>
</html>

<?php
// Close the database connection at the end
$conn->close();
 ?>