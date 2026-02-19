<div id="modalSolicitudMtto" class="modal fade bd-example-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;"
                        data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="solicitud_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="num_solicitud">Número Solicitud:</label>
                        <input type="text" class="form-control" id="num_solicitud" name="num_solicitud" placeholder=""
                            readonly>
                    </div>

                    <span class="form-group" id="selectCondu">
                        <label for="conductor_solicitud">Conductor (*):</label>
                        <select class="select2bs4" name="conductor_solicitud" id="conductor_solicitud"
                            data-placeholder="Conductor" style="width: 100%;" required>


                        </select>

                    </span>

                    <span class="form-group" id="selectVehi">
                        <label for="vehiculo_solicitud">Vehiculo (*):</label>
                        <select class="select2bs4" name="vehiculo_solicitud" id="vehiculo_solicitud"
                            data-placeholder="Vehiculo" style="width: 100%;" required>


                        </select>

                    </span>

                    <div class="form-group">
                        <label for="desc_solicitud">Falla reportada (*) :</label>

                        <textarea name="desc_solicitud" id="desc_solicitud" class="form-control" rows="4"
                            maxlength="400" style="resize: none;"
                            placeholder="Describa la falla reportada..."></textarea>
                    </div>


                    <div class="form-group">
                        <label for="lectura_solicitud">Kilometraje / Horometraje (*):</label>
                        <input type="text" class="form-control" id="lectura_solicitud" name="lectura_solicitud"
                            placeholder="Kilometraje / Horometraje" required>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i
                            class="fas fa-save"></i>&nbsp; Guardar</button>
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