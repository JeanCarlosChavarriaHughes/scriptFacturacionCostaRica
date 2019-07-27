<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
    $codigoImpuesto = str_pad($_GET['mod_codigoImpuesto'], 2, "0", STR_PAD_LEFT);
?>
<?php
    if($codigoImpuesto == "07" || $codigoImpuesto == "1")
    {
	  	$content=file_get_contents(constant('subimpuestos_tarifas_iva'));
	  	$data=json_decode($content);
?>
	    <div class="form-group" id="mod_divTarifaIva">
	    	<label for="mod_codigo" class="col-sm-3 control-label">Tarifa IVA</label>
	    	<div class="col-sm-8">
	    		<select class="form-control" id="mod_tarifaIva" name="mod_tarifaIva" >
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
	if($codigoImpuesto != "07" && $codigoImpuesto != "01" && $codigoImpuesto != "08" && $codigoImpuesto != "12")
    {
    	$subImpuestos = constant('subimpuestos');

    	$content=file_get_contents($subImpuestos[$codigoImpuesto]);
	  	$data=json_decode($content);
?>
	<div class="form-group" id="mod_divTarifaSubimpuesto">
		<label for="mod_TarifaSubimpuesto" class="col-sm-3 control-label">SubImpuesto</label>
		<div class="col-sm-8">
			<select class="form-control" id="mod_TarifaSubimpuesto" name="mod_TarifaSubimpuesto" >
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
