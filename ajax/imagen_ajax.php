	<?php
				/* Connect To Database*/
				require_once ("../config/db.php");
				require_once ("../config/conexion.php");
				require_once('vendor/autoload.php');
				if (isset($_FILES["imagefile"])){
	
				$target_dir="../img/";
				$image_name = time()."_".basename($_FILES["imagefile"]["name"]);
				$target_file = $target_dir . $image_name;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$imageFileZise=$_FILES["imagefile"]["size"];
				
					
				
				/* Inicio Validacion*/
				// Allow certain file formats
				if(($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) and $imageFileZise>0) {
				$errors[]= "<p>Lo sentimos, sólo se permiten archivos JPG , JPEG, PNG y GIF.</p>";
				} else if ($imageFileZise > 1048576) {//1048576 byte=1MB
				$errors[]= "<p>Lo sentimos, pero el archivo es demasiado grande. Selecciona logo de menos de 1MB</p>";
				}  else
			{
				
				
				
				/* Fin Validacion*/
				if ($imageFileZise>0){
					$constant = 'constant';

					if (APPLICATION_ENV == 'local-develop' || APPLICATION_ENV == 'local-prod') {
						move_uploaded_file($_FILES["imagefile"]["tmp_name"], $target_file);
						$logo_update="logo_url='img/$image_name' ";
					} elseif (APPLICATION_ENV == 'remote-develop' || APPLICATION_ENV == 'remote-prod') {
						// Subir a Internet
						$s3 = new Aws\S3\S3Client([
							'region'  => 'us-east-1',
							'version' => 'latest',
							'credentials' => [
								'key'    => CLOUDCUBE_ACCESS_KEY_ID,
								'secret' => CLOUDCUBE_SECRET_ACCESS_KEY,
							]
						]);
						$key="/public/img/logo.jpg";
						$logo_update="logo_url='{$constant('CLOUDCUBE_URL')}$key' ";
						$result = $s3->putObject([
							'Bucket' => 'cloud-cube',
							'Key'    => $key,
							//'Body'   => 'this is the body!',
							'SourceFile' => $_FILES["imagefile"]["tmp_name"]
						]);
						var_dump($result);

					} else {
						// Condición desconocida
						$errors[]= "<p>Valor de APPLICATION_ENV desconocido: </p>";
						$errors[]= "<p>Expected: local-develop, local-prod, remote-prod, remote-develop </p>";
						$errors[]= "<p>Actual: {$constant('APPLICATION_ENV')} </p>";

						move_uploaded_file($_FILES["imagefile"]["tmp_name"], $target_file);
						$logo_update="logo_url='img/$image_name' ";
					}
				}	else { 
					$logo_update="";
				}
				$sql = "UPDATE perfil SET $logo_update WHERE id_perfil='1';";
				$query_new_insert = mysqli_query($con,$sql);

				
				if ($query_new_insert) {
					?>
					<img class="img-responsive" src="img/<?php echo $image_name;?>" alt="Logo">
					<?php
				} else {
					$errors[] = "Lo sentimos, actualización falló. Intente nuevamente. ".mysqli_error($con);
				}
			}
		}	
				
				
				
		
	?>
	<?php 
		if (isset($errors)){
	?>
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error! </strong>
		<?php
			foreach ($errors as $error){
				echo $error;
			}
		?>
		</div>	
	<?php
			}
	?>
