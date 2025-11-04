<div id="modalForm" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100">FORMULARIO FALLAS</h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
                <style>
                    .card-body {
                        height: 500px;
                        width: 100%;
                        overflow-y: auto;
                    }
                </style>
            </div>
            <div class="modal-body">
                <div class="card-header bg-gray mt-0">
                    <div class="row mt-2 ">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_cond">Conductor:</label>
                                <input type="text" id="user_cond" name="user_cond" style="font-size:14px;" placeholder="<?php echo $_SESSION["user_nombre"] . " " ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fech_preop">Fecha:</label>
                                <input type="text" id="fech_preop" name="fech_preop" placeholder="<?php echo date("Y-m-d") ?>" disabled>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div id="">
                    <form id="formulario_fallas">
                        
                        <input type="hidden" id="pre_user" name="pre_user" value="<?php echo $_SESSION["user_id"] ?>">
                        <input type="hidden" id="Placa" name="Placa">
                        <input type="hidden" id="form_vehiculo" name="form_vehiculo">
                        
                        <div id="form_fallas" class="card-body">
                            <!-- Las preguntas se cargarán aquí -->
                        </div>
                        <div class="modal-footer">
                            <input type="submit" id="preo_enviar" class="btn btn-info" value="Guardar">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>



</script>