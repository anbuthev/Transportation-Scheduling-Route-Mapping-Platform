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

// Handle Crew Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_crew"])) {
    $crew_name = htmlspecialchars($_POST['crew_name']);
    $crew_member1 = (int)$_POST['crew_member1'];
    $crew_member2 = (int)$_POST['crew_member2'];
    $crew_member1_role = (int)$_POST['crew_member1_role'];
    $crew_member2_role = (int)$_POST['crew_member2_role'];

    $stmt = $conn->prepare("INSERT INTO crews (crew_name, crew_member1, crew_member2, crew_member1_role, crew_member2_role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("siiii", $crew_name, $crew_member1, $crew_member2, $crew_member1_role, $crew_member2_role);
        $stmt->execute();
        echo "<script>alert('Crew added successfully.'); window.location.href='crewing.php';</script>";
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
//Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM crews WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); exit;
    } else {
        $message = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}
// Handle Crew Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_crew"])) {
    $id = (int)$_POST['id'];
    $crew_name = htmlspecialchars($_POST['crew_name']);
    $crew_member1 = (int)$_POST['crew_member1'];
    $crew_member2 = (int)$_POST['crew_member2'];
    $crew_member1_role = (int)$_POST['crew_member1_role'];
    $crew_member2_role = (int)$_POST['crew_member2_role'];

    $stmt = $conn->prepare("UPDATE crews SET crew_name=?, crew_member1=?, crew_member2=?, crew_member1_role=?, crew_member2_role=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("siiiii", $crew_name, $crew_member1, $crew_member2, $crew_member1_role, $crew_member2_role, $id);
        $stmt->execute();
        echo "<script>alert('Crew updated successfully.'); window.location.href='crewing.php';</script>";
        $stmt->close();
    } else {
        echo "Error updating crew: " . $conn->error;
    }
}

// Fetch Users & Roles for Crew Selection
$userQuery = $conn->query("SELECT id, first_name, last_name, role FROM users");
$users = $userQuery->fetch_all(MYSQLI_ASSOC);

// Fetch Crews
$crewQuery = $conn->query("SELECT * FROM crews");
$crews = $crewQuery->fetch_all(MYSQLI_ASSOC);

// Role Mapping
$roleNames = [
    0 => "Conductor",
    1 => "Driver",
    2 => "Manager",
    3 => "OtherUser"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duty Assignment System</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .form-control, .btn {
            margin-bottom: 10px;
            border-radius: 8px;
        }
        h3 {
            font-weight: bold;
            color: #495057;
        }
        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .btn {
            font-weight: bold;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Crew Management</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="card p-4">
        <h3 class="text-center">Add Crew</h3>
        <form method="post">
        <input type="text" name="crew_name" class="form-control" placeholder="Enter Crew Name" required>
                        
                        <label>Select Crew Member 1:</label>
                        <select name="crew_member1" id="crew_member1" class="form-control" required onchange="updateRole('crew_member1', 'crew_member1_role')">
                            <option value="">Select Crew Member</option>
                            <?php foreach ($users as $user) { ?>
                                <option value="<?= $user['id'] ?>" data-role="<?= $user['role'] ?>">
                                    <?= $user['first_name'] . ' ' . $user['last_name'] .'           '.$user['role']?> 
                                </option>
                            <?php } ?>
                        </select>
                        <input type="text" name="crew_member1_role" id="crew_member1_role" class="form-control" placeholder="Role" readonly>

                        <label>Select Crew Member 2:</label>
                        <select name="crew_member2" id="crew_member2" class="form-control" required onchange="updateRole('crew_member2', 'crew_member2_role')">
                            <option value="">Select Crew Member</option>
                            <?php foreach ($users as $user) { ?>
                                <option value="<?= $user['id'] ?>" data-role="<?= $user['role'] ?>">
                                <?= $user['first_name'] . ' ' . $user['last_name'] .' '.$user['role']?> 
                                </option>
                            <?php } ?>
                        </select>
                        <input type="text" name="crew_member2_role" id="crew_member2_role" class="form-control" placeholder="Role" readonly>
                        
                        <button type="submit" name="add_crew" class="btn btn-primary w-100">Add Crew</button>

            
        </form>
    </div>

    <div class="card mt-4 p-3">
        <h3 class="text-center">Crew Details</h3>
        <table class="table table-striped table-hover table-bordered">
            <thead >
                <tr>
                    <th>Crew Name</th>
                    <th>Crew Member 1</th>
                    <th>Role</th>
                    <th>Crew Member 2</th>
                    <th>Role</th>
                    <th>Actions</th>    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($crews as $crew) { 
                    $member1Query = $conn->query("SELECT first_name, last_name, role FROM users WHERE id = ".$crew['crew_member1']);
                    $member2Query = $conn->query("SELECT first_name, last_name, role FROM users WHERE id = ".$crew['crew_member2']);
                    $member1 = $member1Query->fetch_assoc();
                    $member2 = $member2Query->fetch_assoc();
                ?>
                <tr>
                    <td><?= $crew['crew_name'] ?></td>
                    <td><?= isset($member1) ? $member1['first_name'] . ' ' . $member1['last_name'] : 'N/A' ?></td>
                    <td><?= isset($member1) ? $member1['role'] : 'Unknown' ?></td>
                    <td><?= isset($member2) ? $member2['first_name'] . ' ' . $member2['last_name'] : 'N/A' ?></td>
                    <td><?= isset($member2) ? $member2['role'] : 'Unknown' ?></td>
                
                    <td>
                        <a href="edit_crew.php?id=<?= $crew['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <button class="btn btn-danger" onclick="confirmDelete(<?= $crew['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation -->
<script>
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this crew?")) {
            window.location.href = "delete_crew.php?id=" + id;
        }
    }

    function updateRole(crewSelectId, roleInputId) {
        var crewSelect = document.getElementById(crewSelectId);
        var selectedOption = crewSelect.options[crewSelect.selectedIndex];
        var role = selectedOption.getAttribute('data-role');
        document.getElementById(roleInputId).value = role;
    }
</script>

</body>
</html>


