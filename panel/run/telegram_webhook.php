<?php
// Webhook del bot de Telegram
// Registrar con: https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://TU_DOMINIO/panel/run/telegram_webhook.php
// Comandos disponibles: /ban [ip]  /unban [ip]  /bans

require_once __DIR__ . '/../include/telegram.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) { http_response_code(200); exit; }

$message = $input['message'] ?? null;
if (!$message) { http_response_code(200); exit; }

$text    = trim($message['text'] ?? '');
$chat_id = (string)($message['chat']['id'] ?? '');

// Solo aceptar mensajes del chat configurado
global $TELEGRAM_CHAT_ID;
if ($TELEGRAM_CHAT_ID && $chat_id !== (string)$TELEGRAM_CHAT_ID) {
    http_response_code(200);
    exit;
}

// /ban 1.2.3.4
if (preg_match('/^\/ban\s+([\d\.]+)$/', $text, $m)) {
    $ip = $m[1];
    if (banIp($ip)) {
        sendTelegram("✅ IP <code>{$ip}</code> baneada. No podrá acceder al sistema.");
    } else {
        sendTelegram("⚠️ La IP <code>{$ip}</code> ya estaba baneada.");
    }
}

// /unban 1.2.3.4
elseif (preg_match('/^\/unban\s+([\d\.]+)$/', $text, $m)) {
    $ip = $m[1];
    unbanIp($ip);
    sendTelegram("✅ IP <code>{$ip}</code> desbaneada.");
}

// /bans — listar todas
elseif ($text === '/bans') {
    $file = getBannedIpsFile();
    $list = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (empty($list)) {
        sendTelegram("📋 No hay IPs baneadas.");
    } else {
        $lines = implode("\n", array_map(fn($x) => "• <code>{$x}</code>", $list));
        sendTelegram("🚫 IPs baneadas (" . count($list) . "):\n{$lines}");
    }
}

http_response_code(200);
echo 'OK';
