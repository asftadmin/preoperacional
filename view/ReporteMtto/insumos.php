<div id="card3">
    <div class="form-group">
        <label class="col-form-label" for="tableEditable"><i class="fas fa-check"></i>
            Repuestos e Insumos:</label>
        <input type="hidden" id="tableEditable">
        <button type="button" class="btn btn-primary mb-2" id="addRow"><i class="fas fa-plus"></i> Agregar
            Fila</button>
        <table id="editableTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Ref</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Serial</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>O.C</th>
                    <th>Fact.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla_body_insumos">
                <!-- Filas se agregarán dinámicamente aquí -->
            </tbody>
        </table>
    </div>
    <div class="form-group">
        <label for="total">Total Insumos:</label>
        <input type="text" class="form-control" id="total" name="total" readonly>
    </div>
    <input type="hidden" id="total_real" name="total_real">
    <!-- Preguntar si el proveedor es interno o externo -->
    <div class="form-group">
        <label>¿El proveedor es Interno o Externo?</label><br>
        <div class="icheck-primary d-inline">
            <input type="radio" id="interno" name="proveedor" value="interno" checked>
            <label for="interno">Interno</label>
        </div>
        <div class="icheck-primary d-inline">
            <input type="radio" id="externo" name="proveedor" value="externo">
            <label for="externo">Externo</label>
        </div>
    </div>

    <!-- Sección para PROVEEDOR INTERNO -->
    <div id="internoFields" class="form-group">
        <label class="col-form-label">Nombre: </label><input type="text" id="nombreInterno" name="nombre_interno" class="form-control"><br>
        <label class="col-form-label">Cargo: </label><input type="text" id="cargoInterno" name="carg_interno" class="form-control"><br>
        <label class="col-form-label">N° Orden de Trabajo: </label><input type="text" name="orden_interno" id="ordenTrabajoInterno"
            class="form-control"><br>
    </div>

    <!-- Sección para PROVEEDOR EXTERNO -->
    <div id="externoFields" class="form-group" style="display:none;">

        <input type="hidden" id="tableEditableProv">
        <button type="button" class="btn btn-primary mb-2" id="addRow2"><i class="fas fa-plus"></i> Agregar
            Proveedor</button>
        <table id="editableTableProv" class="table table-bordered">
        <thead>
            <tr>
                <th>Proveedor</th>
                <th>Orden de Trabajo</th>
                <th>Orden de Compra</th>
                <th>Factura</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Filas se agregarán dinámicamente aquí -->
        </tbody>
        </table>

    </div>
    <!-- Sección para PROVEEDOR EXTERNO -->
    <!--  <div id="externoFields" class="form-group" style="display:none;">
        <label class="col-form-label">Proveedor: </label><input type="text" id="proveedorExterno"
            class="form-control"><br>
        <label class="col-form-label">N° Orden de Trabajo: </label><input type="text" id="ordenTrabajoExterno"
            class="form-control"><br>
        <label class="col-form-label">N° Orden de Compra: </label><input type="text" id="ordenCompraExterno"
            class="form-control"><br>
        <label class="col-form-label">N° Factura: </label><input type="text" id="facturaExterno"
            class="form-control"><br>
    </div> -->

    <div class="card-footer" id="cardFooter">

        <button type="submit" 
            class="btn btn-info mt-2 float-right" id="btnGuardar">Enviar</button>
        <button type="button" id="btnAnterior3" class="btn btn-secondary mt-2 float-right" style="margin-right: 5px;">
            Anterior
        </button>
    </div>
</div>