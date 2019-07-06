<?php
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
	if (empty($_POST['codigo'])) {
           $errors[] = "Código vacío";
        } else if (empty($_POST['unidadMedida'])){
			$errors[] = "Medida de unidad vacío";
        } else if (empty($_POST['codigoImpuesto'])){
			$errors[] = "Seleccione un impuesto.";

		} else if ($_POST['codigoImpuesto'] == 1 || $_POST['codigoImpuesto'] == 7 AND !isset($_POST['tarifaIva']) || empty($_POST['tarifaIva'])){
				$errors[] = "Seleccione una Tarifa IVA.";

        } else if ($_POST['codigoImpuesto'] != 1 && $_POST['codigoImpuesto'] != 7 AND !isset($_POST['TarifaSubimpuesto']) || empty($_POST['TarifaSubimpuesto'])){
				$errors[] = "Seleccione un subimpuesto.";

        } else if (empty($_POST['nombre'])){
			$errors[] = "Nombre del producto vacío";
		} else if ($_POST['estado']==""){
			$errors[] = "Selecciona el estado del producto";
		} else if (empty($_POST['precio'])){
			$errors[] = "Precio de venta en dolares vacío";
		} else if (empty($_POST['precio_colon'])){
			$errors[] = "Precio de venta en colones vacío o valor no aceptado.";
		} else if (
			!empty($_POST['codigo']) &&
			!empty($_POST['nombre']) &&
			$_POST['estado']!="" &&
			!empty($_POST['precio']) &&
			!empty($_POST['precio_colon'])
		){
			/* Connect To Database*/
			require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
			$codigo=mysqli_real_escape_string($con,(strip_tags($_POST["codigo"],ENT_QUOTES)));
			$nombre=mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
			$unidad_medida=$_POST['unidadMedida'];
			$estado=intval($_POST['estado']);

			////===============IMPUESTOS
			$codigoImpuesto 	= str_pad($_POST['codigoImpuesto'], 2, "0", STR_PAD_LEFT);
			// exit();

			if(isset($_POST['tarifaIva'])){
				$tarifaIva 			= explode("-", $_POST['tarifaIva']);
				$tarifaIva_codigo 	= str_pad($tarifaIva[0], 2, "0", STR_PAD_LEFT);
				$tarifaIva_tarifa 	= $tarifaIva[1];

				$query_insert = ", impuesto_codigo, impuesto_es_iva, impuesto_iva_codigo, impuesto_iva_tarifa";
				$query_values = ", '".$codigoImpuesto."', 1, '".$tarifaIva_codigo."', ".$tarifaIva_tarifa;
			}

			if(isset($_POST['TarifaSubimpuesto'])){
				$TarifaSubimpuesto 	= explode("-", $_POST['TarifaSubimpuesto']);
				$TarifaSubimpuesto_codigo 	= str_pad($TarifaSubimpuesto[0], 2, "0", STR_PAD_LEFT);
				$TarifaSubimpuesto_tarifa 	= $TarifaSubimpuesto[1];

				$query_insert 		= ", impuesto_codigo, impuesto_es_iva, imp_subimp_tarifa, imp_subimp_codigo";
				$query_values 		= ", '".$codigoImpuesto."', 0, ".$TarifaSubimpuesto_tarifa.", ".$TarifaSubimpuesto_codigo;
			}

			//===============END IMPUESTOS
			$precio_venta=floatval($_POST['precio']);
			$precio_colon = floatval($_POST['precio_colon']);
			$date_added=date("Y-m-d H:i:s");
			$sql="INSERT INTO products
				 (codigo_producto, nombre_producto, status_producto, date_added, precio_producto, precio_colon, unidad_medida $query_insert)
				 VALUES
				 ('$codigo','$nombre','$estado','$date_added','$precio_venta','$precio_colon','$unidad_medida'".$query_values.")";

				$query_new_insert = mysqli_query($con,$sql);
				if ($query_new_insert){
					$messages[] = "El producto ha sido ingresado satisfactoriamente.";
				} else{
					$errors []= mysqli_error($con);
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