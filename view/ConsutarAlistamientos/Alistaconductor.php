<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="AlistaCond" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo3"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="cond_form">
                <div class="modal-body">
                    <div class="form-group">
                    <input type="hidden" name="alista_codigo" id="alista_codigo">
                    <span class="form-group row" id="selectCond">
                        <select class="form-control select2" id="alista_conductor" name="alista_conductor" required>
                        </select>
                    </span>
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
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>