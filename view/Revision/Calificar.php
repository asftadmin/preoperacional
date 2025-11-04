<div id="calificar" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="calificar_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="pre_formulario" id="pre_formulario" >
                            <div class="form-group">
                                <label for="pre_estado" id="lblprecalificar" ></label>
                                <div id="pre_calificar"></div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <textarea id="pre_observaciones_ver" class="textarea"  style="resize: none;" name="pre_observaciones_ver" rows="4" cols="62" placeholder="Escribe Aquí tus observaciones... "  autocapitalize="sentences" spellcheck="true"  maxlength="255"></textarea>
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
2023 */
?>