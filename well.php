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

// Read the well capacity data
$wellData = readDataFromJson('well_data.json');

// Check if well capacity data is available
if (!isset($wellData['well_capacity'])) {
    die("Error: 'well_capacity' data not found in JSON file.");
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capacidade do Poço - TechnoGreen</title>
    <link rel="icon" href="./img/1.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background-color: #f8f9fa;
        }
        .chart-container {
            width: 80%;
            margin: 0 auto;
            margin-top: 30px;
        }
        .title {
            text-align: center;
            margin-top: 20px;
            color: #28a745;
            font-size: 32px;
        }
    </style>
</head>
<body>

<!-- header -->
<?php include 'header.php'; ?>
    <div class="title">Histórico de Capacidade do Poço</div>
    <div class="chart-container">
        <canvas id="wellCapacityChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Prepare data for the chart
        const labels = <?php echo json_encode(array_column($wellData['well_capacity'], 'date')); ?>;
        const dataValues = <?php echo json_encode(array_column($wellData['well_capacity'], 'value')); ?>;

        // Chart configuration
        const ctx = document.getElementById('wellCapacityChart').getContext('2d');
        const wellCapacityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Capacidade do Poço (L)',
                    data: dataValues,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
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
                            text: 'Capacidade (Litros)',
                            color: '#28a745'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Data',
                            color: '#28a745'
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
