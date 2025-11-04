<div id="modalmtprm" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="mtprm_form">
                <div class="modal-body">
                    <input type="hidden" id="mtprm_id" name="mtprm_id">
                    <div class="form-group">
                      <label for="mtprm_nombre">Nombre de la Materia:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="mtprm_nombre" id="mtprm_nombre" maxlength="50" minlength="3" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ><=-0-9\s]+"  placeholder="Digite la Materia Prima" required>
                    </div>
                    <div class="form-group">
                        <label for="mtprm_linea" id="lblinea"></label>
                        <div id="mtprm_linea">    
                        </div>
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

