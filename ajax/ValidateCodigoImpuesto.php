<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
    $codigoImpuesto = str_pad($_GET['codigoImpuesto'], 2, "0", STR_PAD_LEFT);
    include('../config/constants.php');
?>
<?php
    if($codigoImpuesto == "07" || $codigoImpuesto == "01" )
    {
	  	$content=file_get_contents(constant('subimpuestos_tarifas_iva'));
	  	$data=json_decode($content);
?>
	    <div class="form-group" id="divTarifaIva">
	    	<label for="codigo" class="col-sm-3 control-label">Tarifa IVA</label>
	    	<div class="col-sm-8">
	    		<select class="form-control" id="tarifaIva" name="tarifaIva" >
	    			<option value=""> -- Seleccione una tarifa -- </option>
	    			<?php
	    				foreach ($data as $value) {
	    			?>
	    				<option value="<?php echo $value->Codigo ?>-<?php echo $value->Tarifa ?> " > <?php echo $value->TarifaImpuestoValorAgregado ?> </option>
	    			<?php
	    				}
	    			?>
	    		</select>
	    	</div>
	    </div>
<?php
	}
?>
<?php
	if($codigoImpuesto != "07" && $codigoImpuesto != "01" && $codigoImpuesto != "08" )
    {
    	$content=file_get_contents(constant('subimpuestos')[$codigoImpuesto]);
	  	$data=json_decode($content);
?>
	<div class="form-group" id="divTarifaSubimpuesto">
		<label for="TarifaSubimpuesto" class="col-sm-3 control-label">SubImpuesto</label>
		<div class="col-sm-8">
			<select class="form-control" id="TarifaSubimpuesto" name="TarifaSubimpuesto" >
				<option value=""> -- Seleccione un subimpuesto -- </option>
				<?php
					foreach ($data->tipoImpuesto as $value) {
				?>
					<option value="<?php echo $value->idimpuesto ?>-<?php echo $value->porcentaje ?>" > <?php echo $value->descripcion ?> </option>
				<?php
					}
				?>
			</select>
		</div>
	</div>
<?php
	}
?>
