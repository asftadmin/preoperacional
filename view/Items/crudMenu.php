<div id="modalmenu" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="menu_form">
                <div class="modal-body">
                    <input type="hidden" id="menu_id" name="menu_id">
                    <div class="form-group">
                      <label for="menu_nom">Nombre de la Vistas:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="menu_nom" id="menu_nom" placeholder="Digite el Nombre de la Vista" required>
                    </div>
                    <div class="form-group">
                      <label for="menu_ruta">Ruta:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="menu_ruta" id="menu_ruta" placeholder="Digite la Ruta" required>
                    </div>
                    <div class="form-group">
                      <label for="menu_estado">Nivel:<sup>*</sup></label>
                      <input type="number" class="form-control mt-2" name="menu_estado" id="menu_estado" value="1" required>
                    </div>
                    <div class="form-group">
                      <label for="menu_icono">Icono:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="menu_icono" id="menu_icono" value="fas fa-angle-right nav-icon" placeholder="Escriba el codigo del Icono" required>
                    </div>
                    <div class="form-group">
                      <label for="menu_identi">Identificador:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="menu_identi" id="menu_identi" placeholder="Escriba el Identificador" required>
                    </div>
                    <div class="form-group">
                      <label for="menu_grupo">Grupo:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="menu_grupo" id="menu_grupo" placeholder="Defina el Grupo al que Pertenecera">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2025 */
?>

