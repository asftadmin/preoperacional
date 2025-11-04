<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="modalpermiso" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="permiso_form" name="suboper_form">
                <div class="modal-body">
                    <input type="hidden" id="permiso_id" name="permiso_id">

                    <span class="form-group row" id="selectVista">
                        <label class="form-label" for="permiso_menu">Vista:</label>
                            <select class="form-control select2" id="permiso_menu" name="permiso_menu" required>
                            </select>
                    </span>
                    <span class="form-group row align-items-center" id="selectRol">
                        <label for="permiso_rol" class="col-md-4 col-form-label">Rol:</label>
                            <select class="form-control select2" id="permiso_rol" name="permiso_rol[]" multiple="multiple" required>
                            </select>
                    </span>
                    <div class="form-group">
                        <label for="permiso" id="lbestado"></label>
                        <div id="permiso"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp; Guardar</button>
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
2025 */
?>