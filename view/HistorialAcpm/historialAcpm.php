<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "Des");

if (is_array($datos) and count($datos) > 0) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <?php require_once("../MainHead/head.php"); ?>

    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">

    <title>Historial ACPM</title>
    </head>

    <body class="hold-transition sidebar-mini">

        <div class="wrapper">

            <?php require_once("../MainNav/nav.php"); ?>
            <?php require_once("../MainMenu/menu.php"); ?>

            <div class="content-wrapper">

                <!-- HEADER -->
                <div class="content-header">
                    <div class="container-fluid">

                        <div class="row mb-2 align-items-center">

                            <div class="col-sm-6">

                                <h1 class="m-0">
                                    Historial de control ACPM
                                </h1>

                                <small class="text-muted">
                                    Consulta histórica de despachos
                                </small>

                            </div>

                            <div class="col-sm-6 text-right">

                                <ol class="breadcrumb d-inline-flex mb-0 p-0 bg-transparent">

                                    <li class="breadcrumb-item">
                                        <a href="../inicio/inicio.php">
                                            Inicio
                                        </a>
                                    </li>

                                    <li class="breadcrumb-item">
                                        <a href="../ControlAcpm/ControlAcpm.php">
                                            Control ACPM
                                        </a>
                                    </li>

                                    <li class="breadcrumb-item active">
                                        Historial
                                    </li>

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
                        <div class="card card-outline card-secondary">

                            <div class="card-header">

                                <h3 class="card-title">
                                    <i class="fas fa-filter mr-1"></i>
                                    Filtros de consulta
                                </h3>

                                <div class="card-tools">

                                    <button type="button" class="btn btn-secondary btn-sm" id="btn_limpiar_filtros">

                                        <i class="fas fa-eraser mr-1"></i>
                                        Limpiar filtros

                                    </button>

                                    <button type="button" class="btn btn-primary btn-sm" id="btn_volver_control_acpm">

                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Regresar

                                    </button>

                                </div>

                            </div>

                            <div class="card-body">

                                <form id="form_historial_acpm">

                                    <div class="row">


                                        <!-- PERIODO -->
                                        <div class="col-lg-4 col-md-6 col-sm-12">

                                            <div class="form-group">

                                                <label for="rango_fechas_historial">
                                                    Período
                                                </label>

                                                <div class="input-group">

                                                    <div class="input-group-prepend">

                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>

                                                    </div>

                                                    <input type="text" class="form-control" id="rango_fechas_historial"
                                                        name="rango_fechas_historial" placeholder="Seleccione el período"
                                                        autocomplete="off">

                                                </div>

                                            </div>

                                        </div>


                                        <!-- OBRA -->
                                        <div class="col-lg-2 col-md-6 col-sm-12">

                                            <div class="form-group">

                                                <label for="historial_obra">
                                                    Obra
                                                </label>

                                                <select class="form-control select2" id="historial_obra"
                                                    name="historial_obra" style="width: 100%;">

                                                    <option value="">
                                                        Todas las obras
                                                    </option>

                                                </select>

                                            </div>

                                        </div>


                                        <!-- EQUIPO -->
                                        <div class="col-lg-2 col-md-6 col-sm-12">

                                            <div class="form-group">

                                                <label for="historial_equipo">
                                                    Equipo
                                                </label>

                                                <select class="form-control select2" id="historial_equipo"
                                                    name="historial_equipo" style="width: 100%;">

                                                    <option value="">
                                                        Todos los equipos
                                                    </option>

                                                </select>

                                            </div>

                                        </div>


                                        <!-- ESTADO SIESA -->
                                        <div class="col-lg-2 col-md-6 col-sm-12">

                                            <div class="form-group">

                                                <label for="historial_estado_siesa">
                                                    Estado SIESA
                                                </label>

                                                <select class="form-control select2" id="historial_estado_siesa"
                                                    name="historial_estado_siesa" style="width: 100%;">

                                                    <option value="">
                                                        Todos
                                                    </option>

                                                    <option value="cargado">
                                                        Cargado SIESA
                                                    </option>

                                                    <option value="pendiente">
                                                        Pendiente SIESA
                                                    </option>

                                                </select>

                                            </div>

                                        </div>


                                        <!-- CONSULTAR -->
                                        <div class="col-lg-2 col-md-12 col-sm-12 d-flex align-items-end">

                                            <div class="form-group w-100">

                                                <button type="submit" class="btn btn-primary btn-block"
                                                    id="btn_consultar_historial">

                                                    <i class="fas fa-search mr-1"></i>
                                                    Consultar

                                                </button>

                                            </div>

                                        </div>


                                    </div>

                                </form>

                            </div>

                        </div>
                        <!-- /.FILTROS -->


                        <!-- RESUMEN DEL FILTRO -->
                        <div class="row" id="contenedor_resumen_historial" style="display: none;">

                            <div class="col-lg-4 col-md-6 col-sm-12">

                                <div class="info-box">

                                    <span class="info-box-icon bg-primary elevation-1">

                                        <i class="fas fa-gas-pump"></i>

                                    </span>

                                    <div class="info-box-content">

                                        <span class="info-box-text">
                                            Galones despachados
                                        </span>

                                        <span class="info-box-number" id="historial_total_galones">

                                            0.00 gal

                                        </span>

                                        <small class="text-muted" id="historial_resumen_filtro">

                                            Resultado de la consulta

                                        </small>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <!-- /.RESUMEN DEL FILTRO -->


                        <!-- HISTORIAL -->
                        <div class="card card-outline card-dark">

                            <div class="card-header">

                                <h3 class="card-title">

                                    <i class="fas fa-history mr-1"></i>
                                    Historial de despachos

                                </h3>

                            </div>

                            <div class="card-body">


                                <table id="historial_acpm_data" name="historial_acpm_data"
                                    class="table table-bordered table-striped table-vcenter js-dataTable-full dt-responsive nowrap"
                                    style="width: 100%;">

                                    <thead class="bg-info">

                                        <tr>

                                            <th class="text-center">
                                                FECHA
                                            </th>

                                            <th class="text-center">
                                                HORA
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
                                                RECIBO INTERNO
                                            </th>

                                            <th class="text-center">
                                                DOCUMENTO SIESA
                                            </th>

                                            <th class="text-center">
                                                CONDUCTOR
                                            </th>


                                            <th class="text-center">
                                                ESTADO
                                            </th>

                                            <th class="text-center" style="width: 5%;">

                                                ACCIÓN

                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>
                                    </tbody>


                                    <tfoot>

                                        <tr>

                                            <th colspan="4" class="text-right">

                                                Total galones filtrados:

                                            </th>

                                            <th class="text-center" id="historial_total_galones_tabla">

                                                0.00

                                            </th>

                                            <th colspan="5">
                                            </th>

                                        </tr>

                                    </tfoot>

                                </table>


                            </div>

                        </div>
                        <!-- /.HISTORIAL -->


                    </div>

                </div>
                <!-- /.MAIN -->


                <aside class="control-sidebar control-sidebar-dark">
                </aside>


            </div>


            <?php require_once("../MainFooter/footer.php") ?>


        </div>


        <!-- MODAL DETALLE DESPACHO -->
        <div class="modal fade" id="modal_detalle_historial" tabindex="-1" role="dialog"
            aria-labelledby="modal_detalle_historial_title" aria-hidden="true">

            <div class="modal-dialog modal-lg" role="document">

                <div class="modal-content">


                    <div class="modal-header bg-info">

                        <h5 class="modal-title" id="modal_detalle_historial_title">

                            <i class="fas fa-file-alt mr-1"></i>
                            Detalle del despacho

                        </h5>


                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">

                            <span aria-hidden="true">
                                &times;
                            </span>

                        </button>

                    </div>


                    <div class="modal-body">

                        <input type="hidden" id="historial_desp_id" name="historial_desp_id">


                        <div class="row">


                            <!-- DESPACHO -->
                            <div class="col-md-4">

                                <div class="form-group">

                                    <label>
                                        Despacho
                                    </label>

                                    <input type="text" class="form-control" id="detalle_despacho" readonly>

                                </div>

                            </div>


                            <!-- RECIBO INTERNO -->
                            <div class="col-md-4">

                                <div class="form-group">

                                    <label>
                                        Recibo interno
                                    </label>

                                    <input type="text" class="form-control" id="detalle_recibo" readonly>

                                </div>

                            </div>


                            <!-- DOCUMENTO SIESA -->
                            <div class="col-md-4">

                                <div class="form-group">

                                    <label>
                                        Documento SIESA
                                    </label>

                                    <input type="text" class="form-control" id="detalle_documento_siesa" readonly>

                                </div>

                            </div>


                            <!-- FECHA -->
                            <div class="col-md-4">

                                <div class="form-group">

                                    <label>
                                        Fecha
                                    </label>

                                    <input type="text" class="form-control" id="detalle_fecha" readonly>

                                </div>

                            </div>


                            <!-- HORA -->
                            <div class="col-md-4">

                                <div class="form-group">

                                    <label>
                                        Hora
                                    </label>

                                    <input type="text" class="form-control" id="detalle_hora" readonly>

                                </div>

                            </div>


                            <!-- GALONES -->
                            <div class="col-md-4">

                                <div class="form-group">

                                    <label>
                                        Galones
                                    </label>

                                    <input type="text" class="form-control" id="detalle_galones" readonly>

                                </div>

                            </div>


                            <!-- OBRA -->
                            <div class="col-md-6">

                                <div class="form-group">

                                    <label>
                                        Obra
                                    </label>

                                    <input type="text" class="form-control" id="detalle_obra" readonly>

                                </div>

                            </div>


                            <!-- EQUIPO -->
                            <div class="col-md-6">

                                <div class="form-group">

                                    <label>
                                        Equipo
                                    </label>

                                    <input type="text" class="form-control" id="detalle_equipo" readonly>

                                </div>

                            </div>


                            <!-- CONDUCTOR -->
                            <div class="col-md-6">

                                <div class="form-group">

                                    <label>
                                        Conductor
                                    </label>

                                    <input type="text" class="form-control" id="detalle_conductor" readonly>

                                </div>

                            </div>


                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">

                            Cerrar

                        </button>

                    </div>


                </div>

            </div>

        </div>
        <!-- /.MODAL DETALLE DESPACHO -->


        <?php require_once("../MainJS/JS.php") ?>


        <!--
            DataTables, Select2, Daterangepicker y SweetAlert2
            ya se encuentran importados en el proyecto.
            Toda la lógica AJAX de esta vista estará en HistorialAcpm.js.
        -->
        <script type="text/javascript" src="historialAcpm.js">
        </script>


    </body>

    </html>

<?php
} else {

    header(
        "Location:"
            . Conectar::ruta()
            . "Pagina404.php"
    );
}
?>