
<div id="modalActividad" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="actividad_form" name="actividad_form">
                <div class="modal-body">
                    <input type="hidden" id="act_id" name="act_id">
                    <div class="form-group">
                      <label for="act_nombre">Nombre de la Actividad:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="act_nombre" id="act_nombre" maxlength="150" minlength="3" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ><=-0-9\s]+" title="Solo se permiten letras y espacios" placeholder="Digite la actividad" required>
                    </div>
                    <div class="form-group" >
                        <label class="form-label" for="act_tarifa">Tarifa:</label>
                        <input type="text" class="form-control mt-2"  name="act_tarifa" id="act_tarifa" maxlength="150"  pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ$.0-9\s]+" title="Solo se permiten letras y espacios" placeholder="Digite la tarifa" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="act_unidades">UND:</label>
                        <input type="text" class="form-control mt-2" name="act_unidades" id="act_unidades" maxlength="4"  style="text-transform:uppercase" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ0-9\s]+" title="Solo se permiten letras y espacios" placeholder="Digite la Unidad" required>
                    </div>
                    <div class="form-group">
                      <label for="act_tipo">Tipo de Vehiculo:<sup>*</sup></label>
                        <select class="form-control select2bs4" id="act_tipo" name="act_tipo" style="width: 100%;"  required>
                        </select>
                    
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