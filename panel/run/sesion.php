<?php
session_start();
require('../include/link.php');

$con = conectar();
if (!$con) {
    echo "ERR";
    exit;
}

$usr  = $_POST['usr']  ?? '';
$pass = $_POST['pass'] ?? '';

if ($usr === '' || $pass === '') {
    echo "NO";
    exit;
}

// ✅ Prepared statement — inmune a SQL Injection
$stmt = mysqli_prepare($con, "SELECT usuario, password FROM m3us3r WHERE usuario = ?");
mysqli_stmt_bind_param($stmt, 's', $usr);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row    = mysqli_fetch_assoc($result);

if ($row && password_verify($pass, $row['password'])) {
    // ✅ Regenerar ID de sesión — previene Session Fixation
    session_regenerate_id(true);
    $_SESSION['usr-new'] = $usr;
    $_SESSION['sesion']  = 'OK';
    echo "OK";
} else {
    echo "NO";
}

mysqli_stmt_close($stmt);
desconectar($con);
?>