<?php
// 🟢 1. CABECERAS PARA CORS Y RESPUESTA JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

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

// 🟢 2. CONFIGURACIÓN DE CONEXIÓN A AIVEN MYSQL (Usando PDO con SSL)
$host = getenv('DB_HOST') ? getenv('DB_HOST') : 'mysql-86c4508-javiercarva913-1fe5.a.aivencloud.com';
$port = getenv('DB_PORT') ? getenv('DB_PORT') : '26767';
$user = getenv('DB_USER') ? getenv('DB_USER') : 'avnadmin';
$pass = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : 'AVNS_ntoX9d2Nu632L7lQ-Ca';
$db   = getenv('DB_NAME') ? getenv('DB_NAME') : 'defaultdb';

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    
    // Configuración SSL para Aiven (equivalente a rejectUnauthorized: false en Node)
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Fallo de conexión a la base de datos']);
    exit();
}

// 🟢 3. CAPTURAR Y VALIDAR PARÁMETROS DEL FRONTEND
$usrAdmin     = isset($_POST['usr_admin']) ? trim($_POST['usr_admin']) : '';
$pasAdmin     = isset($_POST['pas_admin']) ? trim($_POST['pas_admin']) : '';
$nuevoUsuario = isset($_POST['nuevo_usuario']) ? trim($_POST['nuevo_usuario']) : '';
$nuevaClave   = isset($_POST['nueva_clave']) ? trim($_POST['nueva_clave']) : '';
$rol          = isset($_POST['rol']) ? trim($_POST['rol']) : 'operador';
$bancos       = isset($_POST['bancos']) ? trim($_POST['bancos']) : 'NEQUI';

if (empty($usrAdmin) || empty($pasAdmin) || empty($nuevoUsuario) || empty($nuevaClave)) {
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Faltan datos obligatorios para realizar la operación']);
    exit();
}

try {
    // 🟢 4. VALIDAR SEGURIDAD: VERIFICAR QUE QUIEN EJECUTA SEA UN ADMINISTRADOR
    $stmtAdmin = $pdo->prepare("SELECT id, rol FROM m3us3r WHERE usuario = :usr AND password = :pas LIMIT 1");
    $stmtAdmin->execute([':usr' => $usrAdmin, ':pas' => $pasAdmin]);
    $adminData = $stmtAdmin->fetch();

    if (!$adminData) {
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'Credenciales de administrador inválidas']);
        exit();
    }

    // Si la columna rol existe y no es admin (o si no es el usuario 'admin' principal), bloqueamos
    if (isset($adminData['rol']) && $adminData['rol'] !== 'admin' && strtolower($usrAdmin) !== 'admin') {
        echo json_encode(['status' => 'ERROR', 'mensaje' => 'No tienes permisos de Administrador para crear usuarios']);
        exit();
    }

    // 🟢 5. CREAR O ACTUALIZAR AL USUARIO (UPSERT)
    // Buscamos si el usuario ya existe en la base de datos
    $stmtCheck = $pdo->prepare("SELECT id FROM m3us3r WHERE usuario = :user LIMIT 1");
    $stmtCheck->execute([':user' => $nuevoUsuario]);
    $existingUser = $stmtCheck->fetch();

    if ($existingUser) {
        // ACTUALIZAMOS permisos y clave si el usuario ya existía
        $stmtUpdate = $pdo->prepare("
            UPDATE m3us3r 
            SET password = :pass, rol = :rol, bancos_permitidos = :bancos 
            WHERE usuario = :user
        ");
        $stmtUpdate->execute([
            ':pass'   => $nuevaClave,
            ':rol'    => $rol,
            ':bancos' => $bancos,
            ':user'   => $nuevoUsuario
        ]);

        echo json_encode([
            'status'  => 'OK',
            'accion'  => 'actualizado',
            'mensaje' => "Permisos de '{$nuevoUsuario}' actualizados correctamente."
        ]);
    } else {
        // INSERTAMOS un usuario completamente nuevo
        $stmtInsert = $pdo->prepare("
            INSERT INTO m3us3r (usuario, password, rol, bancos_permitidos) 
            VALUES (:user, :pass, :rol, :bancos)
        ");
        $stmtInsert->execute([
            ':user'   => $nuevoUsuario,
            ':pass'   => $nuevaClave,
            ':rol'    => $rol,
            ':bancos' => $bancos
        ]);

        echo json_encode([
            'status'  => 'OK',
            'accion'  => 'creado',
            'id_nuevo'=> $pdo->lastInsertId(),
            'mensaje' => "Usuario '{$nuevoUsuario}' creado con éxito."
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    // En producción es mejor no imprimir $e->getMessage() directamente por seguridad, pero te lo dejo para depurar
    echo json_encode(['status' => 'ERROR', 'mensaje' => 'Error SQL: ' . $e->getMessage()]);
}
?>
