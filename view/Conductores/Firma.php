<style>
        #canvas {
            border: 1px solid black;
        }
    </style>
<div id="modalFirma" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="firma_form">
                <div class="modal-body">
                <input type="hidden" id="repdia_recib" name="repdia_recib">
                <center><p>Firmar a continuación:</p>
                <canvas id="canvas"></canvas>
                </div></center>
                <div class="modal-footer">
                    <button button id="btnLimpiar" type="button" value="add" class="btn btn-rounded btn-warning"> <i class="fas fa-brush"></i>&nbsp;  Limpiar</button>
                    <button type="button" name="action" id="btnGuardar" value="add" class="btn btn-rounded btn-info"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                    <button type="button" class="close" id="btnDescargar" data-dismiss="modal"><i class="fas fa-download" style="color: red;" data-toggle="tooltip" ></i></button>
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
2023 */
?>