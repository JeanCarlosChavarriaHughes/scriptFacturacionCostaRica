<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
    use Rakit\Validation\Validator;
    use ValidatorsFields\UniqueRule;

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

	/*Declara una regla propia*/
	$validator->addValidator('unique', new UniqueRule());

	/*Valida cada uno de los campos recibidos por post*/
	$validation = $validator->validate($_POST, [
		'mod_id_cliente'         		=> 'required|numeric',
	    'mod_nombre_cliente'         	=> 'required|max:100',
	    'mod_nombre_comercial_cliente'	=> 'max:80',
	    'mod_tipo_cedula_cliente'       => 'required|numeric|digits:2',
	    'mod_cedula_cliente'			=> 'required',
	    'mod_ubicacion_cliente'         => 'required|numeric',
	    'mod_direccion_cliente'      	=> 'required|max:250',
	    'mod_telefono_cod_cliente'      => 'numeric|digits_between:1,3',
	    'mod_telefono_cliente'      	=> 'numeric|digits_between:1,20',
	    'mod_telefono_fax_cod_cliente'  => 'numeric|digits_between:1,3',
	    'mod_telefono_fax_cliente'      => 'numeric|digits_between:1,20',
	    'mod_email_cliente'      		=> 'required|email',
	    'mod_moneda'      				=> 'required|max:3'
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
	switch ($_POST['mod_tipo_cedula_cliente']) {
		/*Cedula fisica*/
		case "01":
			$validation = $validator->validate($_POST, [
			    'mod_cedula_cliente' => 'regex:/^[1-9]{9}$/',
			]);
		break;

		/*Cedula juridica*/
		case "02":
			$validation = $validator->validate($_POST, [
			    'mod_cedula_cliente' => 'regex:/^[1-9]{10}$/',
			]);
		break;

		/*Cedula dimex*/
		case "03":
			$validation = $validator->validate($_POST, [
			    'mod_cedula_cliente' => 'regex:/^[1-9]{11,12}$/',
			]);
		break;

		/*nite*/
		case "04":
			$validation = $validator->validate($_POST, [
			    'mod_cedula_cliente' => 'regex:/^[1-9]{10}$/',
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

	$id_cliente 				= mysqli_real_escape_string($con,(strip_tags($_POST["mod_id_cliente"],ENT_QUOTES)));
	$nombre_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["mod_nombre_cliente"],ENT_QUOTES)));
	$nombre_comercial_cliente 	= mysqli_real_escape_string($con,(strip_tags($_POST["mod_nombre_comercial_cliente"],ENT_QUOTES)));
	$tipo_cedula_cliente 		= mysqli_real_escape_string($con,(strip_tags($_POST["mod_tipo_cedula_cliente"],ENT_QUOTES)));
	$cedula_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["mod_cedula_cliente"],ENT_QUOTES)));
	$ubicacion_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["mod_ubicacion_cliente"],ENT_QUOTES)));
	$direccion_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["mod_direccion_cliente"],ENT_QUOTES)));
	$telefono_cod_cliente 		= mysqli_real_escape_string($con,(strip_tags($_POST["mod_telefono_cod_cliente"],ENT_QUOTES)));
	$telefono_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["mod_telefono_cliente"],ENT_QUOTES)));
	$telefono_fax_cod_cliente 	= mysqli_real_escape_string($con,(strip_tags($_POST["mod_telefono_fax_cod_cliente"],ENT_QUOTES)));
	$telefono_fax_cliente 		= mysqli_real_escape_string($con,(strip_tags($_POST["mod_telefono_fax_cliente"],ENT_QUOTES)));
	$email_cliente 				= mysqli_real_escape_string($con,(strip_tags($_POST["mod_email_cliente"],ENT_QUOTES)));
	$id_moneda 					= mysqli_real_escape_string($con,(strip_tags($_POST["mod_moneda"],ENT_QUOTES)));

	$sql="UPDATE clientes SET
	nombre_cliente 				= '$nombre_cliente',
	nombre_comercial_cliente 	= '$nombre_comercial_cliente',
	tipo_cedula_cliente 		= '$tipo_cedula_cliente',
	cedula_cliente 				= '$cedula_cliente',
	ubicacion_cliente 			= '$ubicacion_cliente',
	direccion_cliente 			= '$direccion_cliente',
	telefono_cod_cliente 		= '$telefono_cod_cliente',
	telefono_cliente 			= '$telefono_cliente',
	telefono_fax_cod_cliente 	= '$telefono_fax_cod_cliente',
	telefono_fax_cliente 		= '$telefono_fax_cliente',
	email_cliente 				= '$email_cliente',
	id_moneda 					= '$id_moneda'
	WHERE id_cliente = '$id_cliente'";

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