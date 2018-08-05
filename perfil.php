<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	session_start();
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
		header("location: login.php");
		exit;
	}

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
								<h3 class="panel-title"><i class='glyphicon glyphicon-cog'></i> Configuración del emisor</h3>
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
													<td class='col-md-3'>Número de Cédula:</td>
													<td><input placeholder="La cedula debe tener 12 digitos. Completar con 0 al inicio. (3 ceros para fisica, 3 para juridica)" type="text" class="form-control input-sm" name="cedula" value="<?php echo $row['cedula']?>" required></td>
												</tr>
												<tr>
													<td>Tipo de cédula:</td>
													<td>
														<select class='form-control input-sm' name="tipo_cedula" required>
															<?php
															$sql="select name, value from  tipo_identificacion group by value order by value ";
															$query=mysqli_query($con,$sql);
															while($rw=mysqli_fetch_array($query)){
																$value=$rw['value'];
																$tipo=$rw['name'];
																if ($row['tipo_cedula']==$value){
																	$selected="selected";
																} else {
																	$selected="";
																}
																?>
																<option value="<?php echo $value;?>" <?php echo $selected;?>><?php echo ($tipo);?></option>
																<?php
															}
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td class='col-md-3'>Nombre de la empresa:</td>
													<td><input placeholder="Nombre de la Empresa" type="text" class="form-control input-sm" name="nombre_empresa" value="<?php echo $row['nombre_empresa']?>" required></td>
												</tr>
												<tr>
													<td>Teléfono:</td>
													<td><input placeholder="Ejemplo: 22334455" type="text" class="form-control input-sm" name="telefono" value="<?php echo $row['telefono']?>" required></td>
												</tr>
           										<tr>
													<td>Cod País Teléfono:</td>
													<td><input placeholder="Ejemplo: 506" type="text" class="form-control input-sm" name="cod_pais_telefono" value="<?php echo $row['cod_pais_telefono']?>" required></td>
												</tr>
           										<tr>
													<td>Fax:</td>
													<td><input placeholder="Ejemplo: 44556677" type="text" class="form-control input-sm" name="fax" value="<?php echo $row['fax']?>" required></td>
												</tr>
           										<tr>
													<td>Cod País Fax:</td>
													<td><input placeholder="Ejemplo: 506" type="text" class="form-control input-sm" name="cod_pais_fax" value="<?php echo $row['cod_pais_fax']?>" required></td>
												</tr>
												<tr>
													<td>Correo electrónico:</td>
													<td><input placeholder="Ejemplo: ejemplo@mail.com" type="email" class="form-control input-sm" name="email" value="<?php echo $row['email']?>" ></td>
												</tr>
              									<tr>
													<td>Nombre del emisor:</td>
													<td><input placeholder="Nombre completo del Emisor" type="text" class="form-control input-sm" name="nombre_emisor" value="<?php echo $row['nombre_emisor']?>" required></td>
												</tr>
												<tr>
													<td>IVA (%):</td>
													<td><input type="text" class="form-control input-sm" required name="impuesto" value="<?php echo $row['impuesto']?>"></td>
												</tr>
												<tr>
													<td>Simbolo de moneda:</td>
													<td>
														<select class='form-control input-sm' name="moneda" required>
															<?php
															$sql="select name, symbol from  currencies group by symbol order by name ";
															$query=mysqli_query($con,$sql);
															while($rw=mysqli_fetch_array($query)){
																$simbolo=$rw['symbol'];
																$moneda=$rw['name'];
																if ($row['moneda']==$simbolo){
																	$selected="selected";
																} else {
																	$selected="";
																}
																?>
																<option value="<?php echo $simbolo;?>" <?php echo $selected;?>><?php echo ($simbolo);?></option>
																<?php
															}
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td>Otras Señas:</td>
													<td><input placeholder="Ejemplo: Apartamento #35-B, porton azul con jardin" type="text" class="form-control input-sm" name="senas" value="<?php echo $row["senas"];?>" required></td>
												</tr>
                                               	<tr>
													<td>Barrio:</td>
													<td><input placeholder="Ejemplo: Barrio Calle Puente Tierra" type="text" class="form-control input-sm" name="barrio" value="<?php echo $row["barrio"];?>" required></td>
												</tr>
                                              	<tr>
													<td>Distrito:</td>
													<td><input placeholder="Ejemplo: Pozos" type="text" class="form-control input-sm" name="direccion" value="<?php echo $row["direccion"];?>" required></td>
												</tr>
												<tr>
													<td>Ciudad:</td>
													<td><input placeholder="Ejemplo: Santa Ana" type="text" class="form-control input-sm" name="ciudad" value="<?php echo $row["ciudad"];?>" required></td>
												</tr>
												<tr>
													<td>Región/Provincia:</td>
													<td><input placeholder="Ejemplo: San Jose" type="text" class="form-control input-sm" name="estado" value="<?php echo $row["estado"];?>"></td>
												</tr>
												<tr>
													<td>Código postal:</td>
													<td><input placeholder="Ejemplo: 45001" type="text" class="form-control input-sm" name="codigo_postal" value="<?php echo $row["codigo_postal"];?>"></td>
												</tr>
												<tr>
													<td>Código del Local-emisor:</td>
													<td><input placeholder="Ejemplo: 002. El 001 es para la sede central. 3 Digitos" type="text" class="form-control input-sm" name="codigo_local_emisor" value="<?php echo $row["codigo_local_emisor"];?>"></td>
												</tr>
												<tr>
													<td>Código Punto de Venta-emisor:</td>
													<td><input placeholder="Ejemplo: 00004. El 00001 es el servidor central. 5 Digitos." type="text" class="form-control input-sm" name="codigo_terminal_emisor" value="<?php echo $row["codigo_terminal_emisor"];?>"></td>
												</tr>
												<tr>
													<td>Mensaje en factura:</td>
													<td><textarea rows="4" cols="50" class="form-control input-sm" name="mensaje_factura">
														<?php echo $row["mensaje_factura"];?>
														</textarea></td>
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

