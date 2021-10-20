<?php
require_once('utilicom.class.php');
require_once('gdd.class.php');



class filePdf
{
  
    public $path ;
    public $listadoPag = array();
    public $totalSize ;
    public $totalSeconds ;
    public $nombreArchivo;

}


class datoArchivo{

    public $rutaTemp = __DIR__."/temp";  // ruta Temporal
    public $listadoPag = array(); # Array Total de todo el pdf, paggina => peso
    public $archivo;     // nombre del pdf
    
    public $path='C:\xampp\htdocs\pdfContador';
    public $FICHERO_PDFTK='C:\xampp\htdocs\pdfContador\pdftk\pdftk.exe';
    public $sizeFile=14;
    public $listaAll = array();
    public $totalByte=0;

    
    function getArchivo($archiPDF){
        $this->archivo = $archiPDF;
        
        $value = __DIR__.chr(47).$this->archivo; # ruta completa archivo original
        $cant_pag = ultilcom::getNumPagesPdf($value);
        date_default_timezone_set('America/Santiago');
        $startDate = new DateTime("now");

        // $peso = filesize($value);
        // $peso = round(round($peso / 1024)/1024);

        $archivo = 'A='.$this->path.chr(47).$this->archivo;

        $pdf_info = exec($this->FICHERO_PDFTK.' '.$archivo.' burst output temp/pagina%d.pdf');
        
        $finalDate = new DateTime("now");
        $diff = $startDate->diff($finalDate);
       # echo  "total de segundos :: ".ultilcom::get_format($diff).' en realizar el burst';
       
        $filePdf=new filePdf();
        $filePdf->totalSize=0;

        $pdf_info = exec($this->FICHERO_PDFTK.' @documentos@ cat output @SALIDA_PDF@');

        $date1 = new DateTime("now");
            for($pag = 1 ; $pag <= $cant_pag; $pag++){
        
                $peso = filesize('temp/pagina'.$pag.'.pdf');
        
                $totalSize=$filePdf->totalSize+$peso;
        
                if ( $totalSize > $this->sizeFile*1024*1024){
                 
                    $filePdf->nombreArchivo = 'paginaNueva'.count($this->listaAll).'.pdf';
                    $filePdf->path = 'temp/'.$filePdf->nombreArchivo;
                    $documentos = "A=";
                    for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
                        $documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
                    }
                    
        
                    $pdf_info = exec($this->FICHERO_PDFTK.' '.$documentos.' cat output '.$filePdf->path);
                   
                    for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
                        unlink('temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf');
                        //$documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
                    }
                    
        
                    $date2 = new DateTime("now");
                    $diffAux = $date1->diff($date2);
                    $mili=ultilcom::get_format($diffAux);
                    $filePdf->totalSeconds=$mili ;

                   
                    $date1 = new DateTime("now");
                    $this->listaAll[]= $filePdf ;
                    $filePdf=new filePdf();
                    $filePdf->totalSize=0;
                }
        
                $filePdf->listadoPag[]=$pag;
                $filePdf->totalSize=$filePdf->totalSize+$peso;
            }
        
            //archivos faltantes
            if ( $filePdf->totalSize > 0){
                $filePdf->nombreArchivo = 'paginaNueva'.count($this->listaAll).'.pdf';
                $filePdf->path = 'temp/'.$filePdf->nombreArchivo;
                $documentos = "A=";
                for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
                    $documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
                }
                
        
                $pdf_info = exec($this->FICHERO_PDFTK.' '.$documentos.' cat output '.$filePdf->path);
                
                for($pagu = 0 ; $pagu < count($filePdf->listadoPag); $pagu++){
                    unlink('temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf');
                    //$documentos = $documentos.'temp/pagina'.$filePdf->listadoPag[$pagu].'.pdf'.' ';
                }
        
                $date2 = new DateTime("now");
                $diffAux = $date1->diff($date2);
                $mili=ultilcom::get_format($diffAux);
                $filePdf->totalSeconds=$mili ;
        
                $this->listaAll[]= $filePdf ;
        
            }

       return $this->listaAll; // Lista de Objetos 

        # return  $this->listaAll;
       # $result = new filePdf();
        # return $result;
    }

}




class Registro{
    public $documentos; # LISTA DE ARCHIVOS SEPRADOS POR 15 MEGAS

}




# SE GENERA EL TOQUEN CON TODAS SUS METODOS EN EL OBJETO
$apiRest = new Gdd("password","apalma","jwtpass");
if($apiRest->respuesta != "ok"){
    # ERROR EN EL TOKEN NO SE HACE NADA
    echo $apiRest->respuesta;
}else{
        # SE CREA EL OBJETO GENERAL DE TODO LO QUE SE VA A SUBIR
        $registro = new Registro();
        # SE CREA LOS DOCUMENTOS EN EL OBJETO
        $archivo = new datoArchivo();
         $resultadoDocs = $archivo->getArchivo("muestra_baja.pdf");
   
         # se agrega el listado a documento a subir en la API
        $registro->documentos = $resultadoDocs;

        # SE AGREGAN LOS PARAMETRO DEL DOCUMENTO (CANTIDAD DE DOCUMENTOS Y DESCRIPCIÓN)
       $registro->descripcion = "Prueba de documento V1.0.1"; 
       $registro->idf_rolunico =  "1500121957-7";
       $registro->numero_documento = count($resultadoDocs);
       $registro->id_fiscalia = 7; 
       $registro->id_unidad_externa = 1;
       $registro->dias_plazo = 17;
       $registro->crr_idactividad = "";

       # AGREGANDO INFORMACIÓN DE LOS DOCUMENTOS QUE SE VAN A ANEXAR
      $result =  $apiRest->addRegistro($registro->descripcion, $registro->idf_rolunico, $registro->numero_documento, $registro->id_fiscalia, $registro->id_unidad_externa, $registro->dias_plazo,$registro->crr_idactividad);
        
        if(isset($result["error"])){
            # se Agrega el error al objeto 
            $registro->error_addRegistro = $result["error"];
            $registro->error_addRegistro_Mensaje = $result["Mensaje"]; 

        }else{
            # se agrega la  subida del nombres documento al objeto y se saca el id documento
            $registro->id_tramitacion = $result["id_tramitacion"];
            $registro->id_add_documento = $result["id_add_documento"];
            $registro->Mensaje = $result["Mensaje"]; 

            foreach( $registro->documentos as $key => $value){
                $doc64 = base64_encode(file_get_contents($value->path));
    
             $resultDoc = $apiRest->addDocumento($registro->idf_rolunico, $registro->id_add_documento, $doc64, $value->nombreArchivo);
    
             if(isset($resultDoc["error"])){
                 # se agrega el error al objeto documento subido
                 $registro->documentos->add_docSubido_error =  $resultDoc["error"];
                 $registro->documentos->add_docSubido_Mensaje = $resultDoc["Mensaje"];
             }else{
                 # se agrega el ok al documento subido
                 $registro->documentos->add_docSubido_id = $resultDoc["id"];
                 $registro->documentos->add_docSubido_Mensaje = $resultDoc["Mensaje"];
             }
            
            }
        }
    }


    //     echo "<pre>";
    //     print_r($registro);
    //    echo "</pre>";

        // $registro->id_tramitacion = 1614;
        // $registro->id_add_documento = 2150;
        // $registro->Mensaje = "Registro correcto";


        //  echo "<pre>";
        //   print_r($resultDoc);
        //  echo "</pre>";
        

    // $respuesta = $apiRest->getRespuestaRuc("1500121957-7");
    // echo "<pre>";
    // print_r($respuesta);
    // echo "</pre>";


