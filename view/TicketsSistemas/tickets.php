<?php
require_once '../../config/conexion.php';
require_once '../../models/Rol.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . Conectar::ruta() . 'index.php');
    exit;
}
$rol = new Rol();
$acceso = $rol->validacion_acceso($_SESSION['user_id'], 'ticketsSistemas');
if (!is_array($acceso) || count($acceso) === 0) {
    header('Location: ' . Conectar::ruta() . 'index.php');
    exit;
}
if (empty($_SESSION['csrf_tickets_sistemas'])) {
    $_SESSION['csrf_tickets_sistemas'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once '../MainHead/head.php'; ?>
<title>Mesa de servicio de Sistemas</title>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php require_once '../MainNav/nav.php'; ?>
    <?php require_once '../MainMenu/menu.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-7"><h1 class="m-0"><i class="fas fa-headset mr-2"></i>Mesa de servicio</h1></div>
                    <div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item">Inicio</li><li class="breadcrumb-item active">Tickets de Sistemas</li></ol></div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Tickets registrados</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="btnNuevoTicket"><i class="fas fa-plus mr-1"></i>Nuevo ticket</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="filtroEstado">Estado</label>
                                <select id="filtroEstado" class="form-control">
                                    <option value="">Todos</option><option value="ABIERTO">Abierto</option>
                                    <option value="EN_PROCESO">En proceso</option><option value="EN_ESPERA">En espera</option>
                                    <option value="RESUELTO">Resuelto</option><option value="CERRADO">Cerrado</option>
                                    <option value="CANCELADO">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroDocumento">Documento</label>
                                <input id="filtroDocumento" class="form-control" maxlength="20" inputmode="numeric" placeholder="Número exacto">
                            </div>
                            <div class="col-md-4">
                                <label for="filtroBuscar">Buscar</label>
                                <input id="filtroBuscar" class="form-control" maxlength="100" placeholder="Número, empleado o asunto">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="btnFiltrar" class="btn btn-outline-primary btn-block"><i class="fas fa-search mr-1"></i>Consultar</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="tablaTicketsSistemas" class="table table-bordered table-striped table-hover w-100">
                                <thead><tr><th>Número</th><th>Empleado</th><th>Asunto</th><th>Categoría</th><th>Prioridad</th><th>Estado</th><th>Responsable</th><th>Fecha</th><th></th></tr></thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once '../MainFooter/footer.php'; ?>
</div>

<div class="modal fade" id="modalTicketSistema" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
        <div class="modal-header bg-primary"><h5 class="modal-title"><i class="fas fa-ticket-alt mr-2"></i>Registrar ticket</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
        <form id="formTicketSistema" autocomplete="off">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_tickets_sistemas'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="empleado_documento" id="empleadoDocumento">
                <div class="form-group">
                    <label for="empleadoSelect">Empleado activo <span class="text-danger">*</span></label>
                    <select id="empleadoSelect" class="form-control" style="width:100%"></select>
                    <small class="form-text text-muted">Escriba el número de documento o el nombre completo y seleccione el resultado.</small>
                </div>
                <div id="resumenEmpleado" class="callout callout-info d-none">
                    <div class="row"><div class="col-md-3"><strong>Nombre</strong><div id="empleadoNombre"></div></div><div class="col-md-3"><strong>Correo</strong><div id="empleadoCorreo"></div></div><div class="col-md-3"><strong>Cargo</strong><div id="empleadoCargo"></div></div><div class="col-md-3"><strong>Área</strong><div id="empleadoArea"></div></div></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4"><label>Tipo <span class="text-danger">*</span></label><select name="tipo" class="form-control" required><option value="">Seleccione</option><option value="SOLICITUD">Solicitud</option><option value="INCIDENTE">Incidente</option><option value="REQUERIMIENTO">Requerimiento</option></select></div>
                    <div class="form-group col-md-4"><label>Categoría <span class="text-danger">*</span></label><select name="categoria_id" id="categoriaTicket" class="form-control" required><option value="">Seleccione</option></select></div>
                    <div class="form-group col-md-2"><label>Prioridad <span class="text-danger">*</span></label><select name="prioridad" class="form-control" required><option value="MEDIA">Media</option><option value="BAJA">Baja</option><option value="ALTA">Alta</option><option value="CRITICA">Crítica</option></select></div>
                    <div class="form-group col-md-2"><label>Canal <span class="text-danger">*</span></label><select name="canal" class="form-control" required><option value="">Seleccione</option><option value="LLAMADA">Llamada</option><option value="CORREO">Correo</option><option value="MENSAJE">Mensaje</option><option value="PRESENCIAL">Presencial</option><option value="SISTEMAS">Detectado por Sistemas</option></select></div>
                </div>
                <div class="form-group"><label>Asunto <span class="text-danger">*</span></label><input name="asunto" class="form-control" maxlength="150" required></div>
                <div class="form-group"><label>Descripción <span class="text-danger">*</span></label><textarea name="descripcion" class="form-control" rows="5" maxlength="4000" required></textarea></div>
                <div class="row"><div class="form-group col-md-6"><label>Ubicación o sede</label><input name="ubicacion" class="form-control" maxlength="150"></div><div class="form-group col-md-6"><label>Equipo o activo afectado</label><input name="equipo" class="form-control" maxlength="150"></div></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary" id="btnGuardarTicket"><i class="fas fa-save mr-1"></i>Guardar ticket</button></div>
        </form>
    </div></div>
</div>

<?php require_once '../MainJS/JS.php'; ?>
<script src="../../public/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="tickets.js"></script>
</body>
</html>
