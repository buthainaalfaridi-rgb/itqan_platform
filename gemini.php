<?php
header('Content-Type: application/json');

// ضعي مفتاح Gemini هنا
$API_KEY = "AIzaSyCdDe3gjOrbsZ5gP--Aw4HwtOjGcbumogU";

$input = json_decode(file_get_contents("php://input"), true);
$userMessage = $input['message'] ?? '';

if (trim($userMessage) === '') {
    echo json_encode([
        "error" => "Empty message"
    ]);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=$API_KEY";

$data = [
    "contents" => [[
        "role" => "user",
        "parts" => [[
            "text" => $userMessage
        ]]
    ]]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($data),
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "error" => "Connection failed"
    ]);
    exit;
}

curl_close($ch);

echo $response;
