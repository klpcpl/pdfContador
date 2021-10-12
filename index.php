<?php

include_once('contar_pag.php');

$rutaTemp = __DIR__."/temp";  // ruta Temporal
$archivo = "muestra.pdf";     // nombre del pdf
$value = __DIR__.chr(47).$archivo; # ruta completa archivo original
$listadoPag = array(); # Array Total de todo el pdf, paggina => peso


$cant_pag = Imagick::getNumPagesPdf($value);

for($pag = 1 ; $pag <= $cant_pag; $pag++){
    $archivo = 'A=C:\xampp\htdocs\comprimir\muestra.pdf';
    $archivoSalida = 'A'.$pag;
    $pdf_info = exec('C:\xampp\htdocs\comprimir\pdftk\pdftk.exe '.$archivo.' cat '.$archivoSalida.' output C:\xampp\htdocs\comprimir\temp\nombre'.$pag.'.pdf');

    $peso = filesize('C:\xampp\htdocs\comprimir\temp\nombre'.$pag.'.pdf');
    error_log($pag."=>". $peso. ' bytes=>'. ($peso/1024) .' kbytes');
}


// pdftk A=archivo1.pdf cat A1-12 A14-end output salida.pdf
// $destino = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
// $FICHERO_PDFTK = $destino.chr(47).'comprimir/pdftk/pdftk.exe';

