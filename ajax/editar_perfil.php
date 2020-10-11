<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
    use Rakit\Validation\Validator;

	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code

	/*Instancia validador*/
	$validator = new Validator([
						'required' 			=> ':attribute no puede estar vacío.',
						'max' 				=> ':attribute no puede tener mas de 100 caracteres.',
						'email' 			=> ':attribute parece estar mal escrito.',
						'numeric' 			=> ':attribute debe ser numérico.',
						'digits' 			=> ':attribute debe tener exactamente x digitos.',
						'regex' 			=> 'Escriba :attribute en su formato original.',
						'digits_between'	=> ':attribute debe tener entre x y x digitos.'
					]);

	/*Valida cada uno de los campos recibidos por pos*/
	$validation = $validator->validate($_POST, [
	    'nombre_empresa'         	=> 'required|max:100',
	    'codigo_actividad_empresa'	=> 'required|numeric|max:11',
	    'nombre_empresa_comercial'	=> 'required|max:80',
	    'tipo_cedula'           	=> 'required|numeric|digits:2',
	    'ubicacion'              	=> 'required|numeric',
	    'direccion'      			=> 'required|max:250',
	    'telefono_cod'      		=> 'required|numeric|digits_between:1,3',
	    'telefono'      			=> 'required|numeric|digits_between:1,20',
	    'telefono_fax_cod'      	=> 'required|numeric|digits_between:1,3',
	    'telefono_fax'      		=> 'required|numeric|digits_between:1,20',
	    'email'      				=> 'required|email'
	]);

	/*Imprime los errores en caso de que hayan*/
	if ($validation->fails()) {
		$errors = $validation->errors();
	?>
		<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>Error!</strong>
				<?php
					$message = $errors->all('<li>:message</li>');
					foreach ($message as $value) {
						echo $value;
					}
				?>
		</div>
	<?php
		exit;
	}

	/*Para validar la cédula segun el tipo de documento*/
	switch ($_POST['tipo_cedula']) {
		/*Cedula fisica*/
		case "01":
			$validation = $validator->validate($_POST, [
			    'cedula' => 'regex:/^[1-9]{1}[0-9]{8}$/',
			]);
		break;

		/*Cedula juridica*/
		case "02":
			$validation = $validator->validate($_POST, [
			    'cedula' => 'regex:/^[1-9]{1}[0-9]{9}$/',
			]);
		break;

		/*Cedula dimex*/
		case "03":
			$validation = $validator->validate($_POST, [
			    'cedula' => 'regex:/^[1-9]{1}[0-9]{10,11}$/',
			]);
		break;

		/*nite*/
		case "04":
			$validation = $validator->validate($_POST, [
			    'cedula' => 'regex:/^[1-9]{1}[0-9]{9}$/',
			]);
		break;
	}

	/*Imprime los errores en caso de que hayan*/
	if ($validation->fails()) {
		$errors = $validation->errors();
	?>
		<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>Error!</strong>
				<?php
					$message = $errors->all('<li>:message</li>');
					foreach ($message as $value) {
						echo $value;
					}
				?>
		</div>
	<?php
		exit;
	}

	$nombre_empresa 			= mysqli_real_escape_string($con,(strip_tags($_POST["nombre_empresa"],ENT_QUOTES)));
	$nombre_empresa_comercial 	= mysqli_real_escape_string($con,(strip_tags($_POST["nombre_empresa_comercial"],ENT_QUOTES)));
	$codigo_actividad_empresa 	= mysqli_real_escape_string($con,(strip_tags($_POST["codigo_actividad_empresa"],ENT_QUOTES)));
	$tipo_cedula 				= mysqli_real_escape_string($con,(strip_tags($_POST["tipo_cedula"],ENT_QUOTES)));
	$cedula 					= mysqli_real_escape_string($con,(strip_tags($_POST["cedula"],ENT_QUOTES)));
	$ubicacion 					= mysqli_real_escape_string($con,(strip_tags($_POST["ubicacion"],ENT_QUOTES)));
	$direccion 					= mysqli_real_escape_string($con,(strip_tags($_POST["direccion"],ENT_QUOTES)));
	$telefono_cod 				= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_cod"],ENT_QUOTES)));
	$telefono 					= mysqli_real_escape_string($con,(strip_tags($_POST["telefono"],ENT_QUOTES)));
	$telefono_fax_cod 			= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_fax_cod"],ENT_QUOTES)));
	$telefono_fax 				= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_fax"],ENT_QUOTES)));
	$email 						= mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
	$moneda 					= mysqli_real_escape_string($con,(strip_tags($_POST["moneda"],ENT_QUOTES)));
	$mensaje_factura 			= mysqli_real_escape_string($con,(strip_tags($_POST["mensaje_factura"],ENT_QUOTES)));

	$sql="UPDATE perfil SET
	nombre_empresa 				='".$nombre_empresa."',
	nombre_empresa_comercial 	='".$nombre_empresa_comercial."',
	codigo_actividad_empresa 	='".$codigo_actividad_empresa."',
	tipo_cedula 				='".$tipo_cedula."',
	cedula 						='".$cedula."',
	ubicacion 					='".$ubicacion."',
	direccion 					='".$direccion."',
	telefono_cod 				='".$telefono_cod."',
	telefono 					='".$telefono."',
	telefono_fax_cod 			='".$telefono_fax_cod."',
	telefono_fax 				='".$telefono_fax."',
	email 						='".$email."',
	moneda  					='".$moneda."',
	mensaje_factura 			='".$mensaje_factura."'WHERE id_perfil='1'";

	$query_update = mysqli_query($con,$sql);

	if ($query_update){
		$messages[] = "Los datos han sido actualizados satisfactoriamente.";
	} else{
		$errors []= "Lo siento algo ha salido mal intenta nuevamente.".mysqli_error($con);
	}

	if (isset($errors)){
	?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>¡Error!</strong>
			<?php
				foreach ($errors as $error) {
						echo $error;
					}
				?>
	</div>
	<?php
	}
	if (isset($messages)){

		?>
		<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Bien hecho!</strong>
				<?php
					foreach ($messages as $message) {
							echo $message;
						}
					?>
		</div>
		<?php
	}

?>