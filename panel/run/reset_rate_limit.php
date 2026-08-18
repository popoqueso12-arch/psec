<?php
// 🔓 Resetear rate limit para testing
require_once __DIR__ . '/rate-limit.php';

$client_ip = getClientIP();
resetRateLimit($client_ip);

echo json_encode([
    'status' => 'OK',
    'message' => "Rate limit reseteado para IP: $client_ip",
    'ip' => $client_ip
]);
?>
