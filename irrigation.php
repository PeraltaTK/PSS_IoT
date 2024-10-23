<?php
session_start(); // Iniciar a sessão

// Verificar se o utilizador tem sessão iniciada
if (!isset($_SESSION['username'])) {
    // Redirecionar para a página de início de sessão se não tiver sessão iniciada
    header("Location: index.php"); 
    exit();
}

// Função para ir buscar dados à API
function fetchDataFromApi($url) {
    $jsonContent = file_get_contents($url);
    return json_decode($jsonContent, true); //Descodificar dados JSON para uma matriz associativa
}

// Obter os dados do campo a partir da API
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
            background-color: #f8f9fa; /* Fundo claro */
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
            width: 100%; /* Assegura que os gráficos ocupam toda a largura da coluna */
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
<?php include 'style/header.php'; ?>

    <div id="map"></div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([37.0902, -95.7129], 5); // Centrado nos Estados Unidos

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Ícones personalizados
        var greenIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/5447/5447063.png', // Substituir pelo URL do seu ícone de corte verde
            iconSize: [48, 48], // tamanho do ícone
            iconAnchor: [24, 48], // ponto do ícone que corresponderá à localização do marcador
            popupAnchor: [0, -48] // ponto a partir do qual o popup deve abrir relativamente ao iconAnchor
        });

        var redIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/5447/5447063.png', // Substituir pelo URL do ícone 
            iconSize: [48, 48], // tamanho do ícone
            iconAnchor: [24, 48], // ponto do ícone que corresponderá à localização do marcador
            popupAnchor: [0, -48] // ponto a partir do qual o popup deve abrir relativamente ao iconAnchor
        });

        // Dados de exemplo para o estado de irrigação das culturas nos Estados Unidos
        var crops = [
            { "name": "Field 1", "lat": 36.7783, "lng": -119.4179, "status": "Irrigated", "lastWatered": "2023-10-01", "soilMoisture": "75%" }, // California
            { "name": "Field 2", "lat": 41.8781, "lng": -87.6298, "status": "Needs Water", "lastWatered": "2023-09-25", "soilMoisture": "30%" }, // Illinois
            { "name": "Field 3", "lat": 40.7128, "lng": -74.0060, "status": "Irrigated", "lastWatered": "2023-10-02", "soilMoisture": "80%" }, // New York
            { "name": "Field 4", "lat": 29.7604, "lng": -95.3698, "status": "Needs Water", "lastWatered": "2023-09-28", "soilMoisture": "40%" }, // Texas
            { "name": "Field 5", "lat": 39.7392, "lng": -104.9903, "status": "Irrigated", "lastWatered": "2023-10-03", "soilMoisture": "85%" }  // Colorado
        ];

        // Função para criar um gráfico
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

        // Adicionar marcadores ao mapa
        crops.forEach(function(crop) {
            var icon = crop.status === "Irrigated" ? greenIcon : redIcon;
            var marker = L.marker([crop.lat, crop.lng], { icon: icon, title: crop.status }).addTo(map);
            marker.bindPopup("<b>" + crop.name + "</b><br>Status: " + crop.status);

            // Adicionar evento de passar o rato para mostrar informações adicionais
            marker.on('mouseover', function(e) {
                var tooltipContent = "<b>" + crop.name + "</b><br>Status: " + crop.status + "<br>Last Watered: " + crop.lastWatered + "<br>Soil Moisture: " + crop.soilMoisture;
                tooltipContent += '<div class="chart-container"><canvas id="chart-' + crop.name.replace(/\s+/g, '-') + '"></canvas></div>';
                marker.bindTooltip(tooltipContent).openTooltip();

                // Gerar dados aleatórios para o gráfico (substituir por dados reais conforme necessário)
                var randomData = Array.from({ length: 12 }, () => Math.floor(Math.random() * 100));
                createChart('chart-' + crop.name.replace(/\s+/g, '-'), randomData);
            });

           // Adicionar o evento mouseout para fechar a dica de ferramenta
            marker.on('mouseout', function(e) {
                marker.closeTooltip();
            });
        });
    </script>

    
</body>

</html>