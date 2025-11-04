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
                                <h1 class="m-0">Detalle Liquidación</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="../Liquidacion/liquidacion.php">Liquidacion</a>
                                    </li>
                                    <li class="breadcrumb-item active">Detalle</li>
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

                                <div class="container-fluid">
                                    <div class="row mb-2">
                                        <!-- Filtros principales -->
                                        <div class="col-md-4 col-sm-4 col- mb-2">
                                            <select id="filtroTipoVehiculo" name="tipo_vehiculo"
                                                class="form-control select2" readonly>
                                                <option value="">-- Tipo Vehículo --</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 col-sm-4 mb-2">
                                            <select id="filtroActividad" class="form-control select2" readonly>
                                                <option value="">-- Actividad --</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 col-sm-4 mb-2">
                                            <select id="filtroObra" class="form-control select2" readonly>
                                                <option value="">-- Obra --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row align-items-end">
                                        <!-- Fechas readonly -->
                                        <div class="col-md-4 col-sm-4 mb-2">
                                            <input type="text" id="fecha_inicio" name="fecha_inicio" class="form-control"
                                                readonly placeholder="Fecha Inicio">
                                        </div>

                                        <div class="col-md-4 col-sm-4 mb-2">
                                            <input type="text" id="fecha_fin" name="fecha_fin" class="form-control" readonly
                                                placeholder="Fecha Fin">
                                        </div>

                                        <!-- Botón Filtrar -->
                                        <div class="col-md-4 col-sm-4 mb-2">
                                            <button id="btnFiltrar" type="button" class="btn btn-primary w-100">
                                                <i class="fas fa-filter"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-body">
                                <div class="box-typical box-typical-padding">
                                    <table id="data_liquidacion" name="data_liquidacion"
                                        class="table table-bordered table-striped table-vcenter js-dataTable-full ">
                                        <thead class="bg-info">
                                            <tr>
                                                <th style="width:120px;">Fecha Reporte</th>
                                                <th>Placa</th>
                                                <th>Volumen</th>
                                                <th>Viajes</th>
                                                <th>Km Inicial</th>
                                                <th>Km Final</th>
                                                <th>Km Total</th>
                                                <th>Tarifa</th>
                                                <th>Subtotal</th>
                                                <th class="none">Observaciones</th>
                                                <!-- th para columnas ocultas (IDs) -->
                                                <th>repdia_id</th>
                                                <th>actividad_id</th>
                                                <th>obra_id</th>
                                                <th><i class="fas fa-check-circle"></i></th>
                                                <!--                                             <th>Subtotal</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <!--                             <div class="mt-3 text-right">
                                <label for="total_liquidacion" class="font-weight-bold">Total Liquidación:</label>
                                <input type="text" id="total_liquidacion" class="form-control text-right" readonly
                                    value="$0">
                            </div> -->
                                <div class="row mb-2 justify-content-center">
                                    <div class="col-md-3 text-center">
                                        <button id="btnGuardarDetalle" type="button" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Guardar
                                        </button>
                                    </div>
                                </div>


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
        <script type="text/javascript" src="detalle_liquidacion.js"></script>
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