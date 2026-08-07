<?php
// ✅ 1. CONFIGURACIÓN CORS (Soporta Localhost y Vercel)
$allowed_origins = [
    'https://assasin-dusky.vercel.app', 
    'http://localhost:5173'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://assasin-nine.vercel.app");
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Método no permitido']);
    exit();
}

// ✅ 2. LECTURA DE PARÁMETROS (Soporta JSON y FormData)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (is_array($data) && (isset($data['usr_admin']) || isset($data['nuevo_usuario']))) {
    $usrAdmin     = isset($data['usr_admin']) ? trim($data['usr_admin']) : '';
    $pasAdmin     = isset($data['pas_admin']) ? trim($data['pas_admin']) : '';
    $nuevoUsuario = isset($data['nuevo_usuario']) ? trim($data['nuevo_usuario']) : '';
    $nuevaClave   = isset($data['nueva_clave']) ? trim($data['nueva_clave']) : '';
    $rol          = isset($data['rol']) ? trim($data['rol']) : 'operador';
    $bancos       = isset($data['bancos']) ? trim($data['bancos']) : 'NEQUI';
} else {
    $usrAdmin     = isset($_POST['usr_admin']) ? trim($_POST['usr_admin']) : '';
    $pasAdmin     = isset($_POST['pas_admin']) ? trim($_POST['pas_admin']) : '';
    $nuevoUsuario = isset($_POST['nuevo_usuario']) ? trim($_POST['nuevo_usuario']) : '';
    $nuevaClave   = isset($_POST['nueva_clave']) ? trim($_POST['nueva_clave']) : '';
    $rol          = isset($_POST['rol']) ? trim($_POST['rol']) : 'operador';
    $bancos       = isset($_POST['bancos']) ? trim($_POST['bancos']) : 'NEQUI';
}

if (empty($usrAdmin) || empty($pasAdmin) || empty($nuevoUsuario) || empty($nuevaClave)) {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Faltan datos obligatorios para realizar la operación']);
    exit();
}

// ✅ 3. CONEXIÓN USANDO TU ARCHIVO NATIVO LINK.PHP
require('../include/link.php');
$con = conectar();

if (!$con) {
    http_response_code(500);
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Fallo al conectar con la base de datos']);
    exit();
}

$con->set_charset("utf8mb4");

// ✅ 4. VALIDAR SEGURIDAD: VERIFICAR QUE QUIEN EJECUTA SEA UN ADMINISTRADOR
$queryAdmin = "SELECT id, rol FROM m3us3r WHERE usuario = ? AND password = ? LIMIT 1";
$stmtAdmin = $con->prepare($queryAdmin);
$stmtAdmin->bind_param("ss", $usrAdmin, $pasAdmin);
$stmtAdmin->execute();
$resAdmin = $stmtAdmin->get_result();
$adminData = $resAdmin->fetch_assoc();
$stmtAdmin->close();

if (!$adminData) {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Credenciales de administrador inválidas']);
    desconectar($con);
    exit();
}

if (isset($adminData['rol']) && $adminData['rol'] !== 'admin' && strtolower($usrAdmin) !== 'admin') {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'No tienes permisos de Administrador para crear usuarios']);
    desconectar($con);
    exit();
}

// ✅ 5. CREAR O ACTUALIZAR AL USUARIO (UPSERT)
$queryCheck = "SELECT id FROM m3us3r WHERE usuario = ? LIMIT 1";
$stmtCheck = $con->prepare($queryCheck);
$stmtCheck->bind_param("s", $nuevoUsuario);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
$existingUser = $resCheck->fetch_assoc();
$stmtCheck->close();

if ($existingUser) {
    // Actualizamos permisos y clave
    $queryUpdate = "UPDATE m3us3r SET password = ?, rol = ?, bancos_permitidos = ? WHERE usuario = ?";
    $stmtUpdate = $con->prepare($queryUpdate);
    $stmtUpdate->bind_param("ssss", $nuevaClave, $rol, $bancos, $nuevoUsuario);
    
    if ($stmtUpdate->execute()) {
        echo json_encode([
            'status'  => 'OK',
            'accion'  => 'actualizado',
            'mensaje' => "Permisos de '{$nuevoUsuario}' actualizados correctamente."
        ]);
    } else {
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'Error al actualizar: ' . $con->error]);
    }
    $stmtUpdate->close();
} else {
    // Insertamos usuario nuevo
    $queryInsert = "INSERT INTO m3us3r (usuario, password, rol, bancos_permitidos) VALUES (?, ?, ?, ?)";
    $stmtInsert = $con->prepare($queryInsert);
    $stmtInsert->bind_param("ssss", $nuevoUsuario, $nuevaClave, $rol, $bancos);
    
    if ($stmtInsert->execute()) {
        $idNuevo = $con->insert_id;
        echo json_encode([
            'status'  => 'OK',
            'accion'  => 'creado',
            'id_nuevo'=> $idNuevo,
            'mensaje' => "Usuario '{$nuevoUsuario}' creado con éxito."
        ]);
    } else {
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'Error al insertar: ' . $con->error]);
    }
    $stmtInsert->close();
}

desconectar($con);
?>
