<?php

// Define the filename for the JSON data
$humidityFile = '/data/humidity/data.json';
$soilMoistureFile = '/data/soil_moisture/data.json';
$temperatureFile = '/data/temperature/data.json';

// Function to read data from a JSON file
function readDataFromJson($filename) {
    if (file_exists($filename)) {
        $jsonContent = file_get_contents($filename);
        return json_decode($jsonContent, true); // Decode JSON data into an associative array
    }
    return ['aaa'];
}

// Function to write data to a JSON file
function writeDataToJson($filename, $data) {
    $existingData = readDataFromJson($filename);
    $existingData[] = $data; // Append new data
    file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));
}

// Handle API requests
header('Content-Type: application/json'); // Set response type to JSON
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
            switch ($type) {
                case 'humidity':
                    $data = readDataFromJson($humidityFile);
                    break;
                case 'soil_moisture':
                    $data = readDataFromJson($soilMoistureFile);
                    break;
                case 'temperature':
                    $data = readDataFromJson($temperatureFile);
                    break;
                case 'latest':
                    // Return latest data for all types
                    $latestData = [
                        'humidity' => end(readDataFromJson($humidityFile)),
                        'soil_moisture' => end(readDataFromJson($soilMoistureFile)),
                        'temperature' => end(readDataFromJson($temperatureFile))
                    ];
                    echo json_encode($latestData);
                    exit();
                default:
                    http_response_code(400); // Bad Request
                    echo json_encode(['message' => 'Invalid type specified']);
                    exit();
            }
            echo json_encode($data);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Type parameter is required']);
        }
        break;

    case 'POST':
        $inputData = json_decode(file_get_contents('php://input'), true); // Get input data
        if (isset($inputData['type']) && isset($inputData['value']) && isset($inputData['date'])) {
            $newData = [
                'value' => $inputData['value'],
                'date' => $inputData['date']
            ];
            switch ($inputData['type']) {
                case 'humidity':
                    writeDataToJson($humidityFile, $newData);
                    break;
                case 'soil_moisture':
                    writeDataToJson($soilMoistureFile, $newData);
                    break;
                case 'temperature':
                    writeDataToJson($temperatureFile, $newData);
                    break;
                default:
                    http_response_code(400); // Bad Request
                    echo json_encode(['message' => 'Invalid type specified']);
                    exit();
            }
            echo json_encode(['message' => 'Data added successfully']);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Invalid input data']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>
