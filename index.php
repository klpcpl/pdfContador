<?php

class filePdf
{
  
    public $path ;
    public $listadoPag = array();
    public $totalSize ;


}



include_once('contar_pag.php');

$rutaTemp = __DIR__."/temp";  // ruta Temporal
$archivo = "muestra.pdf";     // nombre del pdf
$value = __DIR__.chr(47).$archivo; # ruta completa archivo original
$listadoPag = array(); # Array Total de todo el pdf, paggina => peso

$path='C:\xampp2\htdocs\pdfContador';
$FICHERO_PDFTK='C:\xampp2\htdocs\pdfContador\pdftk\pdftk.exe';
$sizeFile=1;
$cant_pag = Imagick::getNumPagesPdf($value);

// for($pag = 1 ; $pag <= $cant_pag; $pag++){
    $archivo = 'A='.$path.chr(47).$archivo;
    $peso = filesize($path.chr(47).$archivo);
 //   $peso = round((($peso/1024)/1024) * 100) / 100;
    echo '.pdf =>'.$peso." Bytes</br>";


//     $archivoSalida = 'A'.$pag;
//     $pdf_info = exec('C:\xampp\htdocs\pdfContador\pdftk\pdftk.exe '.$archivo.' cat '.$archivoSalida.' output C:\xampp\htdocs\pdfContador\temp\nombre'.$pag.'.pdf');

//     $peso = filesize('C:\xampp\htdocs\pdfContador\temp\nombre'.$pag.'.pdf');
//     error_log($pag."=>". $peso. ' bytes=>'. ($peso/1024) .' kbytes');
// }

 //$pdf_info = exec('C:\xampp\htdocs\pdfContador\pdftk\pdftk.exe '.$archivo.' dump_data_utf8 ');
 //echo $pdf_info;

$pdf_info = exec($FICHERO_PDFTK.' '.$archivo.' burst output temp/pagina%d.pdf');
$totalByte=0;

$listaAll= array();

$filePdf=new filePdf();



$pdf_info = exec($FICHERO_PDFTK.' @documentos@ cat output @SALIDA_PDF@');


    for($pag = 1 ; $pag <= $cant_pag; $pag++){

    

       
        $filePdf->listadoPag[]=$pag;

        $peso = filesize('temp/pagina'.$pag.'.pdf');
        $filePdf->totalSize=$filePdf->totalSize+$peso;



        echo 'pagina'.$pag.'.pdf =>'.$peso." Bytes ";
        $totalByte+=$peso;
        $peso = round((($peso/1024)/1024) * 100) / 100;
        echo ' =>'.$peso." megas</br>";




        if ( $filePdf->totalSize > $sizeFile*1024*1024){
            
            $filePdf->path = 'temp/paginaNueva'.count($listaAll).'.pdf';
            $documentos = "A=";
            for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
                $documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
            }
            

            $pdf_info = exec($FICHERO_PDFTK.' '.$documentos.' cat output '.$filePdf->path);
           
            for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
                unlink('temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf');
                //$documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
            }
            
            $listaAll[]= $filePdf ;
            $filePdf=new filePdf();

        }
    }

    if ( $filePdf->totalSize > 0){
        $filePdf->path = 'temp/paginaNueva'.count($listaAll).'.pdf';
        $documentos = "A=";
        for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
            $documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
        }
        

        $pdf_info = exec($FICHERO_PDFTK.' '.$documentos.' cat output '.$filePdf->path);
        
        for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
            unlink('temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf');
            //$documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
        }
        $listaAll[]= $filePdf ;

    }


echo '.pdf =>'.$totalByte." Bytes</br>";


echo '<pre>';
print_r($listaAll);

echo '</pre>';






// pdftk A=archivo1.pdf cat A1-12 A14-end output salida.pdf
// $destino = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
// $FICHERO_PDFTK = $destino.chr(47).'pdfContador/pdftk/pdftk.exe';

