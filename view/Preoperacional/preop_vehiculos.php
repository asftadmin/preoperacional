<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "preop_vehiculos");
if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="../../public/css/preop.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
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
                                    <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                                    <li class="breadcrumb-item active">Formulario Preoperacional</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- HEADER -->
                <!-- MAIN -->
                <section class="content">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <form id="formulario_preop">
                                        <!-- CARD HEADER -->
                                        <div class="card-header bg-gray mt-0">
                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <div class="form-group d-flex align-items-center">
                                                        <label for="select_placa" class="mr-2">Placa:</label>
                                                        <select class="form-control select2bs4" id="select_placa"
                                                            name="select_placa" style="width: 100%;" required>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- /.CARD HEADER -->

                                        <!-- CARD BODY -->
                                        <div id="" class="card-body">

                                            <input type="hidden" id="tipo_id" name="tipo_id">
                                            <input type="hidden" id="vehi_placa" name="vehi_placa">
                                            <input type="hidden" id="vehi_id" name="vehi_id">
                                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
                                            <div id="pregunta-container">
                                                <!-- Las preguntas se cargarán aquí -->


                                            </div>

                                        </div>
                                        <!-- /.CARD BODY -->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>
            <?php require_once("../MainFooter/footer.php") ?>
        </div>
        <!-- ./wrapper -->
        <?php require_once("../MainJS/JS.php") ?>
        <?php require_once("modalFormulario.php") ?>
        <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
        <script type="text/javascript" src="ConsultarPreguntas.js"></script>

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