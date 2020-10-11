<?php
	if (isset($con))
	{
		?>
		<!-- Modal -->
		<div class="modal fade" id="checkHacienda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Confirmar Identificación en Hacienda</h4>
					</div>
					<div class="modal-body">
						<div id="resultados_ajax_hacienda"></div>
						<form class="form-horizontal" method="get" id="check_hacienda" name="check_hacienda">

                            <div class="form-group">
                                <label for="" class="col-sm-4">Identificación:</label>
                                <div class="col-sm-4">
                                    <p>Número</p>
                                    <input type="text" class="form-control input-sm" id="cedula_cliente" name="cedula_cliente" value="">
                                </div>
                            </div>
                            <br/>
                            <div>
                                <label>Nombre: </label>
                                <span id="NombreCliente"></span>
                            </div>
                            <div>
                                <label>Tipo Identificacion: 01: Fisica, 02: Juridica, 03: DIMEX, 04, NITE</label><br/>
                                <span id="TipoIdentificacion"></span>
                            </div>
                            <br/>
                            <div>
                                <span id="ActividadesCliente"></span>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="check_hacienda_datos">Buscar</button>
                    </div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	?>
