<?php

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow', true);

function checkApiHealth($url) {
    $url = filter_var($url, FILTER_SANITIZE_URL);
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return array("status" => "error", "message" => "Invalid or missing API endpoint URL");
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return array("status" => "error", "message" => "Error occurred while checking API health: " . htmlspecialchars($error));
    }

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpStatus == 200) {
        return array("status" => "success", "message" => "API is healthy");
    } else {
        return array("status" => "error", "message" => "API is not healthy");
    }
}

if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    echo json_encode(array("status" => "error", "message" => "Invalid or missing API endpoint URL"));
    exit;
}

$apiEndpoint = $_GET['url'];
$response = checkApiHealth($apiEndpoint);
echo json_encode($response);

?>