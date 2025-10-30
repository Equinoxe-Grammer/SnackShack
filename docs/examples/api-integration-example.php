<?php
// Ejemplo rápido de integración con la API JSON de SnackShop
// Requiere: ext-curl o file_get_contents con allow_url_fopen

$base = 'http://localhost:8000/api/v1';
$endpoint = $base . '/products';

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http >= 200 && $http < 300) {
    $data = json_decode($response, true);
    echo "Productos recibidos: " . count($data) . "\n";
} else {
    echo "Error HTTP: $http\n";
    echo $response . "\n";
}

// Para endpoints que requieran autenticación, añade la cookie o header Authorization:
// curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=...');
// o
// curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer TOKEN']);
?>
