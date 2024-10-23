<?php

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Função para enviar a resposta com código de status HTTP
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

// Função para ler o arquivo JSON do sensor
function getSensorData($sensorType) {
    // Caminho do arquivo JSON baseado no tipo de sensor
    $filePath = __DIR__ . "/../data/{$sensorType}/data.json";

    // Verifica se o arquivo existe
    if (file_exists($filePath)) {
        // Lê e retorna o conteúdo do arquivo JSON
        $jsonData = file_get_contents($filePath);
        return json_decode($jsonData, true); // Retorna o array associativo
    } else {
        // Retorna null se o arquivo não for encontrado
        return null;
    }
}

// Função para salvar dados no arquivo JSON do sensor
function saveSensorData($sensorType, $data) {
    // Caminho do arquivo JSON baseado no tipo de sensor
    $filePath = __DIR__ . "/../data/{$sensorType}/data.json";

    // Converte os dados para JSON e salva no arquivo
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    if (file_put_contents($filePath, $jsonData) !== false) {
        return true;
    } else {
        return false;
    }
}

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Verifica se o parâmetro 'type' foi passado
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
        
        // Força a resposta para retornar o conteúdo do JSON de temperatura
        $sensorData = getSensorData($type);
        
        if ($sensorData !== null) {
            // Responde com os dados do sensor
            sendResponse($sensorData);
        } else {
            // Responde com erro se o arquivo não for encontrado
            sendResponse(['error' => 'Arquivo JSON do sensor não encontrado.'], 404);
        }
    } else {
        // Responde com erro se o parâmetro 'type' estiver ausente
        sendResponse(['error' => 'Parâmetro "type" ausente.'], 400);
    }
} elseif ($method === 'POST') {
    // Lê os dados enviados no corpo da requisição
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Verifica se o parâmetro 'type' foi passado
    if (isset($inputData['type']) && isset($inputData['date']) && isset($inputData['value'])) {
        $type = $inputData['type'];
        
        // Lê os dados existentes do sensor
        $sensorData = getSensorData($type);
        
        if ($sensorData !== null) {
            // Adiciona a nova entrada aos dados existentes
            $newEntry = [
                'date' => $inputData['date'],
                'value' => $inputData['value']
            ];
            $sensorData[] = $newEntry;
            
            // Salva os dados atualizados no arquivo JSON do sensor
            if (saveSensorData($type, $sensorData)) {
                // Responde com sucesso
                sendResponse(['message' => 'Dados salvos com sucesso.']);
            } else {
                // Responde com erro se não conseguir salvar os dados
                sendResponse(['error' => 'Erro ao salvar os dados.'], 500);
            }
        } else {
            // Responde com erro se o arquivo não for encontrado
            sendResponse(['error' => 'Arquivo JSON do sensor não encontrado.'], 404);
        }
    } else {
        // Responde com erro se os parâmetros estiverem ausentes
        sendResponse(['error' => 'Parâmetros "type", "date" ou "value" ausentes.'], 400);
    }
} else {
    // Responde com erro para métodos que não sejam GET ou POST
    sendResponse(['error' => 'Método não permitido.'], 405);
}
?>