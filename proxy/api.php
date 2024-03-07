<?php

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow', true);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(array("error" => "Only GET requests are allowed."));
    exit();
}

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode(array("error" => "API URL not provided."));
    exit();
}

$api_url = filter_var($_GET['url'], FILTER_VALIDATE_URL);
if ($api_url === false) {
    http_response_code(400);
    echo json_encode(array("error" => "Invalid API URL format."));
    exit();
}

$allowed_domains = array(
    'sanweb.info',
    'santhoshveer.com',
    'mskian.com'
);

$parsed_url = parse_url($api_url);
if (!isset($parsed_url['host']) || !in_array($parsed_url['host'], $allowed_domains)) {
    http_response_code(403);
    echo json_encode(array("error" => "Access to the provided URL is not allowed."));
    exit();
}

if (!in_array($parsed_url['scheme'], array('http', 'https'))) {
    http_response_code(400);
    echo json_encode(array("error" => "Only HTTP and HTTPS protocols are allowed."));
    exit();
}

if (strpos($api_url, '..') !== false) {
    http_response_code(400);
    echo json_encode(array("error" => "Directory traversal detected."));
    exit();
}

if (strlen($api_url) > 1000) {
    http_response_code(400);
    echo json_encode(array("error" => "URL length exceeds the maximum allowed limit."));
    exit();
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_status !== 200) {
    http_response_code($http_status);
    echo json_encode(array("error" => "Failed to fetch data from the API."));
    exit();
}

echo $response;

?>