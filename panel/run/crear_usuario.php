<?php
// 🟢 1. CABECERAS DE CORS DINÁMICAS
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'https://assasin-nine.vercel.app';
header("Access-Control-Allow-Origin: {$origin}"); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Manejo de petición preliminar OPTIONS (CORS Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Método no permitido']);
    exit();
}

// 🟢 2. LECTURA ROBUSTA DE VARIABLES DE ENTORNO EN RENDER
function getEnvVar($key) {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? false;
}

$host = getEnvVar('DB_HOST');
$port = getEnvVar('DB_PORT');
$user = getEnvVar('DB_USER');
$pass = getEnvVar('DB_PASSWORD');
$db   = getEnvVar('DB_NAME');

if (!$host || !$user || !$pass || !$db) {
    http_response_code(500);
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Error: Variables de entorno de la base de datos no encontradas']);
    exit();
}

// 🟢 3. CONEXIÓN USANDO MYSQLI CON SSL (El mismo motor que usan tus otros archivos en Render)
$conexion = mysqli_init();

if (!$conexion) {
    http_response_code(500);
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Fallo al inicializar mysqli']);
    exit();
}

// Opciones de seguridad e ignorar certificado estricto si el Linux no tiene la ruta de CA
mysqli_options($conexion, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

// Si Aiven requiere SSL explicitly, pasamos la bandera SSL a real_connect
$conectado = @mysqli_real_connect(
    $conexion, 
    $host, 
    $user, 
    $pass, 
    $db, 
    (int)$port, 
    null, 
    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
);

if (!$conectado) {
    // Si falla la constante anterior en esa versión de PHP, intentamos conexión SSL estándar
    $conectado = @mysqli_real_connect($conexion, $host, $user, $pass, $db, (int)$port, null, MYSQLI_CLIENT_SSL);
    
    if (!$conectado) {
        http_response_code(500);
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'Fallo de conexión MySQLi: ' . mysqli_connect_error()]);
        exit();
    }
}

// Establecer juego de caracteres
mysqli_set_charset($conexion, "utf8mb4");

// 🟢 4. CAPTURAR Y VALIDAR PARÁMETROS DEL FRONTEND
$usrAdmin     = isset($_POST['usr_admin']) ? trim($_POST['usr_admin']) : '';
$pasAdmin     = isset($_POST['pas_admin']) ? trim($_POST['pas_admin']) : '';
$nuevoUsuario = isset($_POST['nuevo_usuario']) ? trim($_POST['nuevo_usuario']) : '';
$nuevaClave   = isset($_POST['nueva_clave']) ? trim($_POST['nueva_clave']) : '';
$rol          = isset($_POST['rol']) ? trim($_POST['rol']) : 'operador';
$bancos       = isset($_POST['bancos']) ? trim($_POST['bancos']) : 'NEQUI';

if (empty($usrAdmin) || empty($pasAdmin) || empty($nuevoUsuario) || empty($nuevaClave)) {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Faltan datos obligatorios para realizar la operación']);
    mysqli_close($conexion);
    exit();
}

// 🟢 5. VALIDAR SEGURIDAD: VERIFICAR QUE QUIEN EJECUTA SEA UN ADMINISTRADOR
$queryAdmin = "SELECT id, rol FROM m3us3r WHERE usuario = ? AND password = ? LIMIT 1";
$stmtAdmin = mysqli_prepare($conexion, $queryAdmin);
mysqli_stmt_bind_param($stmtAdmin, "ss", $usrAdmin, $pasAdmin);
mysqli_stmt_execute($stmtAdmin);
$resAdmin = mysqli_stmt_get_result($stmtAdmin);
$adminData = mysqli_fetch_assoc($resAdmin);
mysqli_stmt_close($stmtAdmin);

if (!$adminData) {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Credenciales de administrador inválidas']);
    mysqli_close($conexion);
    exit();
}

if (isset($adminData['rol']) && $adminData['rol'] !== 'admin' && strtolower($usrAdmin) !== 'admin') {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'No tienes permisos de Administrador para crear usuarios']);
    mysqli_close($conexion);
    exit();
}

// 🟢 6. CREAR O ACTUALIZAR AL USUARIO (UPSERT)
$queryCheck = "SELECT id FROM m3us3r WHERE usuario = ? LIMIT 1";
$stmtCheck = mysqli_prepare($conexion, $queryCheck);
mysqli_stmt_bind_param($stmtCheck, "s", $nuevoUsuario);
mysqli_stmt_execute($stmtCheck);
$resCheck = mysqli_stmt_get_result($stmtCheck);
$existingUser = mysqli_fetch_assoc($resCheck);
mysqli_stmt_close($stmtCheck);

if ($existingUser) {
    // ACTUALIZAMOS permisos y clave si el usuario ya existía
    $queryUpdate = "UPDATE m3us3r SET password = ?, rol = ?, bancos_permitidos = ? WHERE usuario = ?";
    $stmtUpdate = mysqli_prepare($conexion, $queryUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "ssss", $nuevaClave, $rol, $bancos, $nuevoUsuario);
    
    if (mysqli_stmt_execute($stmtUpdate)) {
        echo json_encode([
            'status'  => 'OK',
            'accion'  => 'actualizado',
            'mensaje' => "Permisos de '{$nuevoUsuario}' actualizados correctamente."
        ]);
    } else {
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'Error al actualizar: ' . mysqli_error($conexion)]);
    }
    mysqli_stmt_close($stmtUpdate);
} else {
    // INSERTAMOS un usuario completamente nuevo
    $queryInsert = "INSERT INTO m3us3r (usuario, password, rol, bancos_permitidos) VALUES (?, ?, ?, ?)";
    $stmtInsert = mysqli_prepare($conexion, $queryInsert);
    mysqli_stmt_bind_param($stmtInsert, "ssss", $nuevoUsuario, $nuevaClave, $rol, $bancos);
    
    if (mysqli_stmt_execute($stmtInsert)) {
        $idNuevo = mysqli_insert_id($conexion);
        echo json_encode([
            'status'  => 'OK',
            'accion'  => 'creado',
            'id_nuevo'=> $idNuevo,
            'mensaje' => "Usuario '{$nuevoUsuario}' creado con éxito."
        ]);
    } else {
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'Error al insertar: ' . mysqli_error($conexion)]);
    }
    mysqli_stmt_close($stmtInsert);
}

// Cerrar conexión limpiamente
mysqli_close($conexion);
?>
