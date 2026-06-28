<?php
session_start();

// ✅ CORS (permitir desde localhost y Vercel)
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Responder a preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ VALIDAR SESIÓN - CRÍTICO
if (!isset($_SESSION["sesion"]) || $_SESSION["sesion"] !== "OK") {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Incluir funciones
require('../include/link.php');

// Obtener parámetro caso
$caso = isset($_POST['caso']) ? (int)$_POST['caso'] : 0;

if (!$caso || $caso < 1 || $caso > 3) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro caso inválido']);
    exit;
}

// Conectar a BD
if ($con = conectar()) {
    $datos = array();
    
    // Diferentes queries según caso
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
    
    // Devolver JSON
    http_response_code(200);
    echo json_encode($datos);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
}
?>