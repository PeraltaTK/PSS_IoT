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

// Read the field data
$fieldData = readDataFromJson('field_data.json');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados do Campo - TechnoGreen</title>
    <link rel="icon" href="./img/1.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">

    <style>
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

<!-- header -->
<?php include 'header.php'; ?>

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
