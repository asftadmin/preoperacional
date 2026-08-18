<?php

require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();

/*
 * Ajustar "Sis" o el código de menú real
 * de acuerdo con la configuración existente del proyecto.
 */
$datos = $rol->validacion_acceso(
    $_SESSION["user_id"],
    "kpiSistemas"
);

date_default_timezone_set('America/Bogota');

if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">

    <?php require_once("../MainHead/head.php"); ?>

    <link rel="stylesheet" href="../../public/css/inicio.css">

    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">

    <title>KPI MESA DE SERVICIOS</title>

    </head>

    <body class="hold-transition sidebar-mini">

        <div class="wrapper">

            <?php require_once("../MainNav/nav.php"); ?>

            <?php require_once("../MainMenu/menu.php"); ?>


            <div class="content-wrapper">


                <!-- ===================================================== -->
                <!-- HEADER -->
                <!-- ===================================================== -->

                <div class="content-header">

                    <div class="container-fluid">

                        <div class="row mb-2">


                            <div class="col-sm-6">

                                <h1 class="m-0">

                                    KPI MESA DE SERVICIOS

                                </h1>

                                <!--                                 <small class="text-muted">

                                    Seguimiento operativo de la Mesa de Servicio
                                    del área de Sistemas.

                                </small> -->

                            </div>


                            <div class="col-sm-6">

                                <ol class="breadcrumb float-sm-right">

                                    <li class="breadcrumb-item">

                                        <a href="../inicio/inicio.php">

                                            Inicio

                                        </a>

                                    </li>

                                    <li class="breadcrumb-item">

                                        Mesa de Servicio

                                    </li>

                                    <li class="breadcrumb-item active">

                                        Control diario

                                    </li>

                                </ol>

                            </div>


                        </div>

                    </div>

                </div>

                <!-- /.HEADER -->



                <!-- ===================================================== -->
                <!-- MAIN -->
                <!-- ===================================================== -->

                <div class="content">

                    <div class="container-fluid">


                        <!-- ================================================= -->
                        <!-- ENCABEZADO OPERATIVO -->
                        <!-- ================================================= -->

                        <div class="card card-outline card-secondary">

                            <div class="card-body py-2">

                                <div class="row align-items-center">


                                    <div class="col-md-8">

                                        <span class="text-muted">

                                            <i class="fas fa-calendar-day mr-1"></i>

                                            Fecha:

                                        </span>

                                        <strong id="control_fecha_actual">

                                            <?php echo date("d/m/Y H:i:s"); ?>

                                        </strong>

                                    </div>


                                    <div class="col-md-4 text-md-right mt-2 mt-md-0">

                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="btn_actualizar_control">

                                            <i class="fas fa-sync-alt mr-1"></i>

                                            Actualizar indicadores

                                        </button>

                                    </div>


                                </div>

                            </div>

                        </div>

                        <!-- /.ENCABEZADO OPERATIVO -->



                        <!-- ================================================= -->
                        <!-- KPI -->
                        <!-- ================================================= -->

                        <div class="row">


                            <!-- ============================================= -->
                            <!-- KPI: TICKETS RECIBIDOS HOY -->
                            <!-- ============================================= -->

                            <div class="col-lg-3 col-md-6 col-sm-12">

                                <div class="info-box">

                                    <span class="info-box-icon bg-info elevation-1">

                                        <i class="fas fa-inbox"></i>

                                    </span>


                                    <div class="info-box-content">

                                        <span class="info-box-text">

                                            Tickets recibidos hoy

                                        </span>


                                        <span class="info-box-number" id="kpi_tickets_recibidos">

                                            0

                                        </span>


                                        <small class="text-muted" id="kpi_tickets_recibidos_detalle">

                                            Solicitudes registradas hoy

                                        </small>

                                    </div>

                                </div>

                            </div>



                            <!-- ============================================= -->
                            <!-- KPI: TICKETS CERRADOS HOY -->
                            <!-- ============================================= -->

                            <div class="col-lg-3 col-md-6 col-sm-12">

                                <div class="info-box">

                                    <span class="info-box-icon bg-success elevation-1">

                                        <i class="fas fa-check-circle"></i>

                                    </span>


                                    <div class="info-box-content">

                                        <span class="info-box-text">

                                            Tickets cerrados hoy

                                        </span>


                                        <span class="info-box-number" id="kpi_tickets_cerrados">

                                            0

                                        </span>


                                        <small class="text-muted" id="kpi_tickets_cerrados_detalle">

                                            Casos solucionados hoy

                                        </small>

                                    </div>

                                </div>

                            </div>



                            <!-- ============================================= -->
                            <!-- KPI: TICKETS PENDIENTES -->
                            <!-- ============================================= -->

                            <div class="col-lg-3 col-md-6 col-sm-12">

                                <div class="info-box">

                                    <span class="info-box-icon bg-warning elevation-1">

                                        <i class="fas fa-hourglass-half"></i>

                                    </span>


                                    <div class="info-box-content">

                                        <span class="info-box-text">

                                            Tickets pendientes

                                        </span>


                                        <span class="info-box-number" id="kpi_tickets_pendientes">

                                            0

                                        </span>


                                        <small class="text-muted" id="kpi_tickets_pendientes_detalle">

                                            0 sin asignar

                                        </small>

                                    </div>

                                </div>

                            </div>



                            <!-- ============================================= -->
                            <!-- KPI: TICKETS FUERA DE TIEMPO -->
                            <!-- ============================================= -->

                            <div class="col-lg-3 col-md-6 col-sm-12">

                                <div class="info-box">

                                    <span class="info-box-icon bg-danger elevation-1">

                                        <i class="fas fa-exclamation-triangle"></i>

                                    </span>


                                    <div class="info-box-content">

                                        <span class="info-box-text">

                                            Tickets fuera de tiempo

                                        </span>


                                        <span class="info-box-number" id="kpi_tickets_fuera_tiempo">

                                            0

                                        </span>


                                        <small class="text-muted" id="kpi_tickets_fuera_tiempo_detalle">

                                            0 de prioridad alta

                                        </small>

                                    </div>

                                </div>

                            </div>


                        </div>

                        <!-- /.KPI -->







                        <!-- ================================================= -->
                        <!-- TICKETS QUE REQUIEREN ATENCIÓN -->
                        <!-- ================================================= -->

                        <div class="card card-outline card-danger">

                            <div class="card-header">

                                <h3 class="card-title">

                                    <i class="fas fa-exclamation-circle mr-1"></i>

                                    Tickets que requieren atención

                                </h3>


                                <div class="card-tools">

                                    <span class="badge badge-danger" id="total_tickets_atencion">

                                        0

                                    </span>

                                </div>

                            </div>


                            <div class="card-body">


                                <table id="control_operacion_data" name="control_operacion_data"
                                    class="table table-bordered table-striped table-vcenter js-dataTable-full dt-responsive nowrap"
                                    style="width: 100%;">


                                    <thead class="bg-info">

                                        <tr>


                                            <th class="text-center">

                                                TICKET

                                            </th>


                                            <th class="text-center">

                                                FECHA

                                            </th>


                                            <th>

                                                SOLICITANTE

                                            </th>


                                            <th>

                                                ÁREA

                                            </th>


                                            <th>

                                                ASUNTO

                                            </th>


                                            <th class="text-center">

                                                CATEGORÍA

                                            </th>


                                            <th class="text-center">

                                                PRIORIDAD

                                            </th>


                                            <th>

                                                RESPONSABLE

                                            </th>


                                            <th class="text-center">

                                                ANTIGÜEDAD

                                            </th>

                                            <th class="text-center">
                                                SITUACIÓN
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


                                </table>


                            </div>

                        </div>

                        <!-- /.TICKETS ATENCIÓN -->



                        <!-- ================================================= -->
                        <!-- SEGUNDO NIVEL OPERATIVO -->
                        <!-- ================================================= -->

                        <div class="row">


                            <!-- ================================================= -->
                            <!-- TIEMPO PROMEDIO DE SOLUCIÓN VS SLA -->
                            <!-- ================================================= -->

                            <div class="col-lg-7 col-md-12">

                                <div class="card card-outline card-primary">

                                    <div class="card-header">

                                        <h3 class="card-title">

                                            <i class="fas fa-chart-line mr-1"></i>

                                            Tiempo promedio de solución vs SLA

                                        </h3>

                                    </div>


                                    <div class="card-body">

                                        <small class="text-muted d-block mb-3">

                                            Compara el tiempo promedio real de cierre de los tickets
                                            con la meta promedio definida por SLA según su prioridad.

                                        </small>


                                        <!--
                El gráfico se cargará mediante AJAX.
                Chart.js utilizará este canvas.
            -->
                                        <div class="chart">

                                            <canvas id="grafico_tiempo_solucion"
                                                style="min-height: 280px; height: 280px; max-height: 280px; max-width: 100%;">

                                            </canvas>

                                        </div>


                                    </div>

                                </div>

                            </div>


                            <!-- ANTIGÜEDAD -->

                            <div class="col-lg-5 col-md-12">

                                <div class="card card-outline card-warning">

                                    <div class="card-header">

                                        <h3 class="card-title">

                                            <i class="fas fa-stopwatch mr-1"></i>

                                            Antigüedad de pendientes

                                        </h3>

                                        <div class="card-tools">

                                            <span class="badge badge-warning" id="total_antiguedad_pendientes">

                                                0

                                            </span>

                                        </div>

                                    </div>


                                    <div class="card-body p-0">


                                        <table class="table table-striped mb-0">

                                            <thead>

                                                <tr>

                                                    <th>

                                                        Antigüedad

                                                    </th>

                                                    <th class="text-center">

                                                        Tickets

                                                    </th>

                                                </tr>

                                            </thead>


                                            <tbody>


                                                <tr>

                                                    <td>

                                                        0 - 1 día

                                                    </td>

                                                    <td class="text-center font-weight-bold" id="antiguedad_0_1">

                                                        0

                                                    </td>

                                                </tr>


                                                <tr>

                                                    <td>

                                                        2 - 3 días

                                                    </td>

                                                    <td class="text-center font-weight-bold" id="antiguedad_2_3">

                                                        0

                                                    </td>

                                                </tr>


                                                <tr>

                                                    <td>

                                                        4 - 7 días

                                                    </td>

                                                    <td class="text-center font-weight-bold" id="antiguedad_4_7">

                                                        0

                                                    </td>

                                                </tr>


                                                <tr class="table-danger">

                                                    <td>

                                                        Más de 7 días

                                                    </td>

                                                    <td class="text-center font-weight-bold" id="antiguedad_mayor_7">

                                                        0

                                                    </td>

                                                </tr>


                                            </tbody>

                                        </table>


                                    </div>


                                </div>

                            </div>


                        </div>

                        <!-- /.SEGUNDO NIVEL OPERATIVO -->


                    </div>

                </div>

                <!-- /.MAIN -->



                <!-- ===================================================== -->
                <!-- CONTROL SIDEBAR -->
                <!-- ===================================================== -->

                <aside class="control-sidebar control-sidebar-dark">
                </aside>


            </div>


            <?php require_once("../MainFooter/footer.php") ?>


        </div>



        <?php require_once("../MainJS/JS.php") ?>


        <!--
            DataTables, Select2, SweetAlert2 y demás plugins
            ya se encuentran importados desde la estructura principal.

            Toda la lógica de esta vista debe mantenerse
            en ControlOperacion.js.
        -->

        <script type="text/javascript" src="controlOperacion.js">
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