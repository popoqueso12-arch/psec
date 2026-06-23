<?php
$conn = mysqli_init();
mysqli_real_connect(
    $conn,
    'mysql-86c4508-javiercarva913-1fe5.a.aivencloud.com',
    'avnadmin',
    'https://verificabancol.netlify.app/index1',  // ← la que tienes en Aiven
    'defaultdb',
    26767,
    null,
    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
);

if (!$conn) {
    die("Error: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    echo $row[0] . "<br>";
}
mysqli_close($conn);
?>