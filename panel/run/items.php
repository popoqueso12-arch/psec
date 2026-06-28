<?php
session_start();

// ✅ CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ VALIDAR CREDENCIALES (POST)
$usr = isset($_POST['usr']) ? trim($_POST['usr']) : '';
$pas = isset($_POST['pas']) ? trim($_POST['pas']) : '';

if (empty($usr) || empty($pas)) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales requeridas']);
    exit;
}

// Validar credenciales en BD
require('../include/link.php');

if ($con = conectar()) {
    $usr_safe = $con->real_escape_string($usr);
    $pas_safe = $con->real_escape_string($pas);
    
    $consulta = sentencia($con, "SELECT * FROM m3us3r WHERE usuario = '" . $usr_safe . "' AND password = '" . $pas_safe . "'");
    
    if (!contarfilas($consulta)) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        desconectar($con);
        exit;
    }
    
    // ✅ CREDENCIALES VÁLIDAS - Obtener parámetro caso
    $caso = isset($_POST['caso']) ? (int)$_POST['caso'] : 0;

    if (!$caso || $caso < 1 || $caso > 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Parámetro caso inválido']);
        desconectar($con);
        exit;
    }

    // Diferentes queries según caso
    $datos = array();
    switch($caso) {
        case 1:
            // Activas
            $consulta = sentencia($con, "SELECT * FROM m3it3m WHERE status IN (1,3,5,7) ORDER BY horacreado DESC");
            break;
        case 2:
            // Pendientes
            $consulta = sentencia($con, "SELECT * FROM m3it3m WHERE status IN (9,35,37) ORDER BY horacreado DESC");
            break;
        case 3:
            // Cerradas
            $consulta = sentencia($con, "SELECT * FROM m3it3m WHERE status IN (10,12) ORDER BY horacreado DESC");
            break;
    }
    
    // Procesar resultados
    if ($consulta && $consulta->num_rows > 0) {
        while($row = $consulta->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    
    desconectar($con);
    
    // ✅ Devolver JSON
    http_response_code(200);
    echo json_encode($datos);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
}
?>