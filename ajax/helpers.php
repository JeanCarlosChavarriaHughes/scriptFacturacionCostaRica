<?php
namespace Ajax;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

class Helpers {

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
			$_SESSION['EXISTS_FILE_P12'] = false;
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

		if($consecutive == 9999999999){
			$new_consecutive = 1;
		}else{
			$new_consecutive = $consecutive + 1;
		}

		$pad_final_tipo_comprobante	= str_pad( (string) $tipo_comprobante, 2, '0', STR_PAD_LEFT);
		$pad_final_local 			= str_pad( (string) $local, 3, '0', STR_PAD_LEFT);
		$pad_final_terminal 		= str_pad( (string) $terminal, 5, '0', STR_PAD_LEFT);
		$pad_final_consecutive		= str_pad( (string) $new_consecutive, 10, '0', STR_PAD_LEFT);

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
	public static function getNumericKey($type_id, $id_client, $country, $consecutive, $document_type, $previousNumericKey=null){

		$result_valid	= self::validategetNumericKey($type_id, $id_client, $country, $consecutive, $document_type);
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
	 	               'contents' => $id_client
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
}