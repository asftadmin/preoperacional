<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "ReporteObra");
if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/css/preop.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">

    </head>

    <body class="hold-transition sidebar-mini bodyPreop">
        <div class="wrapper">
            <?php require_once("../MainNav/nav.php"); ?>
            <?php require_once("../MainMenu/menu.php"); ?>
            <div class="content-wrapper">
                <!-- HEADER -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">

                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item active">Reporte de Obra</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- HEADER -->
                <!-- MAIN -->
                <!-- MAIN -->
                <div class="content">
                    <div class="container-fluid">
                        <!-- NAV TABS -->
                        <ul class="nav nav-tabs" id="reporteTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="reporte-tab" data-toggle="tab" href="#reporte" role="tab"><i class="fas fa-sign-in-alt"></i> Abrir Reporte de Obra</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="otra-tab" data-toggle="tab" href="#otra" role="tab"><i class="fas fa-sign-out-alt"></i> Cerrar Reporte de Obra</a>
                            </li>
                        </ul>

                        <!-- CONTENIDO DE LAS PESTAÑAS -->
                        <div class="tab-content" id="reporteTabsContent">

                            <div class="tab-pane fade show active" id="reporte" role="tabpanel">
                                <div class="card">
                                    <form id="form-reporte">
                                        <div class="box-typical box-typical-padding">
                                            <br>
                                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
                                            <button type="button" id="agregarFila" class="btn btn-info"><i class="fas fa-chevron-circle-down"></i> Abrir Reporte Obra</button>
                                            <br><br>
                                            <div class="table-responsive">
                                                <table id="ro_data" name="ro_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                                    <thead class="bg-info">
                                                        <tr>
                                                            <th th class="text-center" style="width: 10%;">FECHA</th>
                                                            <th th class="text-center" style="width: 12%;">INSPECTOR</th>
                                                            <th th class="text-center" style="width: 14%;">OBRA</th>
                                                            <th th class="text-center" style="width: 14%;">OPERADOR</th>
                                                            <th th class="text-center" style="width: 10%;">HORA INICIO</th>
                                                            <th th class="text-center" style="width: 12%;">ACTIVIDAD</th>
                                                            <th th class="text-center" style="width: 6%;">ACCIONES</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tabla-body">
                                                        <!-- Filas agregadas dinámicamente aquí -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br />
                                            <center><button type="submit" name="action" value="add" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp; Guardar</button></center>
                                            <br />
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- CERRAR REPORTE DE OBRA -->
                            <div class="tab-pane fade" id="otra" role="tabpanel">
                                <div class="card">
                                    <br>

                                    <div class="box-typical box-typical-padding">
                                        <br><br>
                                        <table id="ro_clouse_data" name="ro_clouse_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                            <thead class="bg-info">
                                                <tr>
                                                <th class="text-center" style="width: 8%;">FECHA</th>
                                                    <th class="text-center" style="width: 15%;">OPERADOR</th>
                                                    <th class="text-center" style="width: 8%;">HRA INICIO</th>
                                                    <th class="text-center" style="width: 8%;">HRA FINAL</th>
                                                    <th th class="text-center" style="width: 8%;">ACCION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Cierra tab-content -->

                    </div>
                </div>


                <!-- /.content -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>
            <?php require_once("../MainFooter/footer.php") ?>

            <!-- ./wrapper -->
            <?php require_once("crudReporteObra.php") ?>
            <?php require_once("../MainJS/JS.php") ?>

            <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
            <script type="text/javascript" src="ReporteObra.js"></script>


    </body>

    </html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>