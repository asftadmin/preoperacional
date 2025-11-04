<?php
require_once "../../config/conexion.php";
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "ReporteMtto");
if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">

    <?php require_once "../MainHead/head.php"; ?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <!-- SweetAlert -->
    <link rel="stylesheet" href="../../public/plugins/sweetalert2/sweetalert2.css">


    <title>Gestion Mantenimiento</title>
    </head>


    <body class="hold-transition sidebar-mini">

        <div class="wrapper">

            <!-- Navbar -->
            <?php require_once "../MainNav/nav.php"; ?>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <?php require_once "../MainMenu/menu.php"; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Gestion Mantenimiento</h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active">Gestion Mtto</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div><!-- /.content-header -->

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <form class="form-horizontal" method="post" id="rpteMtto">
                                    <?php require_once("encabezado.php"); ?>
                                    <?php require_once("diagnostico.php"); ?>
                                    <?php require_once("insumos.php"); ?>
                                </form>
                            </div>
                            <!-- /.container-fluid -->
                        </div>
                    </div>
                    <!-- /.content -->
                </div>
                <!-- /.content-wrapper -->
            </div>


            <?php require_once("../MainFooter/footer.php") ?>

            <!-- ./wrapper -->
        </div>

        <?php require_once "../MainJS/JS.php" ?>
        <script type="text/javascript" src="reporte_mtto.js"></script>
        <!-- date-range-picker -->
        <!-- SweetAlert -->
        <script src="../../public/plugins/sweetalert2/sweetalert2.js"></script>



    </body>

    </html>

<?php
} else {
    header("location:" . Conectar::ruta() . "index.php");
}
?>