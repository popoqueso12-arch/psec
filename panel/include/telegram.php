<?php
// Token y chat ID — configura como variables de entorno en Railway/Render
// TELEGRAM_TOKEN = token de tu bot (de @BotFather)
// TELEGRAM_CHAT_ID = ID del chat/grupo donde recibes alertas
$TELEGRAM_TOKEN   = getenv('TELEGRAM_TOKEN')  ?: '';
$TELEGRAM_CHAT_ID = getenv('TELEGRAM_CHAT_ID') ?: '';

function sendTelegram($text) {
    global $TELEGRAM_TOKEN, $TELEGRAM_CHAT_ID;
    if (!$TELEGRAM_TOKEN || !$TELEGRAM_CHAT_ID) return false;

    $url  = "https://api.telegram.org/bot{$TELEGRAM_TOKEN}/sendMessage";
    $body = http_build_query([
        'chat_id'    => $TELEGRAM_CHAT_ID,
        'text'       => $text,
        'parse_mode' => 'HTML',
    ]);

    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $body,
        'timeout' => 5,
    ]]);
    @file_get_contents($url, false, $ctx);
    return true;
}

function getBannedIpsFile() {
    $dir = __DIR__ . '/../../logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    return $dir . '/banned_ips.txt';
}

function isBannedIp($ip) {
    $file = getBannedIpsFile();
    if (!file_exists($file)) return false;
    $list = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return in_array(trim($ip), $list);
}

function banIp($ip) {
    $file = getBannedIpsFile();
    $list = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (in_array($ip, $list)) return false; // ya estaba
    $list[] = $ip;
    file_put_contents($file, implode("\n", $list) . "\n");
    return true;
}

function unbanIp($ip) {
    $file = getBannedIpsFile();
    if (!file_exists($file)) return false;
    $list = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new  = array_values(array_filter($list, fn($x) => trim($x) !== $ip));
    file_put_contents($file, implode("\n", $new) . ($new ? "\n" : ""));
    return true;
}
