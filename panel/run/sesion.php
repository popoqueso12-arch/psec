<?php
session_start();

// ✅ CORS (permitir desde localhost y Vercel)
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Responder a preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir funciones
require('../include/link.php');

// Obtener datos
$usr = isset($_POST['usr']) ? trim($_POST['usr']) : '';
$pass = isset($_POST['pas']) ? trim($_POST['pas']) : '';

// Validar que no estén vacíos
if (empty($usr) || empty($pass)) {
    http_response_code(400);
    echo json_encode(['error' => 'Usuario y contraseña requeridos']);
    exit;
}

// Conectar a BD
if ($con = conectar()) {
    // ✅ SEGURO: Escapear para evitar SQL injection
    $usr_safe = $con->real_escape_string($usr);
    $pass_safe = $con->real_escape_string($pass);
    
    // Query segura
    $consulta = sentencia($con, "SELECT * FROM m3us3r WHERE usuario = '" . $usr_safe . "' AND password = '" . $pass_safe . "'");
    
    if (contarfilas($consulta)) {
        // ✅ CREAR SESIÓN
        $_SESSION["usr-new"] = $usr;
        $_SESSION["sesion"] = "OK";
        
        http_response_code(200);
        echo "OK";
    } else {
        http_response_code(401);
        echo "NO";
    }
    desconectar($con);
} else {
    http_response_code(500);
    echo "ERR";
}
?>