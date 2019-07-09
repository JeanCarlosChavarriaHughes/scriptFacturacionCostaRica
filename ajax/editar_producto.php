<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
	if (empty($_POST['mod_id'])) {
           $errors[] = "ID vacío";
        }else if (empty($_POST['mod_codigo'])) {
           $errors[] = "Código vacío";
        } else if (empty($_POST['mod_nombre'])){
			$errors[] = "Nombre del producto vacío";
		} else if ($_POST['mod_estado']==""){
			$errors[] = "Selecciona el estado del producto";
		} else if (empty($_POST['mod_precio'])){
			$errors[] = "Precio de venta en dolares vacío";
		} else if (empty($_POST['mod_precio_colon'])){
			$errors[] = "Precio de venta en colones vacío";
		} else if (
			!empty($_POST['mod_id']) &&
			!empty($_POST['mod_codigo']) &&
			!empty($_POST['mod_nombre']) &&
			$_POST['mod_estado']!="" &&
			!empty($_POST['mod_precio']) &&
			!empty($_POST['mod_precio_colon'])
		){
		/* Connect To Database*/
		require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
		require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
		// escaping, additionally removing everything that could be (html/javascript-) code
		$codigo=mysqli_real_escape_string($con,(strip_tags($_POST["mod_codigo"],ENT_QUOTES)));
		$nombre=mysqli_real_escape_string($con,(strip_tags($_POST["mod_nombre"],ENT_QUOTES)));
		$estado=intval($_POST['mod_estado']);
		$precio_venta=floatval($_POST['mod_precio']);
		$precio_colon=floatval($_POST['mod_precio_colon']);
		$id_producto=$_POST['mod_id'];

		$unidad_medida=$_POST['mod_unidadMedida'];
		$estado=intval($_POST['mod_estado']);

		////===============IMPUESTOS
		$codigoImpuesto 	= str_pad($_POST['mod_codigoImpuesto'], 2, "0", STR_PAD_LEFT);
		// exit();

		$query_to_update = " ";

		if(isset($_POST['mod_tarifaIva']) && !empty($_POST['mod_tarifaIva'])){
			echo "entra";
			$tarifaIva 			= explode("-", $_POST['mod_tarifaIva']);
			$tarifaIva_codigo 	= str_pad($tarifaIva[0], 2, "0", STR_PAD_LEFT);
			$tarifaIva_tarifa 	= $tarifaIva[1];

			$query_to_update = ", impuesto_codigo = '{$codigoImpuesto}', impuesto_es_iva = '1', impuesto_iva_codigo = '{$tarifaIva_codigo}', impuesto_iva_tarifa = '{$tarifaIva_tarifa}', imp_subimp_tarifa = ''";
			// $query_values = ", '".$codigoImpuesto."', 1, '".$tarifaIva_codigo."', ".$tarifaIva_tarifa;
		}

		if(isset($_POST['mod_TarifaSubimpuesto']) && !empty($_POST['mod_TarifaSubimpuesto'])){
			$TarifaSubimpuesto 	= explode("-", $_POST['mod_TarifaSubimpuesto']);
			$TarifaSubimpuesto_codigo 	= str_pad($TarifaSubimpuesto[0], 2, "0", STR_PAD_LEFT);
			$TarifaSubimpuesto_tarifa 	= $TarifaSubimpuesto[1];

			$query_to_update = ", impuesto_codigo = '{$codigoImpuesto}', impuesto_es_iva = '0', imp_subimp_tarifa = '{$TarifaSubimpuesto_codigo}', imp_subimp_codigo='{$TarifaSubimpuesto_tarifa}'";
			// $query_values = ", '".$codigoImpuesto."', 0, ".$TarifaSubimpuesto;
		}

		//===============END IMPUESTOS


		$sql = "UPDATE products
			    SET codigo_producto='".$codigo."', nombre_producto='".$nombre."', status_producto='".$estado."', precio_producto='".$precio_venta. "', precio_colon='". $precio_colon."', unidad_medida='". $unidad_medida."' ".$query_to_update." WHERE id_producto='".$id_producto."'";

		$query_update = mysqli_query($con,$sql);
			if ($query_update){
				$messages[] = "Producto ha sido actualizado satisfactoriamente.";
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