<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="modaldss" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="dss_form" name="suboper_form">
                <div class="modal-body">
                    <div id="suboper_nombre_fields">
                        <input type="hidden" id="dss_id" name="dss_id">
                        <input type="hidden" id="dss_ae" name="dss_ae">
                        <input type="hidden" id="dss_cond" name="dss_cond" value="<?php echo $_SESSION["user_id"] ?>">
                        <span class="form-group row" id="selectOperacion">
                            <label class="form-label" for="dss_operador">Operador:</label>
                            <select class="form-control select2" id="dss_operador" name="dss_operador" required>
                            </select>
                        </span>
                        <div class="form-group">
                            <label for="dss_galones">Numero de galones:</label>
                            <input type="number" class="form-control mt-2" name="dss_galones" id="dss_galones" required>
                        </div>
                        <span class="form-group row" id="selectVehiculo">
                            <label for="dss_vehi">Vehiculo:<sup>*</sup></label>
                            <select class="form-control select2" id="dss_vehi" name="dss_vehi" style="width: 100%;" required>
                            </select>
                        </span>
                    </div>
                    <!-- <button type="button" class="btn btn-primary mt-2" id="add_field">Agregar otro campo</button> -->
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp; Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Función para agregar más campos
    document.getElementById('add_field').addEventListener('click', function() {
        // Crea un nuevo campo de texto
        var newField = document.createElement('input');
        newField.type = 'text';
        newField.className = 'form-control mt-2';
        newField.name = 'suboper_nombre[]';
        newField.placeholder = 'Digite la Suboperacion';
        newField.required = true;

        // Agregar el nuevo campo al contenedor
        document.getElementById('suboper_nombre_fields').appendChild(newField);
    });
</script>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>