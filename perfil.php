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
	$active_clientes="";
	$active_usuarios="";
	$active_perfil="active";
	$title="Configuración | Sistema de Facturación";

	$query_empresa=mysqli_query($con,"select * from perfil where id_perfil=1");
	$row=mysqli_fetch_array($query_empresa);
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
			<div class="row">
				<form method="post" id="perfil">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 toppad" >
						<div class="panel panel-info">
							<div class="panel-heading">
								<h3 class="panel-title"><i class='glyphicon glyphicon-cog'></i> Configuración</h3>
							</div>
							<div class="panel-body">
								<div class="row">

									<div class="col-md-3 col-lg-3 " align="center">
										<div id="load_img">
											<img class="img-responsive" src="<?php echo $row['logo_url'];?>" alt="Logo">

										</div>
										<br>
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<input class='filestyle' data-buttonText="Logo" type="file" name="imagefile" id="imagefile" onchange="upload_image();">
												</div>
											</div>

										</div>
									</div>
									<div class=" col-md-9 col-lg-9 ">
										<table class="table table-condensed">
											<tbody>

												<tr>
													<td class='col-md-3'>Nombre de la empresa:</td>
													<td><input type="text" class="form-control input-sm" name="nombre_empresa" value="<?php echo $row['nombre_empresa']?>" required></td>
												</tr>
												<tr>
													<td class='col-md-3'>Nombre comercial:</td>
													<td><input type="text" class="form-control input-sm" name="nombre_empresa_comercial" value="<?php echo $row['nombre_empresa_comercial']?>" required></td>
												</tr>
												<tr>
													<td class='col-md-3'>Actividad:</td>
													<td>
														<select name="codigo_actividad_empresa" class="form-control select-ubicacion">
															<?php
																$content = file_get_contents(constant('codigo_actividad'));
																$data = json_decode($content);
																foreach ($data as $value) {
															?>
																<option value="<?php echo (int)key($data) ?>"
																	<?php echo (key($data) == $row['codigo_actividad_empresa']) ? "selected" : ''; ?>>
																	<?php echo ucfirst($value->actividad) ?>
																</option>
															<?php
																	next($data);
																}
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td>
														Identificación
													</td>
													<td>
														<div class="row">
															<div class="col-sm-6">
																<p>Tipo</p>
																<select name="tipo_cedula" class="form-control">
																	<?php
																		$content = file_get_contents(constant('tipo_identificacion'));
																		$data = json_decode($content);
																		foreach ($data as $value) {
																	?>
																		<option value="<?php echo $value->Codigo ?>"
																			<?php echo ($value->Codigo == $row['tipo_cedula']) ? "selected" : ''; ?>>
																			<?php echo ucfirst($value->TipoDeIdentificacion) ?>
																		</option>
																	<?php
																		}
																	?>
																</select>
															</div>
															<div class="col-sm-6">
																<p>Número</p>
																<input type="text" class="form-control input-sm" name="cedula" value="<?php echo $row['cedula']?>" required>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														Ubicación
													</td>
													<td>
														<div class="row">
															<div class="col-sm-6">
																<p>Provincia - canton - Distrito - Barrio</p>
																<select name="ubicacion" class="form-control select-ubicacion">
																	<?php
																		$content=file_get_contents(constant('codificacion_ubicacion'));
																		$data=json_decode($content);
																		foreach ($data as $value) {
																	?>
																		<option value="<?php echo $value->internalID ?>"
																			<?php echo ($value->internalID == $row['ubicacion']) ? "selected" : ''; ?>><?php echo ucfirst($value->NombreProvincia)." - ".ucfirst($value->NombreCanton)." - ".ucfirst($value->NombreDistrito)." - ".ucfirst($value->NombreBarrio); ?></option>
																	<?php
																		}
																	?>
																</select>
															</div>
															<div class="col-sm-6">
																<p>Indicación exacta</p>
																<textarea class="form-control" name="direccion" rows="3" placeholder="Ej: Av. 26 sur #9-25 segundo piso"><?php echo $row['direccion']?></textarea>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														Teléfono
													</td>
													<td>
														<div class="row">
															<div class="col-sm-6">
																<p>Código del país</p>
																<input type="text" class="form-control input-sm" name="telefono_cod" value="<?php echo $row['telefono_cod']?>" required>
																</select>
															</div>
															<div class="col-sm-6">
																<p>Número</p>
																<input type="text" class="form-control input-sm" name="telefono" value="<?php echo $row['telefono']?>" required>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														Fax
													</td>
													<td>
														<div class="row">
															<div class="col-sm-6">
																<p>Código del país</p>
																<input type="text" class="form-control input-sm" name="telefono_fax_cod" value="<?php echo $row['telefono_fax_cod']?>" required>
																</select>
															</div>
															<div class="col-sm-6">
																<p>Número</p>
																<input type="text" class="form-control input-sm" name="telefono_fax" value="<?php echo $row['telefono_fax']?>" required>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td>Correo electrónico:</td>
													<td><input type="text" class="form-control input-sm" name="email" value="<?php echo $row['email']?>" ></td>
												</tr>
												<tr>
													<td>Simbolo de moneda:</td>
													<td>
														<select class='select-moneda' name="moneda" style="width: 100%;" required>
															<?php
																$content=file_get_contents(constant('codigos_monedas'));
																$data=json_decode($content);
																foreach ($data as $value) {
															?>
																<option value="<?php echo $value->codigoMoneda; ?>" <?php echo ($value->codigoMoneda == $row['moneda']) ? "selected" : ''; ?>>
																	<?php echo $value->codigoMoneda; ?>
																</option>
															<?php
																}
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td>Mensaje en factura:</td>
													<td>
														<textarea rows="4" cols="50" class="form-control input-sm" name="mensaje_factura"><?php echo $row["mensaje_factura"];?></textarea>
													</td>
												</tr>
												</tbody>
											</table>


										</div>
										<div class='col-md-12' id="resultados_ajax"></div><!-- Carga los datos ajax -->
									</div>
								</div>
								<div class="panel-footer text-center">


									<button type="submit" class="btn btn-sm btn-success"><i class="glyphicon glyphicon-refresh"></i> Actualizar datos</button>


								</div>

							</div>
						</div>
					</form>
				</div>


				<?php
				include("footer.php");
				?>
			</body>
			</html>
			<script src="js/select2.full.min.js"></script>
			<script type="text/javascript">
				// In your Javascript (external .js resource or <script> tag)
				$(document).ready(function() {
				    $('.select-ubicacion').select2();
				    $('.select-moneda').select2();
				});
			</script>
			<script type="text/javascript" src="js/bootstrap-filestyle.js"> </script>
			<script>
				$( "#perfil" ).submit(function( event ) {
					$('.guardar_datos').attr("disabled", true);

					var parametros = $(this).serialize();
					$.ajax({
						type: "POST",
						url: "ajax/editar_perfil.php",
						data: parametros,
						beforeSend: function(objeto){
							$("#resultados_ajax").html("Mensaje: Cargando...");
						},
						success: function(datos){
							$("#resultados_ajax").html(datos);
							$('.guardar_datos').attr("disabled", false);

						}
					});
					event.preventDefault();
				})
			</script>

			<script>
				function upload_image(){

					var inputFileImage = document.getElementById("imagefile");
					var file = inputFileImage.files[0];
					if( (typeof file === "object") && (file !== null) )
					{
						$("#load_img").text('Cargando...');
						var data = new FormData();
						data.append('imagefile',file);


						$.ajax({
						url: "ajax/imagen_ajax.php",        // Url to which the request is send
						type: "POST",             // Type of request to be send, called as method
						data: data, 			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
						contentType: false,       // The content type used when sending data to the server.
						cache: false,             // To unable request pages to be cached
						processData:false,        // To send DOMDocument or non processed data file it is set to false
						success: function(data)   // A function to be called if request succeeds
						{
							$("#load_img").html(data);

						}
					});
					}


				}
			</script>
