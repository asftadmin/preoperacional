<div id="modalImportar" class="modal fade bd-example-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h4 class="modal-title">Importar Repuestos desde SIESA</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;"
                        data-toggle="tooltip" title="Cerrar"></i></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="modal_id_vehiculo">
                <input type="hidden" id="modal_id_reporte">

                <!-- FILTROS -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label><b>Centro Costo:</b></label>
                        <input type="text" class="form-control" id="modal_text_vehiculo" readonly>
                    </div>

                    <div class="col-md-4">
                        <label><b>Rango de Fechas:</b></label>
                        <input type="text" class="form-control" id="modal_rango_fechas">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button onclick="cargarItemsSiesa()" class="btn btn-dark btn-block">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <!-- TABLA DE RESULTADOS -->
                <table class="table table-bordered table-striped" id="tablaSiesa">
                    <thead class="bg-light">
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Documento</th>
                            <th>Desc. Item</th>
                            <th>Cantidad</th>
                            <th>Valor Neto</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Cerrar</button>

                <button class="btn btn-dark" onclick="importarSeleccionados()">
                    <i class="fas fa-file-import"></i> Importar
                </button>
            </div>

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