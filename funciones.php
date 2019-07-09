<?php
	use Illuminate\Database\Capsule\Manager as Capsule;
	date_default_timezone_set('America/Costa_Rica');

	function get_row($table,$row, $id, $equal){
		global $con;
		$query=mysqli_query($con,"select $row from $table where $id='$equal'");
		$rw=mysqli_fetch_array($query);
		$value=$rw[$row];
		return $value;
	}

	function getVentaDolarColones(){
		require_once("get_tipo_cambio/Indicador.php");

		// Constructor recibe como parametro true si se va a usar SOAP, de lo contrario por defecto es false
		$i = new Indicador(false);

		// Metodo recibe el tipo de cambio Indicador::VENTA o Indicador::COMPRA
		$cambioDolar = $i->obtenerIndicadorEconomico(Indicador::VENTA);
		if($cambioDolar == 0){
			$cambioDolar = 550.00;
		}
		return $cambioDolar;
	}

	function getJsonDetalleServicio($num_factura){
		$factura = Capsule::table('facturas')->where('id_factura','=',$num_factura)->get();
		$detalle_factura = Capsule::table('detalle_factura')->where('numero_factura','=',$num_factura)->get();

		$res_json = [];
		$linea = null;
		foreach ($detalle_factura as $detalle) {
			//Taremos la información del producto actual
			$producto = Capsule::table('products')->where('id_producto','=',$detalle->id_producto)->first();

			//contador de líneas
			$linea++;

			//Definimos variables operadoras
			$var_monto_total 	= $detalle->cantidad * $producto->precio_colon;
			$var_subotal 		= $var_monto_total - $detalle->monto_descuento;

			//Organizamos el Json
			$json = [];
			$json[$linea] 						= [];
			$json[$linea]['codigo'] 			= (string) rand(0,500);
			$json[$linea]['codigoComercial']	=  ["1" => [ "tipo" => (string) rand(0,500), "codigo" => (string) rand(0,500) ] ];
			$json[$linea]['cantidad'] 			= (string) $detalle->cantidad;
			$json[$linea]['unidadMedida'] 		= (string) $producto->unidad_medida;
			$json[$linea]['detalle'] 			= (string) $producto->nombre_producto;
			$json[$linea]['precioUnitario'] 	= (string) $producto->precio_colon;
			$json[$linea]['montoTotal'] 		= (string) $var_monto_total;

			//Si aplica descuento, lo declaramos
			if($detalle->monto_descuento != 0){
				$json[$linea]['descuento']		=  ["1" => ["montoDescuento" => (string) $detalle->monto_descuento, "naturalezaDescuento" => (string) $detalle->desc_descuento]];
			}

			$json[$linea]['subtotal'] 			= (string) $var_subotal;

			//Si el impuesto que aplica es IVA o IVA cálculo especial
			if($producto->impuesto_es_iva == 1 && ($producto->impuesto_codigo == "07" || $producto->impuesto_codigo == "01")){

				$var_monto_impuesto = ((int) $producto->precio_colon * (int) $producto->impuesto_iva_tarifa)/100;
				//baseimponible se convierte en obligatorio cuando se seleccione en el campo “Código del impuesto” 07.
				if($producto->impuesto_codigo == "07"){
					$json[$linea]['baseImponible'] 	= (string) $producto->precio_colon;
				}

				$json[$linea]['impuesto']		= ["1" => [
															"codigo" 			=> (string) $producto->impuesto_codigo,
															"codigoTarifa"		=> (string) $producto->impuesto_iva_codigo,
															"tarifa"			=> (string) $producto->impuesto_iva_tarifa,
															// "factorIVA"			=> "",
															"monto"				=> (string) $var_monto_impuesto,
															// "montoExportacion"	=> "",
															// "exoneracion"		=> [
															// 							"tipoDocumento" 		=> "",
															// 							"numeroDocumento" 		=> "",
															// 							"nombreInstitucion" 	=> "",
															// 							"fechaEmision"			=> "",
															// 							"porcentajeExoneracion"	=> "",
															// 							"montoExoneracion" 		=> "",
															// 						]
															]
														];
			}

			//Si el impuesto que aplica no es ningún tipo de IVA
			if($producto->impuesto_es_iva == 0){
				$var_monto_impuesto = ((int) $producto->precio_colon * (int) $producto->imp_subimp_tarifa)/100;
				$json[$linea]['impuesto']		= ["1" => [
															"codigo" 			=> (string) $producto->impuesto_codigo,
															// "codigoTarifa"		=> "",
															"tarifa"			=> (string) $producto->imp_subimp_tarifa,
															// "factorIVA"			=> "",
															"monto"				=> (string) $var_monto_impuesto,
															// "montoExportacion"	=> "",
															// "exoneracion"		=> [
															// 							"tipoDocumento" 		=> "",
															// 							"numeroDocumento" 		=> "",
															// 							"nombreInstitucion" 	=> "",
															// 							"fechaEmision"			=> "",
															// 							"porcentajeExoneracion"	=> "",
															// 							"montoExoneracion" 		=> "",
															// 						]
															]
														];
			}

			// $json[$linea]['impuestoNeto']		= "";
			$json[$linea]['montoTotalLinea']	= (string) ($var_subotal + $var_monto_impuesto);

			$res_json += $json;
		}

		return json_encode($res_json, JSON_PRETTY_PRINT);
	}

?>