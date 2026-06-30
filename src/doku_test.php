<?php

$clientId = 'BRN-0281-1782323890259';
$secretKey = 'SK-81CgrqZXYUhCk5MuRBKh';

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

$target = '/checkout/v1/payment';
$requestId = generateUUID();
$timestamp = gmdate('Y-m-d\TH:i:s\Z');

$payload = [
    'order' => [
        'amount' => 10000,
        'invoice_number' => 'DOKU-TEST-' . time(),
        'currency' => 'IDR',
        'callback_url' => 'http://localhost:8000',
        'callback_url_result' => 'http://localhost:8000',
        'auto_redirect' => true,
        'line_items' => [
            [
                'name' => 'Test Product',
                'price' => 10000,
                'quantity' => 1,
            ]
        ]
    ],
    'payment' => [
        'payment_due_date' => 60
    ]
];

$body = json_encode($payload, JSON_UNESCAPED_SLASHES);

$digest = base64_encode(hash('sha256', $body, true));

$component = "Client-Id:{$clientId}\n"
    ."Request-Id:{$requestId}\n"
    ."Request-Timestamp:{$timestamp}\n"
    ."Request-Target:{$target}\n"
    ."Digest:{$digest}";

$signature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $component, $secretKey, true));

$headers = [
    "Client-Id: {$clientId}",
    "Request-Id: {$requestId}",
    "Request-Timestamp: {$timestamp}",
    "Digest: SHA-256={$digest}",
    "Signature: {$signature}",
    "Content-Type: application/json"
];

$ch = curl_init('https://api-sandbox.doku.com' . $target);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Timestamp: $timestamp\n";
echo "Digest: $digest\n";
echo "Signature Component:\n$component\n";
echo "Signature: $signature\n";
echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
