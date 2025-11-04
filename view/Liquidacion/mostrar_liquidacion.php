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
                            <h1 class="m-0">Detalle Liquidación Cerrada</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="../Liquidacion/liquidacion.php">Liquidacion</a>
                                </li>
                                <li class="breadcrumb-item active">Detalle Liquidacion Cerrada</li>
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
                            <button class="btn btn-info" onclick="goBack()">Volver</button>
                        </div>
                        <div class="card-body">
                            <div class="box-typical box-typical-padding">
                                <table id="data_liquidacion_cerrada" name="data_liquidacion_cerrada"
                                    class="table table-bordered table-striped table-vcenter js-dataTable-full ">
                                    <thead class="bg-info">
                                        <tr>
                                            <th style="width:120px;">Fecha Reporte</th>
                                            <th>Descripcion</th>
                                            <th>Fecha Incio</th>
                                            <th>Fecha Fin</th>
                                            <th>Actividad</th>
											<th>Tipo</th>
											<th>C. Costos</th>
                                            <th>Marca</th>
                                            <th>Placa</th>
											<th>Conductor</th>
                                            <th>Obra</th>
                                            <th>KM/HM Inicial</th>
                                            <th>KM/HM Final</th>
                                            <th>KM/HM Total</th>
                                            <th>Volumen</th>
                                            <th>Tarifa</th>
                                            <th>Subtotal</th>
                                            <th>Total</th>
                                            <th class="none">Observaciones</th>
                                            <!--                                             <th>Subtotal</th> -->
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