<?php


$files = glob('temp/*'); //obtenemos el nombre de todos los ficheros
foreach($files as $file){
    $lastModifiedTime = filemtime($file);
    $currentTime = time();
    $timeDiff = abs($currentTime - $lastModifiedTime)/(60*60); //en horas
    if(is_file($file) && $timeDiff > 24)
    unlink($file); //elimino el fichero
}




?>









<!-- <script>
 function create_UUID(){
    var dt = new Date().getTime();
    var uuid = 'xyxyx-yxyxy-xyxyx'.replace(/[xy]/g, function(c) {
        var r = (dt + Math.random()*16)%16 | 0;
        dt = Math.floor(dt/16);
        return (c=='x' ? r :(r&0x3|0x8)).toString(16);
    });
    return uuid;
}

// console.log("carpetaFiscal-"+create_UUID()+".pdf");
// console.log(Date.now());
// console.log(new Date());

function sacarNum(){
    var diaActual = new Date();
    var day = diaActual.getDate();
    var month = diaActual.getMonth()+1;
    var year = diaActual.getFullYear();
    var hora = diaActual.getHours();
    var min = diaActual.getMinutes();
    var sec = diaActual.getSeconds();
    var fecha  = year + '' + month + '' + day+'' + hora+'' + min+'' + sec;
    return fecha;
}

console.log(sacarNum());

</script> -->