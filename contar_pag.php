<?php


/**
 * Esta función devuelve el número de páginas de un archivo pdf
 * Tiene que recibir la ubicación y nombre del archivo
 */
class Imagick{


    public static function getNumPagesPdf($filepath){ 
	
        $fp = @fopen(preg_replace("/\[(.*?)\]/i", "",$filepath),"r"); 
        $max=0; 
        while(!feof($fp)) { 
            $line = fgets($fp,255); 
            if (preg_match('/\/Count [0-9]+/', $line, $matches)){
                 preg_match('/[0-9]+/',$matches[0], $matches2); 
                 if ($max<$matches2[0]) $max=$matches2[0]; 
                } 
            } 
            fclose($fp); if($max==0){ 
                $im = new imagick($filepath); 
                // $max=$im->getNumberImages(); 
            } return 
            $max; 
        }


    //     function unirArchivos($rutas, $nombre, $ruc , $rutaCarpetaFiscal){
	// 		$rutaCarpetaFiscalTemp=$rutaCarpetaFiscal."/temp";
	// 	//	$ruta= Cambiar_Ruta_Despliegue($this->RUTA_CARPETA_FISCAL."/nombre.pdf",$ruc);
	// 	//	$rutaAbsoluta = Ruta_Absoluta($this->RUTA_CARPETA_FISCAL."/nombre.pdf",$ruc);
	// 		$rutaAbsoluta = Cambiar_Ruta_Absoluta($rutaCarpetaFiscal,$ruc);
	// 		$rutaAbsolutaTmp = Cambiar_Ruta_Absoluta($rutaCarpetaFiscalTemp,$ruc);
			
			
	// 		$destino = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);
	// 		$SALIDA_PDF = $destino.chr(47).'tramitacion/MPDF/LISTAPDF2/descargaPDF';
	// 		$FICHERO_PDFTK = $destino.chr(47).'tramitacion/MPDF/LISTAPDF2/pdftk/pdftk.exe';

    //         $documentos = "";
	// 		$strExplodePath=substr($rutaCarpetaFiscal, 3);
    //         foreach($rutas as $clave => $value){
	// 			//se consulta el http del documento creadodocumento creado
	// 			$split_=explode($strExplodePath, $value);
	// 			if(count($split_)>0){
	// 				$Cambiar_Ruta_Despliegue= Cambiar_Ruta_Despliegue($value,$ruc);
	// 				$documentos = $documentos.$Cambiar_Ruta_Despliegue.' ';
	// 			}else{
	// 				$documentos = $documentos.$value.' ';
	// 			}
                
    //         } 
			
	// 		$SALIDA_PDF= $rutaAbsoluta;
	// 		//echo $FICHERO_PDFTK.' '.$documentos.'cat output '.$SALIDA_PDF.chr(47).$nombre;
    //         $pdf_info = exec($FICHERO_PDFTK.' '.$documentos.'cat output '.$SALIDA_PDF.chr(47).$nombre);

    //         return $SALIDA_PDF.chr(47).$nombre;

    //     }

    //     function sacarPdfs($valor){
    //         $resultado = str_replace(chr(92), chr(47), $valor);
    //         $resultado = end(explode(chr(47),$resultado));
    //         $resultado = end(explode(".",$resultado));
    //         return $resultado;
    //     }

    //     function valoresQuery($sql){
    //             $resultado = mysql_query($sql, $this->conexion);
    //             $result = mysql_fetch_assoc($resultado);
    //             return $result;
    //             // mysql_close($this->conexion);
    //     }

    //     function InsertarQuery($sql){
    //         $resultado = mysql_query($sql, $this->conexion);
    //         $fila = mysql_affected_rows();;
    //         if($fila > 0){
    //             return "ok";
    //         }else{
    //             return "error";
    //         }
    //         // mysql_close($this->conexion);
    // }
        
       
        
}

?>