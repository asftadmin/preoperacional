<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();

$datos = $rol->validacion_acceso(
    $_SESSION["user_id"],
    "trazabilidadMezcla"
);

if (is_array($datos) && count($datos) > 0) {
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <?php require_once("../MainHead/head.php"); ?>

    <title>Bandeja Trazabilidad de Mezcla</title>

    <!-- DataTables -->
    <link rel="stylesheet" href="../../public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="../../public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        <?php require_once("../MainNav/nav.php"); ?>
        <?php require_once("../MainMenu/menu.php"); ?>

        <div class="content-wrapper">

            <!-- Encabezado -->
            <section class="content-header">

                <div class="container-fluid">

                    <div class="row mb-2">

                        <div class="col-sm-8">

                            <h1>
                                Trazabilidad de Mezcla
                            </h1>

                        </div>

                        <div class="col-sm-4">

                            <ol class="breadcrumb float-sm-right">

                                <li class="breadcrumb-item">
                                    <a href="../home/">
                                        Inicio
                                    </a>
                                </li>

                                <li class="breadcrumb-item active">
                                    Trazabilidad de Mezcla
                                </li>

                            </ol>

                        </div>

                    </div>

                </div>

            </section>

            <!-- Contenido -->
            <section class="content">

                <div class="container-fluid">

                    <div class="card card-primary card-outline">

                        <div class="card-header">

                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Trazabilidad de Mezcla registradas
                            </h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-dark btn-sm" id="btn_nuevo_formato">
                                    <i class="fas fa-plus mr-1"></i>
                                    Agregar trazabilidad
                                </button>

                            </div>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table id="tabla_trazabilidad" class="table table-bordered table-striped table-hover"
                                    style="width: 100%;">

                                    <thead>

                                        <tr>

                                            <th>
                                                Fecha
                                            </th>

                                            <th>
                                                Tipo de mezcla
                                            </th>

                                            <th>
                                                Actividad
                                            </th>

                                            <th>
                                                Obra
                                            </th>

                                            <th>
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

        <?php require_once("../MainFooter/footer.php"); ?>

    </div>

    <?php require_once("../MainJS/JS.php"); ?>

    <!-- DataTables -->
    <script src="../../public/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../public/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <!-- JS del módulo -->
    <script src="inboxTrazabilidad.js"></script>

</body>

</html>

<?php
} else {
    header(
        "Location:" .
        Conectar::ruta() .
        "view/404/"
    );
}
?>