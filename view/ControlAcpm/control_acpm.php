<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "con_acpm");

if (is_array($datos) and count($datos) > 0) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <?php require_once("../MainHead/head.php"); ?>


    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
    <!-- SweetAlert -->
    <link rel="stylesheet" href="../../public/plugins/sweetalert2/sweetalert2.css">

    <title>Control ACPM</title>
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
                                <h1 class="m-0">Control operativo de combustible</h1>
                            </div>

                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="../inicio/inicio.php">Inicio</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        Control ACPM
                                    </li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /.HEADER -->

                <!-- MAIN -->
                <div class="content pb-3">
                    <div class="container-fluid">

                        <!-- ACCIONES PRINCIPALES -->
                        <div class="row mb-3">

                            <div class="col-md-12 text-right mt-2 mt-md-0">
                                <button type="button" class="btn btn-outline-primary" id="btn_actualizar_siesa">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    Actualizar SIESA
                                </button>

                                <button type="button" class="btn btn-warning" id="btn_historial_acpm">

                                    <i class="fas fa-history mr-1"></i>
                                    Ver historial

                                </button>
                            </div>

                        </div>

                        <!-- KPI -->
                        <div class="row">

                            <!-- SALDO SIESA -->
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1">
                                        <i class="fas fa-database"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Saldo actual SIESA
                                        </span>

                                        <span class="info-box-number" id="kpi_saldo_siesa">
                                            0.00 gal
                                        </span>

                                        <small class="text-muted">
                                            Corte:
                                            <span id="ultima_actualizacion_siesa">
                                                Sin consultar
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- DESPACHADO HOY -->
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success elevation-1">
                                        <i class="fas fa-gas-pump"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Despachado hoy
                                        </span>

                                        <span class="info-box-number" id="kpi_despachado_hoy">
                                            0.00 gal
                                        </span>

                                        <small class="text-muted">
                                            <span id="kpi_numero_despachos_hoy">0</span>
                                            despachos
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- PENDIENTE SIESA -->
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1">
                                        <i class="fas fa-clock"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Pendiente SIESA
                                        </span>

                                        <span class="info-box-number" id="kpi_pendiente_siesa">
                                            0.00 gal
                                        </span>

                                        <small class="text-muted">
                                            <span id="kpi_numero_pendientes_siesa">0</span>
                                            despachos sin documento
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- DISPONIBILIDAD OPERATIVA -->
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary elevation-1">
                                        <i class="fas fa-warehouse"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Disponibilidad operativa
                                        </span>

                                        <span class="info-box-number" id="kpi_disponibilidad_operativa">
                                            0.00 gal
                                        </span>

                                        <small class="text-muted">
                                            SIESA - pendiente por registrar
                                        </small>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.KPI -->



                        <!-- DISPONIBILIDAD / CONCILIACION -->
                        <div class="row">

                            <!-- DISPONIBILIDAD DEL DIA -->
                            <div class="col-lg-6 col-md-12">
                                <div class="card card-outline card-info h-100">

                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-warehouse mr-1"></i>
                                            Disponibilidad del día
                                        </h3>
                                    </div>

                                    <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">

                                                <tbody>

                                                    <tr>
                                                        <td>
                                                            Saldo consultado en SIESA
                                                        </td>
                                                        <td class="text-right font-weight-bold" id="resumen_saldo_siesa">
                                                            0.00 gal
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            Despachado durante el día
                                                        </td>

                                                        <td class="text-right font-weight-bold text-danger"
                                                            id="resumen_despachado_hoy">

                                                            0.00 gal

                                                        </td>
                                                    </tr>

                                                    <tr class="border-top">
                                                        <td>
                                                            <strong>
                                                                Disponible operativo real
                                                            </strong>
                                                        </td>
                                                        <td class="text-right font-weight-bold text-info">
                                                            <strong class="h4" id="resumen_disponibilidad_operativa">
                                                                0.00 gal
                                                            </strong>
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>
                                        </div>

                                        <div class="alert alert-light border mb-0">
                                            <i class="fas fa-calculator mr-1"></i>
                                            <strong>Disponibilidad operativa:</strong>
                                            saldo actual SIESA menos el total
                                            de ACPM despachado durante el día.

                                        </div>

                                    </div>

                                </div>
                            </div>

                            <!-- CONCILIACION -->
                            <div class="col-lg-6 col-md-12 mt-3 mt-lg-0">
                                <div class="card card-outline card-success h-100">

                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-check-double mr-1"></i>
                                            Conciliación de despachos
                                        </h3>
                                    </div>

                                    <div class="card-body">

                                        <div class="row text-center">

                                            <div class="col-4">
                                                <span class="text-muted">
                                                    Despachado
                                                </span>

                                                <div class="h3 mb-0" id="conciliacion_total_despachado">
                                                    0.00
                                                </div>

                                                <small class="text-muted">
                                                    gal
                                                </small>
                                            </div>

                                            <div class="col-4 border-left border-right">
                                                <span class="text-muted">
                                                    Con documento
                                                </span>

                                                <div class="h3 mb-0 text-success" id="conciliacion_registrado_siesa">
                                                    0.00
                                                </div>

                                                <small class="text-muted">
                                                    gal
                                                </small>
                                            </div>

                                            <div class="col-4">
                                                <span class="text-muted">
                                                    Pendiente
                                                </span>

                                                <div class="h3 mb-0 text-warning" id="conciliacion_pendiente_siesa">
                                                    0.00
                                                </div>

                                                <small class="text-muted">
                                                    gal
                                                </small>
                                            </div>

                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between">
                                            <span>
                                                Conciliado con SIESA
                                            </span>

                                            <strong id="porcentaje_conciliado_siesa">
                                                0%
                                            </strong>
                                        </div>

                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-success" id="barra_conciliacion_siesa"
                                                role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                        <!-- /.DISPONIBILIDAD / CONCILIACION -->

                        <!-- DESPACHOS POR OBRA -->
                        <div class="card card-outline card-primary mt-3">

                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-hard-hat mr-1"></i>
                                    Despachos por obra
                                </h3>

                                <div class="card-tools">
                                    <span class="badge badge-primary" id="total_despachos_obra">
                                        0.00 gal
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">

                                <!--
                                    Este contenedor será construido mediante AJAX
                                    desde ControlAcpm.js.
                                    Se recomienda usar progress bars de Bootstrap/AdminLTE
                                    para no agregar otra librería.
                                -->
                                <div id="contenedor_despachos_obra">

                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Consulte la información para visualizar
                                            los despachos por obra.
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <!-- /.DESPACHOS POR OBRA -->

                        <!-- PENDIENTES SIESA -->
                        <div class="card card-outline card-warning">

                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pendientes de registrar en SIESA
                                </h3>

                                <div class="card-tools">
                                    <span class="badge badge-warning" id="badge_pendientes_siesa">
                                        0 pendientes
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">

                                <table id="pendientes_siesa_data" name="pendientes_siesa_data"
                                    class="table table-bordered table-striped table-vcenter js-dataTable-full dt-responsive nowrap"
                                    style="width: 100%;">

                                    <thead class="bg-warning">
                                        <tr>
                                            <th class="text-center">
                                                FECHA / HORA
                                            </th>
                                            <th class="text-center">
                                                OBRA
                                            </th>
                                            <th class="text-center">
                                                EQUIPO
                                            </th>
                                            <th class="text-center">
                                                GALONES
                                            </th>
                                            <th class="text-center">
                                                TIEMPO PENDIENTE
                                            </th>
                                            <th class="text-center">
                                                DOCUMENTO SIESA
                                            </th>
                                            <th class="text-center" style="width: 10%;">
                                                ACCIONES
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>

                                </table>

                            </div>

                        </div>
                        <!-- /.PENDIENTES SIESA -->



                    </div>
                </div>
                <!-- /.MAIN -->

                <aside class="control-sidebar control-sidebar-dark">
                </aside>

            </div>

            <?php require_once("../MainFooter/footer.php") ?>

        </div>

        <!-- MODAL DOCUMENTO SIESA -->
        <div class="modal fade" id="modal_documento_siesa" tabindex="-1" role="dialog"
            aria-labelledby="modal_documento_siesa_title" aria-hidden="true">

            <div class="modal-dialog modal-md" role="document">

                <div class="modal-content">

                    <div class="modal-header bg-info">

                        <h5 class="modal-title" id="modal_documento_siesa_title">
                            <i class="fas fa-file-alt mr-1"></i>
                            Registrar documento interno SIESA
                        </h5>

                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>

                    </div>

                    <div class="modal-body">

                        <!--
                            Estos IDs son identificadores de frontend.
                            No representan nombres de columnas de base de datos.
                        -->
                        <input type="hidden" id="despacho_id_siesa" name="despacho_id_siesa">

                        <div class="form-group">
                            <label for="despacho_resumen_siesa">
                                Despacho
                            </label>

                            <input type="text" class="form-control" id="despacho_resumen_siesa" readonly>
                        </div>

                        <div class="form-group">
                            <label for="documento_interno_siesa">
                                Documento interno SIESA
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" class="form-control" id="documento_interno_siesa"
                                name="documento_interno_siesa" placeholder="Ingrese el documento registrado en SIESA"
                                autocomplete="off">
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="button" class="btn btn-info" id="btn_guardar_documento_siesa">
                            <i class="fas fa-save mr-1"></i>
                            Guardar
                        </button>

                    </div>

                </div>

            </div>

        </div>
        <!-- /.MODAL DOCUMENTO SIESA -->

        <?php require_once("../MainJS/JS.php") ?>

        <!--
            Las librerías del proyecto ya se cargan desde MainJS/JS.php.
            La lógica AJAX/DataTables/Select2/Daterangepicker/SweetAlert2
            debe quedar en este JS.
        -->
        <script type="text/javascript" src="control_acpm.js"></script>
        <!-- SweetAlert -->
        <script src="../../public/plugins/sweetalert2/sweetalert2.js"></script>

    </body>

    </html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>