<?php
namespace Ajax;
use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

class Helpers {
	public static function LoginApi(){
		$user_info = self::getUserInfo(1); //Recibe toda la información del perfil del usuario
		$client = new Client; //Inicia el cliente HTTP para request
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
			return false;
		}

		//asigna un nuevo nombre al archivo con la extensión requerida
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
}