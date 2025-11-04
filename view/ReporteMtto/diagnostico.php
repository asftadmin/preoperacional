<div id="card2">
    <div class="form-group">
        <label class="col-form-label" for="summernote_inicial"><i class="fas fa-check"></i>
            Diagnostico Inicial (*):</label>
        <textarea id="summernote_inicial" name="deta_diag_inici" class="form-control summernote" required>

                                        </textarea>

    </div>

    <div class="form-group">
        <label class="col-form-label" for="summernote_descripcion"><i class="fas fa-check"></i>
            Descripcion Mantenimiento (*):</label>
        <textarea id="summernote_descripcion" name="deta_desc_mtto" class="form-control summernote" required>

                                        </textarea>

    </div>

    <div class="form-inline">
        <label class="col-form-label mr-2" for="radioEstadFinal"><i class="fas fa-check"></i>
            Estado Final (*):</label>
        <div class="form-group ">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="deta_esta_fina" id="radio_oper" value="1" checked>
                <label class="form-check-label" for="radio_oper">Operativo</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" id="radio_segui" type="radio" name="deta_esta_fina" value="2">
                <label class="form-check-label" for="radio_segui">En Seguimiento</label>
            </div>

        </div>
    </div>

    <div class="form-group row">
        <div class="col-12">
            <button type="button" id="btnSiguiente2" class="btn btn-info float-right">
                Siguiente
            </button>
            <button type="button" id="btnAnterior2" class="btn btn-secondary float-right" style="margin-right: 5px;">
                Anterior
            </button>
        </div>
    </div>
</div>