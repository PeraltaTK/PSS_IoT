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

// Read data from field_data.json
$fieldData = readDataFromJson('field_data.json');

// Read data from data.json
$data = readDataFromJson('data.json');

// Combine data into a single response
$response = [
    'field_data' => $fieldData,
    'data' => $data
];

// Output the combined data as JSON
echo json_encode($response);
?>