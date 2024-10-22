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
    </style>
</head>
<body>
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
        const temperatureLabels = <?php echo json_encode(array_column($fieldData['field_data']['temperature'], 'date')); ?>;
        const temperatureValues = <?php echo json_encode(array_column($fieldData['field_data']['temperature'], 'value')); ?>;

        const humidityLabels = <?php echo json_encode(array_column($fieldData['field_data']['humidity'], 'date')); ?>;
        const humidityValues = <?php echo json_encode(array_column($fieldData['field_data']['humidity'], 'value')); ?>;

        const soilMoistureLabels = <?php echo json_encode(array_column($fieldData['field_data']['soil_moisture'], 'date')); ?>;
        const soilMoistureValues = <?php echo json_encode(array_column($fieldData['field_data']['soil_moisture'], 'value')); ?>;

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
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    // Create the scene
    var scene = new THREE.Scene();
    var camera = new THREE.OrthographicCamera(window.innerWidth / -2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / -2, 1, 1000);
    var renderer = new THREE.WebGLRenderer({ alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    // Create a plane for the map
    var geometry = new THREE.PlaneGeometry(800, 600);
    var texture = new THREE.TextureLoader().load('https://upload.wikimedia.org/wikipedia/commons/6/6a/United_States_Map_-_The_50_States.png');
    var material = new THREE.MeshBasicMaterial({ map: texture });
    var plane = new THREE.Mesh(geometry, material);
    scene.add(plane);

    // Position the camera
    camera.position.z = 500;

    // Create markers for the crops
    var cropMarkers = [];
    crops.forEach(function(crop) {
        var markerGeometry = new THREE.CircleGeometry(10, 32);
        var markerMaterial = new THREE.MeshBasicMaterial({ color: crop.status === "Irrigated" ? 0x00ff00 : 0xff0000 });
        var marker = new THREE.Mesh(markerGeometry, markerMaterial);
        marker.position.set((crop.lng + 100) * 4, (crop.lat - 40) * 4, 0); // Adjust positions based on map coordinates
        scene.add(marker);
        cropMarkers.push(marker);
    });

    // Render the scene
    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();
</script>