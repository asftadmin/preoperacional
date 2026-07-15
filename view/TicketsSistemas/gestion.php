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
$ticketId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$ticketId) {
    header('Location: tickets.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once '../MainHead/head.php'; ?>
<title>Gestión interna del ticket</title>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php require_once '../MainNav/nav.php'; ?>
    <?php require_once '../MainMenu/menu.php'; ?>
    <div class="content-wrapper">
        <div class="content-header"><div class="container-fluid"><div class="row mb-2">
            <div class="col-sm-7"><h1 class="m-0">Gestión interna <small id="numeroTicket" class="text-muted"></small></h1></div>
            <div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="tickets.php">Tickets</a></li><li class="breadcrumb-item active">Gestión</li></ol></div>
        </div></div></div>

        <section class="content"><div class="container-fluid">
            <input type="hidden" id="ticketId" value="<?= (int) $ticketId ?>">
            <input type="hidden" id="csrfToken" value="<?= htmlspecialchars($_SESSION['csrf_tickets_sistemas'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-ticket-alt mr-2"></i>Información del caso</h3><span id="estadoBadge" class="badge float-right"></span></div>
                        <div class="card-body">
                            <h4 id="asuntoTicket"></h4><p id="descripcionTicket" class="text-muted" style="white-space:pre-wrap"></p><hr>
                            <dl class="row mb-0"><dt class="col-sm-3">Tipo</dt><dd class="col-sm-3" id="tipoTicket"></dd><dt class="col-sm-3">Categoría</dt><dd class="col-sm-3" id="categoriaTicket"></dd><dt class="col-sm-3">Canal</dt><dd class="col-sm-3" id="canalTicket"></dd><dt class="col-sm-3">Creación</dt><dd class="col-sm-3" id="fechaTicket"></dd><dt class="col-sm-3">Ubicación</dt><dd class="col-sm-3" id="ubicacionTicket"></dd><dt class="col-sm-3">Equipo</dt><dd class="col-sm-3" id="equipoTicket"></dd></dl>
                        </div>
                    </div>
                    <div class="card card-info card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-user mr-2"></i>Empleado relacionado</h3><small id="origenEmpleado" class="float-right"></small></div>
                        <div class="card-body"><div id="alertaApi" class="alert alert-warning d-none"></div><dl class="row mb-0"><dt class="col-sm-3">Documento</dt><dd class="col-sm-3" id="empleadoDocumento"></dd><dt class="col-sm-3">Nombre</dt><dd class="col-sm-3" id="empleadoNombre"></dd><dt class="col-sm-3">Correo</dt><dd class="col-sm-3" id="empleadoCorreo"></dd><dt class="col-sm-3">Cargo</dt><dd class="col-sm-3" id="empleadoCargo"></dd><dt class="col-sm-3">Área</dt><dd class="col-sm-3" id="empleadoArea"></dd></dl></div>
                    </div>
                    <div class="card card-secondary card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-history mr-2"></i>Historial</h3></div>
                        <div class="card-body"><form id="formSeguimiento" class="mb-4"><div class="input-group"><textarea name="comentario" class="form-control" maxlength="2000" rows="2" placeholder="Registrar avance u observación" required></textarea><div class="input-group-append"><button class="btn btn-secondary" type="submit"><i class="fas fa-comment-medical mr-1"></i>Agregar</button></div></div></form><div id="listaSeguimientos" class="timeline"></div></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-warning card-outline sticky-top" style="top:1rem">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-cogs mr-2"></i>Gestión interna</h3></div>
                        <form id="formGestion"><div class="card-body">
                            <div class="form-group"><label>Estado</label><select name="estado" id="gestionEstado" class="form-control" required><option value="ABIERTO">Abierto</option><option value="EN_PROCESO">En proceso</option><option value="EN_ESPERA">En espera</option><option value="RESUELTO">Resuelto</option><option value="CERRADO">Cerrado</option><option value="CANCELADO">Cancelado</option></select></div>
                            <div class="form-group"><label>Prioridad</label><select name="prioridad" id="gestionPrioridad" class="form-control" required><option value="BAJA">Baja</option><option value="MEDIA">Media</option><option value="ALTA">Alta</option><option value="CRITICA">Crítica</option></select></div>
                            <div class="form-group"><label>Responsable</label><select name="responsable_id" id="gestionResponsable" class="form-control"><option value="">Sin asignar</option></select></div>
                            <div class="form-group"><label>Comentario de la actualización</label><textarea name="comentario_gestion" class="form-control" rows="3" maxlength="2000"></textarea></div>
                            <div class="form-group"><label>Solución</label><textarea name="solucion" id="gestionSolucion" class="form-control" rows="5" maxlength="4000" placeholder="Obligatoria al resolver o cerrar"></textarea></div>
                        </div><div class="card-footer"><button type="submit" class="btn btn-warning btn-block" id="btnGuardarGestion"><i class="fas fa-save mr-1"></i>Actualizar gestión</button><a href="tickets.php" class="btn btn-default btn-block">Volver a la bandeja</a></div></form>
                    </div>
                </div>
            </div>
        </div></section>
    </div>
    <?php require_once '../MainFooter/footer.php'; ?>
</div>
<?php require_once '../MainJS/JS.php'; ?>
<script src="../../public/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="gestion.js"></script>
</body>
</html>
