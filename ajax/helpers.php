<?php
namespace Ajax;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Capsule\Manager as Capsule;

// include(dirname(__FILE__).'/../config/constants.php');

class Helpers {
	private $perfil;

	public function __construct(){
		$this->perfil = Capsule::table('perfil')->where('id_perfil','=',1)->get()[0];
	}
	/**
	 *  Función global para hacer login en la API.
	*/
	public static function LoginApi(){
		$user_info = self::getUserInfo(1);
		$client = new Client;
		$requestLoginApi = $client->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        'r' 		=> 'users_log_me_in',
		        'w' 		=> 'users',
		        'userName'	=> $user_info->usernameAPI,
		        'pwd'		=> $user_info->passwordAPI
		    ]
		]);
		$responseLonginApi = json_decode($requestLoginApi->getBody());
		return $responseLonginApi;
	}

	/**
	 *  Obtiene toda la información de un usuario desde BD.
	*/
	public static function getUserInfo($id_user){
		return Capsule::table('perfil')->where('id_perfil','=',$id_user)->first();
	}

	public static function uploadCertificateToApi($sessionKey, $file, $type){
		$clientHttp = new Client; //Inicia el cliente HTTP para request
		$requestUploadApi = $clientHttp->request('POST', getenv('API_BASE_URL'), [
		    'multipart' => [
		        [
		            'name'     => 'w',
		            'contents' => 'fileUploader'
		        ],
		        [
		            'name'     => 'r',
		            'contents' => 'subir_certif'
		        ],
		        [
		            'name'     => 'sessionKey',
		            'contents' => $sessionKey->sessionKey
		        ],
		        [
		            'name'     => 'iam',
		            'contents' => $sessionKey->userName
		        ],
		        [
		            'name'     => 'fileToUpload',
		            'contents' => fopen($file,'r')
		        ],
		    ]
		]);
		$responseLonginApi = json_decode($requestUploadApi->getBody());
		return $responseLonginApi->resp;
	}

	public static function setFileToUpload($file){
		// Detalles del archivo a subir
		$fileTmpPath 	= $file['tmp_name'];
		$fileName 		= $file['name'];
		$fileSize 		= $file['size'];
		$fileType 		= $file['type'];

		//Para determinar la extensión
		$fileNameCmps 	= explode(".", $fileName);
		$fileExtension	= strtolower(end($fileNameCmps));

		//Verifica la extensión del archivo
		$allowedfileExtensions = array('p12');
		if (!in_array($fileExtension, $allowedfileExtensions)) {
			echo "Solo se permiten archivos .p12";
			return "error";
		}

		// asigna un nuevo nombre al archivo con la extensión requerida
		// $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

		$uploadFileDir = '../'.getenv('BASE_PATH_TEMPCERT_'); //directorio donde se desea guardar el fichero
		$dest_path = $uploadFileDir . $fileName;

		if(!move_uploaded_file($fileTmpPath, $dest_path)){
		  return 'Ha ocurrido un error al tratar de subir el archivo.';
		}

		return $dest_path;
	}

	public static function validateTokenApi(){

		/*Se requiere ques esté cargado un certificado .p12 antes de realizar cualquier consulta API*/
		if(!self::validateIfExistsP12()){
			return "No se ha cargado el certificado .p12";
		}

		/*Si no existe ninguna sesión de token, llama a la función getTokenApi()
		 por primera vez iniciando las sesiones. Si existen las sesiones, las asigna
		 a variables*/

		if(
			isset($_SESSION['api_token']) &&
			isset($_SESSION['api_token_expires_in']) &&
			isset($_SESSION['api_token_refresh']) &&
			isset($_SESSION['api_token_refresh_expires_in']) &&
			isset($_SESSION['api_token_generate_date'])
		){
			$token 				= $_SESSION['api_token'];
			$token_expires_in 	= $_SESSION['api_token_expires_in'];
			$token_refresh 		= $_SESSION['api_token_refresh'];
			$refresh_expires_in = $_SESSION['api_token_refresh_expires_in'];
			$token_date 		= $_SESSION['api_token_generate_date'];
		}else{
			return $res_token = self::getTokenApi('create');
		}

		/* Usa $token si time() es < $token_expires_in*/
		if (time() < ($token_date + $token_expires_in) ) {
			// echo "Token inicial.";
		 	return true;

		/* Solicita un refresh_token con la función getRefreshTokenApi() si time()
		 es >= $token_expires_in y si time() es < $refresh_expires_in*/
		}elseif (time() >= ($token_date + $token_expires_in) && time() < ($token_date + $refresh_expires_in) ){
		 	return $res_token = self::getTokenApi('refresh');
		 	// var_dump($res_token);

		 /* Genera un nuevo token con la función getTokenApi() si time() es >= $refresh_expires_in*/
		}elseif ( time() >= ($token_date + $refresh_expires_in) ) {
			return $res_token = self::getTokenApi('create');
			// var_dump($res_token);
		}
	}

	public static function getTokenApi($getToken_by){
		/*Determina el modo en que debe traer el token*/
		if($getToken_by == 'create'){
			$resGetTokenApi = self::requestTokenApi()->resp;
		}else if($getToken_by == 'refresh'){
			$resGetTokenApi = self::requestRefreshTokenApi()->resp;
		}

		$exist_token = true;

		/*comprueba que el $resGetTokenApi contenga un campo llamado access_token y que ese campo no esté vacío,
		si está vacío reintenta hasta tres veces*/
		if(!property_exists($resGetTokenApi, "access_token") || empty($resGetTokenApi->access_token)){
			for ($i=1; $i <= getenv('TOKEN_ATTEMPS'); $i++) {
				$resGetTokenApi = self::requestTokenApi()->resp;
				if(property_exists($resGetTokenApi, "access_token") || empty($resGetTokenApi->access_token)){
					$exist_token = true;
					break;
				}else{
					$exist_token = false;
				}
			}
		}

		/*Dependiendo de si se pudo extraer el token, por medio de la variable $exist_token determinamos si asignamos
		los nuevos datos de token a las variables de sesión o si emitimos un error, también en una variable de sesión*/
		if ($exist_token && property_exists($resGetTokenApi, "access_token")) {
			$_SESSION['api_token'] 						= $resGetTokenApi->access_token;
			$_SESSION['api_token_expires_in'] 			= $resGetTokenApi->expires_in;
			$_SESSION['api_token_refresh'] 				= $resGetTokenApi->refresh_token;
			$_SESSION['api_token_refresh_expires_in']	= $resGetTokenApi->refresh_expires_in;
			$_SESSION['api_token_generate_date'] 		= time();

			$res_server = [
				"resp" => [
					"res"		=> "ok",
					"message"	=> "Token recuperado mediante: ".$getToken_by.". Sesiones iniciadas"
				]
			];
		}else{
			$res_server = [
				"resp" => [
					"res"		=> "error",
					"message"	=>	"No fue posible recuperar un token desde el servidor"
				]
			];
		}
		return $res_server;
	}

	public static function requestTokenApi(){
		$user_info = self::getUserInfo(1); //Recibe toda la información del perfil del usuario
		$client = new Client; //Inicia el cliente HTTP para request
		$requestTokenApi = $client->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        'r' 			=> 'gettoken',
		        'w' 			=> 'token',
		        'grant_type'	=> 'password',
		        'client_id'		=> getenv('API_ENVIRONMENT'),
		        'username'		=> $user_info->key_username,
		        'password'		=> $user_info->key_password
		    ]
		]);

		$responseTokenApi = json_decode($requestTokenApi->getBody());
		return $responseTokenApi;
	}

	public static function requestRefreshTokenApi(){
		$client = new Client; //Inicia el cliente HTTP para request
		$requestRefreshTokenApi = $client->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        'r' 			=> 'refresh',
		        'w' 			=> 'token',
		        'grant_type'	=> 'refresh_token',
		        'client_id'		=> getenv('API_ENVIRONMENT'),
		        'refresh_token'	=> $_SESSION['api_token_refresh'],
		    ]
		]);

		$responseTokenApi = json_decode($requestRefreshTokenApi->getBody());
		return $responseTokenApi;
	}

	public static function validateIfExistsP12(){
		$user_info = self::getUserInfo(1);
		if(empty($user_info->file_p12)){
			unset($_SESSION['EXISTS_FILE_P12']);
			return false;
		}else{
			$_SESSION['EXISTS_FILE_P12'] = true;
			return true;
		}
	}

	/**
	*  Obtiene el número de consecutivo partiendo de parámetros entregados.
	*
	* @param integer $consecutive.
	* @param string  $tipo_comprobante.
	* @param integer $local.
	* @param integer $terminal.
	*
	* @see validateConsecutiveNumber()
	* @see writeErrorLog()
	*/
	public static function getConsecutiveNumber($consecutive, $tipo_comprobante, $local, $terminal){

		$result_valid = self::validateConsecutiveNumber($consecutive, $tipo_comprobante, $local, $terminal);

		if(!$result_valid){
			return "Error validando los campos validateConsecutiveNumber()";
		}

		// if($consecutive == 9999999999){
		// 	$new_consecutive = 1;
		// }else{
		// 	$new_consecutive = $consecutive + 1;
		// }

		$pad_final_tipo_comprobante	= str_pad( (string) $tipo_comprobante, 2, '0', STR_PAD_LEFT);
		$pad_final_local 			= str_pad( (string) $local, 3, '0', STR_PAD_LEFT);
		$pad_final_terminal 		= str_pad( (string) $terminal, 5, '0', STR_PAD_LEFT);
		$pad_final_consecutive		= str_pad( (string) $consecutive, 10, '0', STR_PAD_LEFT);

		return $pad_final_local.$pad_final_terminal.$tipo_comprobante.$pad_final_consecutive;
	}

	/**
	*  Valida los parámetros recibidos para la función getConsecutiveNumber().
	*
	* @param integer $consecutive.
	* @param string  $tipo_comprobante.
	* @param integer $local.
	* @param integer $terminal.
	*
	* @see writeErrorLog()
	*/
	public static function validateConsecutiveNumber($consecutive, $tipo_comprobante, $local, $terminal){

		if(gettype($consecutive) != "integer" || gettype($local) != "integer" || gettype($terminal) != "integer"){
			return self::writeErrorLog('consecutivo, local y terminal deben ser de tipo integer.');
		}elseif (gettype($tipo_comprobante) != "string") {
			return self::writeErrorLog('tipo_comprobante debe ser de tipo string.');
		}

		$tipos_comprobantes= array("01","02","03","04","05","06","07");
		if(!in_array($tipo_comprobante, $tipos_comprobantes)){
			return self::writeErrorLog('tipo_comprobante debe estar entre 01 y 07.');
		}

		if($consecutive < 1 || $consecutive > 9999999999){
			return self::writeErrorLog('consecutivo no debe ser menor que 1 ni mayor que 9999999999.');
		}

		if ($local < 1 || $local > 999) {
			return self::writeErrorLog('local no debe ser menor que 1 ni mayor que 999.');
		}

		if ($terminal < 1 || $terminal > 99999) {
			return self::writeErrorLog('terminal no debe ser menor que 1 ni mayor que 99999.');
		}

		return true;
	}

	/**
	*  Inicia conexión con api para retornar clave Númerica y Consecutivo.
	*
	* @param string  $type_id.
	* @param integer $id_client.
	* @param integer $country.
	* @param integer $consecutive.
	* @param integer $document_type.
	* @param string  $previousNumericKey.
	*
	* @return Object [clave, consecutivo]
	* @see validategetNumericKey()
	* @see checkStatusForNumericKey()
	*/
	public static function getNumericKey($type_id, $id_emisor, $country, $consecutive, $document_type, $previousNumericKey=null){

		$result_valid	= self::validategetNumericKey($type_id, $id_emisor, $country, $consecutive, $document_type);
		$situacion 		= self::checkStatusForNumericKey($previousNumericKey);

		if($situacion == "sininternet" && $previousNumericKey != null){
			return;
		}

		if(!$result_valid){
			return "Error validando los campos validategetNumericKey()";
		}

		if($situacion == "error"){
			return "No se pudo establecer una situación para la generación de la clave numérica.";
		}

	 	$clientHttp = new Client;
	 	try {
	 	   $requestNumericKey = $clientHttp->request('POST', getenv('API_BASE_URL'), [
	 	       'multipart' => [
	 	           [
	 	               'name'     => 'w',
	 	               'contents' => 'clave'
	 	           ],
	 	           [
	 	               'name'     => 'r',
	 	               'contents' => 'clave'
	 	           ],
	 	           [
	 	               'name'     => 'tipoCedula',
	 	               'contents' => $type_id
	 	           ],
	 	           [
	 	               'name'     => 'cedula',
	 	               'contents' => $id_emisor
	 	           ],
	 	           [
	 	               'name'     => 'codigoPais',
	 	               'contents' => $country
	 	           ],
	 	           [
	 	               'name'     => 'consecutivo',
	 	               'contents' => $consecutive
	 	           ],
	 	           [
	 	               'name'     => 'situacion',
	 	               'contents' => $situacion,
	 	           ],
	 	           [
	 	               'name'     => 'codigoSeguridad',
	 	               'contents' => getenv('NUMERIC_KEY_CODE'),
	 	           ],
	 	           [
	 	               'name'     => 'tipoDocumento',
	 	               'contents' => $document_type,
	 	           ],
	 	           [
	 	               'name'     => 'terminal',
	 	               'contents' => getenv('BASE_TERMINAL'),
	 	           ],
	 	           [
	 	               'name'     => 'sucursal',
	 	               'contents' => getenv('BASE_LOCAL'),
	 	           ],
	 	       ]
	 	   ]);
	 	   $NumericKey = json_decode($requestNumericKey->getBody());
	 	}catch(ClientException $e){
	 	    $response = $e->getResponse();
	 	    return $responseCode = (string)($response->getStatusCode());
	 	}

	 	return $NumericKey->resp;
	}

	/**
	*  Valida los parámetros recibidos para la función getNumericKey().
	*
	* @param string $type_id.
	* @param integer $id_client.
	* @param integer $country.
	* @param integer $consecutive.
	* @param integer $document_type.
	*
	* @return boolean
	* @see getNumericKey()
	* @see writeErrorLog()
	*/
	public static function validategetNumericKey($type_id, $id_client, $country, $consecutive, $document_type){

		if(gettype($id_client) != "integer" || gettype($country) != "integer" || gettype($consecutive) != "integer"){
			return self::writeErrorLog('type_id, id_client y country deben ser de tipo integer.');
		}elseif (gettype($type_id) != "string" || gettype($document_type) != "string") {
			return self::writeErrorLog('type_id y document_type deben ser de tipo string.');
		}

		$types_ids= array("fisico","juridico","dimex","nite");
		if(!in_array($type_id, $types_ids)){
			return self::writeErrorLog('type_id debe estar entre: fisico, juridico, dimex o nite.');
		}

		$documents_types= array("FE","ND","NC","TE","CCE","CPCE","RCE");
		if(!in_array($document_type, $documents_types)){
			return self::writeErrorLog('document_type debe estar entre: FD, ND, NC, TE, CCE, CPCE O RCE.');
		}

		if($consecutive < 1 || $consecutive > 9999999999){
			return self::writeErrorLog('consecutive no debe ser menor que 1 ni mayor que 9999999999.');
		}

		if ($id_client < 1 || $id_client > 999999999999) {
			return self::writeErrorLog('id_client no debe ser menor que 1 ni mayor que 999.');
		}

		if ($country < 1 || $country > 999) {
			return self::writeErrorLog('country no debe ser menor que 1 ni mayor que 99999.');
		}

		return true;
	}

	/**
	*  Determina si la api de hacienda se encuentra online.
	*
	* @return Integer. 1=normal. 3=sininternet.
	*/
	public static function checkConnectionWithApiHacienda(){
		$client = new Client;
		try {
		   $response = $client->request('GET', getenv('URL_CHECK_CONNECTION'));
		   $responseCode = (string)($response->getStatusCode());
		} catch (ClientException $e) {
		    $response = $e->getResponse();
		    $responseCode = (string)($response->getStatusCode());
		}

		if(preg_match('/^2[0-8][0-9]$|^29[0-9]$|^(400)$/', $responseCode)){
			return 1;
		}else{
			return 3;
		}
	}

	/**
	* Determina el status de una clave numérica dada dependiendo de la conexión
	* con hacienda y/o del mismo status pasado en el argumento.
	*
	* @param integer $numeric_key.
	* @return string "normal", "contingencia", "sininternet" o "error".
	* @see checkConnectionWithApiHacienda()
	*/
	public static function checkStatusForNumericKey($numeric_key="00000000000000000000000000000000000000000000000000"){
		$status_numeric_key = substr($numeric_key, 41, 1);
		$status_connection = self::checkConnectionWithApiHacienda();

		//Si no hay conexión con hacienda inmediatamente establece el status como "sin internet".
		if($status_connection == 3){
			return "sininternet";
		}

		//Si hay internet determina el status actual del argumento $numeric_key pasado.
		if($status_numeric_key == 0){
			return "normal";
		}elseif ($status_numeric_key == 3){
			return "contingencia";
		}

		self::writeErrorLog('No se pudo establecer una situación para la generación de la clave numérica.');
		return "error";
	}

	/**
	* Escribe los errores en error_log del sistema.
	*
	* @param string $message.
	* @return boolean False
	*/
	public static function writeErrorLog($message){
		if(getenv('APPLICATION_ENV') == "local-develop"){
			error_log("\n".date('d-m-Y H:i:s')."= ".$message, 3, "./error.log");
		}elseif (getenv('APPLICATION_ENV') == "production") {
			error_log("\n".date('d-m-Y H:i:s')."= ".$message);
		}

		return false;
	}

	public static function getJsonDetalleServicio($num_factura){
		$factura = Capsule::table('facturas')->where('id_factura','=',$num_factura)->get()[0];
		$detalle_factura = Capsule::table('detalle_factura')->where('numero_factura','=',$factura->numero_factura)->get();

		$res_json	= [];
		$linea 		= 0;
		foreach ($detalle_factura as $detalle) {
			//Taremos la información del producto actual
			$producto = Capsule::table('products')->where('id_producto','=',$detalle->id_producto)->first();

			//contador de líneas
			$linea++;

			//Definimos variables operadoras
			$var_monto_total 	= $detalle->cantidad * $producto->precio_colon;
			$var_subotal 		= $var_monto_total - $detalle->monto_descuento;

			//Organizamos el Json
			$json = [];
			$json[$linea] 						= [];
			$json[$linea]['codigo'] 			= (string) $producto->codigo_producto;
			$json[$linea]['codigoComercial']	=  ["1" => [ "tipo" => (string) $producto->tip_cod_comerc_producto, "codigo" => (string) $producto->codigo_producto ] ];
			$json[$linea]['cantidad'] 			= (string) $detalle->cantidad;
			$json[$linea]['unidadMedida'] 		= (string) trim($producto->unidad_medida);
			$json[$linea]['detalle'] 			= (string) $producto->nombre_producto;
			$json[$linea]['precioUnitario'] 	= (string) $producto->precio_colon;
			$json[$linea]['montoTotal'] 		= (string) $var_monto_total;

			//Si aplica descuento, lo declaramos
			if($detalle->monto_descuento != 0){
				$json[$linea]['descuento']		=  ["1" => ["montoDescuento" => (string) $detalle->monto_descuento, "naturalezaDescuento" => (string) $detalle->desc_descuento]];
			}

			$json[$linea]['subtotal'] 			= (string) $var_subotal;

			//Si el impuesto que aplica es IVA o IVA cálculo especial
			if($producto->impuesto_es_iva == 1 && ($producto->impuesto_codigo == "07" || $producto->impuesto_codigo == "01")){

				$var_monto_impuesto = ((int) $producto->precio_colon * (int) $producto->impuesto_iva_tarifa)/100;
				//baseimponible se convierte en obligatorio cuando se seleccione en el campo “Código del impuesto” 07.
				if($producto->impuesto_codigo == "07"){
					$json[$linea]['baseImponible'] 	= (string) $producto->precio_colon;
				}

				$json[$linea]['impuesto']		= ["1" => [
															"codigo" 			=> (string) $producto->impuesto_codigo,
															"codigoTarifa"		=> (string) $producto->impuesto_iva_codigo,
															"tarifa"			=> (string) $producto->impuesto_iva_tarifa,
															// "factorIVA"			=> "",
															"monto"				=> (string) $var_monto_impuesto,
															// "montoExportacion"	=> "",
															// "exoneracion"		=> [
															// 							"tipoDocumento" 		=> "",
															// 							"numeroDocumento" 		=> "",
															// 							"nombreInstitucion" 	=> "",
															// 							"fechaEmision"			=> "",
															// 							"porcentajeExoneracion"	=> "",
															// 							"montoExoneracion" 		=> "",
															// 						]
															]
														];
			}

			//Si el impuesto que aplica no es ningún tipo de IVA
			if($producto->impuesto_es_iva == 0){
				$var_monto_impuesto = ((int) $producto->precio_colon * (int) $producto->imp_subimp_tarifa)/100;
				$json[$linea]['impuesto']		= ["1" => [
															"codigo" 			=> (string) $producto->impuesto_codigo,
															// "codigoTarifa"		=> "",
															"tarifa"			=> (string) $producto->imp_subimp_tarifa,
															// "factorIVA"			=> "",
															"monto"				=> (string) $var_monto_impuesto,
															// "montoExportacion"	=> "",
															// "exoneracion"		=> [
															// 							"tipoDocumento" 		=> "",
															// 							"numeroDocumento" 		=> "",
															// 							"nombreInstitucion" 	=> "",
															// 							"fechaEmision"			=> "",
															// 							"porcentajeExoneracion"	=> "",
															// 							"montoExoneracion" 		=> "",
															// 						]
															]
														];
			}

			$json[$linea]['impuestoNeto']		= "";
			$json[$linea]['montoTotalLinea']	= (string) ($var_subotal + $var_monto_impuesto);

			$res_json += $json;
		}

		return json_encode($res_json, JSON_PRETTY_PRINT);
	}

	public static function getNodeResumenFactura($num_factura){
		$factura = Capsule::table('facturas')->where('id_factura','=',$num_factura)->get()[0];
		$detalle_factura = Capsule::table('detalle_factura')->where('numero_factura','=',$factura->numero_factura)->get();

		$TotalServGravados 		= 0;
		$TotalServExentos 		= 0;
		$TotalServExonerado 	= 0; //No operable por ahora

		$TotalMercanciasGravadas 	= 0;
		$TotalMercanciasExentas 	= 0;
		$TotalMercExonerada 		= 0; //No operable por ahora

		$TotalDescuentos 	= 0;
		$TotalGravado 		= 0;
		$TotalExento 		= 0;
		$TotalExonerado		= 0; //No operable por ahora
		$TotalVenta			= 0;
		$TotalVentaNeta		= 0;
		$TotalImpuesto 		= 0;
		$TotalIVADevuelto 	= 0;
		$TotalOtrosCargos	= 0;
		$TotalComprobante	= 0;

		// $linea = null;
		foreach ($detalle_factura as $detalle) {
			//Taremos la información del producto actual
			$producto = Capsule::table('products')->where('id_producto','=',$detalle->id_producto)->first();

			//Definimos variables operadoras
			$var_monto_total = $detalle->cantidad * $producto->precio_colon;
			// $var_subotal 		= $var_monto_total - $detalle->monto_descuento;

			/*
			*	Sumatoria de productos RELACIONADOS con impuestos IVA.
			*/
			if($producto->impuesto_es_iva == 1 && $producto->impuesto_iva_codigo != "01"){

				/*Suma los totales de productos de tipo SERVICIO */
				if($producto->tipo_producto == "servicio"){
					$TotalServGravados += (int) $var_monto_total;
				}

				/*Suma los totales de productos de tipo MERCANCIA */
				if($producto->tipo_producto == "mercancia"){
					$TotalMercanciasGravadas += (int) $var_monto_total;
				}

				/* Sumatoria de impuestos relacionados con IVA*/
				$TotalImpuesto += $detalle->cantidad * (((int) $producto->precio_colon * (int) $producto->impuesto_iva_tarifa)/100);
				$TotalGravado  += $var_monto_total;
			}

			/*
			*	Sumatoria de productos EXCENTOS de impuesto IVA en cualquiera de sus variaciones.
			*/
			if($producto->impuesto_es_iva == 1 && $producto->impuesto_iva_codigo == "01"){
				if($producto->tipo_producto == "servicio"){
					$TotalServExentos += (int) $var_monto_total;
				}

				if($producto->tipo_producto == "mercancia"){
					$TotalMercanciasExentas += (int) $var_monto_total;
				}

				$TotalExento 		+= $var_monto_total;
			}

			/* Sumatoria de impuestos NO relacionados con IVA*/
			if($producto->impuesto_es_iva == 0){
				$TotalImpuesto += $detalle->cantidad * (((int) $producto->precio_colon * (int) $producto->imp_subimp_tarifa)/100);
			}

			/*Suma todos los descuentos de una factura*/
			if($detalle->monto_descuento != 0){
				$TotalDescuentos += (int) $detalle->monto_descuento;
			}
		}

		$TotalVenta 		= $TotalGravado + $TotalExento + $TotalExonerado;
		$TotalVentaNeta 	= $TotalVenta - $TotalDescuentos;
		$TotalComprobante 	= $TotalVentaNeta + $TotalImpuesto + $TotalOtrosCargos;
		/*Respuesta en formato array*/
		$res_node = [
						"TotalServGravados"			=> $TotalServGravados,
						"TotalServExentos"			=> $TotalServExentos,
						"TotalServExonerado"		=> $TotalServExonerado,
						"TotalMercanciasGravadas"	=> $TotalMercanciasGravadas,
						"TotalMercanciasExentas"	=> $TotalMercanciasExentas,
						"TotalMercExonerada"		=> $TotalMercExonerada,
						"TotalGravado"				=> $TotalGravado,
						"TotalExento" 				=> $TotalExento,
						"TotalExonerado" 			=> $TotalExonerado,
						"TotalVenta" 				=> $TotalVenta,
						"TotalDescuentos"			=> $TotalDescuentos,
						"TotalVentaNeta"			=> $TotalVentaNeta,
						"TotalImpuesto"				=> $TotalImpuesto,
						"TotalIVADevuelto"			=> $TotalIVADevuelto,
						"TotalOtrosCargos"			=> $TotalOtrosCargos,
						"TotalComprobante"			=> $TotalComprobante
					];

		return $res_node;
	}

	public static function createXmlFE(Helpers $helpers, $num_factura){
		$factura 	= Capsule::table('facturas')->where('id_factura','=',$num_factura)->get()[0];
		$receptor 	= Capsule::table('clientes')->where('id_cliente','=',$factura->id_cliente)->get()[0];

		/*Recuperamos la información sobre ubicación*/
		$emisor_ubi		= self::getUbicacion($helpers, "emisor", 1);
		$receptor_ubi 	= self::getUbicacion($helpers, "receptor", $factura->id_cliente);

		/*Recuperamos consecutivo para esta factura*/
		$actual_cons 	= self::getConsecutiveNumber( (int) $factura->numero_factura, "01", (int)getenv('BASE_LOCAL'), (int)getenv('BASE_TERMINAL'));

		/*Recuperamos clave numérica para esta factura*/
		$actual_clave 	= self::getNumericKey( self::getTipoIdEnString($receptor->tipo_cedula_cliente), (int) $helpers->perfil->cedula, 506, (int) $factura->numero_factura, "FE")->clave;

		/*Recuperamos nodo ResumenFactura*/
		$resumen_fact	= self::getNodeResumenFactura($num_factura);

		$clientHTTP = new Client; //Inicia el cliente HTTP para request
		$reqCreateXmlFE = $clientHTTP->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        "r" 						=> "gen_xml_fe",
		        "w" 						=> "genXML",
		        "clave"						=> $actual_clave,
		        "codigo_actividad"			=> $helpers->perfil->codigo_actividad_empresa,
		        "consecutivo"				=> $actual_cons,
		        "fecha_emision"				=> date("Y-m-d")."T".date('H:i:sP'),

		        "emisor_nombre"				=> $helpers->perfil->nombre_empresa,
		        "emisor_tipo_identif"		=> $helpers->perfil->tipo_cedula,
				"emisor_num_identif"		=> $helpers->perfil->cedula,
				"emisor_nombre_comercial"	=> $helpers->perfil->nombre_empresa_comercial,
				"emisor_provincia"			=> $emisor_ubi['Provincia'],
				"emisor_canton"				=> $emisor_ubi['Canton'],
				"emisor_distrito"			=> $emisor_ubi['Distrito'],
				"emisor_barrio"				=> $emisor_ubi['Barrio'],
				"emisor_otras_senas"		=> $helpers->perfil->direccion,
				"emisor_cod_pais_tel" 		=> $helpers->perfil->telefono_cod,
				"emisor_tel"				=> $helpers->perfil->telefono,
				"emisor_cod_pais_fax"		=> $helpers->perfil->telefono_fax_cod,
				"emisor_fax"				=> $helpers->perfil->telefono_fax,
				"emisor_email"				=> $helpers->perfil->email,

				"receptor_nombre"			=> $receptor->nombre_cliente,
				"receptor_nombre_comercial"	=> $receptor->nombre_comercial_cliente,
				"receptor_tipo_identif"		=> $receptor->tipo_cedula_cliente,
				"receptor_num_identif"		=> $receptor->cedula_cliente,
				"receptor_provincia"		=> $receptor_ubi['Provincia'],
				"receptor_canton"			=> $receptor_ubi['Canton'],
				"receptor_distrito"			=> $receptor_ubi['Distrito'],
				"receptor_barrio"			=> $receptor_ubi['Barrio'],
				"receptor_cod_pais_tel"		=> $receptor->telefono_cod_cliente,
				"receptor_tel"				=> $receptor->telefono_cliente,
				"receptor_cod_pais_fax"		=> $receptor->telefono_fax_cod_cliente,
				"receptor_fax"				=> $receptor->telefono_fax_cliente,
				"receptor_email"			=> $receptor->email_cliente,

				"condicion_venta"			=> $factura->condiciones,
				"plazo_credito"				=> $factura->plazo_credito,
				"medios_pago"				=> '[{ "codigo": "'.$factura->medio_pago.'" }]',
				"cod_moneda"				=> $factura->moneda,
				"tipo_cambio"				=> $factura->tipo_cambio,
				"total_serv_gravados"		=> $resumen_fact['TotalServGravados'],
				"total_serv_exentos"		=> $resumen_fact['TotalServExentos'],
				"total_serv_exonerados"		=> $resumen_fact['TotalServExonerado'],
				"total_merc_gravada"		=> $resumen_fact['TotalMercanciasGravadas'],
				"total_merc_exenta"			=> $resumen_fact['TotalMercanciasExentas'],
				"total_merc_exonerada"		=> $resumen_fact['TotalMercExonerada'],
				"total_gravados"			=> $resumen_fact['TotalGravado'],
				"total_exento"				=> $resumen_fact['TotalExento'],
				"total_exonerado"			=> $resumen_fact['TotalExonerado'],
				"total_ventas"				=> $resumen_fact['TotalVenta'],
				"total_descuentos"			=> $resumen_fact['TotalDescuentos'],
				"total_ventas_neta"			=> $resumen_fact['TotalVentaNeta'],
				"total_impuestos"			=> $resumen_fact['TotalImpuesto'],
				"totalIVADevuelto"			=> $resumen_fact['TotalIVADevuelto'],
				"TotalOtrosCargos"			=> $resumen_fact['TotalOtrosCargos'],
				"total_comprobante"			=> $resumen_fact['TotalComprobante'],
				"otros"						=> null, //Pendiente de adaptación
				"otrosType"					=> null, //Pendiente de adaptación
				"detalles"					=> self::getJsonDetalleServicio($num_factura),
				"infoRefeTipoDoc"			=> null, //Pendiente de adaptación
				"infoRefeNumero"			=> null, //Pendiente de adaptación
				"infoRefeFechaEmision"		=> null, //Pendiente de adaptación
				"infoRefeCodigo"			=> null, //Pendiente de adaptación
				"infoRefeRazon"				=> null, //Pendiente de adaptación
				"otrosCargos"				=> null  //Pendiente de adaptación
 		    ]
		]);

		$resCreateXmlFE = json_decode($reqCreateXmlFE->getBody());
		return $resCreateXmlFE;
	}

	/**
	*  Envía solicitud a la API para firmar y devuelve el xml
	*  firmado en formato base64.
	*
	* @param integer $xml.
	* @param string  $tipo_doc.
	*
	* @return object
	*/

	public static function firmarXML(Helpers $helpers, $xml, $tipo_doc){
		/*Inicia el cliente HTTP para request*/
		$client = new Client;
		$requestFirmarXML = $client->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        'r' 			=> 'firmar',
		        'w' 			=> 'firmarXML',
		        'inXml'			=> $xml,
		        'p12Url'		=> $helpers->perfil->downloadCode,
		        'pinP12'		=> $helpers->perfil->pin_p12,
		        'tipodoc'		=> $tipo_doc,
		    ]
		]);

		$responseFirmarXML = json_decode($requestFirmarXML->getBody());
		return $responseFirmarXML;
	}

	public static function getUbicacion(Helpers $helpers, $tipo_usuario, $id_usuario){
		if($tipo_usuario == "emisor"){
			// $usuario =  Capsule::table('perfil')->where('id_perfil','=',$id_usuario)->first();
			$ubicacion = $helpers->perfil->ubicacion;
		}elseif ($tipo_usuario == "receptor") {
			$usuario = Capsule::table('clientes')->where('id_cliente','=',$id_usuario)->first();
			$ubicacion = $usuario->ubicacion_cliente;
		}

		$ubicaciones_file=file_get_contents(constant('codificacion_ubicacion'));
		$ubicaciones=json_decode($ubicaciones_file);
		foreach ($ubicaciones as $ubi) {
			if($ubi->internalID == $ubicacion){
				return array(
					    "Provincia"			=> $ubi->Provincia,
					    "Canton"			=> $ubi->Canton,
					    "Distrito"			=> $ubi->Distrito,
					    "Barrio"			=> $ubi->Barrio
				);
			}
		}
	}

	/**
	*	Función que retorna nombre en STRING del tipo de identificación
	*/
	public static function getTipoIdEnString($tipo_id){
		$tipos_id_file=file_get_contents(constant('tipo_identificacion'));
		$tipos_ids=json_decode($tipos_id_file);
		foreach ($tipos_ids as $tipos_id) {
			if($tipos_id->Codigo == $tipo_id){
				return $tipos_id->NombreIdentificacion;
			}
		}
	}

	/**
	*  Envía solicitud a la API para firmar y devuelve el xml
	*  firmado en formato base64.
	*
	* @param string $xml.
	* @param string $tipo_doc.
	*
	* @return object
	*/
	public static function envioHaciendaFE(Helpers $helpers, $xml, $id_factura){
		/*Trae la información necesaria para la petición a la api*/
		$factura 	= Capsule::table('facturas')->where('id_factura','=',$id_factura)->get()[0];
		$receptor 	= Capsule::table('clientes')->where('id_cliente','=',$factura->id_cliente)->get()[0];

		/*Recuperamos clave numérica para esta factura*/
		$actual_clave 	= self::getNumericKey( self::getTipoIdEnString($receptor->tipo_cedula_cliente), (int)$receptor->cedula_cliente, 506, (int) $factura->numero_factura, "FE")->clave;

		/*Inicia el cliente HTTP para request*/
		$client = new Client;
		$requestEnvioHacienda = $client->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        'r' 						=> 'json',
		        'w' 						=> 'send',
		        'token'						=> $_SESSION['api_token'],
		        'clave'						=> $actual_clave,
		        'fecha'						=> date("Y-m-d")."T".date('H:i:sP'),
		        'emi_tipoIdentificacion'	=> $helpers->perfil->tipo_cedula,
		        'emi_numeroIdentificacion'	=> $helpers->perfil->cedula,
		        'recp_tipoIdentificacion'	=> $receptor->tipo_cedula_cliente,
		        'recp_numeroIdentificacion'	=> $receptor->cedula_cliente,
		        'comprobanteXml'			=> $xml,
		        'client_id'					=> getenv('API_ENVIRONMENT'),
		    ]
		]);

		$responseEnvioHacienda = json_decode($requestEnvioHacienda->getBody());
		return $responseEnvioHacienda;
	}

	/**
	*  Envía solicitud a la API para consultar el estado de un documento
	*
	* @param string $clave_documento.
	*
	* @return object
	*/
	public static function consultaEnvioHaciendaFE($clave_documento){
		/*Inicia el cliente HTTP para request*/
		$client = new Client;
		$requestConsultaEnvio = $client->request('POST', getenv('API_BASE_URL'), [
		    'form_params' => [
		        'r' 		=> 'consultarCom',
		        'w' 		=> 'consultar',
		        'token'		=> $_SESSION['api_token'],
		        'clave'		=> $clave_documento,
		        'client_id' => getenv('API_ENVIRONMENT')
		    ]
		]);

		$responseConsultaEnvio = json_decode($requestConsultaEnvio->getBody());
		return $responseConsultaEnvio;
	}
}