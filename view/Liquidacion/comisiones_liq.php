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
                                <h1 class="m-0">Comisiones</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="../Liquidacion/liquidacion.php">Liquidacion</a>
                                    </li>
                                    <li class="breadcrumb-item active">Comisiones</li>
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
                                <div class="row align-items-center">
                                    <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                        <select id="filtroTipoVehiculo" name="tipo_vehiculo" class="form-control select2bs4"
                                            multiple="multiple" data-placeholder="Tipo de Vehiculo">
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                        <button id="btnGenerarComision" class="btn btn-warning btn-block">
                                            <i class="fas fa-cog"></i> Generar Comision
                                        </button>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <button id="btnDescargar" class="btn btn-danger btn-block">
                                            <i class="fas fa-file-download"></i> Descargar PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="box-typical box-typical-padding">
                                    <table id="data_comisiones" name="data_comisiones"
                                        class="table table-bordered table-striped table-vcenter js-dataTable-full ">
                                        <thead class="bg-info">
                                            <tr>
                                                <th>№</th>
                                                <th>Conductor</th>
                                                <th>Placa</th>
                                                <th>Produccion</th>
                                                <th>%Comision</th>
                                                <th>Subtotal</th>
                                                <th>Valor Pagar</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">TOTAL</th>
                                                <th id="t-total-produccion"></th>
                                                <th></th>
                                                <th id="t-total-subtotal"></th>
                                                <th id="t-total-pagar"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="box-typical box-typical-padding">
                                    <table id="data_resumen" name="data_resumen"
                                        class="table table-bordered table-striped table-vcenter js-dataTable-full ">
                                        <thead class="bg-info">
                                            <tr>
                                                <th>№</th>
                                                <th>Conductor</th>
                                                <th>Valor Pagar</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">TOTAL</th>
                                                <th id="r-total-pagar"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-info" onclick="goBack()">Volver</button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- /.MAIN -->

                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>

            <?php require_once("../MainFooter/footer.php") ?>

        </div>


        <?php require_once("../MainJS/JS.php") ?>
        <script type="text/javascript" src="comisiones.js"></script>
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