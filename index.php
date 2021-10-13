<?php

include_once('contar_pag.php');

$rutaTemp = __DIR__."/temp";  // ruta Temporal
$archivo = "muestra.pdf";     // nombre del pdf
$value = __DIR__.chr(47).$archivo; # ruta completa archivo original
$listadoPag = array(); # Array Total de todo el pdf, paggina => peso


$cant_pag = Imagick::getNumPagesPdf($value);

// for($pag = 1 ; $pag <= $cant_pag; $pag++){
    $archivo = 'A=C:\xampp\htdocs\pdfContador\muestra.pdf';
//     $archivoSalida = 'A'.$pag;
//     $pdf_info = exec('C:\xampp\htdocs\pdfContador\pdftk\pdftk.exe '.$archivo.' cat '.$archivoSalida.' output C:\xampp\htdocs\pdfContador\temp\nombre'.$pag.'.pdf');

//     $peso = filesize('C:\xampp\htdocs\pdfContador\temp\nombre'.$pag.'.pdf');
//     error_log($pag."=>". $peso. ' bytes=>'. ($peso/1024) .' kbytes');
// }

// $pdf_info = exec('C:\xampp\htdocs\pdfContador\pdftk\pdftk.exe '.$archivo.' dump_data_utf8 ');
// echo $pdf_info;

$pdf_info = exec('C:\xampp\htdocs\pdfContador\pdftk\pdftk.exe '.$archivo.' burst output temp/pagina%d.pdf');

for($pag = 1 ; $pag <= $cant_pag; $pag++){
    $peso = filesize('temp/pagina'.$pag.'.pdf');
    $peso = round((($peso/1024)/1024) * 100) / 100;
    echo 'pagina'.$pag.'.pdf =>'.$peso." megas</br>";
}

// pdftk A=archivo1.pdf cat A1-12 A14-end output salida.pdf
// $destino = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
// $FICHERO_PDFTK = $destino.chr(47).'pdfContador/pdftk/pdftk.exe';

