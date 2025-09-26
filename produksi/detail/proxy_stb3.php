<?php
// Ambil data dari frontend
$chip_id     = $_POST['chip_id']     ?? '';
$placement   = $_POST['placement']   ?? '';
$custom_name = $_POST['custom_name'] ?? '';
$lasttime    = $_POST['lasttime']    ?? '';
$api_key     = $_POST['api_key']     ?? '';

// Endpoint third-party
$url = "http://owlfork.co.id/iot.php/api/module/stb/get_data/load";

// Data body yang diteruskan
$postData = [
    'chip_id'     => $chip_id,
    'placement'   => $placement,
    'custom_name' => $custom_name,
    'lasttime'    => $lasttime
];

// Kirim ke API
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Tambahkan header api_key
$headers = [];
if (!empty($api_key)) {
    $headers[] = "api_key: $api_key";
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Balikin hasil ke frontend
http_response_code($httpCode);
header("Content-Type: application/json");
echo $response;
