<div id="ModalRepdia" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="Repdia_form">
                    <div class="modal-body">
                        <div class="form-group">
                        <input type="hidden" name="repdia_id" id="repdia_id" >
                        <div class="form-group">

                        <label for="repdia_kilo">KM/HR Inicial:<sup>*</sup></label>
                        <input type="text" class="form-control mt-2" name="repdia_kilo" id="repdia_kilo"  required>
                    </div>

                    <div class="form-group">
                      <label for="repdia_kilo_final">KM/HR Final:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="repdia_kilo_final" id="repdia_kilo_final"  required>
                    </div>

                    <div class="form-group">
                      <label for="repdia_obras">Obra:<sup>*</sup></label>
                      <select class="form-control select2bs4" id="repdia_obras" name="repdia_obras" style="width: 100%;"  required>
                        </select>
                    </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="boton" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                    </div>
            </form>
        </div>
    </div>
</div>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>