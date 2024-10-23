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

// Read the data
$data = readDataFromJson('data.json');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechnoGreen</title>
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
        .clock-container {
            text-align: center;
            margin-top: 5%;
        }
        .data-section {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
            gap: 20px; /* Space between boxes */
        }
        .data-card {
            background-color: #fff; /* White background for cards */
            border: 2px solid #28a745; /* Green border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            text-align: center;
            padding: 20px; /* Padding inside the card */
            width: 200px; /* Fixed width for uniformity */
        }
        .data-title {
            font-size: 24px;
            color: #28a745; /* Green color */
            margin: 10px 0;
        }
        .data-value {
            font-size: 32px;
            font-weight: bold;
            color: #333; /* Darker text color */
        }
        .data-time {
            font-size: 12px;
            color: #333; /* Darker text color */
        }
        .button-container {
            margin-top: 30px;
            text-align: center;
        }
        .btn-irrigate {
            background-color: #28a745;
            color: white;
        }
        .about-us {
            font-size: 32px;
            color: #28a745;
            text-align: center;
            margin-top: 50px;
            opacity: 0;
            animation: fadeIn 3s forwards;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>


<!-- header -->
<?php include 'header.php'; ?>


    <!-- Últimos Dados -->
    <div class="data-section">
        <div class="data-card">
            <div class="data-title">Temperatura</div>
            <div class="data-value"><?php echo $data['temperature']['value']; ?></div>
            <div class="data-time">Última atualização:<br><?php echo $data['temperature']['last_update']; ?></div>
        </div>
        
        <div class="data-card">
            <div class="data-title">Umidade</div>
            <div class="data-value"><?php echo $data['humidity']['value']; ?></div>
            <div class="data-time">Última atualização:<br><?php echo $data['humidity']['last_update']; ?></div>
        </div>
        
        <div class="data-card">
            <div class="data-title">Umidade do Solo</div>
            <div class="data-value"><?php echo $data['soil_humidity']['value']; ?></div>
            <div class="data-time">Última atualização:<br><?php echo $data['soil_humidity']['last_update']; ?></div>
        </div>

        <div class="data-card">
            <div class="data-title">Capacidade do Poço</div>
            <div class="data-value"><?php echo $data['well_capacity']['value']; ?></div>
            <div class="data-time">Última atualização:<br><?php echo $data['well_capacity']['last_update']; ?></div>
        </div>
    </div>

<div class="button-container">
    <button class="btn btn-irrigate" id="startIrrigationBtn">Iniciar Irrigação</button>
</div>

<div class="progress-container" id="progressContainer" style="display: none; text-align: center; margin-top: 20px;">
    <div class="progress" style="height: 30px; width: 80%; margin: 0 auto; background-color: #e9ecef; border-radius: 15px; overflow: hidden;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #28a745; height: 100%;"></div>
    </div>
    <div id="progressText" style="margin-top: 10px; font-size: 18px; color: #28a745;">0%</div>
</div>

<script>
    document.getElementById('startIrrigationBtn').addEventListener('click', function() {
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = progressContainer.querySelector('.progress-bar');
        const progressText = document.getElementById('progressText');
        
        progressContainer.style.display = 'block';
        let progress = 0;
        
        const interval = setInterval(() => {
            if (progress >= 100) {
                clearInterval(interval);
                progressText.innerHTML = 'Irrigação Completa!';
            } else {
                progress += Math.floor(Math.random() * 10) + 1; // Increment progress randomly between 1 and 10
                if (progress > 100) progress = 100;
                progressBar.style.width = progress + '%';
                progressText.innerHTML = progress + '%';
            }
        }, 500); // Update every 500ms
    });
</script>
    <!-- Sobre Nós com animação -->
    <div class="about-us" id="aboutUs">
        Bem-vindo ao Sistema de Gestão Agrícola!
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
<!-- Sensor Graphs Section -->
<div class="container mt-5">
    <h2 class="text-center">Gráficos de Sensores</h2>
    <div class="row">
        <div class="col-md-6">
            <canvas id="temperatureChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="humidityChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="soilHumidityChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="wellCapacityChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fetch data from API
    fetch('api.php')
        .then(response => response.json())
        .then(data => {
            // Temperature Chart
            const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
            new Chart(temperatureCtx, {
                type: 'line',
                data: {
                    labels: data.timestamps,
                    datasets: [{
                        label: 'Temperatura',
                        data: data.values,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'minute'
                            }
                        }
                    }
                }
            });

            // Humidity Chart
            const humidityCtx = document.getElementById('humidityChart').getContext('2d');
            new Chart(humidityCtx, {
                type: 'line',
                data: {
                    labels: data.humidity.timestamps,
                    datasets: [{
                        label: 'Umidade',
                        data: data.humidity.values,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'minute'
                            }
                        }
                    }
                }
            });

            // Soil Humidity Chart
            const soilHumidityCtx = document.getElementById('soilHumidityChart').getContext('2d');
            new Chart(soilHumidityCtx, {
                type: 'line',
                data: {
                    labels: data.soil_humidity.timestamps,
                    datasets: [{
                        label: 'Umidade do Solo',
                        data: data.soil_humidity.values,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'minute'
                            }
                        }
                    }
                }
            });

            // Well Capacity Chart
            const wellCapacityCtx = document.getElementById('wellCapacityChart').getContext('2d');
            new Chart(wellCapacityCtx, {
                type: 'line',
                data: {
                    labels: data.well_capacity.timestamps,
                    datasets: [{
                        label: 'Capacidade do Poço',
                        data: data.well_capacity.values,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'minute'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
</script>