<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "liquidacion");
if (is_array($datos) and count($datos) > 0) {
?>

<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
<!-- SweetAlert -->
<link rel="stylesheet" href="../../public/plugins/sweetalert2/sweetalert2.css">
<title>Liquidacion</title>
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
                            <h1 class="m-0">Liquidación</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Liquidacion</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.HEADER-->

            <!-- MAIN -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex flex-wrap gap-2">
                                <!-- Botón Crear Liquidación alineado a la derecha -->
                                <button id="btnCrearLiquidacion" class="btn btn-success mr-4">
                                    <i class="fas fa-plus"></i> Crear Liquidación
                                </button>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="box-typical box-typical-padding">
                                <table id="liquidacion_data_status" name="liquidacion_data_status"
                                    class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                    <thead class="bg-info">
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Descripcion</th>
                                            <th>Fechas Extremas</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.MAIN -->
            </div>


            <aside class="control-sidebar control-sidebar-dark">
            </aside>

        </div>
        <?php require_once("../MainFooter/footer.php") ?>
    </div>
    <script src="../../config/config.js"></script>
    <?php require_once("crudLiquidacion.php") ?>
    <?php require_once("../MainJS/JS.php") ?>
    <script type="text/javascript" src="liquidacion.js"></script>
    <!-- SweetAlert -->
    <script src="../../public/plugins/sweetalert2/sweetalert2.js"></script>
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