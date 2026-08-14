<?php
require_once '../../config/conexion.php';
require_once '../../models/Rol.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . Conectar::ruta() . 'index.php');
    exit;
}
$rol = new Rol();
$acceso = $rol->validacion_acceso($_SESSION['user_id'], 'inventarioEquipos');
if (!is_array($acceso) || count($acceso) === 0) {
    header('Location: ' . Conectar::ruta() . 'index.php');
    exit;
}
if (empty($_SESSION['csrf_inventario_equipos'])) {
    $_SESSION['csrf_inventario_equipos'] = bin2hex(random_bytes(32));
}
$equipoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (isset($_GET['id']) && !$equipoId) {
    header('Location: inventario.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once '../MainHead/head.php'; ?>
<title><?= $equipoId ? 'Editar' : 'Registrar' ?> equipo de cómputo</title>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php require_once '../MainNav/nav.php'; ?>
    <?php require_once '../MainMenu/menu.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-7">
                        <h1 class="m-0">
                            <i class="fas fa-laptop mr-2"></i>
                            <span id="tituloFormulario"><?= $equipoId ? 'Editar equipo' : 'Registrar equipo' ?></span>
                        </h1>
                    </div>
                    <div class="col-sm-5">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Sistemas</li>
                            <li class="breadcrumb-item"><a href="inventario.php">Inventario</a></li>
                            <li class="breadcrumb-item active"><?= $equipoId ? 'Editar' : 'Registrar' ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <input type="hidden" id="csrfInventario"
                       value="<?= htmlspecialchars($_SESSION['csrf_inventario_equipos'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" id="equipoConsultaId" value="<?= $equipoId ? (int) $equipoId : '' ?>">

                <form id="formEquipoPagina" autocomplete="off">
                    <input type="hidden" name="equipo_id" id="equipoId" value="<?= $equipoId ? (int) $equipoId : '' ?>">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Información básica</h3>
                            <div class="card-tools"><span class="badge badge-primary">Campos obligatorios</span></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del equipo <span class="text-danger">*</span></label>
                                    <input name="nombre" class="form-control" maxlength="120"
                                           placeholder="Ej. Portátil Sistemas 01" autofocus required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Tipo <span class="text-danger">*</span></label>
                                    <select name="tipo_equipo_id" id="equipoTipo" class="form-control" required></select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Estado <span class="text-danger">*</span></label>
                                    <select name="estado_equipo_id" id="equipoEstado" class="form-control" required></select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Marca <span class="text-danger">*</span></label>
                                    <input name="marca" class="form-control" maxlength="80" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Modelo <span class="text-danger">*</span></label>
                                    <input name="modelo" class="form-control" maxlength="100" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Serial <span class="text-danger">*</span></label>
                                    <input name="serial" class="form-control text-uppercase" maxlength="120" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Ubicación <span class="text-danger">*</span></label>
                                    <select name="ubicacion_id" id="equipoUbicacion" class="form-control" required></select>
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Responsable de custodia <span class="text-danger">*</span></label>
                                    <select name="custodio_id" id="equipoCustodio" class="form-control" required></select>
                                    <small class="text-muted">La custodia inicial quedará registrada en la trazabilidad.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-barcode mr-2"></i>Identificación institucional</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4"><label>Código SIESA</label><input name="codigo_siesa" class="form-control text-uppercase" maxlength="60"></div>
                                <div class="form-group col-md-4"><label>Código de activo fijo</label><input name="codigo_activo_fijo" class="form-control text-uppercase" maxlength="60"></div>
                                <div class="form-group col-md-4"><label>Fecha de adquisición</label><input type="date" name="fecha_adquisicion" class="form-control"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-microchip mr-2"></i>Características técnicas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4"><label>Disco duro</label><input name="disco_duro" class="form-control" maxlength="120" placeholder="Ej. SSD 512 GB"></div>
                                <div class="form-group col-md-4"><label>RAM</label><input name="ram" class="form-control" maxlength="80" placeholder="Ej. 16 GB"></div>
                                <div class="form-group col-md-4"><label>Procesador</label><input name="procesador" class="form-control" maxlength="150"></div>
                                <div class="form-group col-md-4"><label>Sistema operativo</label><input name="sistema_operativo" class="form-control" maxlength="120"></div>
                                <div class="form-group col-md-4"><label>Licencia Windows</label><input name="licencia_windows" class="form-control" maxlength="180"></div>
                                <div class="form-group col-md-4"><label>Office</label><input name="office" class="form-control" maxlength="120"></div>
                                <div class="form-group col-md-4"><label>Licencia Office</label><input name="licencia_office" class="form-control" maxlength="180"></div>
                                <div class="form-group col-md-4"><label>MAC WLAN/WIFI</label><input name="mac_wlan" class="form-control text-uppercase" maxlength="50"></div>
                                <div class="form-group col-md-4"><label>MAC LAN/Ethernet</label><input name="mac_lan" class="form-control text-uppercase" maxlength="50"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-keyboard mr-2"></i>Periféricos y componentes</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4"><label>Serial del cargador</label><input name="serial_cargador" class="form-control text-uppercase" maxlength="120"></div>
                                <div class="form-group col-md-4"><label>Serial del teclado</label><input name="serial_teclado" class="form-control text-uppercase" maxlength="120"></div>
                                <div class="form-group col-md-4"><label>Serial del mouse</label><input name="serial_mouse" class="form-control text-uppercase" maxlength="120"></div>
                            </div>
                            <div id="componentesExistentes" class="d-none mb-3"></div>
                            <div id="componentesNuevos">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Agregue únicamente los accesorios que necesite individualizar.</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarComponenteFila">
                                        <i class="fas fa-plus mr-1"></i>Agregar componente
                                    </button>
                                </div>
                                <div id="componentesIniciales"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clipboard-list mr-2"></i>Mantenimiento y observaciones</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4"><label>Fecha del último mantenimiento</label><input type="date" name="fecha_mantenimiento" class="form-control"></div>
                                <div class="form-group col-md-8"><label>Observaciones</label><textarea name="observaciones" class="form-control" rows="3" maxlength="4000"></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body d-flex flex-wrap justify-content-between">
                            <a href="inventario.php" class="btn btn-default mb-2">
                                <i class="fas fa-arrow-left mr-1"></i>Cancelar
                            </a>
                            <div>
                                <?php if (!$equipoId): ?>
                                    <button type="submit" class="btn btn-outline-primary mb-2 mr-2" data-continuar="1">
                                        <i class="fas fa-plus-circle mr-1"></i>Guardar y agregar otro
                                    </button>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary mb-2" data-continuar="0">
                                    <i class="fas fa-save mr-1"></i>Guardar equipo
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <?php require_once '../MainFooter/footer.php'; ?>
</div>

<?php require_once '../MainJS/JS.php'; ?>
<script src="../../public/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="registrar_equipo.js"></script>
</body>
</html>
