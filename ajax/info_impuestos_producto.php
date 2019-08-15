<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
    include("../config/constants.php");

	$id_producto = $_GET['id_producto'];
	$query = mysqli_query($con, "select * from products where id_producto='".$id_producto."'");
	$row = mysqli_fetch_array($query);

	$codigoImpuesto = $row['impuesto_codigo'];

	//Imprime el nombre del impuesto
	$impuestos=file_get_contents(constant('impuestos'));
  	$data_impuestos=json_decode($impuestos);
	foreach ($data_impuestos as $value) {
		if(str_pad($value->Codigo, 2, "0", STR_PAD_LEFT) == $codigoImpuesto){
			echo $value->CodigoDelImpuesto.".<br>";
		}
	}

	//Imprime el nombre del subimpuesto tarifa IVA
	if($codigoImpuesto == "07" || $codigoImpuesto == "01" ){
		$subimp_iva=file_get_contents(constant('subimpuestos_tarifas_iva'));
	  	$data_subimp_iva=json_decode($subimp_iva);

	  	foreach ($data_subimp_iva as $value) {
			if(str_pad($value->Codigo, 2, "0", STR_PAD_LEFT) == $row['impuesto_iva_codigo']){
				echo $value->TarifaImpuestoValorAgregado.".<br>";
			}
		}
    }

    //Imprime el nombre del subimpuesto sin iva
    if($codigoImpuesto != "07" && $codigoImpuesto != "01" && $codigoImpuesto != "08")
    {
		$content = file_get_contents(constant('subimpuestos')[$codigoImpuesto]);
	  	$data = json_decode($content);

	  	foreach ($data->tipoImpuesto as $value) {
			if(str_pad($value->idimpuesto, 2, "0", STR_PAD_LEFT) == $row['imp_subimp_codigo']){
				echo $value->descripcion.".<br>";
			}
		}

		echo "Tarifa: ".$row['imp_subimp_tarifa']."% <br>";
    }


?>