<div id="calificar" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="calificarInspe_form">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="alista_id" id="alista_id">
                        <input type="hidden" name="alista_codigo" id="alista_codigo">
                    </div>
                    <div class="form-group">
                        <label for="alista_estado" id="lblprecalificar"></label>
                        <div id="pre_calificar">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <label for="observaciones_inspe" class="col-md-4 col-form-label">Observaciones:</label>
                        <textarea id="observaciones_inspe" class="textarea" style="resize: none;" name="observaciones_inspe" rows="4" cols="62" placeholder="Escribe Aquí tus observaciones... " autocapitalize="sentences" spellcheck="true" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="boton" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp; Guardar</button>
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