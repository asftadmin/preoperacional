<div id="modalcond" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="cond_form">
                <div class="modal-body">
                <input type="hidden" id="cond_id" name="cond_id">
                    <div class="form-group">
                      <label for="conductor_usuario">Nombre del Conductor:<sup>*</sup></label>
                        <select class="form-control select2bs4" id="conductor_usuario" name="conductor_usuario" style="width: 100%;"  required>
                        </select>
                    </div>
                    <div class="form-group">
                      <label for="cond_expedicion_licencia">Fecha de Expedicion de la Lincencia:<sup>*</sup></label>
                      <input type="date" class="form-control mt-2" name="cond_expedicion_licencia" id="cond_expedicion_licencia"  required>
                    </div>

                    <div class="form-group">
                      <label for="cond_vencimiento_licencia">Fecha de Vencimiento lincencia:<sup>*</sup></label>
                      <input type="date" class="form-control mt-2" name="cond_vencimiento_licencia" id="cond_vencimiento_licencia"  required>
                    </div>

                    <div class="form-group">
                      <label for="cond_categoria_licencia">Categoria (Licencia):<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="cond_categoria_licencia" id="cond_categoria_licencia" minlength="2" maxlength="2" placeholder="Digite la Categoria de la Licencia" required>
                    </div>
                    <div class="form-group">
                      <label for="rolcond">Rol Conductor:<sup>*</sup></label>
                      <div id="pre_cal">    
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
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>