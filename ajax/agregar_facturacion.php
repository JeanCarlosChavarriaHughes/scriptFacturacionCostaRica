<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	$session_id= session_id();

	if (isset($_POST['id'])){
		$id=$_POST['id'];
		$moneda_id = $_POST['moneda'];
	}
	if (isset($_POST['cantidad'])){
		$cantidad=$_POST['cantidad'];
	}
	if (isset($_POST['precio_venta'])){
		$precio_venta=$_POST['precio_venta'];
	}

	if (isset($_POST['descuento_monto'])){
		$descuento_monto = $_POST['descuento_monto'];
	}

	if (isset($precio_venta) && isset($descuento_monto)){
		$total_venta = $precio_venta * $cantidad;
		if($descuento_monto > $total_venta){
?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong>
					El descuento no puede ser mayor al total de la venta en el producto ID#<?php echo $id ?>.

			</div>
<?php
		exit();
		}
	}

	if (isset($_POST['descuento_desc'])){
		$descuento_desc = $_POST['descuento_desc'];
	}

	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	include("../funciones.php");

	if (!empty($id) and !empty($cantidad) and !empty($precio_venta)){
		$query_consult = mysqli_query($con, "SELECT id_tmp FROM tmp WHERE id_producto='{$id}' AND session_id='{$session_id}'");
		$query_consult_result = mysqli_fetch_array($query_consult);

		//Si $query_consult_result no retorna un array, quiere decir que el registro no existe, entonces lo crea. Si sí existe, lo edita.
		if(!is_array($query_consult_result)){
			$insert_tmp=mysqli_query($con,
				"INSERT INTO tmp (id_producto, cantidad_tmp, precio_tmp, monto_descuento, desc_descuento, session_id, moneda_tmp)
				VALUES ('$id','$cantidad','$precio_venta','$descuento_monto','$descuento_desc','$session_id','$moneda_id')");
		}else{
			$update_tmp=mysqli_query($con,
				"UPDATE tmp
				SET cantidad_tmp='{$cantidad}', precio_tmp='{$precio_venta}', monto_descuento='{$descuento_monto}', desc_descuento='{$descuento_desc}'
				WHERE id_producto='{$id}' AND session_id='{$session_id}'");
		}
	}

	//Elimina un producto por GET
	if (isset($_GET['id'])){
		$id_tmp 	= intval($_GET['id']);
		$moneda_id 	= $_GET['moneda'];
		$delete 	= mysqli_query($con, "DELETE FROM tmp WHERE id_tmp='".$id_tmp."'");
	}
?>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th class='text-center'>CODIGO</th>
				<th class='text-center'>CANT.</th>
				<th>DESCRIPCION</th>
				<th class='text-right'>PRECIO UNIT.</th>
				<th class='text-right'>PRECIO TOTAL</th>
				<th></th>
			</tr>
			<?php
				// $cambio = getVentaDolarColones();
				$sumador_total = 0;
				$sumador_total_impuestos  = 0;
				$sumador_total_descuentos = 0;
				$sql = mysqli_query($con, "SELECT * FROM products, tmp
					WHERE products.id_producto = tmp.id_producto AND tmp.session_id='".$session_id."' AND tmp.moneda_tmp='".$moneda_id."'");
				while ($row=mysqli_fetch_array($sql))
				{
					$id_tmp 			= $row["id_tmp"];
					$codigo_producto 	= $row['codigo_producto'];
					$cantidad 			= $row['cantidad_tmp'];
					$nombre_producto 	= $row['nombre_producto'];
					$descuento_monto 	= $row['monto_descuento'];
					$descuento_desc 	= $row['desc_descuento'];

					if($descuento_monto > 0){
						$sumador_total_descuentos += $descuento_monto;
					}


					$precio_venta 		= $row['precio_tmp'];
					$precio_venta_f 	= number_format($precio_venta,2);//Formateo variables
					$precio_venta_r 	= str_replace(",","",$precio_venta_f);//Reemplazo las comas

					$precio_total 		= $precio_venta_r*$cantidad;
					$precio_total_f 	= number_format($precio_total,2);//Precio total formateado
					$precio_total_r 	= str_replace(",","",$precio_total_f);//Reemplazo las comas
					$sumador_total 		+= $precio_total_r;//sumador_total

					//Sumador de impuestos
					if($row['impuesto_es_iva'] == 1){
						$sumador_total_impuestos += ($precio_venta * $cantidad) * (int) $row['impuesto_iva_tarifa']/100;
					}else{
						$sumador_total_impuestos += ($precio_venta * $cantidad) * (int) $row['imp_subimp_tarifa']/100;
					}

					?>
					<tr>
						<td class='text-center'><?php echo $codigo_producto;?></td>
						<td class='text-center'><?php echo $cantidad;?></td>
						<td><?php echo $nombre_producto;?></td>
						<td class='text-right'><?php echo $precio_venta_f;?></td>
						<td class='text-right'>
							<?php echo $precio_total_f; ?> <br>
							<?php
								if($descuento_monto > 0){
							?>
								<b>Descuento</b> <?php echo $descuento_desc." -".$descuento_monto; ?>
							<?php
								}
							?>
						</td>
						<td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_tmp ?>')"><i class="glyphicon glyphicon-trash"></i></a></td>
					</tr>
					<?php
				}
					// $impuesto 				= get_row('perfil','impuesto', 'id_perfil', 1);
					$impuesto 				= $sumador_total_impuestos;
					$descuentos 			= $sumador_total_descuentos;
					$subtotal 				= number_format($sumador_total,2,'.','');

					$total_iva 				= ($subtotal * $impuesto )/100;
					$total_iva 				= number_format($total_iva,2,'.','');

					$total_factura 			= $subtotal+$impuesto;
					$total_colones 			= $total_factura;
					$total_colones_f 		= number_format( ($total_colones - $descuentos ) , 2);

					$total_iva_colones   	= $total_iva;
					$total_iva_colones_f 	= number_format($total_iva_colones,2);

			?>
			<tr>
				<td class='text-right' colspan=4>SUBTOTAL </td>
				<td class='text-right'><?php echo $moneda_id;?> <?php echo number_format($subtotal,2);?></td>
				<input type="hidden" class="form-control input-sm" name="subtotal" id="subtotal" readonly value="<?php echo $total_colones_f;?>">
				<td></td>
			</tr>

			<tr>
				<td class='text-right' colspan=4>DESCUENTOS </td>
				<td class='text-right'><?php echo $moneda_id;?> -<?php echo number_format($descuentos,2);?></td>
				<td></td>
			</tr>

			<tr>
				<td class='text-right' colspan=4>IMPUESTOS:</td>
				<td class='text-right'>
					<!-- <input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="impuestoShow" id="impuestoShow" readonly value=""> -->
					<?php echo $moneda_id;?> <?php echo number_format($impuesto,2); ?>
				</td>

				<input type="hidden" class="form-control input-sm" name="impuestoCalculo" id="impuestoCalculo" readonly value="<?php echo $total_iva_colones;?>">
				<input type="hidden" class="form-control input-sm" name="impuestoDolares" id="impuestoDolares" readonly value="<?php echo $total_iva;?>">
				<td></td>
			</tr>

			<tr>
				<td class='text-right' colspan=4>TOTAL: </td>
				<td class='text-right'><input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="total_colonesShow" id="total_colonesShow" readonly value="<?php echo $moneda_id;?> <?php echo $total_colones_f;?>"> </td>

				<input type="hidden" class="form-control input-sm" name="total_colones" id="total_colones" readonly value="<?php echo $total_colones;?>">
				<input type="hidden" class="form-control input-sm" name="total_colonesOriginal" id="total_colonesOriginal" readonly value="<?php echo $total_colones;?>">
				<input type="hidden" class="form-control input-sm" name="total_factura" id="total_factura" readonly value="<?php echo $total_factura;?>">
				<td></td>
			</tr>

		</table>
	</div>
