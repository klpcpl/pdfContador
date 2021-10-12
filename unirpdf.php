<?php

include_once('contar_pag.php');

class ItemPDF extends Imagick{

    public $resultado = [
        "status" => "ok", 
        "nombre"=> ""
        ];
    public $codigo = "200";
    public $sumaTotalParcial = 0;
    public $sumaAterior = 0;
    public $lista_num_pag = []; // numero de paginas
    public $rutas; // rutas totales
    public $nuevaPagEliminadas = [];
	public $idTipoDoc = [];
	public $idSisdcto = [];
	public $nomPdfs = [];
	public $nombre = "";
	public $ruc = "";
	public $idUsuario = "";
	public $dataInfo = [];
	public $idRecuperado;
	public $RUTA_CARPETA_FISCAL='../carpeta_fiscal';
	//public $RUTA_CARPETA_FISCAL='';
	public $idOrigenDoc = "";
	public $idMenu = "";
	
    // salida del evento complidar
    function salida(){
		//mysql_close($this->conexion);
        header('Content-Type: application/json');
        echo json_encode($this->resultado, http_response_code($this->codigo));
    }

	public function getPdFusionado($allData){

					// $this->rutas = json_decode($item,true);
					$item = json_decode($allData["item"],true);
					$this->nombre = $allData["nombre"];
					$this->ruc = $allData["ruc"];
					$this->idUsuario = $allData["idUsuario"];
					$this->idOrigenDoc = $allData["id_origen_doc"];
					$this->idMenu = $allData["id_menu"];
					
					$this->rutas = $item[0]; // lista de rutas del archivo
					$this->idTipoDoc = $item[1];
					$this->nomPdfs = $item[2];
					$this->idSisdcto = $item[3];
					$this->dataInfo = $item[4];
					
					$valor_suma_dataInfo = array_sum($this->dataInfo);
					 
					
			////////////////////////////////////////////////////////////////////////////////////////////
			///////////                   IMPORTANTE OJO CON ESTO  ///////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////
			///////////////////////////  FALTA VERIFICACION DE ARCHIVOS EXISTE EN LAS RUTAS 0 SI SON 
			///////////////////////////  DE TIPO PDF O pdf
			////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////
			
					//Se recupera le la ruta donde va a quedar el archivo
					$queryDocMenu = "select * from origen_docs_carp_fiscal where  id_origen_doc=".$this->idOrigenDoc." and id_menu=".$this->idMenu; 
					$resultDocMenu = parent::valoresQuery($queryDocMenu);  
					$consultarutaPrincipal = $resultDocMenu['ruta_principal'];
					$this->RUTA_CARPETA_FISCAL=$consultarutaPrincipal;
					
				
					//SI EXISTE ARCHIVO EN CARPETA FISCAL DETERMINANDO SI ES MAYOR QUE 0
					if($valor_suma_dataInfo > 0){
							
						$CF_datos = [];
						foreach($this->dataInfo as $clave => $value){
							if($value > 0){
								$this->idRecuperado = $value;
								break;
							}
						}
					 
					
					
						$query = "SELECT * FROM carpeta_fiscal WHERE id_cptfcl = '".$this->idRecuperado."'"; 
						$CF_datos = parent::valoresQuery($query);   

						$id = $CF_datos['id_cptfcl'];
						$consultaRutaArchivo = $CF_datos['ruta_cptfcl'];
						 

						$query =  "SELECT * 
										FROM carpeta_fiscal CF 
										INNER JOIN detalle_carpeta_fiscal CPF ON (CPF.id_cptfcl=CF.id_cptfcl) 
										WHERE CPF.ID_DET_CPTFCL IN (
																	SELECT MAX(ID_DET_CPTFCL) FROM carpeta_fiscal CF 
																	INNER JOIN detalle_carpeta_fiscal CPF ON (CPF.id_cptfcl=CF.id_cptfcl) 
																	WHERE CF.id_cptfcl=".$id.")";
			 
						$consulta = parent::valoresQuery($query);
						// SI TIENE VALORES CAMBI LOS VALORES DETALLE DE BD DETALLE CARPETA FICAL
						if($consulta != ""){
							// extrae la cantidad de paginas de un archivo
							foreach($this->rutas as $clave => $value){
								$numPag=0;
								if( $value == $this->RUTA_CARPETA_FISCAL."/".$consulta['nombre_cptfcl']) {
									$Cambiar_Ruta_Despliegues= Cambiar_Ruta_Despliegue($value,$this->ruc);
									$numPag=parent::getNumPagesPdf($Cambiar_Ruta_Despliegues);
								}
								else{
									$numPag=parent::getNumPagesPdf($value);
								}
								$this->lista_num_pag[] = $numPag;
							}
						
						
						
					
							// =============================================================
							// saca calculos de las hojas para actualización de datos
							foreach($this->rutas as $clave => $valor){
								if($consultaRutaArchivo === $valor){
									$this->sumaTotalParcial += $this->lista_num_pag[$clave];
									break;
								}else{
									$this->sumaTotalParcial += $this->lista_num_pag[$clave];
									$this->sumaAterior += $this->lista_num_pag[$clave];
								}
								
							}
							

							// ACTUALIZA LOS DATOS DE DETALLE
							$nuevaListaConte = [];
							$ListaConte = json_decode($consulta['detalle'],true);
							for($pag = 1; $pag <= $this->sumaTotalParcial; $pag++ ){
								for($y = 0; $y < count($ListaConte); $y++){
									if($ListaConte[$y]["Pag"] == $pag){
										$tempList = $ListaConte[$y];
										$tempList["Pag"] = $pag + $this->sumaAterior;	
										array_push($nuevaListaConte, $tempList);
									}
								}
							}
							
							
							
							// ACTUALIZA LOS DATOS DE PAG ELIMINADAS DE  DE PTF
							$pagEliminadas = $consulta['pag_eliminadas'];
							if($pagEliminadas != ""){
								$ListaPagEliminada = json_decode($pagEliminadas,true);
								foreach( $ListaPagEliminada as $clave => $valor){
								$this->nuevaPagEliminadas[] =  $valor + $this->sumaAterior;
								}
							}
								
														
							
							// inserta nuevo registo en DCF CON NUEVOS VALORES
							$id_cptfcl_dcf = $consulta['id_cptfcl'];
							$lista_num_pag_cdf = array_sum($this->lista_num_pag);
							$nuevaListaConte_cdf = json_encode($nuevaListaConte, JSON_UNESCAPED_UNICODE );
							if (count($this->nuevaPagEliminadas)>0){
								//$nuevaPagEliminadas_cdf = json_encode($this->nuevaPagEliminadas);
								
								$nuevaPagEliminadasTemp=[];
								foreach( $this->nuevaPagEliminadas as $clave => $valor){
									$nuevaPagEliminadasTemp[] = strval($valor);
								}
								
								//$nuevaPagEliminadas_cdf = json_encode($this->nuevaPagEliminadas);
								$nuevaPagEliminadas_cdf = json_encode($nuevaPagEliminadasTemp);
								
								
							}else{
								$nuevaPagEliminadas_cdf = "";
							}
							/*
							    $nuevaPagEliminadasTemp=[];
								foreach( $this->nuevaPagEliminadas as $clave => $valor){
									$nuevaPagEliminadasTemp[] = '"'.$valor.'"';
								}
								
								//$nuevaPagEliminadas_cdf = json_encode($this->nuevaPagEliminadas);
								$nuevaPagEliminadas_cdf = json_encode($nuevaPagEliminadasTemp);
							
							*/
							
							$hist_pag_eliminadas_cdf = $consulta['hist_pag_eliminadas'];

							$ingrsoDatos = true;
							//  inicio de los COMIT O ROLLBACK CON PARAMETROS
							$null = mysql_query("START TRANSACTION", $this->conexion);
							mysql_query("BEGIN", $this->conexion);
						   
							$query = "INSERT INTO `detalle_carpeta_fiscal` (`id_cptfcl`, `total_pag`, `detalle`, `fec_creacion`, `pag_eliminadas`, `hist_pag_eliminadas`) VALUES (".$id_cptfcl_dcf.",".$lista_num_pag_cdf.",'".$nuevaListaConte_cdf."',CURRENT_TIMESTAMP,'".$nuevaPagEliminadas_cdf."', '".$hist_pag_eliminadas_cdf."')";

							// AGREGA LOS NUEVOS DATOS ACTUALIZADOS A DETALLE CARPETA FISCAL
							mysql_query($query, $this->conexion);
							$fila = mysql_affected_rows();
							if($fila == 0){ $ingrsoDatos = false; }

							// ACTUALIZA PARAMETROS A CARPETA FISCAL
							$sum = $consulta['nro_dcto'] + 1;
							$query2 = "UPDATE `carpeta_fiscal` SET `nro_dcto`= '".$sum."' ,`fec_modificacion`= CURRENT_TIMESTAMP, `id_usuario_modificacion` =".$this->idUsuario." WHERE `id_cptfcl`= $id_cptfcl_dcf";
							mysql_query($query2, $this->conexion);
							$fila = mysql_affected_rows();
							if($fila == 0){ $ingrsoDatos = false; }

							// CREA DATOS EN LA CARPETA 

							for($x = 0; $x < count($this->rutas); $x++){
								$query = "INSERT INTO `docs_carpeta_fiscal`(`id_cptfcl`, `nombre_documento`, `ruta_documento`, `orden_documentos`,`fec_creacion`, `id_sisdcto`, `id_tipo_documento`) VALUES ('".$id_cptfcl_dcf."','".$this->nomPdfs[$x]."','".$this->rutas[$x]."','".($x+1)."', CURRENT_TIMESTAMP ,'".$this->idSisdcto[$x]."','".$this->idTipoDoc[$x]."')";
								
								mysql_query($query, $this->conexion);
								$fila = mysql_affected_rows();
								if($fila == 0){ $ingrsoDatos = false; }
							}

							$elNombre = $consulta['nombre_cptfcl'];
							$ruta_pdf = $consulta['ruta_cptfcl'];
							$pdfCreado = parent::unirArchivos($this->rutas, $elNombre, $this->ruc, $this->RUTA_CARPETA_FISCAL);

							if(!is_file($pdfCreado)){
								$ingrsoDatos = false;
							}
							if($ingrsoDatos){
								mysql_query("COMMIT", $this->conexion);
								//mysql_close($this->conexion);
								$this->resultado = ["status" => "ok", "nombre"=>$elNombre, "ruta"=>$ruta_pdf, "id"=>$this->idRecuperado, "ruc" => $this->ruc ];   
								$this->codigo = "200";
							}else{
								mysql_query("ROLLBACK",$this->conexion);
								//mysql_close($this->conexion);
								$this->resultado = ["status" => "error",  "nombre"=>"Error en unir archivos"];
								$this->codigo = "400";

							}
							mysql_close($this->conexion);
							$this->salida();
						
						}else{
						   // NO TIENE CAMBIO DE VALORES EN BD DETALLE CARPETA FISCAL Archivos eliminados
							$id = $CF_datos['id_cptfcl'];
							$ingrsoDatos = true;
							//  inicio de los COMIT O ROLLBACK CON PARAMETROS
							$null = mysql_query("START TRANSACTION", $this->conexion);
							mysql_query("BEGIN", $this->conexion);

							$sum = $CF_datos['nro_dcto'] + 1;
							$query2 = "UPDATE `carpeta_fiscal` SET `nro_dcto`= '".$sum."' ,`fec_modificacion`= CURRENT_TIMESTAMP , `id_usuario_modificacion` =".$this->idUsuario." WHERE `id_cptfcl`= $id";
							mysql_query($query2, $this->conexion);
							$fila = mysql_affected_rows();
							if($fila == 0){ $ingrsoDatos = false; }

							// CREA DATOS EN LA CARPETA 

							for($x = 0; $x < count($this->rutas); $x++){			
								$query = "INSERT INTO `docs_carpeta_fiscal`(`id_cptfcl`, `nombre_documento`, `ruta_documento`, `orden_documentos`,`fec_creacion`, `id_sisdcto`, `id_tipo_documento`) VALUES ('".$id."','".$this->nomPdfs[$x]."','".$this->rutas[$x]."','".($x+1)."', CURRENT_TIMESTAMP ,'".$this->idSisdcto[$x]."','".$this->idTipoDoc[$x]."')";
								mysql_query($query, $this->conexion);
								$fila = mysql_affected_rows();
								if($fila == 0){ $ingrsoDatos = false; }
							}

							$elNombre = $CF_datos['nombre_cptfcl'];
							$pdfCreado = parent::unirArchivos($this->rutas, $elNombre, $this->ruc, $this->RUTA_CARPETA_FISCAL);
							
							////$rutaArchivopdf = 'http://localhost/tramitacion/MPDF/LISTAPDF2/descargaPDF'.chr(47).$elNombre;
							//$rutaArchivopdf = '../carpeta_fiscal'.chr(47).$elNombre;
							$rutaArchivopdf = $this->RUTA_CARPETA_FISCAL.chr(47).$elNombre;
							
							if(!is_file($pdfCreado)){
								$ingrsoDatos = false;
							}
							if($ingrsoDatos){
								mysql_query("COMMIT", $this->conexion);
								//mysql_close($this->conexion);
								$this->resultado = ["status" => "ok", "nombre"=>$elNombre, "ruta"=>$rutaArchivopdf, "id"=>$id, "ruc" => $this->ruc ];
								$this->codigo = "200";
							}else{
								mysql_query("ROLLBACK",$this->conexion);
								//mysql_close($this->conexion);
								$this->resultado = ["status" => "error",  "nombre"=>"Error en unir archivos"];
								$this->codigo = "400";

							}
							mysql_close($this->conexion);
							$this->salida();

						} 
						// NO EXISTE ARCHIVO CREADO EN CARPETA FISCAL , CREA POR PRIMERA VEZ EL ARCHIVO CUANDO NO EXISTE
					}else{
					   
						$ingrsoDatos = true;
						//  inicio de los COMIT O ROLLBACK CON PARAMETROS
						$null = mysql_query("START TRANSACTION", $this->conexion);
						mysql_query("BEGIN", $this->conexion);


						$pdfCreado = parent::unirArchivos($this->rutas, $this->nombre, $this->ruc, $this->RUTA_CARPETA_FISCAL);

						if(!is_file($pdfCreado)){
							$ingrsoDatos = false;
						}
						////$rutaArchivopdf = 'http://localhost/tramitacion/MPDF/LISTAPDF2/descargaPDF'.chr(47).$this->nombre;
						//$rutaArchivopdf = '../carpeta_fiscal'.chr(47).$this->nombre;
						$rutaArchivopdf = $this->RUTA_CARPETA_FISCAL.chr(47).$this->nombre;
						
						$nro_dcto = 1;
						$id_tipodoct = 1;
						$idf_rolunico = $this->ruc;
						$id_usuario = $this->idUsuario;
						$cod_comuna = 1;
						$id_origen_doc = $this->idOrigenDoc;

						$query3 = "INSERT INTO `carpeta_fiscal`(`nro_dcto`, `id_tipodoct`, `fec_creacion`, `fec_modificacion`, `idf_rolunico`, `nombre_cptfcl`, `ruta_cptfcl`, `id_usuario_creacion`, `id_usuario_modificacion`, `cod_comuna`, `id_origen_doc`) VALUES ('".$nro_dcto."','".$id_tipodoct."',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'".$idf_rolunico."','".$this->nombre."','".$rutaArchivopdf."','".$id_usuario."','".$id_usuario."','".$cod_comuna."','".$id_origen_doc."')";

							mysql_query($query3, $this->conexion);
							$id = mysql_insert_id();
							$fila = mysql_affected_rows();
							if($fila == 0){ $ingrsoDatos = false; }

						for($x = 0; $x < count($this->rutas); $x++){

							$query = "INSERT INTO `docs_carpeta_fiscal`(`id_cptfcl`, `nombre_documento`, `ruta_documento`, `orden_documentos`,`fec_creacion`, `id_sisdcto`, `id_tipo_documento`) VALUES ('".$id."','".$this->nomPdfs[$x]."','".$this->rutas[$x]."','".($x+1)."', CURRENT_TIMESTAMP ,'".$this->idSisdcto[$x]."','".$this->idTipoDoc[$x]."')";

							mysql_query($query, $this->conexion);
							 $fila = mysql_affected_rows();
							 if($fila == 0){ $ingrsoDatos = false; }
						}

						if($ingrsoDatos){
							mysql_query("COMMIT", $this->conexion);
							mysql_close($this->conexion);
							$this->resultado = ["status" => "ok", "nombre"=> $this->nombre, "ruta"=>$rutaArchivopdf, "id"=>$id, "ruc" => $this->ruc ];
							$this->codigo = "200";
						}else{
							mysql_query("ROLLBACK",$this->conexion);
							mysql_close($this->conexion);
							$this->resultado = ["status" => "error",  "nombre"=>"Error en unir archivos"];
							$this->codigo = "400";

						}
						 
						$this->salida();
					
					}

				
	
	}
}



//=====================================================================================================
if(isset($_POST["nombre"])){
	     $allData=  $_POST;		
		 $PDF_resultado = new ItemPDF();
		 $PDF_resultado -> getPdFusionado($allData);	

}