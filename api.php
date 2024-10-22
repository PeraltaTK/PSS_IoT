<?php
header('Content-Type: application/json');

// Function to read data from a JSON file
function readDataFromJson($filename) {
    if (!file_exists($filename)) {
        return null;
    }
    $jsonContent = file_get_contents($filename);
    return json_decode($jsonContent, true);
}

// Function to write data to a JSON file
function writeDataToJson($filename, $data) {
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filename, $jsonContent);
}

// Determine which data to serve based on the 'file' query parameter
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : '';

$response = [];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($file) {
            case 'fielddataa':
                // Read data from field_data.json
                $fieldData = readDataFromJson(__DIR__ . '/field_data.json');
                if ($fieldData === null) {
                    $response = ['error' => 'field_data.json not found or invalid JSON'];
                } else {
                    $response = ['field_data' => $fieldData];
                }
                break;
            case 'index':
            case 'dashboard':
            case 'well':
                // Read data from data.json
                $data = readDataFromJson(__DIR__ . '/data.json');
                if ($data === null) {
                    $response = ['error' => 'data.json not found or invalid JSON'];
                } else {
                    $response = ['data' => $data];
                }
                break;
            default:
                $response = ['error' => 'Invalid file parameter'];
                break;
        }
        break;
    case 'POST':
        // Handle POST request to update data
        $inputData = json_decode(file_get_contents('php://input'), true);
        if ($inputData === null) {
            $response = ['error' => 'Invalid JSON input'];
        } else {
            switch ($file) {
                case 'fielddataa':
                    writeDataToJson(__DIR__ . '/field_data.json', $inputData);
                    $response = ['success' => 'field_data.json updated'];
                    break;
                case 'index':
                case 'dashboard':
                case 'well':
                    writeDataToJson(__DIR__ . '/data.json', $inputData);
                    $response = ['success' => 'data.json updated'];
                    break;
                default:
                    $response = ['error' => 'Invalid file parameter'];
                    break;
            }
        }
        break;
    default:
        $response = ['error' => 'Unsupported request method'];
        break;
}

// Output the response as JSON
echo json_encode($response);
// Function to generate random data
function generateRandomData() {
    return [
        'temperature' => rand(-10, 40),
        'humidity' => rand(0, 100),
        'pressure' => rand(950, 1050)
    ];
}

// Function to serve backup data
function serveBackupData($file) {
    $backupData = generateRandomData();
    switch ($file) {
        case 'fielddataa':
            return ['field_data' => $backupData];
        case 'index':
        case 'dashboard':
        case 'well':
            return ['data' => $backupData];
        default:
            return ['error' => 'Invalid file parameter'];
    }
}

// Modify the GET request handling to use backup data if main data fails
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($file) {
            case 'fielddataa':
                $fieldData = readDataFromJson(__DIR__ . '/field_data.json');
                if ($fieldData === null) {
                    $response = serveBackupData($file);
                } else {
                    $response = ['field_data' => $fieldData];
                }
                break;
            case 'index':
            case 'dashboard':
            case 'well':
                $data = readDataFromJson(__DIR__ . '/data.json');
                if ($data === null) {
                    $response = serveBackupData($file);
                } else {
                    $response = ['data' => $data];
                }
                break;
            default:
                $response = ['error' => 'Invalid file parameter'];
                break;
        }
        break;
    // The rest of the code remains unchanged
}