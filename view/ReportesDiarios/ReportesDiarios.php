<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "ReportesDiarios");
if (is_array($datos) and count($datos) > 0) {
?>

    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/css/repdia.css">
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
                                    <li class="breadcrumb-item active">Reporte Diario</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- HEADER -->
                <!-- MAIN -->
                <div class="row mt-2 ">
                    <div class="col-md-2">
                        <div class="content">
                            <div class="container-fluid">
                                <div class="box-typical box-typical-padding">
                                    <button type="button" id="displayNone" class="btn btn-sussec">Comenzar</button>
                                </div>
                            </div>
                        </div>
                    </div>&nbsp;&nbsp;

                    <div class="col-md-2">
                        <div class="container-fluid">
                            <div class="box-typical box-typical-padding">
                                <button type="button" class="btn btn-info" id="btnCerrarAct">Cerrar Actividad</button>
                                <br><br>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="hide-me" style="display:none">
                    <form method="post" id="repdia_form">
                        <div class="modal-body">
                            <div class="card-header bg-gray mt-0">
                                <div class="row mt-2 ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="user_cond">Conductor:</label>
                                            <input type="text" id="user_cond" name="user_cond" style="font-size:14px;"
                                                placeholder="<?php echo $_SESSION["user_nombre"] . " " ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="repdia_fech">Fecha:</label>
                                            <input type="text" id="repdia_fech" name="repdia_fech" placeholder="<?php echo date("Y-m-d") ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="repdia_hr_inic">Hora inicio:</label>
                                            <input type="text" id="repdia_hr_inic" name="user_cond" style="font-size:14px;"
                                                placeholder="<?php echo date("H:i"); ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="recibo"># Recibo:</label>
                                            <input type="text" id="recibo" name="recibo" style="font-size:14px;"
                                                value="<?php echo date("Ymd") . '' . $_SESSION["user_id"]; ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <input type="hidden" id="repdia_id" name="repdia_id">
                            <div class="row mt-2 ">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group d-flex align-items-center">
                                            <label for="repdia_residente" class="mr-2">Residente:</label>
                                            <select class="form-control select2" id="repdia_residente" name="repdia_residente" style="width: 100%;" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group d-flex align-items-center">
                                        <label for="repdia_inspec" class="mr-2">Inspector:</label>
                                        <select class="form-control select2" id="repdia_inspec" name="repdia_inspec" style="width: 100%;" required>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label for="repdia_vehi" class="mr-2">Placa:</label>
                                <select class="form-control select2" id="repdia_vehi"
                                    name="repdia_vehi" style="width: 100%;" required>
                                </select>
                            </div>
                            <div id="kilo_horo">
                                <!-- el kilometraje u horometraje se cargaran aqui -->
                            </div>
                            <br />
                            <div class="row mt-2 ">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repdia_gaso">Gasolina</label>&nbsp;&nbsp;
                                        <input type="text" id="repdia_gaso" name="repdia_gaso" style="font-size:14px;" placeholder="Galones" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repdia_acpm">Acpm</label>&nbsp;&nbsp;
                                        <input type="text" id="repdia_acpm" name="repdia_acpm" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="radio-group">
                                            <label>
                                                <input type="radio" id="aceite" name="repdia_ca" value="aceite" />
                                                <span>Cambio Aceite</span>
                                            </label>
                                            <label>
                                                <input type="radio" id="adicion" name="repdia_ca" value="adicion" />
                                                <span>Adición</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="km/hr">
                                        <div>
                                        <label for="repdia_km_hm"></label>&nbsp;&nbsp;
                                            <input type="text" id="repdia_km_hm" name="repdia_km_hm" placeholder="Escriba el KM/HR actual" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repdia_acet_moto">Motor</label>&nbsp;&nbsp;
                                        <input type="number" id="repdia_acet_moto" name="repdia_acet_moto" style="font-size:14px;" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repdia_acet_hidr">Hidraulico</label>&nbsp;&nbsp;
                                        <input type="number" id="repdia_acet_hidr" name="repdia_acet_hidr" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repdia_acet_tram">Trasmición</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="number" id="repdia_acet_tram" name="repdia_acet_tram" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repdia_acet_gras">Grasa</label>&nbsp;&nbsp;
                                        <input type="number" id="repdia_acet_gras" name="repdia_acet_gras" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-3">
                                <h5 style="font-weight: bold;">Observaciones</h5>
                            </div>
                            <div class="row justify-content-center">
                                <textarea id="repdia_observa" class="textarea" style="resize: none;" name="repdia_observa" rows="4" cols="100" placeholder="Escribe Aquí..." autocapitalize="sentences" spellcheck="true" maxlength="400"></textarea>
                            </div>
                            <br />

                            <input type="hidden" id="repdia_recib" name="repdia_recib" value="<?php echo date("Ymd");
                                                                                                echo $_SESSION["user_id"]; ?>">
                            <input type="hidden" id="repdia_cond" name="repdia_cond" value="<?php echo $_SESSION["user_id"] ?>">
                            <input type="hidden" id="repdia_estado" name="repdia_estado" value="0">
                            <input type="hidden" id="repdia_kilo_final" name="repdia_kilo_final" value="0">

                            <div class="modal-footer">
                                <input type="submit" id="preo_enviar" class="btn btn-info" value="Guardar">
                                <!-- <button type="button" id="finalizar" class="btn btn-info btn-icon " >Finalizar</button> -->
                            </div>
                    </form>
                </div>
            </div>
            <!-- /.content -->
            <aside class="control-sidebar control-sidebar-dark">
            </aside>
        </div>
        <?php require_once("../MainFooter/footer.php") ?>

        <!-- ./wrapper -->
        <?php require_once("../MainJS/JS.php") ?>

        <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
        <script type="text/javascript" src="Reportes_diarios.js"></script>


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