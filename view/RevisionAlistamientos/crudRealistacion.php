<link rel="stylesheet" href="../../public/css/inicio.css">

<div id="realistacion" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo1"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="realistacion_form">
                <div class="modal-body">
                <input type="hidden" name="alista_id" id="alista_id">
                        <div class="col-md-12">
                        <div class="form-group row align-items-center">
                        <label for="alista_inspec" class="col-md-3 col-form-label">Inspector:</label>
                        <div class="col-md-9">
                            <select class="form-control select2" id="alista_inspec" name="alista_inspec"  required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="alista_obras" class="col-md-3 col-form-label">Obra:</label>
                        <div class="col-md-9">
                            <select class="form-control select2" id="alista_obras" name="alista_obras" required>
                            </select>
                        </div>
                    </div>
                    </div>
                <div class="modal-footer">
                    <button type="submit" name="boton" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp; Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
