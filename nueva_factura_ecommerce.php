<?php
	/*-------------------------
	Autor: JeanCarlos Chavarria
	Web: https://jeancarloschavarriahughes.github.io
	Mail: negrotico19@gmail.com
    ---------------------------*/
    
	$active_facturas="active";
	$active_productos="";
	$active_clientes="";
	$active_usuarios="";
	$title="Nueva Factura | Sistema de Facturacion";

	/* Connect To Database*/
	require_once ("config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
	require_once ("config/conexion.php"); //Contiene funcion que conecta a la base de datos
    require_once ("funciones.php"); //Contiene funcion que conecta a la base de datos
    require_once ("config/constants_min.php"); //Contiene los valores de Condicion, Medios de Pago, Moneda y otros.

	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<?php include("head.php");?>
		<link href="css/select2.min.css" rel="stylesheet"/>
	</head>
	<body>
		<?php
		include("navbar.php");
		?>
		<div class="container">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4><i class='glyphicon glyphicon-edit'></i> Nueva Factura</h4>
				</div>
				<div class="panel-body">
					<?php
					include("modal/buscar_productos.php");
					include("modal/registro_clientes.php");
					include("modal/registro_productos.php");
					?>
					<form class="form-horizontal" role="form" id="datos_factura">
						<div class="form-group row">
							<div class="col-md-3">
								<label for="">Cliente</label>
								<input type="text" class="form-control input-sm" id="nombre_cliente" placeholder="Selecciona un cliente" required>
								<input id="id_cliente" type='hidden'>
							</div>
							<!-- /.col-md -->

							<div class="col-md-3">
								<label for="">Teléfono</label>
								<input type="text" class="form-control input-sm" id="tel1" placeholder="Teléfono" readonly>
							</div>
							<!-- /.col-md -->

							<div class="col-md-3">
								<label for="">Email</label>
								<input type="text" class="form-control input-sm" id="mail" placeholder="Email" readonly>
							</div>
							<!-- /.col-md -->

							<div class="col-md-3">
								<label for="" class="">Fecha</label>
								<input type="text" class="form-control input-sm" id="fecha" value="<?php echo date("d/m/Y");?>" readonly>
							</div>
							<!-- /.col-md -->
						</div>

						<!-- /.form-group -->

						<div class="form-group row">

							<div class="col-md-3">
								<label for="">Vendedor</label>
								<select class="form-control input-sm" id="id_vendedor">
									<?php
									$sql_vendedor=mysqli_query($con,"select * from users where user_id = 1");
									while ($rw=mysqli_fetch_array($sql_vendedor)){
										$id_vendedor=$rw["user_id"];
										$nombre_vendedor=$rw["firstname"]." ".$rw["lastname"];
										if ($id_vendedor==$_SESSION['user_id']){
											$selected="selected";
										} else {
											$selected="";
										}
										?>
										<option value="<?php echo $id_vendedor?>" <?php echo $selected;?>><?php echo $nombre_vendedor?></option>
										<?php
									}
									?>
								</select>
							</div>
							<!-- /.col-md -->

							<div class="col-md-3">
								<label for="">Condición</label>
								<select class='form-control input-sm' id="condiciones">
									<?php
										$content=file_get_contents(constant('condiciones_venta'));
										$data=json_decode($content);
										foreach ($data as $value) {
                                            if ($value->CondicionesDeLaVenta == "Contado") {
									?>
										<option value="<?php echo $value->Codigo ?>">
											<?php echo ucfirst($value->CondicionesDeLaVenta) ?>
										</option>
									<?php
                                            break;
                                            }
                                        }
									?>
								</select><br>
								<div id="plazo_credito" style="display: none;">
									<label for="">Plazo crédito en días</label>
									<input type="text" id="plazo_credito_dias" class="form-control">
								</div>
							</div>
							<!-- /.col-md -->

							<div class="col-md-3">
								<label for="">Medio de pago</label>
								<select class='form-control input-sm' id="medio_pago">
									<?php
										$content=file_get_contents(constant('medios_pago'));
										$data=json_decode($content);
										foreach ($data as $value) {
									?>
										<option value="<?php echo str_pad($value->Codigo, 2, '0', STR_PAD_LEFT); ?>">
											<?php echo ucfirst($value->MediosDePago) ?>
										</option>
									<?php
										}
									?>
								</select>
							</div>
							<!-- /.col-md -->

							<div class="col-md-3">
								<label for="">Moneda</label><br>
								<select class='select-moneda' id="moneda" style="width: 100%;">
									<?php
										$content=file_get_contents(constant('codigos_monedas'));
										$data=json_decode($content);
										foreach ($data as $value) {
									?>
										<option value="<?php echo $value->codigoMoneda; ?>"
                                            <?php
                                                if ($value->codigoMoneda == "CRC") {
                                            ?>
                                                selected
                                            <?php
                                                }
                                            ?>
                                            >
											<?php echo $value->codigoMoneda; ?>
										</option>
									<?php
										}
									?>
								</select>
							</div>
						</div>


						<div class="col-md-12">
                            <div class="pull-left">
                                <input type="checkbox" id="send_link" value="send_link" Checked disabled>Enviar <b>Link de Pago</b> en Línea
                            </div>
							<div class="pull-right">
								<button type="button" class="btn btn-warning button-in-mobile" data-toggle="modal" data-target="#nuevoCliente">
									<span class="glyphicon glyphicon-user"></span> Nuevo cliente
								</button>
								<button type="button" class="btn btn-info button-in-mobile" data-toggle="modal" data-target="#agregarProductos">
									<span class="glyphicon glyphicon-search"></span> Agregar productos
								</button>
								<button type="submit" class="btn btn-primary button-in-mobile">
									<span class="glyphicon glyphicon-print"></span> Guardar e Imprimir
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<!-- Carga los datos ajax -->
					<div id="resultados" style="margin-bottom: 5% !important;"></div>
				</div>
			</div>
		</div>
		<hr>
		<?php
		include("footer.php");
		?>
		<script type="text/javascript" src="js/VentanaCentrada.js"></script>
		<script type="text/javascript" src="js/nueva_factura.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<!-- Select de ubicación -->
		<script src="js/select2.full.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
			    $('.select-ubicacion').select2({
				    dropdownParent: $('#nuevoCliente .modal-content')
				});
				$('.select-moneda').select2();
			});
		</script>
		<!-- END Select de ubicación -->
		<script>
			$(function() {
				$("#nombre_cliente").autocomplete({
					source: "./ajax/autocomplete/clientes.php",
					minLength: 2,
					select: function(event, ui) {
						event.preventDefault();
						$('#id_cliente').val(ui.item.id_cliente);
						$('#nombre_cliente').val(ui.item.nombre_cliente);
						$('#tel1').val(ui.item.telefono_cliente);
						$('#mail').val(ui.item.email_cliente);

						$('#moneda').val(ui.item.moneda);
						$('#moneda').trigger('change');

						load(1);
					}
				});


			});

			$("#nombre_cliente" ).on( "keydown", function( event ) {
				if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
				{
					$("#id_cliente" ).val("");
					$("#tel1" ).val("");
					$("#mail" ).val("");
					$("#moneda" ).val("");

				}
				if (event.keyCode==$.ui.keyCode.DELETE){
					$("#nombre_cliente" ).val("");
					$("#id_cliente" ).val("");
					$("#tel1" ).val("");
					$("#mail" ).val("");
					$("#moneda" ).val("");
				}
			});
		</script>

	</body>
	</html>