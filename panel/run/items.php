<?php
session_start();

// ✅ 1. CONFIGURACIÓN CORS (Soporta Localhost y Vercel)
$allowed_origins = [
    'https://assasin-nine.vercel.app', 
    'http://localhost:5173'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://assasin-nine.vercel.app");
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Manejo de la petición de pre-vuelo (Preflight) de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ 2. LECTURA DE DATOS (Soporta JSON y FormData)
$usr = '';
$pas = '';
$caso = 1;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (is_array($data) && (isset($data['usr']) || isset($data['pas']))) {
    $usr = isset($data['usr']) ? trim($data['usr']) : '';
    $pas = isset($data['pas']) ? trim($data['pas']) : '';
    $caso = isset($data['caso']) ? (int)$data['caso'] : 1;
} else {
    $usr = isset($_POST['usr']) ? trim($_POST['usr']) : '';
    $pas = isset($_POST['pas']) ? trim($_POST['pas']) : '';
    $caso = isset($_POST['caso']) ? (int)$_POST['caso'] : 1;
}

// Validar que existan las credenciales
if (empty($usr) || empty($pas)) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales requeridas']);
    exit;
}

if ($caso < 1 || $caso > 3) {
    $caso = 1; 
}

// ✅ 3. CONEXIÓN Y AUTENTICACIÓN SEGURA
require('../include/link.php');
$con = conectar();

if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// Uso de Prepared Statements para evitar Inyección SQL
$stmt = $con->prepare("SELECT rol, bancos_permitidos FROM m3us3r WHERE usuario = ? AND password = ? LIMIT 1");
$stmt->bind_param("ss", $usr, $pas);
$stmt->execute();
$resultado_auth = $stmt->get_result();

if ($resultado_auth->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales inválidas']);
    $stmt->close();
    desconectar($con);
    exit;
}

// 🟢 CORRECCIÓN CLAVE: Extraemos los datos del operador logueado
$user_data = $resultado_auth->fetch_assoc();
$stmt->close();

$rol = isset($user_data['rol']) ? trim($user_data['rol']) : 'admin';
$bancosPermitidos = isset($user_data['bancos_permitidos']) ? trim($user_data['bancos_permitidos']) : 'TODOS';

// 🟢 CONSTRUCCIÓN DEL FILTRO DE BANCOS
$whereBanco = "";
if ($rol !== 'admin' && strtolower($usr) !== 'admin' && strpos($bancosPermitidos, 'TODOS') === false) {
    $listaBancos = explode(',', $bancosPermitidos);
    $bancosLimpios = array_map(function($b) use ($con) {
        return "'" . $con->real_escape_string(trim($b)) . "'";
    }, $listaBancos);
    
    if (count($bancosLimpios) > 0) {
        $inBancos = implode(',', $bancosLimpios);
        $whereBanco = " AND UPPER(banco) IN ({$inBancos}) ";
    } else {
        // Si el operador no tiene bancos válidos, devolvemos array vacío de inmediato
        echo json_encode([]);
        desconectar($con);
        exit;
    }
}

// ✅ 4. CONSULTA DE ITEMS SEGÚN EL CASO Y PERMISOS DE BANCO
$datos = array();

switch($caso) {
    case 1:
        // Activas (sin archivadas ni declinadas)
        $query = "SELECT * FROM m3it3m WHERE status NOT IN (10,12) {$whereBanco} ORDER BY horacreado DESC LIMIT 200";
        break;
    case 2:
        // Pendientes/En proceso
        $query = "SELECT * FROM m3it3m WHERE status IN (1,3,5,7,9) {$whereBanco} ORDER BY horacreado DESC LIMIT 200";
        break;
    case 3:
        // Archivadas/Cerradas (aprobadas y declinadas)
        $query = "SELECT * FROM m3it3m WHERE status IN (10,12) {$whereBanco} ORDER BY horacreado DESC LIMIT 200";
        break;
}

$consulta_items = $con->query($query);

if ($consulta_items && $consulta_items->num_rows > 0) {
    while($row = $consulta_items->fetch_assoc()) {
        $datos[] = $row;
    }
}

desconectar($con);

// ✅ 5. RESPUESTA FINAL
http_response_code(200);
echo json_encode($datos);
?>
