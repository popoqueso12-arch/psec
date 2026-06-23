<?php
session_start();
require('../include/link.php');

$con = conectar();
if (!$con) { echo "ERR"; exit; }

$usr  = $_POST['usr']  ?? '';
$pass = $_POST['pass'] ?? '';

if ($usr === '' || $pass === '') { echo "NO"; exit; }

$stmt = mysqli_prepare($con, "SELECT usuario FROM m3us3r WHERE usuario = ? AND password = ?");
mysqli_stmt_bind_param($stmt, 'ss', $usr, $pass);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
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