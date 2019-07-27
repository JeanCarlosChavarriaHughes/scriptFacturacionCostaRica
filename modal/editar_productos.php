	<?php
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Editar producto</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="editar_producto" name="editar_producto">
			<div id="resultados_ajax2"></div>
			  <div class="form-group">
				<label for="mod_codigo" class="col-sm-3 control-label">Código</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="mod_codigo" name="mod_codigo" placeholder="Código del producto" maxlength="13" required>
					<input type="hidden" name="mod_id" id="mod_id">
				</div>
			  </div>
			  <div class="form-group">
				<label for="codigo" class="col-sm-3 control-label">Tipo de Código</label>
				<div class="col-sm-8">
					<select class="form-control" id="mod_tip_cod_comerc_producto" name="mod_tip_cod_comerc_producto" >
						<?php
							$content=file_get_contents(constant('codigo_tipo_producto_servicio'));
							$data=json_decode($content);
							foreach ($data as $value) {
						?>
							<option value="<?php echo str_pad($value->Codigo, 2, '0', STR_PAD_LEFT); ?>"> <?php echo trim($value->TipoCodigoProductoServicio) ?> </option>
						<?php
							}
						?>
					</select>
				</div>
			  </div>
			   <div class="form-group">
				<label for="mod_nombre" class="col-sm-3 control-label">Nombre</label>
				<div class="col-sm-8">
				  <textarea class="form-control" id="mod_nombre" name="mod_nombre" placeholder="Nombre del producto" required></textarea>
				</div>
			  </div>

			  <div class="form-group">
				<label for="mod_estado" class="col-sm-3 control-label">Estado</label>
				<div class="col-sm-8">
				 <select class="form-control" id="mod_estado" name="mod_estado" required>
					<option value="">-- Selecciona estado --</option>
					<option value="1" selected>Activo</option>
					<option value="0">Inactivo</option>
				  </select>
				</div>
			  </div>

			  <div class="form-group">
				<label for="mod_tipo_producto" class="col-sm-3 control-label">Tipo</label>
				<div class="col-sm-8">
				 <select class="form-control" id="mod_tipo_producto" name="mod_tipo_producto" >
					<option value="">-- Selecciona --</option>
					<option value="mercancia">Mercancía</option>
					<option value="servicio">Servicio</option>
				  </select>
				</div>
			  </div>

			  <div class="form-group">
				<label for="mod_precio" class="col-sm-3 control-label">Precio $</label>
				<div class="col-sm-8">
				  <input type="number" class="form-control" id="mod_precio" name="mod_precio" placeholder="Precio de venta del producto" required step="any" title="Ingrese el valor del producto en dolares" maxlength="14">
				</div>
			  </div>
			  <div class="form-group">
				<label for="mod_precio_colon" class="col-sm-3 control-label">Precio ¢</label>
				<div class="col-sm-8">
				  <input type="number" class="form-control" id="mod_precio_colon" name="mod_precio_colon" placeholder="Precio de venta colones del producto" required step="any" title="Ingrese el valor del producto en colones" maxlength="14">
				</div>
			  </div>

			   <?php
			   	$content=file_get_contents(constant('unidad_medida'));
			   	$data=json_decode($content);
			   ?>
			   <div class="form-group">
			 	<label for="codigo" class="col-sm-3 control-label">Medida de unidad</label>
			 	<div class="col-sm-8">
			 		<select class="form-control" id="mod_unidadMedida" name="mod_unidadMedida" >
			 			<?php
			 				foreach ($data as $key => $value) {
			 			?>
			 				<option value=" <?php echo $value ?> " > <?php echo trim($key) ?> </option>
			 			<?php
			 				}
			 			?>
			 		</select>
			 	</div>

			 	<div class="form-group">
				<label for="mod_precio_colon" class="col-sm-3 control-label">Impuesto asignado</label>
					<div class="col-sm-8" style="padding-top: 1%; padding-left: 3%;">
						<div id="info_impuestos"></div>
					</div>
			  	</div>

			   </div>

			     <?php
			     	$content=file_get_contents(constant('impuestos'));
			     	$data=json_decode($content);
			     ?>
			     <div class="form-group">
			   	<label for="codigo" class="col-sm-3 control-label">Impuesto</label>
			   	<div class="col-sm-8">
			   		<select class="form-control" id="mod_codigoImpuesto" name="mod_codigoImpuesto" >
			   			<option value=""> -- Seleccione un impuesto -- </option>
			   			<?php
			   				foreach ($data as $value) {
			   			?>
			   				<option value="<?php echo $value->Codigo ?>" > <?php echo $value->CodigoDelImpuesto ?> </option>
			   			<?php
			   				}
			   			?>
			   		</select>
			   	</div>
			     </div>

			     <div id="mod_ValidateCodigoImpuesto">
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