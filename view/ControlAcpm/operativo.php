<div class="card card-outline card-info mt-3">
    <div class="card-header"><h3 class="card-title">Cierre diario</h3></div>
    <div class="card-body">
        <div id="operativo_error" class="alert alert-danger d-none" role="alert"></div>
        <p><strong>Día <span id="operativo_fecha"></span>:</strong> <span id="operativo_diario">Consultando…</span></p>
        <div class="border rounded p-3 mb-3">
            <strong>Siguiente cierre pendiente: <span id="operativo_fecha_cierre">Consultando…</span></strong>
            <p id="operativo_preview_cierre" class="my-2"></p>
            <button type="button" id="btn_cerrar_operativo" class="btn btn-primary" disabled>Cerrar día</button>
            <small class="d-block text-muted mt-2">El saldo de cierre será la apertura del día siguiente.
                Asociar documentos SIESA no cambia el saldo ni el cierre.</small>
        </div>
        <h5>Últimos cierres</h5>
        <div class="table-responsive"><table class="table table-sm table-bordered">
            <thead><tr><th>Fecha</th><th>Apertura</th><th>Despachado</th><th>Saldo de cierre</th><th>Pendientes al cerrar</th><th>Responsable</th></tr></thead>
            <tbody id="operativo_cierres"></tbody>
        </table></div>
    </div>
</div>
