<?php

require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();

if (
    isset($_SESSION["user_id"]) &&
    $rol->validacion_acceso($_SESSION["user_id"], "ticketsGerencial")
) {

?>

    <!DOCTYPE html>
    <html lang="es">

    <head>

        <?php
        require_once("../MainHead/head.php");
        ?>

        <!-- Daterangepicker -->
        <link rel="stylesheet" href="../../public/plugins/daterangepicker/daterangepicker.css">

        <title>DASHBOARD GERENCIAL - MESA DE SERVICIOS</title>


    </head>

    <body class="hold-transition sidebar-mini">

        <div class="wrapper">


            <!-- ================================================= -->
            <!-- NAVBAR -->
            <!-- ================================================= -->

            <?php
            require_once("../MainNav/nav.php");
            ?>


            <!-- ================================================= -->
            <!-- MENU LATERAL -->
            <!-- ================================================= -->

            <?php
            require_once("../MainMenu/menu.php");
            ?>


            <!-- ================================================= -->
            <!-- CONTENT WRAPPER -->
            <!-- ================================================= -->

            <div class="content-wrapper">


                <!-- ================================================= -->
                <!-- ENCABEZADO -->
                <!-- ================================================= -->

                <section class="content-header">

                    <div class="container-fluid">

                        <div class="row mb-2">

                            <div class="col-sm-6">

                                <h1>
                                    Dashboard gerencial
                                </h1>

                                <small class="text-muted">

                                    Seguimiento de las solicitudes
                                    de soporte del área de Sistemas.

                                </small>

                            </div>


                            <div class="col-sm-6">

                                <ol class="breadcrumb float-sm-right">

                                    <li class="breadcrumb-item">

                                        <a href="#">
                                            Inicio
                                        </a>

                                    </li>

                                    <li class="breadcrumb-item">

                                        Mesa de servicio

                                    </li>

                                    <li class="breadcrumb-item active">

                                        Dashboard gerencial

                                    </li>

                                </ol>

                            </div>

                        </div>

                    </div>

                </section>



                <!-- ================================================= -->
                <!-- CONTENIDO PRINCIPAL -->
                <!-- ================================================= -->

                <section class="content p-2">

                    <div class="container-fluid">


                        <!-- ================================================= -->
                        <!-- FILTRO DE PERIODO -->
                        <!-- ================================================= -->

                        <div class="card card-outline card-primary">

                            <div class="card-header">

                                <h3 class="card-title">

                                    <i class="fas fa-filter mr-1"></i>

                                    Periodo de análisis

                                </h3>

                            </div>


                            <div class="card-body">

                                <div class="row">


                                    <div class="col-lg-4 col-md-6">

                                        <div class="form-group">

                                            <label for="rango_fechas_gerencial">

                                                Rango de fechas

                                            </label>

                                            <input type="text" class="form-control" id="rango_fechas_gerencial"
                                                name="rango_fechas_gerencial" autocomplete="off">

                                        </div>

                                    </div>


                                    <div class="
                                    col-lg-2
                                    col-md-3
                                    d-flex
                                    align-items-end
                                ">

                                        <div class="form-group w-100">

                                            <button type="button" class="btn btn-primary btn-block"
                                                id="btn_consultar_gerencial">

                                                <i class="fas fa-search mr-1"></i>

                                                Consultar

                                            </button>

                                        </div>

                                    </div>


                                    <div class="
                                    col-lg-2
                                    col-md-3
                                    d-flex
                                    align-items-end
                                ">

                                        <div class="form-group w-100">

                                            <button type="button" class="btn btn-default btn-block"
                                                id="btn_limpiar_gerencial">

                                                <i class="fas fa-eraser mr-1"></i>

                                                Limpiar

                                            </button>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </div>



                        <!-- ================================================= -->
                        <!-- KPI PRINCIPALES -->
                        <!-- ================================================= -->

                        <div class="row">


                            <!-- TICKETS RECIBIDOS -->

                            <div class="col-lg-3 col-6">

                                <div class="small-box bg-info">

                                    <div class="inner">

                                        <h3 id="kpi_tickets_recibidos">
                                            0
                                        </h3>

                                        <p>
                                            Tickets recibidos
                                        </p>

                                    </div>

                                    <div class="icon">

                                        <i class="fas fa-inbox"></i>

                                    </div>

                                    <span class="small-box-footer">

                                        Solicitudes registradas
                                        en el periodo

                                    </span>

                                </div>

                            </div>



                            <!-- TICKETS CERRADOS -->

                            <div class="col-lg-3 col-6">

                                <div class="small-box bg-success">

                                    <div class="inner">

                                        <h3 id="kpi_tickets_cerrados">
                                            0
                                        </h3>

                                        <p>
                                            Tickets cerrados
                                        </p>

                                    </div>

                                    <div class="icon">

                                        <i class="fas fa-check-circle"></i>

                                    </div>

                                    <span class="small-box-footer">

                                        Solicitudes solucionadas

                                    </span>

                                </div>

                            </div>



                            <!-- TICKETS PENDIENTES -->

                            <div class="col-lg-3 col-6">

                                <div class="small-box bg-warning">

                                    <div class="inner">

                                        <h3 id="kpi_tickets_pendientes">
                                            0
                                        </h3>

                                        <p>
                                            Tickets pendientes
                                        </p>

                                    </div>

                                    <div class="icon">

                                        <i class="fas fa-hourglass-half"></i>

                                    </div>

                                    <span class="small-box-footer">

                                        Solicitudes en espera
                                        de gestión

                                    </span>

                                </div>

                            </div>



                            <!-- CUMPLIMIENTO -->

                            <div class="col-lg-3 col-6">

                                <div class="small-box bg-primary">

                                    <div class="inner">

                                        <h3>

                                            <span id="kpi_cumplimiento">
                                                0
                                            </span>%

                                        </h3>

                                        <p>
                                            Cumplimiento de atención
                                        </p>

                                    </div>

                                    <div class="icon">

                                        <i class="fas fa-stopwatch"></i>

                                    </div>

                                    <span class="small-box-footer">

                                        Dentro del tiempo establecido

                                    </span>

                                </div>

                            </div>


                        </div>



                        <!-- ================================================= -->
                        <!-- COMPORTAMIENTO + BACKLOG -->
                        <!-- ================================================= -->

                        <div class="row">


                            <!-- COMPORTAMIENTO -->

                            <div class="col-lg-8 col-md-12">

                                <div class="card card-outline card-primary">

                                    <div class="card-header">

                                        <h3 class="card-title">

                                            <i class="fas fa-chart-line mr-1"></i>

                                            Comportamiento de las solicitudes

                                        </h3>

                                    </div>


                                    <div class="card-body">

                                        <div id="contenedor_comportamiento_tickets">

                                            <div class="
                                            text-center
                                            text-muted
                                            py-5
                                        ">

                                                <i class="
                                                fas
                                                fa-chart-line
                                                fa-3x
                                                mb-3
                                            "></i>

                                                <p class="mb-0">

                                                    La información será cargada
                                                    mediante AJAX.

                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="card-footer">

                                        <small class="text-muted">

                                            Compara los tickets recibidos,
                                            cerrados y pendientes durante
                                            el periodo seleccionado.

                                        </small>

                                    </div>

                                </div>

                            </div>



                            <!-- BACKLOG -->

                            <div class="col-lg-4 col-md-12">

                                <div class="card card-outline card-danger">

                                    <div class="card-header">

                                        <h3 class="card-title">

                                            <i class="fas fa-clock mr-1"></i>

                                            Antigüedad del backlog

                                        </h3>

                                    </div>


                                    <div class="card-body">

                                        <div id="contenedor_antiguedad_backlog">

                                            <div class="
                                            text-center
                                            text-muted
                                            py-5
                                        ">

                                                <i class="
                                                fas
                                                fa-chart-pie
                                                fa-3x
                                                mb-3
                                            "></i>

                                                <p class="mb-0">

                                                    La información será cargada
                                                    mediante AJAX.

                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="card-footer">

                                        <small class="text-muted">

                                            Permite identificar solicitudes
                                            que llevan mayor tiempo pendientes.

                                        </small>

                                    </div>

                                </div>

                            </div>


                        </div>



                        <!-- ================================================= -->
                        <!-- CATEGORIA + AREA -->
                        <!-- ================================================= -->

                        <div class="row">


                            <!-- CATEGORIA -->

                            <div class="col-lg-6 col-md-12">

                                <div class="card card-outline card-info">

                                    <div class="card-header">

                                        <h3 class="card-title">

                                            <i class="fas fa-tags mr-1"></i>

                                            Tickets por categoría

                                        </h3>

                                    </div>


                                    <div class="card-body">

                                        <div id="contenedor_tickets_categoria">

                                            <div class="
                                            text-center
                                            text-muted
                                            py-5
                                        ">

                                                <i class="
                                                fas
                                                fa-chart-bar
                                                fa-3x
                                                mb-3
                                            "></i>

                                                <p class="mb-0">

                                                    La información será cargada
                                                    mediante AJAX.

                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="card-footer">

                                        <small class="text-muted">

                                            Muestra los tipos de solicitudes
                                            que generan mayor demanda.

                                        </small>

                                    </div>

                                </div>

                            </div>



                            <!-- AREA -->

                            <div class="col-lg-6 col-md-12">

                                <div class="card card-outline card-secondary">

                                    <div class="card-header">

                                        <h3 class="card-title">

                                            <i class="fas fa-building mr-1"></i>

                                            Tickets por área

                                        </h3>

                                    </div>


                                    <div class="card-body">

                                        <div id="contenedor_tickets_area">

                                            <div class="
                                            text-center
                                            text-muted
                                            py-5
                                        ">

                                                <i class="
                                                fas
                                                fa-chart-bar
                                                fa-3x
                                                mb-3
                                            "></i>

                                                <p class="mb-0">

                                                    La información será cargada
                                                    mediante AJAX.

                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="card-footer">

                                        <small class="text-muted">

                                            Identifica las áreas que concentran
                                            mayor cantidad de solicitudes.

                                        </small>

                                    </div>

                                </div>

                            </div>


                        </div>



                        <!-- ================================================= -->
                        <!-- RESUMEN EJECUTIVO -->
                        <!-- ================================================= -->

                        <div class="card card-outline card-success">

                            <div class="card-header">

                                <h3 class="card-title">

                                    <i class="fas fa-chart-pie mr-1"></i>

                                    Resumen ejecutivo del periodo

                                </h3>

                            </div>


                            <div class="card-body">

                                <div class="row text-center">


                                    <div class="
                                    col-lg-4
                                    col-md-4
                                    border-right
                                ">

                                        <h3 id="indicador_porcentaje_cierre">
                                            0%
                                        </h3>

                                        <small class="text-muted">

                                            Tickets cerrados frente
                                            a los recibidos

                                        </small>

                                    </div>



                                    <div class="
                                    col-lg-4
                                    col-md-4
                                    border-right
                                ">

                                        <h3 id="indicador_cumplimiento">
                                            0%
                                        </h3>

                                        <small class="text-muted">

                                            Cumplimiento de tiempos
                                            de atención

                                        </small>

                                    </div>



                                    <div class="col-lg-4 col-md-4">

                                        <h3 id="indicador_backlog_critico">
                                            0
                                        </h3>

                                        <small class="text-muted">

                                            Solicitudes con mayor
                                            antigüedad

                                        </small>

                                    </div>


                                </div>

                            </div>

                        </div>


                    </div>

                </section>


            </div>



            <!-- ================================================= -->
            <!-- FOOTER -->
            <!-- ================================================= -->

            <?php
            require_once("../MainFooter/footer.php");
            ?>


        </div>


        <!-- ================================================= -->
        <!-- SCRIPTS GENERALES DEL PROYECTO -->
        <!-- ================================================= -->

        <?php
        require_once("../MainJS/JS.php");
        ?>


        <!-- DATERANGEPICKER -->

        <script src="../../public/plugins/moment/moment.min.js">
        </script>

        <script src="../../public/plugins/daterangepicker/daterangepicker.js">
        </script>


        <!-- CONFIGURACIÓN GENERAL -->

        <script type="text/javascript" src="../../config/config.js">
        </script>


        <!-- JS DE LA VISTA -->

        <script type="text/javascript" src="ticketsGerencial.js">
        </script>


    </body>

    </html>


<?php

} else {

    header(
        "Location:" .
            Conectar::ruta() .
            "Pagina404.php"
    );
}

?>