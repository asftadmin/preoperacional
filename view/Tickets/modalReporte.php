<div id="modalRerporteMtto" class="modal fade bd-example-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;"
                        data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="rpte_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="numb_reporte">Número Reporte:</label>
                        <input type="text" class="form-control" id="numb_reporte" name="numb_reporte" placeholder=""
                            readonly>
                    </div>
                    <!--                     <div class="form-group">
                        <label for="codig_equipo">Codigo Equipo:</label>
                        <input type="text" class="form-control" id="codig_equipo" name="codig_equipo"
                            placeholder="Ingrese Codigo Interno Equipo">
                    </div> -->
                    <div class="form-group">
                        <label for="hora_reporte">Horas Programadas (*):</label>
                        <input type="text" class="form-control" id="hora_reporte" name="hora_reporte"
                            placeholder="Horas Programadas" required>
                    </div>
                    <div class="form-group">
                        <label for="fech_reporte">Fecha Asignacion (*):</label>
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input type="text" id="fecha_reporte" name="fecha_reporte"
                                class="form-control datetimepicker-input" data-target="#reservationdate" required />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <span class="form-group" id="selectObra">
                        <label for="nomb_obra">Ubicacion (Obra) * :</label>
                        <select class="select2" name="nomb_obra" id="nomb_obra" data-placeholder="Buscar Obra"
                            style="width: 100%;" required>

                        </select>
                    </span>
                    <span class="form-group" id="selectMtto">
                        <label for="tipo_mtto">Tipo de Mantenimiento (*):</label>
                        <select class="select2" name="tipo_mtto" id="tipo_mtto" data-placeholder="Tipo Mantenimiento"
                            style="width: 100%;" required>

                        </select>

                    </span>


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