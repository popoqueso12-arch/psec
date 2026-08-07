<?php
session_start();

// ✅ CORS dinámico (permite localhost y Vercel)
$allowed_origins = [
    'https://assasin-dusky.vercel.app', 
    'http://localhost:5173'
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Responder a preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir funciones
require('../include/link.php');

// Obtener datos: soporta tanto form-urlencoded/form-data como JSON
$usr = '';
$pass = '';

if (isset($_POST['usr']) || isset($_POST['pas'])) {
    // Vino como application/x-www-form-urlencoded o multipart/form-data
    $usr = isset($_POST['usr']) ? trim($_POST['usr']) : '';
    $pass = isset($_POST['pas']) ? trim($_POST['pas']) : '';
} else {
    // Vino como application/json
    $input = json_decode(file_get_contents('php://input'), true);
    $usr = isset($input['usr']) ? trim($input['usr']) : '';
    $pass = isset($input['pas']) ? trim($input['pas']) : '';
}

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
        echo json_encode(['status' => 'OK']);
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'NO']);
    }
    desconectar($con);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'ERR']);
}
?>
