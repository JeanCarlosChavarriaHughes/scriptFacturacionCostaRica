<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/

	require_once ("is_logged.php");
	/* Connect To Database*/
	require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos

	$active_facturas="";
	$active_productos="";
	$active_clientes="active";
	$active_usuarios="";
	$title="Clientes | Sistema de Facturación";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("head.php");?>
    <link href="css/select2.css" rel="stylesheet"/>
  </head>
  <body>
	<?php
	include("navbar.php");
	?>

    <div class="container">
	<div class="panel panel-info">
		<div class="panel-heading">

		    <div class="btn-group pull-right">
				<button type='button' class="btn btn-info" style="margin-left: 20px;" data-toggle="modal" data-target="#nuevoCliente"><span class="glyphicon glyphicon-plus" ></span> Nuevo Cliente</button>
			</div>

			<div class="btn-group pull-right">
				<button type='button' class="btn btn-info" data-toggle="modal" data-target="#checkHacienda"><span class="glyphicon glyphicon-plus" ></span>Check Hacienda</button>
			</div>

			<h4><i class='glyphicon glyphicon-search'></i> Buscar Clientes</h4>
		</div>
		<div class="panel-body">



			<?php
				include("modal/registro_clientes.php");
				include("modal/editar_clientes.php");
				include("modal/check_hacienda.php");

			?>
			<form class="form-horizontal" role="form" id="datos_cotizacion">

						<div class="form-group row">
							<label for="q" class="col-md-2 control-label">Cliente</label>
							<div class="col-md-5">
								<input type="text" class="form-control" id="q" placeholder="Nombre del cliente" onkeyup='load(1);'>
							</div>
							<div class="col-md-3">
								<button type="button" class="btn btn-default" onclick='load(1);'>
									<span class="glyphicon glyphicon-search" ></span> Buscar</button>
								<span id="loader"></span>
							</div>

						</div>
			</form>
				<div id="resultados"></div><!-- Carga los datos ajax -->
				<div class='outer_div'></div><!-- Carga los datos ajax -->
  			</div>
</div>

	</div>
	<hr>
	<?php
	include("footer.php");
	?>
	<!-- END Select de ubicación -->
	<script type="text/javascript" src="js/clientes.js"></script>

	<!-- Select de ubicación -->
	<script src="js/select2.full.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('.select-ubicacion').select2({
			    dropdownParent: $('#nuevoCliente .modal-content')
			});
			$('.mod-select-ubicacion').select2({
			    dropdownParent: $('#myModal2 .modal-content')
			});
			$('.select_moneda').select2({
			    dropdownParent: $('#nuevoCliente .modal-content')
			});
			$('.mod_select_moneda').select2({
			    dropdownParent: $('#myModal2 .modal-content')
			});
		});
	</script>
  </body>
</html>
