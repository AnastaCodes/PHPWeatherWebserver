<?php

define('OPENWEATHERMAP_API_KEY', getenv('OPENWEATHERMAP_API_KEY'));
define('IPIFY_API_KEY', getenv('IPIFY_API_KEY'));

// Function for performing GET requests
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

// Function for setting CORS headers
function setCorsHeaders() {
    // Get Origin header from request
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

    // Array of allowed domains
    $allowed_origins = [
        'https://advanced-weather-fetcher.vercel.app',
        'https://anastacodes.github.io'
    ];

    // Check if Origin is allowed
    if (in_array($origin, $allowed_origins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    } else {
        header('Access-Control-Allow-Origin: '); // Не разрешаем запрос
        exit;
    }

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

// Set CORS headers
setCorsHeaders();

// Get the request URI
$requestArray = [];
parse_str($_SERVER['QUERY_STRING'], $requestArray);

$route = $requestArray['route'] ?? null;
$url = urldecode($requestArray['url'] ?? '');

// Define routes
if ($route === 'openweathermap') {
    $apiUrl = sprintf(
        "https://api.openweathermap.org/data/2.5/%s&appid=%s",
        $url,
        OPENWEATHERMAP_API_KEY
    );

    $response = makeGetRequest($apiUrl);
    header('Content-Type: application/json');
    echo $response;
    
} elseif ($route === 'ipify') {
    $apiUrl = sprintf(
        "https://geo.ipify.org/api/v2/%s&apiKey=%s",
        $url,
        IPIFY_API_KEY
    );

    $response = makeGetRequest($apiUrl);
    header('Content-Type: application/json');
    echo $response;
    
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
