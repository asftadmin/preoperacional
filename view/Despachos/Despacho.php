<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="modaldespacho" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="despacho_form">
                <div class="modal-body">
                    <div class="card-header bg-gray mt-0">
                        <div class="row mt-2 ">
                            <div class="col-md-6">
                                <div class="form-group row align-items-center">
                                    <label for="desp_cond" class="col-md-4 col-form-label">Placa:</label>
                                    <div class="col-md-8">
                                        <input type="text" id="vehi_placa" name="vehi_placa" style="font-size:14px;" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <input type="hidden" id="desp_id" name="desp_id">
                    <div class="form-group row align-items-center">
                        <label for="desp_cond" class="col-md-4 col-form-label">Conductor:</label>
                        <div class="col-md-8">
                            <select class="form-control select2bs4" id="desp_cond" name="desp_cond"  required>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row align-items-center">
                        <label for="desp_galones" class="col-md-4 col-form-label">Galones:</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="desp_galones" id="desp_galones" placeholder="# De Galones" required>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="desp_recibo" class="col-md-4 col-form-label">KM/HR:</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="desp_km_hr" id="desp_km_hr" placeholder="Digite el Kilometraje u Horometraje" required>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="desp_recibo" class="col-md-4 col-form-label">Observaciones:</label>
                        <div class="col-md-8">
                            <textarea id="desp_observaciones" class="textarea" name="desp_observaciones" rows="3" style="resize: none; width: 100%; max-width: 100%; box-sizing: border-box;" placeholder="Escribe Aquí..." autocapitalize="sentences" spellcheck="true"></textarea>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" name="desp_despachador" id="desp_despachador" value="<?php echo $_SESSION["user_id"] ?>">
                    <input type="hidden" class="form-control" name="desp_recibo" id="desp_recibo" >
                    <input type="hidden" class="form-control" name="desp_fech" id="desp_fech" value="<?php echo date("Ymd");?>">
                    <input type="hidden" class="form-control" name="desp_hora" id="desp_hora">
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