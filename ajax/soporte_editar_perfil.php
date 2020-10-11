<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
	if (empty($_POST['usernameAPI'])) {
           $errors[] = "API username está vacío";

        }else if (empty($_POST['passwordAPI'])) {
           $errors[] = "API password está vacío";

        } else if (empty($_POST['iduserapi'])) {
           $errors[] = "API IDuser está vacío";

        } else if (empty($_POST['key_username'])) {
           $errors[] = "Hacienda username está vacío";

        } else if (empty($_POST['key_password'])) {
           $errors[] = "Hacienda password esta vacío";

        } else if (empty($_POST['pin_p12'])) {
           $errors[] = "PIN P12 esta vacío";

        } else if (
			!empty($_POST['usernameAPI']) &&
			!empty($_POST['passwordAPI']) &&
			!empty($_POST['iduserapi']) &&
			!empty($_POST['key_username']) &&
			!empty($_POST['key_password']) &&
			!empty($_POST['pin_p12'])
		){
		/* Connect To Database*/
		require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
		require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
		// escaping, additionally removing everything that could be (html/javascript-) code
		$usernameAPI=mysqli_real_escape_string($con,(strip_tags($_POST["usernameAPI"],ENT_QUOTES)));
		$passwordAPI=mysqli_real_escape_string($con,(strip_tags($_POST["passwordAPI"],ENT_QUOTES)));
		$iduserapi=$_POST["iduserapi"];
		$key_username=mysqli_real_escape_string($con,(strip_tags($_POST["key_username"],ENT_QUOTES)));
		$key_password=mysqli_real_escape_string($con,(strip_tags($_POST["key_password"],ENT_QUOTES)));
		$pin_p12=mysqli_real_escape_string($con,(strip_tags($_POST["pin_p12"],ENT_QUOTES)));
		$acercade=mysqli_real_escape_string($con,(strip_tags($_POST["acercade"],ENT_QUOTES)));
		$ubicacion=mysqli_real_escape_string($con,(strip_tags($_POST["ubicacion"],ENT_QUOTES)));

		$sql="UPDATE perfil SET usernameAPI='".$usernameAPI."', passwordAPI='".$passwordAPI."', iduserapi='".$iduserapi."', key_username='".$key_username."', key_password='".$key_password."', pin_p12='".$pin_p12."', acercade='".$acercade."', ubicacion='".$ubicacion."' WHERE id_perfil='1'";
		$query_update = mysqli_query($con,$sql);
			if ($query_update){
				$messages[] = "Datos han sido actualizados satisfactoriamente.";
			} else{
				$errors []= "Lo siento algo ha salido mal intenta nuevamente.".mysqli_error($con);
			}
		} else {
			$errors []= "Error desconocido.";
		}

		if (isset($errors)){

			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong>
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