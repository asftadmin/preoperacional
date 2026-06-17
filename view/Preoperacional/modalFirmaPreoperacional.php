<style>
    #preFirmaCanvas {
        width: 100%;
        max-width: 460px;
        height: 180px;
        border: 1px solid #777;
        background: #fff;
        touch-action: none;
    }

    .pre-firma-help {
        font-size: 14px;
        margin-bottom: 8px;
    }
</style>

<div id="modalFirmaPreoperacional" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloFirmaPreoperacional" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="tituloFirmaPreoperacional">Firma del conductor/operador</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="pre-firma-help">Firmar a continuacion:</p>
                <canvas id="preFirmaCanvas" width="460" height="180"></canvas>
            </div>
            <div class="modal-footer">
                <button id="btnPreFirmaLimpiar" type="button" class="btn btn-warning">
                    <i class="fas fa-brush"></i>&nbsp; Limpiar
                </button>
                <button id="btnPreFirmaGuardar" type="button" class="btn btn-info">
                    <i class="fas fa-save"></i>&nbsp; Firmar y enviar
                </button>
            </div>
        </div>
    </div>
</div>
