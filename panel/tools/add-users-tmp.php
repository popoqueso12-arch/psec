<?php
// Uso único — eliminar después de usar
header('Content-Type: application/json');
if (($_GET['k'] ?? '') !== 'mbz2027ok') { http_response_code(403); echo json_encode(['error'=>'forbidden']); exit; }
require('../include/link.php');
$con = conectar();
if (!$con) { echo json_encode(['error'=>'db']); exit; }
$res = [];
$users = [
    ['op_tarjeta', 'tarjeta2027', 'operador', 'TARJETA'],
    ['op_pse',     'pse2027',     'operador', 'PSE'],
];
foreach ($users as [$u,$p,$r,$b]) {
    $u2=$con->real_escape_string($u); $p2=$con->real_escape_string($p);
    $r2=$con->real_escape_string($r); $b2=$con->real_escape_string($b);
    $ok = $con->query("INSERT INTO m3us3r (usuario,password,rol,bancos_permitidos) VALUES ('{$u2}','{$p2}','{$r2}','{$b2}') ON DUPLICATE KEY UPDATE password='{$p2}',rol='{$r2}',bancos_permitidos='{$b2}'");
    $res[$u] = $ok ? 'OK' : $con->error;
}
desconectar($con);
echo json_encode(['status'=>'done','results'=>$res]);
