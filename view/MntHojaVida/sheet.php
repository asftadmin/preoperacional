<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "HojaVida");
if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <!-- SweetAlert -->
    <link rel="stylesheet" href="../../public/plugins/sweetalert2/sweetalert2.css">
    <title>Hoja de Vida - Mantenimiento</title>
    </head>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <?php require_once("../MainNav/nav.php"); ?>
            <?php require_once("../MainMenu/menu.php"); ?>

            <div class="content-wrapper">

                <!-- HEADER -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Hoja de Vida - Equipos</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                                    <li class="breadcrumb-item active">Hoja de Vida</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.HEADER -->

                <!-- MAIN -->
                <div class="content">
                    <div class="container-fluid">

                        <!-- FILTROS -->
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Equipo / Placa</label>
                                            <select class="form-control select2bs4" id="filtroVehiculo">
                                                <option value="">-- Selecciona un vehículo --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Rango de fechas</label>
                                            <input type="text" class="form-control" id="filtroFechas"
                                                placeholder="DD/MM/AAAA — DD/MM/AAAA">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipo mantenimiento</label>
                                            <select class="form-control" id="filtroTipoMtto">
                                                <option value="">Todos</option>
                                                <option value="1">Preventivo</option>
                                                <option value="2">Correctivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button class="btn btn-info" id="btnBuscar">
                                                    <i class="fas fa-search"></i> Buscar
                                                </button>
                                                <button class="btn btn-secondary" id="btnLimpiar">
                                                    <i class="fas fa-broom"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.FILTROS -->

                        <!-- DATOS DEL EQUIPO -->
                        <div class="card card-outline card-secondary" id="cardEquipo" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-truck"></i> Datos del equipo</h3>
                                <div class="card-tools">
                                    <button class="btn btn-danger btn-sm" id="btnExportarPDF">
                                        <i class="fas fa-file-pdf"></i> Exportar PDF
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted">Equipo</small>
                                        <p class="font-weight-bold mb-1" id="equipoNombre">—</p>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Placa</small>
                                        <p class="font-weight-bold mb-1" id="equipoPlaca">—</p>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Código</small>
                                        <p class="font-weight-bold mb-1" id="equipoCodigo">—</p>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Ubicación</small>
                                        <p class="font-weight-bold mb-1" id="equipoUbicacion">—</p>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Total mantenimientos</small>
                                        <p class="font-weight-bold mb-1" id="equipoTotal">—</p>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">Valor total repuestos</small>
                                        <p class="font-weight-bold mb-1 text-danger" id="equipoValor">—</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.DATOS DEL EQUIPO -->

                        <!-- HISTORIAL -->
                        <div class="card card-outline card-primary" id="cardHistorial" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-history"></i> Historial de mantenimientos</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-striped table-hover" id="tablaHojaVida">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th style="width:5%"></th>
                                            <th style="width:9%">Fecha</th>
                                            <th style="width:10%">N° Reporte</th>
                                            <th style="width:10%">N° OTM</th>
                                            <th style="width:12%">Tipo Mto.</th>
                                            <th style="width:14%">Km Anterior</th>
                                            <th style="width:14%">Km Actual</th>
                                            <th style="width:13%">Obra</th>
                                            <th style="width:10%">Valor repuestos</th>
                                            <th style="width:13%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyHojaVida">
                                        <!-- filas dinámicas -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.HISTORIAL -->

                    </div>
                </div>
                <!-- /.MAIN -->

                <aside class="control-sidebar control-sidebar-dark"></aside>

            </div>
            <?php require_once("../MainFooter/footer.php") ?>
        </div>

        <?php require_once("../MainJS/JS.php") ?>
        <script src="../../config/config.js"></script>
        <script src="sheet.js"></script>
        <!-- SweetAlert -->
        <script src="../../public/plugins/sweetalert2/sweetalert2.js"></script>
    </body>

    </html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>