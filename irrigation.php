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
            { "name": "Field 1", "lat": 36.7783, "lng": -119.4179, "status": "Irrigated", "lastWatered": "2023-10-01", "soilMoisture": "75%" }, // California
            { "name": "Field 2", "lat": 41.8781, "lng": -87.6298, "status": "Needs Water", "lastWatered": "2023-09-25", "soilMoisture": "30%" }, // Illinois
            { "name": "Field 3", "lat": 40.7128, "lng": -74.0060, "status": "Irrigated", "lastWatered": "2023-10-02", "soilMoisture": "80%" }, // New York
            { "name": "Field 4", "lat": 29.7604, "lng": -95.3698, "status": "Needs Water", "lastWatered": "2023-09-28", "soilMoisture": "40%" }, // Texas
            { "name": "Field 5", "lat": 39.7392, "lng": -104.9903, "status": "Irrigated", "lastWatered": "2023-10-03", "soilMoisture": "85%" }  // Colorado
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

    <!-- Chart Section -->
    <div class="container">
        <div class="row">
            <div class="col-md-4 chart-container">
                <div class="table-title">Gráfico de Temperatura (°C)</div>
                <canvas id="temperatureChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                <div class="table-title">Gráfico de Umidade (%)</div>
                <canvas id="humidityChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                <div class="table-title">Gráfico de Umidade do Solo (%)</div>
                <canvas id="soilMoistureChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Prepare data for the charts
        const temperatureLabels = <?php echo isset($fieldData['temperature']) ? json_encode(array_column($fieldData['temperature'], 'date')) : json_encode([]); ?>;
        const temperatureValues = <?php echo isset($fieldData['temperature']) ? json_encode(array_column($fieldData['temperature'], 'value')) : json_encode([]); ?>;

        const humidityLabels = <?php echo isset($fieldData['humidity']) ? json_encode(array_column($fieldData['humidity'], 'date')) : json_encode([]); ?>;
        const humidityValues = <?php echo isset($fieldData['humidity']) ? json_encode(array_column($fieldData['humidity'], 'value')) : json_encode([]); ?>;

        const soilMoistureLabels = <?php echo isset($fieldData['soil_moisture']) ? json_encode(array_column($fieldData['soil_moisture'], 'date')) : json_encode([]); ?>;
        const soilMoistureValues = <?php echo isset($fieldData['soil_moisture']) ? json_encode(array_column($fieldData['soil_moisture'], 'value')) : json_encode([]); ?>;

        // Create Temperature Chart
        const ctxTemp = document.getElementById('temperatureChart').getContext('2d');
        const temperatureChart = new Chart(ctxTemp, {
            type: 'line',
            data: {
                labels: temperatureLabels,
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: temperatureValues,
                    borderColor: '#ff6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Temperatura (°C)',
                            color: '#ff6384'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Data',
                            color: '#ff6384'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' °C';
                            }
                        }
                    }
                }
            }
        });

        // Create Humidity Chart
        const ctxHum = document.getElementById('humidityChart').getContext('2d');
        const humidityChart = new Chart(ctxHum, {
            type: 'line',
            data: {
                labels: humidityLabels,
                datasets: [{
                    label: 'Umidade (%)',
                    data: humidityValues,
                    borderColor: '#36a2eb',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Umidade (%)',
                            color: '#36a2eb'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Data',
                            color: '#36a2eb'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' %';
                            }
                        }
                    }
                }
            }
        });

        // Create Soil Moisture Chart
        const ctxSoil = document.getElementById('soilMoistureChart').getContext('2d');
        const soilMoistureChart = new Chart(ctxSoil, {
            type: 'line',
            data: {
                labels: soilMoistureLabels,
                datasets: [{
                    label: 'Umidade do Solo (%)',
                    data: soilMoistureValues,
                    borderColor: '#4bc0c0',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Umidade do Solo (%)',
                            color: '#4bc0c0'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Data',
                            color: '#4bc0c0'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' %';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
<script>
    // Function to generate random data
    function generateRandomData(length, min, max) {
        return Array.from({ length: length }, () => Math.floor(Math.random() * (max - min + 1)) + min);
    }

    // Generate random data for the charts
    const randomTemperatureData = generateRandomData(12, 0, 40); // Random temperatures between 0 and 40°C
    const randomHumidityData = generateRandomData(12, 0, 100); // Random humidity between 0 and 100%
    const randomSoilMoistureData = generateRandomData(12, 0, 100); // Random soil moisture between 0 and 100%

    // Update Temperature Chart with random data
    temperatureChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    temperatureChart.data.datasets[0].data = randomTemperatureData;
    temperatureChart.update();

    // Update Humidity Chart with random data
    humidityChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    humidityChart.data.datasets[0].data = randomHumidityData;
    humidityChart.update();

    // Update Soil Moisture Chart with random data
    soilMoistureChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    soilMoistureChart.data.datasets[0].data = randomSoilMoistureData;
    soilMoistureChart.update();
</script>
</html>