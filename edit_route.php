<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth"; // Adjust to match your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if route_id is provided
if (!isset($_GET['route_id'])) {
    die("Invalid request: Route ID is required.");
}

$route_id = $_GET['route_id'];

// Fetch route details
$sql = "SELECT * FROM routes WHERE route_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$route = $result->fetch_assoc();

if (!$route) {
    die("Route not found.");
}

// Fetch intermediate stops
$stops_sql = "SELECT stop_name, stop_latitude, stop_longitude FROM intermediate_stops WHERE route_id = ?";
$stops_stmt = $conn->prepare($stops_sql);
$stops_stmt->bind_param("i", $route_id);
$stops_stmt->execute();
$stops_result = $stops_stmt->get_result();
$intermediate_stops = [];
while ($row = $stops_result->fetch_assoc()) {
    $intermediate_stops[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Route</title>
    <script src="https://cdn.jsdelivr.net/gh/somanchiu/Keyless-Google-Maps-API@v6.8/mapsJavaScriptAPI.js"async defer"></script>
    
</head>
<body>

<h2>Edit Route</h2>

<form method="POST">
    <label for="route_number">Route Number:</label>
    <input type="text" id="route_number" name="route_number" value="<?php echo $route['route_number']; ?>" required>

    <label for="start_location">Start Location:</label>
    <input type="text" id="start_location" name="start_location" value="<?php echo $route['start_location']; ?>" required>
    <input type="hidden" id="start_latitude" name="start_latitude" value="<?php echo $route['start_latitude']; ?>">
    <input type="hidden" id="start_longitude" name="start_longitude" value="<?php echo $route['start_longitude']; ?>">

    <label for="end_location">End Location:</label>
    <input type="text" id="end_location" name="end_location" value="<?php echo $route['end_location']; ?>" required>
    <input type="hidden" id="end_latitude" name="end_latitude" value="<?php echo $route['end_latitude']; ?>">
    <input type="hidden" id="end_longitude" name="end_longitude" value="<?php echo $route['end_longitude']; ?>">

    <label for="total_distance">Total Distance (km):</label>
    <input type="text" id="total_distance" name="total_distance" value="<?php echo $route['total_distance']; ?>" required readonly>

    <!-- Hidden input for intermediate stops -->
    <input type="hidden" id="intermediate_stops" name="intermediate_stops">

    <button type="button" onclick="calculateAndDisplayRoute()">Calculate Distance</button>
    <button type="submit">Update Route</button>
    <button type="button" onclick="resetMap()">Reset Markers</button>
</form>


<!-- Google Map -->
<div id="map" style="height: 400px; width: 100%;"></div>

<script>
let map, startMarker, endMarker, intermediateMarkers = [];
let intermediateStops = <?php echo json_encode($intermediate_stops); ?>;
let directionsService, directionsRenderer;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 11.1956, lng: 77.2675 },
        zoom: 10
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

    // Load existing markers
    startMarker = createMarker({ lat: <?php echo $route['start_latitude']; ?>, lng: <?php echo $route['start_longitude']; ?> }, "S");
    endMarker = createMarker({ lat: <?php echo $route['end_latitude']; ?>, lng: <?php echo $route['end_longitude']; ?> }, "E");

    intermediateStops.forEach((stop, index) => {
        let marker = createMarker({ lat: parseFloat(stop.stop_latitude), lng: parseFloat(stop.stop_longitude) }, "" + (index + 1));
        intermediateMarkers.push(marker);
    });

    calculateAndDisplayRoute();

    // Click event to add locations
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

function createMarker(position, label) {
    return new google.maps.Marker({
        position: position,
        map: map,
        label: label,
        draggable: true
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

function updateInputFields(type, position, name) {
    document.getElementById(type + "_location").value = name;
    document.getElementById(type + "_latitude").value = position.lat();
    document.getElementById(type + "_longitude").value = position.lng();
}

function calculateAndDisplayRoute() {
    if (!startMarker || !endMarker) {
        alert("Please set both Start and End locations.");
        return;
    }

    let waypoints = intermediateStops.map(stop => ({
        location: new google.maps.LatLng(parseFloat(stop.latitude), parseFloat(stop.longitude)),
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
            let totalDistance = response.routes[0].legs.reduce((sum, leg) => sum + leg.distance.value, 0) / 1000;
            document.getElementById("total_distance").value = totalDistance.toFixed(2);
        }
    });
}

function resetMap() {
    startMarker?.setMap(null);
    endMarker?.setMap(null);
    intermediateMarkers.forEach(marker => marker.setMap(null));
    intermediateMarkers = [];
    intermediateStops = [];
    directionsRenderer.setDirections({ routes: [] });
}

window.onload = initMap;
</script>

</body>
</html>
