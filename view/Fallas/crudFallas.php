<div id="modalfalla" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="falla_form" name="falla_form">
                <div class="modal-body">
                    <input type="hidden" id="id_fallas" name="id_fallas">
                    <div class="form-group">
                      <label for="user_nombre">Nombre de la Falla:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="fallas_nombre" id="fallas_nombre" maxlength="150" minlength="3" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo se permiten letras y espacios" placeholder="Digite la Falla" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="oper_id">Operacion:</label>
                        <select class="form-control select2bs4" id="fallasid_oper" name="fallasid_oper" required>
                        </select>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                    </div>
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