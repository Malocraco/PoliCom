<?php

$baseUrl = 'https://555g5z.api.infobip.com';
$apiKey = 'a7325d4da2383a6e50010ebb1e842282-0b7cca34-8558-4091-8290-ec31dc592837';

echo "Consultando dominios en Infobip via CURL...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/email/1/domains');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: App ' . $apiKey,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $status\n";
echo "Body: $response\n";
