<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php"); // Change this to your login page
    exit();
}

// Function to read data from JSON files
function readDataFromJson($filePath) {
    $jsonContent = file_get_contents($filePath);
    return json_decode($jsonContent, true); // Decode JSON data into an associative array
}

// Fetch data from JSON files
$humidityData = readDataFromJson('data/humidity/data.json');
$soilMoistureData = readDataFromJson('data/soil_moisture/data.json');
$temperatureData = readDataFromJson('data/temperature/data.json');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<?php include 'style/head.php'; ?>
<style>
    #map {
        height: 600px;
        width: 100%;
    }
</style>

</head>

<body>

<!-- header -->
<?php include 'style/header.php'; ?>

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

        // PHP arrays converted to JavaScript
        var humidityData = <?php echo json_encode($humidityData); ?>;
        var soilMoistureData = <?php echo json_encode($soilMoistureData); ?>;
        var temperatureData = <?php echo json_encode($temperatureData); ?>;

        // Combine the data based on field names or IDs
        var crops = humidityData.map((crop, index) => {
            return {
                name: crop.name,
                lat: crop.lat,
                lng: crop.lng,
                humidity: crop.humidity,
                soilMoisture: soilMoistureData[index] ? soilMoistureData[index].soilMoisture : 'N/A',
                temperature: temperatureData[index] ? temperatureData[index].temperature : 'N/A',
                status: crop.humidity > 70 ? "Irrigated" : "Not Irrigated" // Example status logic
            };
        });

        // Function to create a chart
        function createChart(canvasId, data, label, color) {
            var ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
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
            marker.bindPopup("<b>" + crop.name + "</b><br>Status: " + crop.status + "<br>Humidity: " + crop.humidity + "%<br>Soil Moisture: " + crop.soilMoisture + "%<br>Temperature: " + crop.temperature + "°C");

            // Add mouseover event to show additional info with charts
            marker.on('mouseover', function(e) {
                var tooltipContent = "<b>" + crop.name + "</b><br>Status: " + crop.status + "<br>Humidity: " + crop.humidity + "%<br>Soil Moisture: " + crop.soilMoisture + "%<br>Temperature: " + crop.temperature + "°C";
                tooltipContent += '<div class="chart-container"><canvas id="chart-' + crop.name.replace(/\s+/g, '-') + '"></canvas></div>';
                marker.bindTooltip(tooltipContent).openTooltip();

                // Example data for the chart (use real data if available)
                var randomData = Array.from({ length: 12 }, () => Math.floor(Math.random() * 100));
                createChart('chart-' + crop.name.replace(/\s+/g, '-'), randomData, 'Humidity (%)', 'rgba(75, 192, 192, 1)');
            });

            // Add mouseout event to close the tooltip
            marker.on('mouseout', function(e) {
                marker.closeTooltip();
            });
        });
    </script>

</body>

</html>
