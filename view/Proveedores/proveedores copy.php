<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "ProveedorSiesa");
if (is_array($datos) and count($datos) > 0) {
?>
    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
    <title>Proveedores Siesa</title>
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
                                <h1 class="m-0">Proveedores Siesa</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                                    <li class="breadcrumb-item active">Proveedores Siesa</li>
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
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" id="filtro-fechas" class="form-control"
                                                placeholder="Rango de fechas">
                                        </div>
                                        <div class="col-md-3">
                                            <button id="btn-filtrar-fechas" class="btn btn-primary">Buscar</button>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="pageSelect" class="form-control">
                                                <!-- Las páginas se llenarán dinámicamente con JavaScript -->
                                            </select>

                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">
                                    <div id="loadingIndicator" style="display: none;">
                                        <i class="fa fa-spinner fa-spin"></i> Cargando...
                                    </div>
                                    <table id="proveedores_data_siesa" name="proveedores_data_siesa"
                                        class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                        <thead class="bg-info">
                                            <tr>
                                                <th th class="text-center" style="width: 15%;">NIT</th>
                                                <th th class="text-center" style="width: 40%">RAZON SOCIAL</th>
                                                <!-- <th th class="text-center" style="width: 15%">ACCIONES</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
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

        <?php require_once("../MainJS/JS.php") ?>
        <script type="text/javascript" src="proveedores.js"></script>
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