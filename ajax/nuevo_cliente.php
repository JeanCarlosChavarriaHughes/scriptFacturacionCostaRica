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
	    'nombre_cliente'         	=> 'required|max:100',
	    'nombre_comercial_cliente'	=> 'max:80',
	    'tipo_cedula_cliente'       => 'required|numeric|digits:2',
	    'ubicacion_cliente'         => 'required|numeric',
	    'direccion_cliente'      	=> 'required|max:250',
	    'telefono_cod_cliente'      => 'numeric|digits_between:1,3',
	    'telefono_cliente'      	=> 'numeric|digits_between:1,20',
	    'telefono_fax_cod_cliente'  => 'numeric|digits_between:1,3',
	    'telefono_fax_cliente'      => 'numeric|digits_between:1,20',
	    'email_cliente'      		=> 'required|email|unique:clientes,email_cliente',
	    'estado_cliente'			=> 'required|numeric|digits:1',
	    'id_moneda'      			=> 'required|max:3'
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
	switch ($_POST['tipo_cedula_cliente']) {
		/*Cedula fisica*/
		case "01":
			$validation = $validator->validate($_POST, [
			    'cedula_cliente' => 'regex:/^[1-9]{1}[0-9]{8}$/|unique:clientes,cedula_cliente',
			]);
		break;

		/*Cedula juridica*/
		case "02":
			$validation = $validator->validate($_POST, [
			    'cedula_cliente' => 'regex:/^[1-9]{1}[0-9]{9}$/|unique:clientes,cedula_cliente',
			]);
		break;

		/*Cedula dimex*/
		case "03":
			$validation = $validator->validate($_POST, [
			    'cedula_cliente' => 'regex:/^[1-9]{1}[0-9]{10,11}$/|unique:clientes,cedula_cliente',
			]);
		break;

		/*nite*/
		case "04":
			$validation = $validator->validate($_POST, [
			    'cedula_cliente' => 'regex:/^[1-9]{1}[0-9]{9}$/|unique:clientes,cedula_cliente',
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

	$nombre_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["nombre_cliente"],ENT_QUOTES)));
	$nombre_comercial_cliente 	= mysqli_real_escape_string($con,(strip_tags($_POST["nombre_comercial_cliente"],ENT_QUOTES)));
	$tipo_cedula_cliente 		= mysqli_real_escape_string($con,(strip_tags($_POST["tipo_cedula_cliente"],ENT_QUOTES)));
	$cedula_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["cedula_cliente"],ENT_QUOTES)));
	$ubicacion_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["ubicacion_cliente"],ENT_QUOTES)));
	$direccion_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["direccion_cliente"],ENT_QUOTES)));
	$telefono_cod_cliente 		= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_cod_cliente"],ENT_QUOTES)));
	$telefono_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_cliente"],ENT_QUOTES)));
	$telefono_fax_cod_cliente 	= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_fax_cod_cliente"],ENT_QUOTES)));
	$telefono_fax_cliente 		= mysqli_real_escape_string($con,(strip_tags($_POST["telefono_fax_cliente"],ENT_QUOTES)));
	$email_cliente 				= mysqli_real_escape_string($con,(strip_tags($_POST["email_cliente"],ENT_QUOTES)));
	$estado_cliente 			= mysqli_real_escape_string($con,(strip_tags($_POST["estado_cliente"],ENT_QUOTES)));
	$id_moneda 					= mysqli_real_escape_string($con,(strip_tags($_POST["id_moneda"],ENT_QUOTES)));
	$fecha_creacion_cliente		= date('Y/m/d H:i:s');

	$sql="INSERT INTO clientes (
	nombre_cliente,
	nombre_comercial_cliente,
	tipo_cedula_cliente,
	cedula_cliente,
	ubicacion_cliente,
	direccion_cliente,
	telefono_cod_cliente,
	telefono_cliente,
	telefono_fax_cod_cliente,
	telefono_fax_cliente,
	email_cliente,
	estado_cliente,
	fecha_creacion_cliente,
	id_moneda)
	VALUES (
	'$nombre_cliente',
	'$nombre_comercial_cliente',
	'$tipo_cedula_cliente',
	'$cedula_cliente',
	'$ubicacion_cliente',
	'$direccion_cliente',
	'$telefono_cod_cliente',
	'$telefono_cliente',
	'$telefono_fax_cod_cliente',
	'$telefono_fax_cliente',
	'$email_cliente',
	'$estado_cliente',
	'$fecha_creacion_cliente',
	'$id_moneda')";

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