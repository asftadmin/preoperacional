<div id="card1">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="numb_reporte">Número Reporte:</label>
                <input type="text" class="form-control" id="numb_reporte" name="numb_reporte" placeholder="" readonly>
            </div>

            <span class="form-group" id="selectVehi">
                <label for="nomb_vehi">Vehiculo / Equipo (*):</label>
                <select class="select2" name="nomb_vehi" id="nomb_vehi" data-placeholder="Buscar Vehiculo"
                    style="width: 100%;" data-select2-id="nomb_vehi" tabindex="0" required>

                </select>
            </span>

            <div class="form-group">
                <label for="codig_equipo">Codigo Equipo:</label>
                <input type="text" class="form-control" id="codig_equipo" name="codig_equipo" placeholder="Ingrese Codigo Interno Equipo">
            </div>

            <div class="form-group">
                <label for="hora_reporte">Horas Programadas (*):</label>
                <input type="text" class="form-control" id="hora_reporte" name="hora_reporte" placeholder="Horas Programadas" required>
            </div>

        </div>

        <div class="col-sm-6">

            <div class="form-group">
                <label for="fech_reporte">Fecha Asignacion (*):</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text" name="fecha_reporte" class="form-control datetimepicker-input" data-target="#reservationdate" required/>
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

            <span class="form-group" id="selectResponsable">
                <label for="nomb_cond">Reponsable (Conductor/Operador) *:</label>
                <select class="select2" name="nomb_cond" id="nomb_cond" data-placeholder="Buscar Operador"
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

    </div>

    <div class="form-group">
        <div class="col-12">
            <button type="button" id="btnSiguiente1" class="btn btn-info float-right">
                Siguiente
            </button>
        </div>
    </div>

</div>