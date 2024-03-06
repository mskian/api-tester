<?php

// Set appropriate security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow', true);

require('random.php');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST requests are allowed']);
    exit;
}

// Get the request data from the POST payload
$requestData = json_decode(file_get_contents('php://input'), true);

// Extract parameters from the request data
$apiUrl = $requestData['url'] ?? '';
$method = strtoupper($requestData['method'] ?? 'GET');
$headers = $requestData['headers'] ?? [];
$body = $requestData['body'] ?? '';
$useProxy = $requestData['use_proxy'] ?? false; // Default to false if not provided

// Validate API URL
if (!filter_var($apiUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid API URL']);
    exit;
}

// Validate HTTP method
$validMethods = ['GET', 'POST', 'PUT', 'DELETE'];
if (!in_array($method, $validMethods)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid HTTP method']);
    exit;
}

// Validate request headers
if (!is_array($headers)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Request headers must be provided as an array']);
    exit;
}

foreach ($headers as $key => $value) {
    if (!is_string($key) || !is_string($value)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid format for request headers']);
        exit;
    }
}

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');

// Check if it's a preflight request and handle it
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // OK
    exit;
}

// Use proxy
if ($useProxy) {
    // Set up cURL to make the request to the API endpoint
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_USERAGENT, $randoagent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // Set request headers
    if (!empty($headers)) {
        $headerArray = [];
        foreach ($headers as $key => $value) {
            $headerArray[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    }

    // Set request body
    if (!empty($body)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    // Execute the request and get the response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to make request via proxy: ' . $error]);
        exit;
    }

    // Close cURL
    curl_close($ch);

    // Set the appropriate HTTP status code
    http_response_code($httpCode);

    // Output the API response
    echo $response;
} else {
    // Use direct request without proxy
    // Make the direct request using fetch
    $options = [
        'http' => [
            'method' => $method,
            'header' => (!empty($headers) ? implode("\r\n", $headers) . "\r\n" : ''),
            'content' => $body
        ]
    ];

    // Create a stream context
    $context = stream_context_create($options);

    // Make the request and get the response
    $response = file_get_contents($apiUrl, false, $context);

    // Check for errors
    if ($response === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to make direct request to API endpoint']);
        exit;
    }

    // Output the API response
    echo $response;
}

?>