<div id="modalLiquidacion" class="modal fade bd-example-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;"
                        data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="crear_liquidacion_form" name="crear_ligquidacion_form">
                <div class="modal-body">
                    <!-- <input type="hidden" id="act_id" name="act_id"> -->

                    <input type="hidden" id="user_idx" name="user_idx" value="<?php echo $_SESSION["user_id"] ?>">

                    <div class="form-group">
                        <label class="form-label" for="name_liquidacion">Nombre Liquidación::</label>
                        <input type="text" class="form-control mt-2" name="name_liquidacion" id="name_liquidacion"
                            maxlength="150" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ$.0-9\s]+"
                            title="Solo se permiten letras y espacios"
                            placeholder="Digite un nombre para la liquidación" required>
                    </div>
                    <div class="form-group">
                        <label for="fechas_extremas">Mes:<sup>*</sup></label>
                        <input type="text" class="form-control mt-2" name="fechas_extremas" id="fechas_extremas"
                            required>

                        <input type="hidden" id="fecha_inicio" name="fecha_inicio">
                        <input type="hidden" id="fecha_fin" name="fecha_fin">

                    </div>

                    <div class="form-group">

                        <div class="modal-footer">
                            <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary">
                                <i class="fas fa-save"></i>&nbsp; Guardar</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>