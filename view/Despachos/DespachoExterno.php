<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="modaldpex" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="dpex_form">
                <div class="modal-body">
                    <input type="hidden" id="ae_id" name="ae_id">
                    <span class="form-group row" id="selectAD">
                        <label for="ae_cond">Conductor:</label>
                        <select class="form-control select2" id="ae_cond" name="ae_cond" required>
                        </select>
                    </span>
                    <span class="form-group row" id="selectDO">
                        <label for="ae_obra" class="col-md-4 col-form-label">Obra:</label>
                        <select class="form-control select2bs4" id="ae_obra" name="ae_obra" required>
                        </select>
                    </span>
                    <div class="form-group">
                        <label for="ae_eds">Estacion de Servicio:</label>
                        <input type="text" class="form-control mt-2" name="ae_eds" id="ae_eds" placeholder="Escriba la Estacion de Servicio" required>
                    </div>
                    <div class="form-group">
                        <label for="ae_galones_aut">Numero de Galones:</label>
                        <input type="number" class="form-control mt-2" name="ae_galones_aut" id="ae_galones_aut" placeholder="Digite la Cantidad de Galones" required>
                    </div>
                    <input type="hidden" id="ae_estado" name="ae_estado">
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
2023 */
?>