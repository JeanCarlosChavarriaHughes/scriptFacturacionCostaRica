<?php
if (isset($con)){
?>
<!-- MODAL EDITAR CLIENTES -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Editar cliente</h4>
			</div>

			<div class="modal-body">
				<form class="form-horizontal" method="post" id="editar_cliente" name="editar_cliente">
					<div id="resultados_ajax2"></div>

					<div class="form-group">
						<label for="" class="col-sm-4">Nombres:</label>
						<div class="col-sm-8">
							<input type="text" class="form-control input-sm" name="mod_nombre_cliente" id="mod_nombre_cliente" value="">
						</div>
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Nombre comercial:</label>
						<div class="col-sm-8">
							<input type="text" class="form-control input-sm" name="mod_nombre_comercial_cliente" id="mod_nombre_comercial_cliente" value="">
						</div>
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Identificación:</label>
						<div class="col-sm-4">
							<p>Tipo</p>
							<select name="mod_tipo_cedula_cliente" id="mod_tipo_cedula_cliente" class="form-control">
								<?php
									$content=file_get_contents(constant('tipo_identificacion'));
									$data=json_decode($content);
									foreach ($data as $value) {
								?>
									<option value="<?php echo $value->Codigo ?>">
										<?php echo ucfirst($value->TipoDeIdentificacion) ?>
									</option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="col-sm-4">
							<p>Número</p>
							<input type="text" class="form-control input-sm" name="mod_cedula_cliente" id="mod_cedula_cliente" value="" >
							<input type="hidden" name="mod_id_cliente" id="mod_id_cliente" value="">
						</div>
						<!-- <div class="row mt-2">
							<div class="col-sm-6">
								<p>ID extranjero</p>
								<input type="text" class="form-control input-sm" name="id_extranjero_cliente" value="" >
							</div>
						</div> -->
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Ubicación:</label>
						<div class="col-sm-4">
							<p>Provincia - canton - Distrito - Barrio</p>
							<select name="mod_ubicacion_cliente" id="mod_ubicacion_cliente" class="mod-select-ubicacion" style="width: 100%;">
								<?php
									$content=file_get_contents(constant('codificacion_ubicacion'));
									$data=json_decode($content);
									foreach ($data as $value) {
								?>
									<option value="<?php echo $value->internalID ?>"><?php echo ucfirst($value->NombreProvincia)." - ".ucfirst($value->NombreCanton)." - ".ucfirst($value->NombreDistrito)." - ".ucfirst($value->NombreBarrio); ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="col-sm-4">
							<p>Indicación exacta</p>
							<textarea class="form-control" name="mod_direccion_cliente" id="mod_direccion_cliente" rows="3" placeholder="Ej: Av. 26 sur #9-25 segundo piso"></textarea>
						</div>
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Teléfono:</label>
						<div class="col-sm-4">
							<p>Código del país</p>
							<input type="text" class="form-control input-sm" name="mod_telefono_cod_cliente" id="mod_telefono_cod_cliente" value="">
						</div>
						<div class="col-sm-4">
							<p>Número</p>
							<input type="text" class="form-control input-sm" name="mod_telefono_cliente" id="mod_telefono_cliente" value="">
						</div>
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Fax:</label>
						<div class="col-sm-4">
							<p>Código del país</p>
							<input type="text" class="form-control input-sm" name="mod_telefono_fax_cod_cliente" id="mod_telefono_fax_cod_cliente" value="" >
						</div>
						<div class="col-sm-4">
							<p>Número</p>
							<input type="text" class="form-control input-sm" name="mod_telefono_fax_cliente" id="mod_telefono_fax_cliente" value="" >
						</div>
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Correo electrónico:</label>
						<div class="col-sm-8">
							<input type="text" class="form-control input-sm" name="mod_email_cliente" id="mod_email_cliente" value="">
						</div>
					</div>
					<!-- /. div>form-group -->

					<div class="form-group">
						<label for="" class="col-sm-4">Estado:</label>
						<div class="col-sm-8">
							<select class="form-control" name="mod_estado_cliente" id="mod_estado_cliente">
								<option value="">-- Selecciona estado --</option>
								<option value="1" selected>Activo</option>
								<option value="0">Inactivo</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label for="mod_moneda" class="col-sm-4">Moneda</label>
						<div class="col-sm-8">
							<select class="mod_select_moneda" style="width: 100%;" name="mod_moneda" id="mod_moneda" required>
								<?php
									$content=file_get_contents(constant('codigos_monedas'));
									$data=json_decode($content);
									foreach ($data as $value) {
								?>
									<option value="<?php echo $value->codigoMoneda; ?>">
										<?php echo $value->codigoMoneda; ?>
									</option>
								<?php
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary" id="actualizar_datos">Actualizar datos</button>
				</div>
			</form>

		</div>

	</div>
</div>
<?php
}
?>