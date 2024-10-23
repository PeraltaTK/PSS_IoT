<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php"); // Change this to your login page
    exit();
}

// Function to fetch data from the API
function fetchDataFromApi($url) {
    $jsonContent = file_get_contents($url);
    return json_decode($jsonContent, true); // Decode JSON data into an associative array
}

// Fetch the field data from the API
$fieldData = fetchDataFromApi('api.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crop Irrigation Status</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
        .chart-container {
            width: 200px;
            height: 200px;
        }
        body {
            font-family: 'Orbitron', sans-serif;
            background-color: #f8f9fa; /* Light background */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .table-container {
            margin-top: 30px;
            width: 80%;
            margin: 0 auto;
        }
        .table-title {
            text-align: center;
            margin-top: 20px;
            color: #28a745;
            font-size: 32px;
        }
        .chart-container {
            width: 100%; /* Ensures the charts take full column width */
            margin: 0 auto;
            margin-top: 30px;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
        }
        .row {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
        }
        .col-md-4 {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 10px;
        }
    </style>
</head>
<body>
    <!-- header -->
<?php include 'header.php'; ?>

    <div id="map"></div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([37.0902, -95.7129], 5); // Centered on the United States

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Custom icons
        var greenIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/5447/5447063.png', // Replace with the URL of your green crop icon
            iconSize: [48, 48], // size of the icon
            iconAnchor: [24, 48], // point of the icon which will correspond to marker's location
            popupAnchor: [0, -48] // point from which the popup should open relative to the iconAnchor
        });

        var redIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/5447/5447063.png', // Replace with the URL of your red crop icon
            iconSize: [48, 48], // size of the icon
            iconAnchor: [24, 48], // point of the icon which will correspond to marker's location
            popupAnchor: [0, -48] // point from which the popup should open relative to the iconAnchor
        });

        
        
        // Example data for crop irrigation status in the United States
        var crops = [
            { "name": "Field 51", "lat": 37.24804, "lng": -115.800155, "status": "Irrigated", "lastWatered": "2023-10-01", "soilMoisture": "75%" }, // nevada
        ];

        // Function to create a chart
        function createChart(canvasId, data) {
            var ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Soil Moisture (%)',
                        data: data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Add markers to the map
        crops.forEach(function(crop) {
            var icon = crop.status === "Irrigated" ? greenIcon : redIcon;
            var marker = L.marker([crop.lat, crop.lng], { icon: icon, title: crop.status }).addTo(map);
            marker.bindPopup("<b>" + crop.name + "</b><br>Status: " + crop.status);

            // Add mouseover event to show additional info
            marker.on('mouseover', function(e) {
                var tooltipContent = "<b>" + crop.name + "</b><br>Status: " + crop.status + "<br>Last Watered: " + crop.lastWatered + "<br>Soil Moisture: " + crop.soilMoisture;
                tooltipContent += '<div class="chart-container"><canvas id="chart-' + crop.name.replace(/\s+/g, '-') + '"></canvas></div>';
                marker.bindTooltip(tooltipContent).openTooltip();

                // Generate random data for the chart (replace with real data as needed)
                var randomData = Array.from({ length: 12 }, () => Math.floor(Math.random() * 100));
                createChart('chart-' + crop.name.replace(/\s+/g, '-'), randomData);
            });

            // Add mouseout event to close the tooltip
            marker.on('mouseout', function(e) {
                marker.closeTooltip();
            });
        });
    </script>

</body>

</html>