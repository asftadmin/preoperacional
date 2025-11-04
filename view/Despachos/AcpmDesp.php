<?php require_once("../../config/conexion.php");
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
    <title>ACPM</title>
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
                                    <li class="breadcrumb-item active">Despachos Acpm</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.HEADER -->

                <!-- MAIN -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="box-typical box-typical-padding">
                            <table id="acpm_data" name="acpm_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                <thead class="bg-info">
                                    <tr>
                                        <th th class="text-center" style="width: 8%;">CODIGO</th>
                                        <th th class="text-center" style="width: 15%;">FECHA</th>
                                        <th th class="text-center" style="width: 15%;">PLACA</th>
                                        <th th class="text-center" style="width: 15%;"># GALONES AUTORIZADOS</th>
                                        <th th class="text-center" style="width: 5%;">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- MAIN -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>
            <?php require_once("../MainFooter/footer.php") ?>
        </div>
        <?php require_once("../MainJS/JS.php") ?>
        <?php require_once("Despacho.php") ?>
        <script type="text/javascript" src="AcpmDesp.js"></script>
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