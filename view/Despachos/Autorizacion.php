<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="modaldesp" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="desp_form">
                <div class="modal-body">
                    <input type="hidden" id="desp_id" name="desp_id">
                    <span class="form-group row" id="selectAD">
                        <label for="desp_vehi">Placa:<sup>*</sup></label>
                        <select class="form-control select2" id="desp_vehi" name="desp_vehi" required>
                        </select>
                    </span>
                    <span class="form-group row" id="selectDO">
                        <label for="desp_obra" class="col-md-4 col-form-label">Obra:</label>
                        <select class="form-control select2bs4" id="desp_obra" name="desp_obra" required>
                        </select>
                    </span>
                    <div class="form-group">
                        <label for="desp_galones_autorizados">Numero de Galones:<sup>*</sup></label>
                        <input type="number" class="form-control mt-2" name="desp_galones_autorizados" id="desp_galones_autorizados" placeholder="Digite la Cantidad de Galones" required>
                    </div>
                    <input type="hidden" id="desp_user" name="desp_user" value="<?php echo $_SESSION["user_id"] ?>">
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