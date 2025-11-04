<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="modalsuboper" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="suboper_form" name="suboper_form">
                <div class="modal-body">
                    <input type="hidden" id="suboper_id" name="suboper_id">

                    <span class="form-group row" id="selectOperacion">
                        <label class="form-label" for="oper_id">Operacion:</label>
                            <select class="form-control select2" id="suboper_oper" name="suboper_oper" required>
                            </select>
                    </span>
                    <div class="form-group">
                        <label for="user_nombre">Nombre de la SubOperacion:<sup>*</sup></label>
                        <div id="suboper_nombre_fields">
                            <input type="text" class="form-control mt-2" name="suboper_nombre[]" id="suboper_nombre" placeholder="Digite la Suboperacion" required>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" id="add_field">Agregar otro campo</button>
                    </div>
                    <span class="form-group row" id="selectVehiculo">
                        <label for="suboper_vehi">Vehiculo:<sup>*</sup></label>
                        <select class="form-control select2" id="suboper_vehi" name="suboper_vehi" style="width: 100%;" required>
                        </select>
                        </span>
                    <div class="form-group">
                        <label for="suboper_estado" id="lbestado"></label>
                        <div id="suboper_estado"></div>
                    </div>
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