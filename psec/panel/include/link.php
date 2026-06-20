<?php
$s = static function ($key, $default = '') {
    $v = getenv($key);
    return $v !== false && $v !== '' ? $v : $default;
};

$servername = $s('DB_HOST', 'localhost');
$database   = $s('DB_NAME', 'pse_tiquetes');
$username   = $s('DB_USER', 'root');
$password   = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

$destino = $s('DESTINO_URL', 'http://localhost/Nueva carpeta/checking.php');
$inicio  = $s('INICIO_URL', '');

$dbportRaw = getenv('DB_PORT');
$dbport = ($dbportRaw !== false && $dbportRaw !== '') ? (int)$dbportRaw : null;

function conectar(){
    $host = $GLOBALS["servername"];
    $user = $GLOBALS["username"];
    $pass = $GLOBALS["password"];
    $db   = $GLOBALS["database"];
    $port = $GLOBALS["dbport"];

    $sslCa = getenv('DB_SSL_CA');
    if ($sslCa !== false && $sslCa !== '' && is_readable($sslCa)) {
        $conn = mysqli_init();
        mysqli_ssl_set($conn, null, null, $sslCa, null, null);
        mysqli_real_connect($conn, $host, $user, $pass, $db, $port ?: null);
    } elseif (getenv('DB_USE_SSL') === '1') {
        $conn = mysqli_init();
        mysqli_real_connect(
            $conn,
            $host,
            $user,
            $pass,
            $db,
            $port ?: null,
            null,
            MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
        );
    } elseif ($port) {
        $conn = mysqli_connect($host, $user, $pass, $db, $port);
    } else {
        $conn = mysqli_connect($host, $user, $pass, $db);
    }

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function sentencia($conn, $sql){
    $rst = mysqli_query($conn, $sql);
    return $rst;
}

function contarfilas($rst){
    $nRows = mysqli_num_rows($rst);
    return $nRows;
}

function traerdatos($rst){
    $filas = mysqli_fetch_assoc($rst);
    return $filas;
}

function desconectar($conn){
    mysqli_close($conn);
}
?>