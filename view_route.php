<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct route numbers for dropdown
$route_numbers = $conn->query("SELECT DISTINCT route_number FROM routes ORDER BY route_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filter Routes on Map</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/somanchiu/Keyless-Google-Maps-API@v6.8/mapsJavaScriptAPI.js"async defer"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 20px; }
        h2 { text-align: center; color: #333; }
        #route_number { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        #map { width: 100%; height: 500px; margin-top: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .btn-primary { width: 100%; margin-top: 10px; }
        .loading { display: none; text-align: center; margin-top: 10px; color: #007bff; }
    </style>
</head>
<body>

<div class="container">

    <h2>View Routes on Map</h2>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a><br><br>
    <label for="route_number" class="form-label">Select Route:</label>
    <select id="route_number" class="form-select" onchange="filterRoutes()">
        <option value="">All Routes</option>
        <?php while ($row = $route_numbers->fetch_assoc()) { ?>
            <option value="<?= htmlspecialchars($row['route_number']) ?>"><?= htmlspecialchars($row['route_number']) ?></option>
        <?php } ?>
    </select>

  

    <div class="loading">Loading routes, please wait...</div>

    <div id="map"></div>

    
</div>

<script>
let map, directionsService;
let markers = [];
let routeRenderers = [];

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 11.1956, lng: 77.2675 },
        zoom: 7
    });

    directionsService = new google.maps.DirectionsService();
    filterRoutes(); // Load routes on page load
}

// Generate a random color for routes
function getRandomColor() {
    return "#" + Math.floor(Math.random() * 16777215).toString(16);
}

function loadRoutes(routes) {
    clearMarkers();
    clearRoutes();
    
    if (routes.length === 0) {
        alert("No routes found for the selected filter.");
        return;
    }

    routes.forEach(route => {
        let start = { lat: parseFloat(route.start_latitude), lng: parseFloat(route.start_longitude) };
        let end = { lat: parseFloat(route.end_latitude), lng: parseFloat(route.end_longitude) };

        let startMarker = createMarker(start, `Start: ${route.start_location}`, "S");
        let endMarker = createMarker(end, `End: ${route.end_location}`, "E");

        $.get("fetch_stops.php", { route_id: route.route_id }, function(response) {
            let stops = JSON.parse(response);
            let waypoints = [];

            stops.forEach((stop, index) => {
                let position = { lat: parseFloat(stop.stop_latitude), lng: parseFloat(stop.stop_longitude) };
                let stopMarker = createMarker(position, `Stop ${index + 1}: ${stop.stop_name}`, String(index + 1));
                waypoints.push({ location: position, stopover: true });
            });

            drawRoute(start, end, waypoints, getRandomColor());
        });
    });
}

function createMarker(position, title, label) {
    let marker = new google.maps.Marker({
        position: position,
        map: map,
        title: title,
        label: label
    });
    markers.push(marker);
    return marker;
}

function drawRoute(start, end, waypoints, color) {
    let directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        polylineOptions: { strokeColor: color, strokeWeight: 5 }
    });

    directionsService.route({
        origin: start,
        destination: end,
        waypoints: waypoints,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === "OK") {
            directionsRenderer.setDirections(response);
            routeRenderers.push(directionsRenderer);
        }
    });
}

function clearMarkers() {
    markers.forEach(marker => marker.setMap(null));
    markers = [];
}

function clearRoutes() {
    routeRenderers.forEach(renderer => renderer.setMap(null));
    routeRenderers = [];
}

function filterRoutes() {
    $(".loading").show();  // Show loading text
    let routeNumber = $("#route_number").val();

    $.get("filter_routes.php", { route_number: routeNumber }, function(response) {
        loadRoutes(JSON.parse(response));
        $(".loading").hide();  // Hide loading text
    }).fail(function() {
        alert("Failed to load routes. Please try again.");
        $(".loading").hide();
    });
}
</script>

</body>
</html>
