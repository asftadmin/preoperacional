<link rel="stylesheet" href="../../public/css/alista.css">

<div id="modalalista" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="form_alistamiento">
                <div class="modal-body">
                    <div class="card-header bg-gray mt-0">
                        <div class="row mt-2 ">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="alista_residente">Residente:</label>
                                    <input type="text" id="alista_residente" name="alista_residente" style="font-size:14px;" placeholder="<?php echo $_SESSION["user_nombre"] . " " ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fech_preop">Fecha:</label>
                                    <input type="text" id="fech_preop" name="fech_preop"style="font-size:14px;" placeholder="<?php echo date("Y-m-d") ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="repdia_hr_inic">Hora inicio:</label>
                                    <input type="text" id="repdia_hr_inic" name="user_cond" style="font-size:14px;" placeholder="<?php echo  date("H:i"); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="recibo">Codigo:</label>
                                    <input type="text" id="recibo" name="recibo" style="font-size:14px;" value="<?php echo date("Ymd") . '' . $_SESSION["user_id"]; ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <input type="hidden" id="alista_id" name="alista_id">
                    <span class="form-group row align-items-center" id="alistainspec">
                        <label for="alista_inspec" class="col-md-4 col-form-label">Inspector:</label>
                        <div class="col-md-8">
                            <select class="form-control select2" id="alista_inspec" name="alista_inspec" required>
                            </select>
                        </div>
                    </span>
                    <span class="form-group row align-items-center" id="alistaobras">
                        <label for="alista_obras" class="col-md-4 col-form-label">Obra:</label>
                        <div class="col-md-8">
                            <select class="form-control select2" id="alista_obras" name="alista_obras" required>
                            </select>
                        </div>
                    </span>
                    <span class="form-group row align-items-center" id="alistavehi">
                        <label for="alista_vehi" class="col-md-4 col-form-label">Equipos:</label>
                        <div class="col-md-8">
                            <select class="form-control select2" id="alista_vehi" name="alista_vehi[]" multiple="multiple" required>
                            </select>
                        </div>
                    </span>
                    <div class="form-group row align-items-center">
                        <label for="alista_observaciones" class="col-md-4 col-form-label">Observaciones:</label>
                        <div class="col-md-8">
                            <textarea id="alista_observaciones" class="textarea" name="alista_observaciones" rows="3" style="resize: none; width: 100%; max-width: 100%; box-sizing: border-box;" placeholder="Escribe Aquí..." autocapitalize="sentences" spellcheck="true"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="alista_residente" name="alista_residente" value="<?php echo $_SESSION["user_id"] ?>">
                    <input type="hidden" id="alista_recibo" name="alista_recibo" >
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