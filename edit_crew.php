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

// Fetch Existing Crew Details
$crew = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM crews WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $crew = $result->fetch_assoc();
    $stmt->close();
    if (!$crew) {
        die("Crew not found.");
    }
} else {
    die("Invalid crew ID.");
}

// Handle Crew Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_crew"])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $crew_name = htmlspecialchars($_POST['crew_name']);
    $crew_member1 = intval($_POST['crew_member1']);
    $crew_member2 = intval($_POST['crew_member2']);
    $crew_member1_role = htmlspecialchars($_POST['crew_member1_role']);
    $crew_member2_role = htmlspecialchars($_POST['crew_member2_role']);
    
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE crews SET crew_name=?, crew_member1=?, crew_member2=?, crew_member1_role=?, crew_member2_role=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("siissi", $crew_name, $crew_member1, $crew_member2, $crew_member1_role, $crew_member2_role, $id);
            if ($stmt->execute()) {
                echo "<script>alert('Crew updated successfully.'); window.location.href='crewing.php';</script>";
            } else {
                echo "Error updating crew: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "<script>alert('Invalid crew ID.');</script>";
    }
}

// Fetch Users & Roles for Crew Selection
$userQuery = $conn->query("SELECT id, first_name, last_name, role FROM users");
$users = $userQuery->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Crew</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
</head>
<body>
    
        <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg p-4">
            <h3 class="text-center">Edit Crew</h3>
            
            <form method="POST">
                <input type="hidden" name="id" value="<?= $crew['id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Crew Name:</label>
                    <input type="text" name="crew_name" class="form-control" value="<?= htmlspecialchars($crew['crew_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Crew Member 1:</label>
                    <select name="crew_member1" id="crew_member1" class="form-control" required onchange="updateRole('crew_member1', 'crew_member1_role')">
                        <option value="">Select Crew Member</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?= $user['id'] ?>" data-role="<?= htmlspecialchars($user['role']) ?>" <?= $user['id'] == $crew['crew_member1'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['role'] . ')') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role:</label>
                    <input type="text" name="crew_member1_role" id="crew_member1_role" class="form-control" value="<?= htmlspecialchars($crew['crew_member1_role']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Crew Member 2:</label>
                    <select name="crew_member2" id="crew_member2" class="form-control" required onchange="updateRole('crew_member2', 'crew_member2_role')">
                        <option value="">Select Crew Member</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?= $user['id'] ?>" data-role="<?= htmlspecialchars($user['role']) ?>" <?= $user['id'] == $crew['crew_member2'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['role'] . ')') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role:</label>
                    <input type="text" name="crew_member2_role" id="crew_member2_role" class="form-control" value="<?= htmlspecialchars($crew['crew_member2_role']) ?>" readonly>
                </div>

                <script>
                    function updateRole(memberId, roleId) {
                        let memberSelect = document.getElementById(memberId);
                        let roleInput = document.getElementById(roleId);
                        let selectedRole = memberSelect.options[memberSelect.selectedIndex].getAttribute('data-role');
                        roleInput.value = selectedRole || ''; // Prevent null values
                    }
                </script>
                <div class="text-center">
                <button type="submit" name="update_crew" class="btn btn-primary">Update Crew</button>
                <button type="button" class="btn btn-danger" onclick="window.location.href='crewing.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>

</body>
</html>
