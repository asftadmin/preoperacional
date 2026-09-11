<?php
require_once ('../../config/conexion.php');
require_once ('../../models/Rol.php');

$rol = new Rol();

$datos = $rol->validacion_acceso(
    $_SESSION['user_id'],
    'aprobarTrazabilidad'
);

if (is_array($datos) && count($datos) > 0) {
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once ('../MainHead/head.php'); ?>

    <title>Trazabilidad de Mezcla</title>

    <!-- SweetAlert -->
    <link rel="stylesheet" href="../../public/plugins/sweetalert2/sweetalert2.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="../../public/plugins/daterangepicker/daterangepicker.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        <?php require_once ('../MainNav/nav.php'); ?>
        <?php require_once ('../MainMenu/menu.php'); ?>

        <div class="content-wrapper p-2">

            <section class="content-header">
                <div class="container-fluid">

                    <div class="row mb-2">

                        <div class="col-sm-6">
                            <h1>
                                Aprobación de trazabilidad de mezcla
                            </h1>
                        </div>

                    </div>

                </div>
            </section>


            <section class="content">

                <div class="container-fluid">

                    <div class="card card-outline card-primary">

                        <div class="card-header">

                            <h3 class="card-title">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Formatos pendientes de aprobación
                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="table-responsive">

                                <table id="tabla_aprobacion_trazabilidad"
                                    class="table table-bordered table-striped table-hover" style="width: 100%;">

                                    <thead>

                                        <tr>

                                            <th>Fecha</th>

                                            <th>Elaboró</th>

                                            <th>Tipo de mezcla</th>

                                            <th>Actividad</th>

                                            <th>Obra</th>

                                            <th>Fecha envío</th>

                                            <th class="text-center">
                                                Estado
                                            </th>

                                            <th class="text-center">
                                                Acción
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>
                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>

        <?php require_once ('../MainFooter/footer.php'); ?>

    </div>

    <?php require_once ('../MainJS/JS.php'); ?>

    <!-- Select2 -->
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>

    <!-- Moment y Daterangepicker -->
    <script src="../../public/plugins/moment/moment.min.js"></script>
    <script src="../../public/plugins/daterangepicker/daterangepicker.js"></script>

    <!-- SweetAlert -->
    <script src="../../public/plugins/sweetalert2/sweetalert2.js"></script>

    <!-- JS exclusivo del módulo -->
    <script src="inboxAprobacionT.js"></script>

</body>

</html>

<?php
} else {
    header('Location:' . Conectar::ruta() . 'index.php');
}
?>