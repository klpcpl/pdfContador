<?php

class Gdd{

    public $username;
    public $grant_type;
    public $password;

    public $access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2MzQ2ODEwMzYsInVzZXJfbmFtZSI6ImFwYWxtYSIsImF1dGhvcml0aWVzIjpbIlJPTF9HRU5FUkFMIl0sImp0aSI6IjNkM2YxNTA3LTU4OWYtNDJkNi1hZWY3LTQ2ZmRhNzNhOTg0OSIsImNsaWVudF9pZCI6ImF1dGVudGljYWRvckZyb250Iiwic2NvcGUiOlsicmVhZCIsIndyaXRlIl19.9V0b0SbQS0VeCngs_Yo6BkPuYxRYg3jzwO0JMYT7sYw";
    public $token_type;
    public $refresh_token;
    public $expires_in;
    public $scope;
    public $jti;
    public $rutaRaiz = "http://172.18.8.164/api_gdd/";
    public $respuesta;
    public $isError=false;


    function __construct($typo, $nombre, $clave){
        $this->grant_type = $typo;
        $this->username = $nombre;
        $this->password =  $clave;

        // $this->servicioToken();
        $this->respuesta = "ok";
    }

    #Servicio de token (token).
    function servicioToken(){
        $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://autenticadorFront:secret@172.18.1.125:8080/autenticador-api/oauth/token?grant_type='.$this->grant_type.'&username='.$this->username.'&password='.$this->password.'',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $data = json_decode( $response, true );

            # esto es para pasar nada mas
            $data = "estamosOk";

            if($data == ""){
                $this->respuesta = "No tiene acceso";
            }
            elseif(isset($data["error_description"])) {
                $this->respuesta = $data["Mensaje"];
            }else{
                // $this->access_token = $data["access_token"];
                // $this->token_type = $data["token_type"];
                // $this->refresh_token = $data["refresh_token"];
                // $this->expires_in = $data["expires_in"];
                // $this->scope = $data["scope"];
                // $this->jti = $data["jti"];

                $this->respuesta = "ok";
            }

    }

   #function de curl de salida GET
   function init_Curl_Get($parametro){

    $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $parametro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
           /* CURLOPT_HTTPHEADER => array(
                'token: SI3Lki0xvyhr8Ih/pKsZBEg325Y='
            ),*/
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode( $response, true );
        return $data;

    }

#function de curl de salida POST
    function init_Curl_Post($ruta, $parametro){
        $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL =>$ruta,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $parametro,
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $data = json_decode( $response, true );
            return $data;
    }

    function isError($result){
        if( isset( $result["error"]) ){
            $this->isError = true;
        }
        
    
    /* {
    "error": "error_token",
    "Mensaje": "No es valido el token: B4Ncn6GPfSfJdUkCeEvVytnvMgI"
    }
    {
    "error": "error_token",
    "Mensaje": "No es valido el token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2MzQxNTM5OTUsInVzZXJfbmFtZSI6ImFwYWxtYSIsImF1dGhvcml0aWVzIjpbIlJPTF9HRU5FUkFMIl0sImp0aSI6IjY5YjI2Yzc1LTNiZGYtNDk3Yy04ZGY5LTJlNmJkYjQ1MTI3NiIsImNsaWVudF9pZCI6ImF1dGVudGljYWRvckZyb250Iiwic2NvcGUiOlsicmVhZCIsIndyaXRlIl19.f6DIqEdTwJdKbGdHNhYzglLSmlBLben6kdAUUgN301g"
    }
    {
    "error": "error_login",
    "Mensaje": "Usuario no autorizado"
    }*/

    }

    #Servicio inyección de datos para emisión de documentos hacia policías (add_registro).
    function addRegistro($descripcion, $idf_rolunico, $numero_documento,
                                    $id_fiscalia, $id_unidad_externa,$dias_plazo,$crr_idactividad){

        $parametro = array('token' => $this->access_token,
                            'descripcion'  => $descripcion,
                            'idf_rolunico'  => $idf_rolunico,
                            'numero_documento'  => $numero_documento,
                            'id_fiscalia'  => $id_fiscalia,
                            'id_unidad_externa'  => $id_unidad_externa,
                            'dias_plazo'  => $dias_plazo,
                            'crr_idactividad'  => $crr_idactividad);
        $detalle = "add_registro";
        $ruta = $this->rutaRaiz.$detalle;

        return  $this->init_Curl_Post($ruta, $parametro);

    }

    #Servicio inyección de documentos (add_documento).
    function addDocumento($idf_rolunico, $id_add_documento, $doc64, $nombre_doc){

        $parametro = array('token' => $this->access_token,
                            'idf_rolunico' => $idf_rolunico,
                            'doc64' => urlencode($doc64),
                            'nombre_doc' => $nombre_doc,
                            'id_add_documento' => $id_add_documento);
                            print_r($parametro);
        $detalle = "add_documento";
        $ruta = $this->rutaRaiz.$detalle;

        return  $this->init_Curl_Post($ruta, $parametro);
    }

    #Obtención partes (get_partes).
    function getPartes($fechaI, $fechaF, $id){
        $token = "token=".$this->access_token;
        $detalle = "get_partes?";
        $fechaInicio = "&fecha_inicio=".$fechaI;
        $fechaFin = "&fecha_fin=".$fechaF;
        $idFiscalia = "&id_fiscalia=".$id;

        $pedido = $this->rutaRaiz.$detalle.$token.$idR;
        return  $this->init_Curl_Get($pedido);

    }

    #Obtención de respuesta a solicitudes mediante fechas (get_respuestas):
    function getRespuesta($fechaI, $fechaF, $id){
        $token = "token=".$this->access_token;
        $detalle = "get_respuestas?";
        $fechaInicio = "&fecha_inicio=".$fechaI;
        $fechaFin = "&fecha_fin=".$fechaF;
        $idFiscalia = "&id_fiscalia=".$id;

        $pedido = $this->rutaRaiz.$detalle.$token.$idR;
        return  $this->init_Curl_Get($pedido);

    }

    #Obtención de respuesta a solicitudes mediante RUC (get_respuesta_ruc).
    function getRespuestaRuc($rol){

        $token = "token=".$this->access_token;
        $detalle = "get_respuesta_ruc?";
        $idR = "&idf_rolunico=".$rol;    
        $pedido = $this->rutaRaiz.$detalle.$token.$idR;		
        $result=$this->init_Curl_Get($pedido);
        $this->isError($result);
        return  $result;
    }

    #Obtención de respuesta a solicitudes mediante id_tramitacion (get_respuesta_id).
    function getRespuestaId($id){
        $token = "token=".$this->access_token;
        $detalle = "get_respuesta_id?";
        $idR = "&id_tramitacion=".$id;

        $pedido = $this->rutaRaiz.$detalle.$token.$idR;
        return  $this->init_Curl_Get($pedido);

    }

    #Obtención de documento (get_documento).
    function getDocumento($id){
        $token = "token=".$this->access_token;
        $detalle = "get_documento?";
        $idR = "&id=".$id;

        $pedido = $this->rutaRaiz.$detalle.$token.$idR;
        return  $this->init_Curl_Get($pedido);
    }

    #Obtención de unidades policiales (get_unidades).
    function getUnidad($id){
        $token = "token=".$this->access_token;
        $detalle = "get_unidades?";
        $idR = "&id_region=".$id;

        $pedido = $this->rutaRaiz.$detalle.$token.$idR;
        return  $this->init_Curl_Get($pedido);
    }
}


/*
$inicio = new Gdd("password","apalma","jwtpass");
$inicio->getUnidad(10); // Consulto la ID
$inicio->getDocumento({'DFF3DB26-C784-46B3-8FB3-6039E2BC8444'}); // get Unidad
$inicio->getRespuestaId(247); // get id
$inicio->getRespuestaRuc(1-9); // rol unico
$inicio->getRespuesta(20200101, 20201030, 1001); // rol unico
$inicio->getPartes(20200101, 20201030, 1001); // rol unico

//=================================
//=========== los post ============
//=================================

$inicio->addDocumentoInyeccion(1-9, 'archivo.pdf', 20); // envio los datos del post
$inicio->addRegistroInyeccionDoc("prueba",1-9,30,10,1,17); // envio los datos del post
*/
