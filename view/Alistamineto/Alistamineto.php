<?php

require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "Alistamiento");
if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/css/alista.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
    <style>
        .card-body {
            height: 500px;
            width: 100%;
            overflow-y: auto;
        }
    </style>
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
                                    <li class="breadcrumb-item active">Alistamineto</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- HEADER -->
                <!-- MAIN -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="box-typical box-typical-padding">
                            <button type="button" id="btnnuevoalista" class="btn btn-info">ALISTAMIENTO</button>
                            <br><br>
                            <table id="alista_data" name="alista_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                <thead class="bg-info">
                                    <tr>
                                        <th th class="text-center" style="width: 8%;">FECHA DE CREACIÓN</th>
                                        <th th class="text-center" style="width: 8%;">ESTADO</th>
                                        <th th class="text-center" style="width: 10%;">INSPECTOR</th>
                                        <th th class="text-center" style="width: 8%;">OBRA</th>
                                        <th th class="text-center" style="width: 8%;">FECHA DE REVISION</th>
                                        <th th class="text-center" style="width: 8%;">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.content -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>
            <?php require_once("../MainFooter/footer.php") ?>

            <!-- ./wrapper -->
            <?php require_once("crudAlistamientos.php") ?>
            <?php require_once("../MainJS/JS.php") ?>
            <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
            <script src="../../config/config.js"></script>
            <script type="text/javascript" src="Alistamineto.js"></script>
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