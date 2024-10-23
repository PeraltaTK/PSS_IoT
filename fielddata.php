<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php"); // Change this to your login page
    exit();
}

// Function to read data from a JSON file
function readDataFromJson($filename) {
    $jsonContent = file_get_contents($filename);
    return json_decode($jsonContent, true); // Decode JSON data into an associative array
}

// Read data from JSON files
$humidityData = readDataFromJson('data/humidity/data.json');
$soil_moistureData = readDataFromJson('data/soil_moisture/data.json');
$temperatureData = readDataFromJson('data/temperature/data.json');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<?php include 'style/head.php'; ?>
</head>
<body>

<!-- header -->
<?php include 'style/header.php'; ?>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for the charts
    const temperatureLabels = <?php echo json_encode(array_column($temperatureData, 'date')); ?>;
    const temperatureValues = <?php echo json_encode(array_column($temperatureData, 'value')); ?>;

    const humidityLabels = <?php echo json_encode(array_column($humidityData, 'date')); ?>;
    const humidityValues = <?php echo json_encode(array_column($humidityData, 'value')); ?>;

    const soilMoistureLabels = <?php echo json_encode(array_column($soil_moistureData, 'date')); ?>;
    const soilMoistureValues = <?php echo json_encode(array_column($soil_moistureData, 'value')); ?>;

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
            }
        }
    });
</script>

</body>
</html>
