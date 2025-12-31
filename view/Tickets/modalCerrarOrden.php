<div id="modalCerrarOrden" class="modal fade bd-example-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;"
                        data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="orden_form_close">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="codi_orden" name="codi_orden">
                    <div class="form-group">
                        <label for="num_orden">Número OT:</label>
                        <input type="text" class="form-control" id="num_orden" name="num_orden" placeholder="" readonly>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">Equipo operativo:</label>
                        <div class="d-flex gap-4 mt-2">

                            <div class="icheck-primary d-inline mr-2">
                                <input type="radio" id="equipo_operativo_si" name="equipo_operativo" value="1" required>
                                <label for="equipo_operativo_si">Sí</label>
                            </div>

                            <div class="icheck-primary d-inline ms-3  mr-2">
                                <input type="radio" id="equipo_operativo_no" name="equipo_operativo" value="0">
                                <label for="equipo_operativo_no">No</label>
                            </div>

                            <div class="icheck-primary d-inline ms-3">
                                <input type="radio" id="equipo_operativo_pdte" name="equipo_operativo" value="2">
                                <label for="equipo_operativo_pdte">Si, pero con pendientes</label>
                            </div>

                        </div>
                    </div>

                    <div class="form-group mt-3" id="campo_observaciones" style="display:none;">
                        <label class="fw-bold" for="observaciones_pdtes">Observaciones de Pendientes:</label>
                        <input type="text" class="form-control" id="observaciones_pdtes" name="observaciones_pdtes"
                            placeholder="">
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">Requiere solicitud en SIESA:</label>
                        <div class="d-flex gap-4 mt-2">

                            <div class="icheck-primary d-inline mr-2">
                                <input type="radio" id="req_compra_si" name="requiere_compra" value="1">
                                <label for="req_compra_si">Sí</label>
                            </div>

                            <div class="icheck-primary d-inline ms-3">
                                <input type="radio" id="req_compra_no" name="requiere_compra" value="0" checked>
                                <label for="req_compra_no">No</label>
                            </div>

                        </div>
                    </div>

                    <div class="form-group mt-3" id="campo_siesa" style="display:none;">
                        <label class="fw-bold" for="num_siesa">Número de solicitud en SIESA:</label>
                        <input type="text" class="form-control" id="num_siesa" name="num_solicitud_siesa"
                            placeholder="Ej: 001-SCS-1223">
                    </div>

                    <div class="form-group mt-3">
                        <label class="fw-bold" for="horas_progma">Horas Programadas de Mantenimiento:</label>
                        <input type="text" class="form-control" id="horas_progma" name="horas_progma"
                            placeholder="Ej: 5.00">
                    </div>

                    <div class="form-group">
                        <label for="desp_obra" class="col-form-label">Obra:</label>
                        <select class="form-control select2bs4" id="selectObras" name="selectObras" required>
                        </select>
                    </div>



                </div>

                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-dark"> <i
                            class="fas fa-lock"></i>&nbsp; Cerrar OTM</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>