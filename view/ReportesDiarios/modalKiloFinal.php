<div id="modalKilometraje" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="repdia_form_final" name="repdia_form_final">
                <div class="modal-body">
                    <input type="hidden" id="repdia_id" name="repdia_id">
                    <div class="row mt-4 text-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" style=" width: 100%; max-width: 200px;" for="repdia_kilo_final">Kilometraje Final:</label>
                                    <input class="form" type="text" id="repdia_kilo_final"  name="repdia_kilo_final"   placeholder="Kilometraje Final"  required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="boton" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>