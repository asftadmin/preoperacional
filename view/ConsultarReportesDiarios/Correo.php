<div id="ModalCorreo" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="correo_form">
                    <div class="modal-body">
                        <div class="form-group">
                        <input type="hidden" name="repdia_id" id="repdia_id" >

                    <div class="form-group">
                      <label for="user_email">Ingrese el Correo:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="user_email" id="user_email"  required>
                    </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="btnenviar" id="btnenviar" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
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