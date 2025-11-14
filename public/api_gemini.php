<?php
header('Content-Type: application/json; charset=utf-8');

// ---- Tratamento de erros ----
error_reporting(E_ALL);
ini_set('display_errors', 0);

set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode(['error' => "Erro PHP: $errstr em $errfile:$errline"]);
    exit;
});

// ---- Chave da API ----
$apiKey = 'AIzaSyBVnWovGo_GWOZ00TkYbEgBynpiKToJrik'; // substitua pela sua

// ---- Verifica método ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método inválido']);
    exit;
}

// ---- Lê entrada JSON ----
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'JSON inválido ou vazio.']);
    exit;
}

$productName = trim($input['product_name'] ?? '');
if ($productName === '') {
    echo json_encode(['error' => 'Nome do produto não informado.']);
    exit;
}

// ---- Configuração da requisição Gemini ----
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

// 🔹 Prompt otimizado para descrição curta e objetiva
$prompt = "Crie uma breve descrição em português para um produto chamado \"$productName\". 
A resposta deve ter no máximo 20 palavras e descrever o produto de forma simples e natural, 
como em uma loja de alimentos. Não use emojis nem linguagem publicitária.";

// ---- Monta corpo da requisição ----
$data = [
    "contents" => [[
        "parts" => [[ "text" => $prompt ]]
    ]]
];

// ---- Executa requisição ----
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
]);
$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ---- Trata erros ----
if ($error) {
    echo json_encode(['error' => "Erro cURL: $error"]);
    exit;
}

if ($httpCode >= 400) {
    echo json_encode(['error' => "Erro HTTP $httpCode", 'response' => $response]);
    exit;
}

// ---- Extrai resultado ----
$json = json_decode($response, true);
$description = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Descrição não gerada.';

// ---- Retorna JSON final ----
echo json_encode(['description' => trim($description)], JSON_UNESCAPED_UNICODE);
