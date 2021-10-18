<?php

class filePdf
{
  
    public $path ;
    public $listadoPag = array();
    public $totalSize ;
    public $totalSeconds ;

}



include_once('contar_pag.php');


$rutaTemp = __DIR__."/temp";  // ruta Temporal
$archivo = "477527.pdf";     // nombre del pdf
$value = __DIR__.chr(47).$archivo; # ruta completa archivo original
$listadoPag = array(); # Array Total de todo el pdf, paggina => peso

$path='C:\xampp2\htdocs\pdfContador';
$FICHERO_PDFTK='C:\xampp2\htdocs\pdfContador\pdftk\pdftk.exe';
$sizeFile=15;
$cant_pag = Imagick::getNumPagesPdf($value);

$startDate = new DateTime("now");

$peso = filesize($archivo);
echo '.pdf =>'.$peso." Bytes</br>";


$archivo = 'A='.$path.chr(47).$archivo;
   
$pdf_info = exec($FICHERO_PDFTK.' '.$archivo.' burst output temp/pagina%d.pdf');

$finalDate = new DateTime("now");
$diff = $startDate->diff($finalDate);
echo  "total de segundos :: ".Imagick::get_format($diff).' en realizar el burst';

$totalByte=0;

$listaAll= array();

$filePdf=new filePdf();
$filePdf->totalSize=0;


$pdf_info = exec($FICHERO_PDFTK.' @documentos@ cat output @SALIDA_PDF@');

$date1 = new DateTime("now");
    for($pag = 1 ; $pag <= $cant_pag; $pag++){

       
      

        $peso = filesize('temp/pagina'.$pag.'.pdf');


        $totalSize=$filePdf->totalSize+$peso;


        if ( $totalSize > $sizeFile*1024*1024){
         
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
            

            $date2 = new DateTime("now");
            $diffAux = $date1->diff($date2);
            $mili=Imagick::get_format($diffAux);
            $filePdf->totalSeconds=$mili ;
           
            
           
           
            $date1 = new DateTime("now");
            $listaAll[]= $filePdf ;
            $filePdf=new filePdf();
            $filePdf->totalSize=0;
        }

        $filePdf->listadoPag[]=$pag;
        $filePdf->totalSize=$filePdf->totalSize+$peso;
    }

    //archivos faltantes
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

        $date2 = new DateTime("now");
        $diffAux = $date1->diff($date2);
        $mili=Imagick::get_format($diffAux);
        $filePdf->totalSeconds=$mili ;

        $listaAll[]= $filePdf ;

    }




echo '<pre>';
print_r($listaAll);

echo '</pre>';

echo '</br>';

$finalDate = new DateTime("now");
$diff = $startDate->diff($finalDate);
echo  "total de segundos :: ".Imagick::get_format($diff);


