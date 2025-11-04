<div id="modaltipovehi" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="tipovehi_form">
                <div class="modal-body">
                    <input type="hidden" id="tipo_id" name="tipo_id">
                    <div class="form-group">
                      <label for="tipo_nombre">Nombre del Tipo de Vehiculo:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="tipo_nombre" id="tipo_nombre" maxlength="40" minlength="2"  placeholder="Digite el nombre del Tipo de vehiculo" required>
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
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>