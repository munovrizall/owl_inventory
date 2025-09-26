<?php
header("Content-Type: application/json");

$resultPrep = [];
$input_json = file_get_contents("php://input");

// Log raw input
writeLog("raw input: " . $input_json);
$request_data = json_decode($input_json, true);

// Validate we got valid JSON data
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Prepare data for forwarding (convert to x-www-form-urlencoded)
$postFields = http_build_query($request_data);

// Initialize cURL session for local API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/owl_inventory/produksi/api_inventaris.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-Forwarded-By: Inventaris-Proxy'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10-second timeout

// Execute and close cURL
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Handle cURL errors
if ($curlError) {
    writeLog("cURL Error: " . $curlError);
    http_response_code(500);
    echo json_encode(['error' => 'Internal forwarding error', 'details' => $curlError]);
    exit;
}

// Forward the API response
http_response_code($httpCode);
echo $response;

// Helper logging function (make sure this exists in your environment)
function writeLog($message) {
    // Implement your actual logging mechanism here
    // Example: file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}
