<?php

// Включаем отображение ошибок для отладки (в продакшене лучше отключить)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('OPENWEATHERMAP_API_KEY', getenv('OPENWEATHERMAP_API_KEY'));
define('IPIFY_API_KEY', getenv('IPIFY_API_KEY'));

// Функция для выполнения GET-запросов
function makeGetRequest($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($httpCode !== 200) {
        http_response_code($httpCode);
    }

    return $response;
}

// Получаем URI запроса
$requestUri = $_SERVER['REQUEST_URI'];

// Определяем маршруты
if (preg_match('/^\/openweathermap\/(.+)/', $requestUri, $matches)) {
    $query = $matches[1];
    $apiUrl = sprintf(
        "https://api.openweathermap.org/data/2.5/%s&appid=%s",
        $query,
        OPENWEATHERMAP_API_KEY
    );

    $response = makeGetRequest($apiUrl);
    header('Content-Type: application/json');
    echo $response;
    
} elseif (preg_match('/^\/ipify\/(.+)/', $requestUri, $matches)) {
    $query = $matches[1];

    $apiUrl = sprintf(
        "https://geo.ipify.org/api/v2/%s&apiKey=%s",
        $query,
        IPIFY_API_KEY
    );

    $response = makeGetRequest($apiUrl);
    header('Content-Type: application/json');
    echo $response;
    
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
