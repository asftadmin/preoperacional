<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "RteObra");
if (is_array($datos) and count($datos) > 0) {
?>
    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
    <title>Consultar Reportes Obra</title>
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
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item active">Consultar Reporte Obra</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.HEADER -->

                <!-- MAIN -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="card" id="container">
                            <br>
                            <div class="box-typical box-typical-padding">
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                    <span class="form-group d-flex align-items-center" id="selectInspector">
                                            <select class="form-control select2" id="ro_id_inspector" name="ro_id_inspector" required></select>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="form-group d-flex align-items-center" id="selectOperador">
                                            <select class="form-control select2" id="ro_id_operador" name="ro_id_operador" required></select>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group d-flex align-items-center">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="rangoFechas" placeholder="Seleccione un rango de fechas">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group d-flex align-items-center gap-2">
                                            <button type="button" id="btnBuscar" class="btn btn-info">Buscar</button>&nbsp;&nbsp;
                                            <!-- <button type="button" id="btnLimpiar" class="btn btn-info">Ver Todo</button> -->
                                        </div>
                                    </div>
                                </div>
                                <table id="rte_obra_data" name="rte_obra_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                    <thead class="bg-info">
                                        <tr>
                                            <th th class="text-center" style="width: 8%;">FECHA DE REALIZACION </th>
                                            <th th class="text-center" style="width: 10%;">INSPECTOR </th>
                                            <th th class="text-center" style="width: 10%;">OPERADOR </th>
                                            <th th class="text-center" style="width: 6%;">HORA INICIO</th>
                                            <th th class="text-center" style="width: 10%;">ACTIVIDAD</th>
                                            <th th class="text-center" style="width: 6%;">HORA FINAL</th>
                                            <th th class="text-center" style="width: 6%;">HORAS TRABAJADAS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MAIN -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>
            <?php require_once("../MainFooter/footer.php") ?>
        </div>


        <!-- MODAL DE CALIFICAR -->
        <?php require_once("../MainJS/JS.php") ?>
        <script src="../../config/config.js"></script>
        <script type="text/javascript" src="RteObra.js"></script>
        <script src="../../public/plugins/select2/js/select2.full.min.js"></script>




    </body>

    </html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE:JACKSON DANIEL BORJA 
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>