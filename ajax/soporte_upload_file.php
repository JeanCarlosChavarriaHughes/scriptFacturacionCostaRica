<?php
namespace Ajax;
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
require_once('../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use Ajax\Helpers as Helpers;
use Illuminate\Database\Capsule\Manager as Capsule;

//Verifica que se hayan llenado los campos de la API
foreach (Capsule::table('perfil')->where('id_perfil','=',1)->first() as $key => $value) {
	if($key == "usernameAPI" && empty($value)){
		echo '<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Error!</strong> Complete los datos de conxeión a la API para poder subir un certificado.
		</div>';
		exit();
	}
	if($key == "passwordAPI" && empty($value)){
		echo '<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Error!</strong> Complete los datos de conxeión a la API para poder subir un certificado.
		</div>';
		exit();
	}
	if($key == "iduserapi" && empty($value)){
		echo '<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Error!</strong> Complete los datos de conxeión a la API para poder subir un certificado.
		</div>';
		exit();
	}
}

if(!isset($_POST["by"])){
	echo '<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>¡Error!</strong> Elija un tipo de operación para subir el certificado.
	</div>';
	exit();
}

$by = $_POST["by"];

if ($by == "url") {
	if(!isset($_POST["nameFile"]) || empty($_POST["nameFile"])){
		echo '<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Error!</strong> Ingrese una URL válida terminada en <b>.p12</b>
		</div>';
		exit();
	}

	$url_file = $_POST["nameFile"]; //Recibe la ruta desde donde se debe descargar el archivo

	//Para determinar la extensión
	$fileName 		= explode(".", $url_file);
	$fileExtension	= strtolower(end($fileName));
	$defineName 	= explode("/", $url_file);
	$definedName	= strtolower(end($defineName));

	//Verifica la extensión del archivo
	$allowedfileExtensions = array('p12');
	if (!in_array($fileExtension, $allowedfileExtensions)) {
		echo "<div class='alert alert-danger' role='alert'><button type='button' class='close' data-dismiss='alert'>&times;</button> <b>¡Error!</b> Especifica una URL terminada en <b>.p12</b></div>";
		exit();
	}

	//Hace login con la API y recupera un sessionKey
	$actual_sessionKey = Helpers::LoginApi()->resp;

	// VERIFICA LA URL CON UN REQUEST ANTES DE OPERARLA
	// Si existe algún problema, captura el error y lo muestra
	$clientHttp = new Client; //inicial un cliente HTTP para consultar la URL suministrada
	try{
		$clientHttp->request('GET', $url_file); //Consulta la URL suministrada y descarga el archivo
	} catch (ClientException $e) {
	    // echo Psr7\str($e->getResponse()); // Muestra posibles errores en la solicitud
	    echo '<div class="alert alert-danger" role="alert">
	    		<button type="button" class="close" data-dismiss="alert">&times;</button>
	    		<strong>¡Error!</strong> Error al consultar la URL: '. Psr7\str($e->getResponse()).'
	    </div>';
	    exit();
	}
	// ======================================================================

	//Sube el certificado a la API
	$response_upload = Helpers::uploadCertificateToApi($actual_sessionKey, $url_file, $by);
	//Y actuliza en BD
	$save_response_upload = Capsule::table('perfil')
            ->where('id_perfil','=',1)
            ->update([
            	'downloadCode'	=> $response_upload->downloadCode,
            	'idFile' 		=> $response_upload->idFile,
            	'file_p12'		=> $definedName,
            ]);

    //Si se actuliza correctamente, entonces lo notifica
    if($save_response_upload){
    	echo '<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong> El archivo se ha subido con éxito. <br>
						<b>Nuevo idFile:</b> '.$response_upload->idFile.' <br>
						<b>Nuevo downloadCode:</b> '.$response_upload->downloadCode.' <br>
				</div>';
    }
} elseif ($by == "local") {

		$fileToUpload = $_FILES["filetoupload"];

		// Para subir por medio de archivos temporales
		// $route 	= explode("Temp", $_FILES["filetoupload"]['tmp_name']);
		// $new_route = $route[0]."Temp\\".$_FILES["filetoupload"]['name'];
		// $new_file = rename($_FILES["filetoupload"]['tmp_name'], $route[0]."Temp\\".$_FILES["filetoupload"]['name']);
		// _____________________________________________________________________________________________________________

		//valida y sube el archivo al servidor local
		$file_uploaded = Helpers::setFileToUpload($fileToUpload); //retirna la ruta del archivo

		//Hace login con la API y recupera un sessionKey
		$actual_sessionKey = Helpers::LoginApi()->resp;

		//Sube el certificado a la API
		$response_upload = Helpers::uploadCertificateToApi($actual_sessionKey, $file_uploaded, $by);

		//Y actuliza en BD
		$save_response_upload = Capsule::table('perfil')
	            ->where('id_perfil','=',1)
	            ->update([
	            	'downloadCode'	=> $response_upload->downloadCode,
	            	'idFile' 		=> $response_upload->idFile,
	            	'file_p12' 		=> $fileToUpload['name'],
	            ]);

	    //Si se actuliza correctamente, entonces lo notifica
	    if($save_response_upload){
	    	$res = ["message" =>  '<div class="alert alert-success" role="alert">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong>¡Bien hecho!</strong> El archivo se ha subido con éxito. <br>
							<b>Nuevo idFile:</b> '.$response_upload->idFile.' <br>
							<b>Nuevo downloadCode:</b> '.$response_upload->downloadCode.' <br>
					</div>'];
	    	// return json($res);
	    	echo json_encode($res, JSON_FORCE_OBJECT);
	    }
}