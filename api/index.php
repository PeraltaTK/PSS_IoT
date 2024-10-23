<?php
// Define os cabeçalhos da API
header("Content-Type: application/json");

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Função para enviar respostas da API
function sendResponse($status, $data) {
    http_response_code($status);
    echo json_encode($data);
}

// Função para lidar com requisições GET
function handleGetRequest() {
    // Verifica se o parâmetro 'nome' foi passado na URL
    $nome = isset($_GET['nome']) ? $_GET['nome'] : null;
    
    // Verifica se o nome foi fornecido
    if (!$nome) {
        sendResponse(400, ["message" => "Nome do sensor/atuador não foi fornecido"]);
        return;
    }

    // Simula leitura de dados a partir do arquivo JSON correspondente
    $filePath = "files/{$nome}.json";  // Presumindo que os arquivos JSON estão em uma pasta chamada 'data'
    
    if (!file_exists($filePath)) {
        sendResponse(404, ["message" => "Sensor/Atuador não encontrado"]);
        return;
    }

    // Lê o conteúdo do arquivo JSON
    $data = file_get_contents($filePath);
    $sensorData = json_decode($data, true);
    
    // Retorna os dados
    sendResponse(200, $sensorData);
}

// Função para lidar com requisições POST
function handlePostRequest() {
    // Decodifica o corpo da requisição JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verifica se os parâmetros esperados foram fornecidos
    if (!isset($input['nome'])) {
        sendResponse(400, ["message" => "Nome do sensor/atuador não foi fornecido"]);
        return;
    }
    
    if (!isset($input['valor']) || !isset($input['hora'])) {
        sendResponse(400, ["message" => "Falta 1 ou mais parâmetros (nome, valor, hora)"]);
        return;
    }

    $nome = $input['nome'];
    $valor = $input['valor'];
    $hora = $input['hora'];
    
    // Simula a gravação de dados em um arquivo JSON correspondente
    $filePath = "data/{$nome}.json";  // Presumindo que os arquivos são armazenados na pasta 'data'
    
    // Dados a serem gravados no arquivo JSON
    $sensorData = [
        "nome" => $nome,
        "valor" => $valor,
        "hora" => $hora
    ];
    
    // Grava os dados no arquivo
    file_put_contents($filePath, json_encode($sensorData));
    
    // Retorna uma resposta de sucesso
    sendResponse(201, ["message" => "Dados gravados com sucesso"]);
}

// Lida com os diferentes métodos HTTP
switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    default:
        // Retorna erro 405 para métodos não permitidos
        sendResponse(405, ["message" => "Método não permitido"]);
        break;
}
?>

// ver descrição da api
