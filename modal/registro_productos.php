	<?php
		include('config/constants.php');
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="nuevoProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Agregar nuevo producto</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="guardar_producto" name="guardar_producto">
			<div id="resultados_ajax_productos"></div>
			  <div class="form-group">
				<label for="codigo" class="col-sm-3 control-label">Código</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código del producto" >
				</div>
			  </div>

			  <div class="form-group">
				<label for="nombre" class="col-sm-3 control-label">Nombre</label>
				<div class="col-sm-8">
					<textarea class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto"  maxlength="255" ></textarea>
				</div>
			  </div>

			  <div class="form-group">
				<label for="estado" class="col-sm-3 control-label">Estado</label>
				<div class="col-sm-8">
				 <select class="form-control" id="estado" name="estado" >
					<option value="">-- Selecciona estado --</option>
					<option value="1" selected>Activo</option>
					<option value="0">Inactivo</option>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label for="precio" class="col-sm-3 control-label">Precio $</label>
				<div class="col-sm-8">
				  <input type="number" class="form-control" id="precio" name="precio" placeholder="Precio de venta en dolares" step="any" title="Ingresa el valor del producto" maxlength="14" >
				</div>
			  </div>
			  <div class="form-group">
				<label for="precio_colon" class="col-sm-3 control-label">Precio ¢</label>
				<div class="col-sm-8">
				  <input type="number" class="form-control" id="precio_colon" name="precio_colon" placeholder="Precio de venta en colones" step="any" title="Ingresa el valor del producto" maxlength="14" >
				</div>
			  </div>

			  <?php
			  	$content=file_get_contents(constant('unidad_medida'));
			  	$data=json_decode($content);
			  ?>
			  <div class="form-group">
				<label for="codigo" class="col-sm-3 control-label">Medida de unidad</label>
				<div class="col-sm-8">
					<select class="form-control" id="unidadMedida" name="unidadMedida" >
						<?php
							foreach ($data as $key => $value) {
						?>
							<option value=" <?php echo $value ?> " > <?php echo $key ?> </option>
						<?php
							}
						?>
					</select>
				</div>
			  </div>

			    <?php
			    	$content=file_get_contents(constant('impuestos'));
			    	$data=json_decode($content);
			    ?>
			    <div class="form-group">
			  	<label for="codigo" class="col-sm-3 control-label">Impuesto</label>
			  	<div class="col-sm-8">
			  		<select class="form-control" id="codigoImpuesto" name="codigoImpuesto" >
			  			<option value=""> -- Seleccione un impuesto -- </option>
			  			<?php
			  				foreach ($data as $value) {
			  					if($value->Codigo != 8 && $value->Codigo != 12){
			  			?>
			  				<option value="<?php echo $value->Codigo ?>" > <?php echo $value->CodigoDelImpuesto ?> </option>
			  			<?php
			  					}
			  				}
			  			?>
			  		</select>
			  	</div>
			    </div>

			    <div id="ValidateCodigoImpuesto">
			    </div>

		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			<button type="submit" class="btn btn-primary" id="guardar_datos">Guardar datos</button>
		  </div>
		  </form>
		</div>
	  </div>
	</div>
	<?php
		}
	?>