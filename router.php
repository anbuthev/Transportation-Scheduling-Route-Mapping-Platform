<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Edit Route Logic
$route_data = null;
if (isset($_GET['edit'])) {
    $route_id = $_GET['edit'];
    $edit_query = "SELECT * FROM routes WHERE route_id = ?";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bind_param("i", $route_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    $edit_route = $edit_result->fetch_assoc();

    // Populate existing data
    $route_number = $edit_route['route_number'];
    $start_location = $edit_route['start_location'];
    $start_latitude = $edit_route['start_latitude'];
    $start_longitude = $edit_route['start_longitude'];
    $end_location = $edit_route['end_location'];
    $end_latitude = $edit_route['end_latitude'];
    $end_longitude = $edit_route['end_longitude'];
    $total_distance = $edit_route['total_distance'];
}

// Delete Route Logic
if (isset($_GET['delete'])) {
    $route_id = $_GET['delete'];
    $delete_query = "DELETE FROM routes WHERE route_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $route_id);
    if ($delete_stmt->execute()) {
        $delete_stops_query = "DELETE FROM intermediate_stops WHERE route_id = ?";
        $delete_stops_stmt = $conn->prepare($delete_stops_query);
        $delete_stops_stmt->bind_param("i", $route_id);
        $delete_stops_stmt->execute();
        echo "<script>alert('Route deleted successfully!'); window.location = 'router.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_id = isset($_GET['edit']) ? $_GET['edit'] : null;
    $route_number = $_POST['route_number'];
    $start_location = $_POST['start_location'];
    $start_latitude = $_POST['start_latitude'];
    $start_longitude = $_POST['start_longitude'];
    $end_location = $_POST['end_location'];
    $end_latitude = $_POST['end_latitude'];
    $end_longitude = $_POST['end_longitude'];
    $total_distance = $_POST['total_distance'];

    if (!$route_id) { // Only check for existing route number when adding a new route
        $check_query = "SELECT route_id FROM routes WHERE route_number = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $route_number);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Route number already exists. Please use a different number.');</script>";
            exit;
        }
    }

    if ($route_id) {
        // Update existing route
        $sql = "UPDATE routes SET 
                route_number = ?, 
                start_location = ?, start_latitude = ?, start_longitude = ?, 
                end_location = ?, end_latitude = ?, end_longitude = ?, 
                total_distance = ? 
                WHERE route_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddssddi", $route_number, $start_location, $start_latitude, $start_longitude, 
                          $end_location, $end_latitude, $end_longitude, $total_distance, $route_id);
    } else {
        // Insert new route
        $sql = "INSERT INTO routes (route_number, start_location, start_latitude, start_longitude, 
                end_location, end_latitude, end_longitude, total_distance) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddssdd", $route_number, $start_location, $start_latitude, $start_longitude, 
                          $end_location, $end_latitude, $end_longitude, $total_distance);
    }

    if ($stmt->execute()) {
        $route_id = $route_id ?: $stmt->insert_id; // Get inserted route ID if it's new

        // Delete previous stops if editing
        if (isset($_GET['edit'])) {
            $delete_stops_query = "DELETE FROM intermediate_stops WHERE route_id = ?";
            $delete_stops_stmt = $conn->prepare($delete_stops_query);
            $delete_stops_stmt->bind_param("i", $route_id);
            $delete_stops_stmt->execute();
        }

        // Insert intermediate stops
        if (!empty($_POST["intermediate_stops"])) {
            $intermediate_stops = json_decode($_POST["intermediate_stops"], true);
            foreach ($intermediate_stops as $stop) {
                $stop_name = $stop["name"];
                $stop_latitude = $stop["latitude"];
                $stop_longitude = $stop["longitude"];

                $stop_sql = "INSERT INTO intermediate_stops (route_id, stop_name, stop_latitude, stop_longitude) VALUES (?, ?, ?, ?)";
                $stop_stmt = $conn->prepare($stop_sql);
                $stop_stmt->bind_param("isdd", $route_id, $stop_name, $stop_latitude, $stop_longitude);
                $stop_stmt->execute();
            }
        }

        echo "<script>alert('Route " . ($route_id ? 'added' : 'updated') . " successfully!'); window.location = 'router.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}



// Fetch routes and stops
$routes_query = "SELECT * FROM routes";
$routes_result = $conn->query($routes_query);

// Fetch intermediate stops
$stops_query = "SELECT * FROM intermediate_stops";
$stops_result = $conn->query($stops_query);
$intermediate_stops = [];
while ($row = $stops_result->fetch_assoc()) {
    $intermediate_stops[$row['route_id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Management</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        form input, form button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        form button {
            background-color:rgb(175, 144, 76);
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th, td {
            padding: 12px;
        }

        th {
            background-color: #f2f2f2;
        }



        /* Style for Calculate Distance button */
        #calculateDistanceBtn {
            background-color: #007BFF;  /* Blue */
            color: white;
            border: none;
            font-size: 16px;
        }

        #calculateDistanceBtn:hover {
            background-color: #0056b3;  /* Darker blue */
        }

        /* Style for Add Route button */
        button[type="submit"] {
            background-color: #28a745;  /* Green */
            color: white;
            border: none;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #218838;  /* Darker green */
        }
        .back-btn {
    display: inline-block;
    padding: 10px 15px;
    background-color: #0b7dda;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 10px;
}

.back-btn:hover {
    background-color: #0a6bbd;  /* Slightly darker blue */
}
.edit-btn {
    background-color: #ffc107; /* Yellow */
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
}

.edit-btn:hover {
    background-color: #e0a800; /* Darker yellow */
}

/* Delete Button */
.delete-btn {
    background-color: #dc3545; /* Red */
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
}

.delete-btn:hover {
    background-color: #c82333; /* Darker red */
}

    </style>
</head>
<body>

<div class="container">
    <h2>Create Routes</h2>
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
<!-- Add a condition to pre-populate the form if editing -->
<form method="post">
    <label for="route_number">Route Number:</label>
    <input type="text" name="route_number" id="route_number" value="<?= isset($route_number) ? $route_number : '' ?>" required>

    <label for="start_location">Start Location:</label>
    <input type="text" id="start_location" name="start_location" value="<?= isset($start_location) ? $start_location : '' ?>" required>
    <input type="hidden" id="start_latitude" name="start_latitude" value="<?= isset($start_latitude) ? $start_latitude : '' ?>">
    <input type="hidden" id="start_longitude" name="start_longitude" value="<?= isset($start_longitude) ? $start_longitude : '' ?>">
    <input type="hidden" id="intermediate_stops" name="intermediate_stops">
    <label for="end_location">End Location:</label>
    <input type="text" id="end_location" name="end_location" value="<?= isset($end_location) ? $end_location : '' ?>" required>
    <input type="hidden" id="end_latitude" name="end_latitude" value="<?= isset($end_latitude) ? $end_latitude : '' ?>">
    <input type="hidden" id="end_longitude" name="end_longitude" value="<?= isset($end_longitude) ? $end_longitude : '' ?>">

    <label for="total_distance">Total Distance (km):</label>
    <input type="text" id="total_distance" name="total_distance" value="<?= isset($total_distance) ? $total_distance : '' ?>" readonly>

    <button id="calculateDistanceBtn" type="button" onclick="calculateAndDisplayRoute()">Calculate Distance</button>

    <div id="map"></div>
    <button type="submit">
    <?php 
    // Check if the route_data exists and if we are in editing mode
    echo isset($route_id) ? 'Update Route' : 'Add Route'; 
    ?>
</button>


    
</form>
    <h2>Existing Routes</h2>
    <table>
        <thead>
            <tr>
                <th>Route No.</th>
                <th>Start Location</th>
                <th>Intermediate Stops</th>
                <th>End Location</th>
                <th>Total Distance (km)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $routes_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["route_number"] ?></td>
                    <td><?= $row["start_location"] ?></td>
                    <td>
                        <?php
                        if (isset($intermediate_stops[$row["route_id"]])) {
                            echo implode(", ", array_column($intermediate_stops[$row["route_id"]], 'stop_name'));
                        } else {
                            echo "None";
                        }
                        ?>
                    </td>
                    <td><?= $row["end_location"] ?></td>
                    <td><?= $row["total_distance"] ?> km</td>
                    <td>
                <a href="?edit=<?= $row["route_id"] ?>" class="edit-btn">Edit</a>
                <a href="?delete=<?= $row["route_id"] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this route?')">Delete</a>
            </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
  let map, startMarker, endMarker, intermediateMarkers = [];
let intermediateStops = [];
let directionsService, directionsRenderer;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 11.1956, lng: 77.2675 },
        zoom: 10
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

    map.addListener("click", (event) => {
        const latLng = event.latLng;
        const locationType = prompt("Enter location type (start, stop, or end):");

        if (!locationType) return;

        const locationName = prompt("Enter location name:");
        if (!locationName) return;

        if (locationType.toLowerCase() === "start") {
            setMarker("start", latLng, locationName);
        } else if (locationType.toLowerCase() === "stop") {
            addStop(latLng, locationName);
        } else if (locationType.toLowerCase() === "end") {
            setMarker("end", latLng, locationName);
        } else {
            alert("Invalid location type. Please enter 'start', 'stop', or 'end'.");
        }
    });
}

function setMarker(type, position, name) {
    if (type === "start") {
        if (startMarker) startMarker.setMap(null);
        startMarker = createMarker(position, "S");
        updateInputFields("start", position, name);
    } else if (type === "end") {
        if (endMarker) endMarker.setMap(null);
        endMarker = createMarker(position, "E");
        updateInputFields("end", position, name);
    }

    if (startMarker && endMarker) {
        calculateAndDisplayRoute();
    }
}

function addStop(position, name) {
    let interMarker = createMarker(position, "" + (intermediateMarkers.length + 1));
    intermediateMarkers.push(interMarker);
    intermediateStops.push({ name: name, latitude: position.lat(), longitude: position.lng() });
    document.getElementById("intermediate_stops").value = JSON.stringify(intermediateStops);
    calculateAndDisplayRoute();
}

function createMarker(position, label) {
    let marker = new google.maps.Marker({
        position: position,
        map: map,
        label: label,
        draggable: true
    });

    // Double-click event to remove marker
    marker.addListener("dblclick", function () {
        marker.setMap(null);

        if (marker === startMarker) {
            startMarker = null;
            document.getElementById("start_location").value = "";
            document.getElementById("start_latitude").value = "";
            document.getElementById("start_longitude").value = "";
        } else if (marker === endMarker) {
            endMarker = null;
            document.getElementById("end_location").value = "";
            document.getElementById("end_latitude").value = "";
            document.getElementById("end_longitude").value = "";
        } else {
            let index = intermediateMarkers.indexOf(marker);
            if (index > -1) {
                intermediateMarkers.splice(index, 1);
                intermediateStops.splice(index, 1);
                document.getElementById("intermediate_stops").value = JSON.stringify(intermediateStops);
            }
        }
        calculateAndDisplayRoute();
    });
    
    return marker;
}

function updateInputFields(type, position, name) {
    document.getElementById(type + "_location").value = name;
    document.getElementById(type + "_latitude").value = position.lat();
    document.getElementById(type + "_longitude").value = position.lng();
}

function calculateAndDisplayRoute() {
    if (!startMarker || !endMarker) return;

    let waypoints = intermediateStops.map(stop => ({
        location: new google.maps.LatLng(stop.latitude, stop.longitude),
        stopover: true
    }));

    directionsService.route({
        origin: startMarker.getPosition(),
        destination: endMarker.getPosition(),
        waypoints: waypoints,
        travelMode: google.maps.TravelMode.DRIVING
    }, function (response, status) {
        if (status === "OK") {
            directionsRenderer.setDirections(response);
            document.getElementById("total_distance").value = (response.routes[0].legs.reduce((sum, leg) => sum + leg.distance.value, 0) / 1000).toFixed(2);
        }
    });
}

</script>

<script src="https://cdn.jsdelivr.net/gh/somanchiu/Keyless-Google-Maps-API@v6.8/mapsJavaScriptAPI.js"async defer></script>

</body>
</html>
