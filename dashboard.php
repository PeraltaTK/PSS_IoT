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
    $dataArray = json_decode($jsonContent, true); // Decode JSON data into an associative array
    return end($dataArray); // Return the most recent data (last entry in the array)
}

// Read the most recent data
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

    <!-- Últimos Dados -->
    <div class="data-section">
        <div class="data-card">
            <div class="data-title">Temperatura</div>
            <div class="data-value"><?php echo $temperatureData['value']; ?>ºC</div>
            <div class="data-time">Última atualização:<br><?php echo $temperatureData['date']; ?></div>
        </div>
        
        <div class="data-card">
            <div class="data-title">Umidade</div>
            <div class="data-value"><?php echo $humidityData['value']; ?>%</div>
            <div class="data-time">Última atualização:<br><?php echo $humidityData['date']; ?></div>
        </div>
        
        <div class="data-card">
            <div class="data-title">Umidade do Solo</div>
            <div class="data-value"><?php echo $soil_moistureData['value']; ?>%</div>
            <div class="data-time">Última atualização:<br><?php echo $soil_moistureData['date']; ?></div>
        </div>

        <!-- If you have other data (e.g., well capacity), you can add more cards here -->
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
