<?php
	require_once ("is_logged.php");
	require_once ("funciones.php");//Contiene funcion que conecta a la base de datos
	$res=getJsonDetalleServicio($_GET['id_factura']);
	echo $res;

	// $suma = 0;
	// foreach (json_decode($res) as $value) {
	// 	$suma += $value->montototallinea;
	// }

	// echo $suma;
?>