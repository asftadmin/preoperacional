<div id="modalcar" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="vehiculos_form">
                <div class="modal-body">
                  <input type="hidden" id="vehi_id" name="vehi_id">
                    <div class="form-group">
                      <label for="vehi_placa">Nº Placa:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="vehi_placa" id="vehi_placa" maxlength="200" placeholder="Digite la placa del vehiculo" required>
                    </div>
                    <div class="form-group">
                      <label for="vehi_placa">Modelo:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="vehi_modelo" id="vehi_modelo" maxlength="30"  placeholder="Digite el modelo del vehiculo" required>
                    </div>
                    <div class="form-group">
                      <label for="vehi_marca">Marca:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="vehi_marca" id="vehi_marca" maxlength="50"  placeholder="Digite la marca del vehiculo" required>
                    </div>
                    <div class="form-group">
                      <label for="vehi_soat_vence">Fecha de Vencimiento del Soat:<sup>*</sup></label>
                      <input type="date" class="form-control mt-2" name="vehi_soat_vence" id="vehi_soat_vence" required>
                    </div>
                    <div class="form-group">
                      <label for="vehi_tecnicomecanica">Tecnicomecanica :<sup>*</sup></label>
                      <input type="date" class="form-control mt-2" name="vehi_tecnicomecanica" id="vehi_tecnicomecanica" placeholder="Digite los apellidos del empleado" required>
                    </div>
                    <div class="form-group">
                      <label for="vehi_tarjeta_propiedad">Tarjeta de Propiedad :<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="vehi_tarjeta_propiedad" id="vehi_tarjeta_propiedad" maxlength="40"  placeholder="Digite el numero de la tarjeta de propiedad"  required>
                    </div>
                    <div class="form-group">
                      <label for="vehi_poliza">Poliza:</label> 
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="vehi_poliza" id="vehi_polizaSi" maxlength="6"  value="Si"  autocomplete="off" checked>
                          <label class="form-check-label" for="vehi_polizaSi">Sí</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="vehi_poliza" id="vehi_polizaNo" value="No" >
                          <label class="form-check-label" for="vehi_polizaNo">No</label>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="vehi_poliza_vence">Fecha de Vencimiento del la Poliza:<sup>*</sup></label>
                      <input type="date" class="form-control mt-2" name="vehi_poliza_vence" id="vehi_poliza_vence" value="1999-01-01" > 
                    </div>
                    <div class="form-group">
                      <label for="vehi_tipo">Tipo de Vehiculo:<sup>*</sup></label>
                        <select class="form-control select2bs4" id="vehi_tipo" name="vehi_tipo" style="width: 100%;"  required>
                        </select>
                    </div>
				  <div class="form-group">
					<label for="vehi_costo">C. Costo:<sup>*</sup></label>
					<input type="text" class="form-control mt-2" name="vehi_costo" id="vehi_costo" maxlength="50"
					  placeholder="Digite la marca del vehiculo" required>
					</select>
				  </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>