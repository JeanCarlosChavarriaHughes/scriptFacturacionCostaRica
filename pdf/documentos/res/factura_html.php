<style type="text/css">
	table { vertical-align: top; }
	tr    { vertical-align: top; }
	td    { vertical-align: top; }
	.midnight-blue{
		background:#2c3e50;
		padding: 4px 4px 4px;
		color:white;
		font-weight:bold;
		font-size:12px;
	}
	.silver{
		background:white;
		padding: 3px 4px 3px;
	}
	.clouds{
		background:#ecf0f1;
		padding: 3px 4px 3px;
	}
	.border-top{
		border-top: solid 1px #bdc3c7;

	}
	.border-left{
		border-left: solid 1px #bdc3c7;
	}
	.border-right{
		border-right: solid 1px #bdc3c7;
	}
	.border-bottom{
		border-bottom: solid 1px #bdc3c7;
	}
	table.page_footer {width: 100%; border: none; background-color: white; padding: 2mm;border-collapse:collapse; border: none;}
}
</style>
<page backtop="15mm" backbottom="15mm" backleft="15mm" backright="15mm" style="font-size: 12pt; font-family: arial" >
	<page_footer>
	<table class="page_footer">
		<tr>

			<td style="width: 15%; text-align: left;font-size:11px">
				P&aacute;gina [[page_cu]]/[[page_nb]]
			</td>
			<td style="width: 85%; text-align: right;color:#696969;font-size:10px">
				&copy; <?php echo  $anio=date('Y'); echo " - Autorizado mediante resolución #11-97 de la Dirección General de Tributación Directa, publicado en la Gaceta #171 del 5 septiembre de 1997. "; ?>
			</td>
		</tr>
	</table>
	</page_footer>
	<?php include("encabezado_factura.php");?>
	<br>



	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
		<tr>
			<td style="width:50%;" class='midnight-blue'>FACTURAR A</td>
		</tr>
		<tr>
			<td style="width:50%;" >
				<?php
				//Moneda de la factura
				$moneda=$_GET['moneda'];
				$sql_cliente=mysqli_query($con,"select * from clientes where id_cliente='$id_cliente'");
				$rw_cliente=mysqli_fetch_array($sql_cliente);
				echo $rw_cliente['nombre_cliente'];
				echo "<br>Cédula: ";
				echo $rw_cliente['cedula_cliente'];
				echo "<br>";
				echo $rw_cliente['direccion_cliente'];
				echo "<br> Teléfono: ";
				echo $rw_cliente['telefono_cliente'];
				echo "<br> Email: ";
				echo $rw_cliente['email_cliente'];
				?>

			</td>
		</tr>


	</table>

	<br>
	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
		<tr>
			<td style="width:25%;" class='midnight-blue'>Vendedor</td>
			<td style="width:25%;" class='midnight-blue'>Fecha</td>
			<td style="width:20%;" class='midnight-blue'>Forma de pago</td>
			<?php
				if($condiciones == 2 && !empty($credito_dias)){
			?>
				<td style="width:15%;" class='midnight-blue'>Plazo crédito</td>
			<?php
				}
			?>
			<td style="width:15%;" class='midnight-blue'>Moneda</td>
		</tr>
		<tr>
			<td style="width:25%;">
				<?php
				$sql_user=mysqli_query($con,"select * from users where user_id='$id_vendedor'");
				$rw_user=mysqli_fetch_array($sql_user);
				echo $rw_user['firstname']." ".$rw_user['lastname'];
				?>
			</td>
			<td style="width:25%;"><?php echo date("d/m/Y");?></td>
			<td style="width:20%;" >
				<?php
					$content=file_get_contents(constant('condiciones_venta'));
					$data=json_decode($content);
					foreach ($data as $value) {
						if( $value->Codigo == $condiciones ){
							echo $value->CondicionesDeLaVenta;
						}
					}
				?>
			</td>
			<?php
				if($condiciones == 2 && !empty($credito_dias)){
			?>
				<td style="width:15%;"> <?php echo $credito_dias; ?> días</td>
			<?php
				}
			?>
			<td style="width:15%;" ><?php echo $simbolo_moneda; ?></td>
		</tr>



	</table>
	<br>

	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;">
		<tr>
			<th style="width: 10%;text-align:center" class='midnight-blue'>Cantidad</th>
			<th style="width: 60%" class='midnight-blue'>Descripción</th>
			<th style="width: 15%;text-align: right" class='midnight-blue'>Precio Unit.</th>
			<th style="width: 15%;text-align: right" class='midnight-blue'>Precio Total</th>
		</tr>

		<?php
		// $cambio = getVentaDolarColones();
		$cambio = 0;
		$nums = 1;
		$sumador_total = 0;
		$sumador_total_impuestos  = 0;
		$sumador_total_descuentos = 0;
		$sql=mysqli_query($con, "select * from products, tmp where products.id_producto=tmp.id_producto and tmp.session_id='".$session_id."' and tmp.moneda_tmp='".$moneda."'");
		while ($row=mysqli_fetch_array($sql))
		{
			$id_tmp 			= $row["id_tmp"];
			$id_producto 		= $row["id_producto"];
			$codigo_producto 	= $row['codigo_producto'];
			$cantidad 			= $row['cantidad_tmp'];
			$nombre_producto 	= $row['nombre_producto'];
			$descuento_monto 	= (int) $row['monto_descuento'];
			$descuento_desc 	= $row['desc_descuento'];

			if($descuento_monto > 0){
				$sumador_total_descuentos += $descuento_monto;
			}

			$precio_venta 		= (int) $row['precio_tmp'];
			$precio_venta_f 	= number_format($precio_venta,2);//Formateo variables
			$precio_venta_r 	= str_replace(",","",$precio_venta_f);//Reemplazo las comas

			$precio_total 		= $precio_venta_r*$cantidad;
			$precio_total_f 	= number_format($precio_total,2);//Precio total formateado
			$precio_total_r 	= str_replace(",","",$precio_total_f);//Reemplazo las comas
			$sumador_total 		+= $precio_total_r;//sumador_total

			//Sumador de impuestos
			if($row['impuesto_es_iva'] == 1){
				$sumador_total_impuestos += ( ($precio_venta * $cantidad) * (int) $row['impuesto_iva_tarifa'] )/100;
			}else{
				$sumador_total_impuestos += ( ($precio_venta * $cantidad) * (int) $row['imp_subimp_tarifa'] )/100;
			}

			if ($nums%2==0){
				$clase="clouds";
			} else {
				$clase="silver";
			}
	?>

			<tr>
				<td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
				<td class='<?php echo $clase;?>' style="width: 60%; text-align: left"><?php echo $nombre_producto;?></td>
				<td class='<?php echo $clase;?>' style="width: 15%; text-align: right">$<?php echo $precio_venta_f;?> <?php echo $simbolo_moneda; ?></td>
				<td class='<?php echo $clase;?>' style="width: 15%; text-align: right">$<?php echo $precio_total_f;?> <?php echo $simbolo_moneda; ?></td>
			</tr>

	<?php
			if($descuento_monto > 0){
	?>
				<tr>
					<td class='<?php echo $clase;?>' style="width: 10%; text-align: center"></td>
					<td class='<?php echo $clase;?>' style="width: 60%; text-align: left">Descuento: <?php echo $descuento_desc;?></td>
					<td class='<?php echo $clase;?>' style="width: 15%; text-align: right"></td>
					<td class='<?php echo $clase;?>' style="width: 15%; text-align: right">-$<?php echo $descuento_monto;?> <?php echo $simbolo_moneda; ?></td>
				</tr>
	<?php
			}

			$nums++;
	}

	$impuesto  			= number_format($sumador_total_impuestos,2,'.','');
	$descuentos 		= $sumador_total_descuentos;
	$subtotal 			= number_format($sumador_total,2,'.','');
	$total_factura 		= $subtotal+$impuesto;
	$total_colones 		= $total_factura;
	$total_colones_f 	= number_format( ($total_colones - $descuentos ) , 2);

	// if($moneda == 1){
	// 	$total_colones = $total_colones_f*$cambio;
	// } else {
		$total_colones = $total_colones_f;
	// }

	$total_colones_f = number_format($total_colones, 2);
	$total_colones_r = str_replace(",","",$total_colones_f);

	$date=date("d-m-Y");

	/*Guarda la factura en BD*/
	$insert=mysqli_query($con,"INSERT INTO facturas VALUES (NULL,'$numero_factura','$date','$id_cliente','$id_vendedor','$condiciones','$medio_pago','$total_factura','1','$total_colones_r','$cambio','$impuesto', '$moneda', '$credito_dias',0,'','','','','')");

	if(!$insert){
		echo "Lo siento algo ha salido mal intenta nuevamente. ".mysqli_error($con);
		exit();
	}

	$sql=mysqli_query($con,"SELECT * FROM products, tmp WHERE products.id_producto=tmp.id_producto AND tmp.session_id='{$session_id}' AND tmp.moneda_tmp='{$moneda}'");

	if(!$sql){
		echo "Lo siento algo ha salido mal intenta nuevamente. ".mysqli_error($con);
		exit();
	}


	while($row=mysqli_fetch_array($sql)){
		/*Insert en la tabla detalle_factura*/
		$insert_detail=mysqli_query($con, "INSERT INTO detalle_factura (numero_factura, id_producto, cantidad, precio_venta, monto_descuento, desc_descuento)
			VALUES ('".$numero_factura."', '".$row['id_producto']."', '".$row['cantidad_tmp']."', '".$row['precio_tmp']."', '".$row['monto_descuento']."', '".$row['desc_descuento']."')");

		if(!$insert_detail){
			echo "Lo siento algo ha salido mal intenta nuevamente. ".mysqli_error($con);
		}

	}

	/*Elimina los elementos pertenecientes a esta factura en la tabla temporal*/
	$delete=mysqli_query($con,"DELETE FROM tmp WHERE session_id='".$session_id."'");

	if(!$delete){
		echo "Lo siento algo ha salido mal intenta nuevamente. ".mysqli_error($con);
	}

?>

<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Subtotal:</td>
	<td style="widtd: 15%; text-align: right;"> $<?php echo number_format($subtotal,2);?> <?php echo $simbolo_moneda; ?></td>
</tr>

<?php if($descuentos > 0){ ?>
<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Descuentos:</td>
	<td style="widtd: 15%; text-align: right;"> -$<?php echo number_format($descuentos,2);?> <?php echo $simbolo_moneda; ?></td>
</tr>
<?php } ?>

<?php if($impuesto > 0){ ?>
<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Impuestos: </td>
	<td style="widtd: 15%; text-align: right;"> $<?php echo number_format($impuesto,2);?> <?php echo $simbolo_moneda; ?></td>
</tr>
<?php } ?>
<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Total: </td>
	<td style="widtd: 15%; text-align: right;"> $<?php echo number_format($total_colones_f,2); ?> <?php echo $simbolo_moneda; ?></td>
</tr>
</table>

<table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;margin-top: 60px;border:none;">
	<tr>
		<td style="width: 100%; text-align: left;">¡Gracias por su preferencia!</td>
	</tr>
</table>
<table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;margin-top: 20px;border:1px;border-color: #778899">
	<tr>
		<td style="width: 100%; text-align: left;"><?php echo get_row('perfil','mensaje_factura', 'id_perfil', 1); ?></td>
	</tr>
</table>


<br>

</page>