<?php
// 🔓 Resetear rate limit para testing
$allowed_origins = [
  'http://127.0.0.1:5500',
  'http://localhost:5173',
  'https://assasin-dusky.vercel.app',
  'https://essa-blush.vercel.app'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
  header('Access-Control-Allow-Origin: ' . $origin);
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  header('Access-Control-Allow-Credentials: true');
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

require_once __DIR__ . '/rate-limit.php';

$client_ip = getClientIP();
resetRateLimit($client_ip);

echo json_encode([
    'status' => 'OK',
    'message' => "Rate limit reseteado para IP: $client_ip",
    'ip' => $client_ip
]);
?>

