
<div id="modalObras" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="obras_form" name="obras_form">
                <div class="modal-body">
                    <input type="hidden" id="obras_id" name="obras_id">
                    <div class="form-group">
                      <label for="obras_codigo">Codigo:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="obras_codigo" id="obras_codigo" maxlength="150" minlength="3" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ0-9\s]+"  placeholder="Digite el Codigo" required>
                    </div>
                    <div class="form-group" >
                        <label class="form-label" for="obras_nom">Nombre de la Obra:</label>
                        <input type="text" class="form-control mt-2"  name="obras_nom" id="obras_nom" maxlength="150"  pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ><()=-0-9\s]+" placeholder="Digite el Nombre de la Obra" required>
                    </div>
                    <div class="form-group">
                            <label for="obra_estado" id="lblobraEstado" >Estado:</label>
                            <div id="pre_calificar">    
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_obra" id="lblobraTipo" >Tipo Obra:</label>
                            <div id="pre_tipo">    
                            </div>
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
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>