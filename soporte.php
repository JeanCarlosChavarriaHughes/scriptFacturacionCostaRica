<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	require_once ("is_logged.php");
	// if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	// 	header("location: login.php");
	// 	exit;
	// }

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
			<?php
			// echo $_SESSION['api_token'];
			// echo time()."<br>";
			// echo date("F d, Y h:i:s A", $_SESSION['api_token_expires_in'])."<br>";
			// if ( time() > (1558540360 + 300) ) {
			//  	echo "Excede";
			//  }else{
			//  	echo "No excede";
			//  }

			?>
			<div class="row">
				<form method="post" id="perfil">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 toppad" >
						<div class="panel panel-info">
							<div class="panel-heading">
								<h3 class="panel-title"><i class='glyphicon glyphicon-cog'></i> Soporte Técnico</h3>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3 col-lg-3">
										<div id="load_img">
											<!-- <img class="img-responsive" src="<?php echo $row['logo_url'];?>" alt="Logo"> -->
										</div>
										<br>
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<input class='filestyle' data-buttonText=".p12" type="file" name="localfile" id="localfile">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-inline">
													<div class="input-group">
														<input type="text" class="form-control" id="urlfile" placeholder="URL file">
														<div class="input-group-addon" style="cursor: pointer;" id="subirButton">
															Subir
															<!-- <button type="submit" class="btn btn-primary btn-sm">Subir</button> -->
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class=" col-md-9 col-lg-9 ">
										<table class="table table-condensed">
											<tbody>
												<tr>
													<td class='col-md-3'>API username:</td>
													<td><input type="text" class="form-control input-sm" name="usernameAPI" value="<?php echo $row['usernameAPI']?>" ></td>
												</tr>
												<tr>
													<td class='col-md-3'>API password:</td>
													<td><input type="text" class="form-control input-sm" name="passwordAPI" value="<?php echo $row['passwordAPI']?>" ></td>
												</tr>
												<tr>
													<td>API IDuser:</td>
													<td><input type="text" class="form-control input-sm" name="iduserapi" value="<?php echo $row['iduserapi']?>"></td>
												</tr>
												<tr>
													<td>Hacienda username:</td>
													<td><input type="text" class="form-control input-sm" name="key_username" value="<?php echo $row['key_username']?>"></td>
												</tr>
												<tr>
													<td>Hacienda password:</td>
													<td><input type="text" class="form-control input-sm"  name="key_password" value="<?php echo $row['key_password']?>"></td>
												</tr>
												<tr>
													<td>PIN P12:</td>
													<td><input type="text" class="form-control input-sm"  name="pin_p12" value="<?php echo $row['pin_p12']?>"></td>
												</tr>
												<tr>
													<td>Acerca:</td>
													<td><textarea class="form-control input-sm"  name="acercade"><?php echo $row['acercade']?></textarea></td>
												</tr>
												<tr>
													<td>Ubicación:</td>
													<td><input type="text" class="form-control input-sm"  name="ubicacion" value="<?php echo $row['ubicacion']?>"></td>
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
			<script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
			<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
			<script>
				$( "#perfil" ).submit(function( event ) {
					$('.guardar_datos').attr("disabled", true);

					var parametros = $(this).serialize();
					$.ajax({
						type: "POST",
						url: "ajax/soporte_editar_perfil.php",
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
					// var inputFileImage = document.getElementById("urlfile");
					$('#subirButton').click(function(){
						var inputUrlFile = document.getElementById("urlfile");

						$.ajax({
							type: "POST",
							url: "ajax/soporte_upload_file.php",
							data: {
								'nameFile'	: inputUrlFile.value,
								'by'		: 'url',
							},
							beforeSend: function(objeto){
								$("#resultados_ajax").html("Mensaje: Cargando...");
							},
							success: function(datos){
								$("#resultados_ajax").html(datos);
								$('.guardar_datos').attr("disabled", false);
							}
						});
					});
			</script>

			<script>
					// var inputFileImage = document.getElementById("urlfile");
					$('#localfile').change(function(){
						var inputLocalFile = document.getElementById("localfile");
						var file = inputLocalFile.files[0];
						// console.log(file);
						// return true;

						if(file.type != "application/x-pkcs12"){
							$("#resultados_ajax").html("<div class='alert alert-danger' role='alert'><button type='button' class='close' data-dismiss='alert'>&times;</button> <b>¡Error!</b> Solo puede subir archivos de tipo <b>.p12</b></div>");
							return false;
						}

						var formData = new FormData();
						formData.append('filetoupload', file);
						formData.append('by', 'local');

						$("#resultados_ajax").html("Mensaje: Cargando...");
						axios.post('ajax/soporte_upload_file.php',
						    formData,
						    {
						        headers: {
						            'Content-Type': 'multipart/form-data'
						        }
						    }
						).then(function(response){
						    $("#resultados_ajax").html(response.data.message);
						}).catch(function(error){
							$("#resultados_ajax").html("<div class='alert alert-danger' role='alert'><button type='button' class='close' data-dismiss='alert'>&times;</button> <b>¡Error!</b> Algo ha salido mal, intenta de nuevo.</div>");
						});

					});
			</script>

