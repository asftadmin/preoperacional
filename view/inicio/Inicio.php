<?php
require_once "../../config/conexion.php";
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "inicio");
if (is_array($datos) and count($datos) > 0) {
    if (isset($_SESSION["user_id"])) {

?>

        <!DOCTYPE html>
        <html lang="es">
        <?php require_once "../MainHead/head.php"; ?>
        <link rel="stylesheet" href="../../public/css/graficos.css">
        <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
        <style>
            .small-box {
                background-color: #f4f6f9;
                border-radius: 5px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                padding: 20px;
                text-align: center;
                transition: transform 0.3s;
            }

            .small-box:hover {
                transform: translateY(-5px);
            }

            .small-box .inner {
                font-size: 2em;
                font-weight: bold;
            }

            .small-box .label {
                font-size: 1em;
                color: #999;
            }

            .small-box .icon {
                font-size: 3em;
                color: #666;
            }

            .small-box .footer {
                font-size: 0.9em;
                color: #007bff;
                text-decoration: none;
            }
        </style>
        <title>Inicio</title>
        </head>


        <body class="hold-transition sidebar-mini">
            <div class="wrapper">
                <?php require_once "../MainNav/nav.php"; ?>
                <?php require_once "../MainMenu/menu.php"; ?>
                <div class="content-wrapper">
                    <!-- HEADER -->
                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class="m-0"></h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                        <li class="breadcrumb-item active">Preoperacionales</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($_SESSION["user_rol_usuario"] == 1 ||$_SESSION["user_rol_usuario"] == 10) { ?>
                        <div class="row mt-2 ">
                            <div class="col-md-3">
                                <div class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card" style="max-width: 400px; max-height: 400px;">
                                                    <div class="d-flex justify-content-center mt-3">
                                                        <select class="form-control  select2bs4" id="vehi_placax" name="vehi_placax" style="width: 70%;" required>
                                                        </select>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-md-6 text-center" style="margin: 0 auto; display:inline; max-width: 400px;">
                                                                <label>KILOMETRAJE POR DIA</label>

                                                                <div class="knob-container">
                                                                    <input type="text" disabled class="knob" data-thickness="0.2" data-anglearc="250" data-angleoffset="-125" data-width="140" data-height="140" data-fgcolor="#00c0ef">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
                                                <div class="small-box">
                                                    <div class="nav-icon fas fa-truck-monster"></div>
                                                    <div class="inner">
                                                        <div id="TOTAL"></div>
                                                    </div>
                                                    <div class="label">Preoperacionales Mensuales</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- MAIN -->
                        <div class="content">
                            <div class="container-fluid">
                                <div class="card">
                                    <br>
                                    <div class="box-typical box-typical-padding">
                                        <table id="pre_data" name="pre_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                            <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">PREOPERACIONALES</caption>
                                            <thead class="bg-info">
                                                <tr>
                                                    <th th class="text-center" style="width: 5%;">PLACA</th>
                                                    <th th class="text-center" style="width: 5%;">FECHA DE REALIZACIÓN</th>
                                                    <th th class="text-center" style="width: 5%;">FECHA DE REVISIÓN</th>
                                                    <th th class="text-center" style="width: 8%;">ESTADO</th>
                                                    <th th class="text-center" style="width: 18%;">OBSERVACIONES VERIFICADOR</th>
                                                    <th th class="text-center" style="width: 3%;">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content">
                            <div class="container-fluid">
                                <div class="card">
                                    <br>
                                    <div class="box-typical box-typical-padding">
                                        <table id="repdia_data" name="repdia_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                            <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">REPORTES DIARIOS</caption>
                                            <thead class="bg-info">
                                                <tr>
                                                    <th th class="text-center" style="width: 8%;">CODIGO</th>
                                                    <th th class="text-center" style="width: 5%;">FECHA DE REALIZACIÓN</th>
                                                    <th th class="text-center" style="width: 8%;">USUARIO</th>
                                                    <th th class="text-center" style="width: 8%;">PLACA</th>
                                                    <th th class="text-center" style="width: 3%;">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php  } ?>

                    <!------------------------------------- RESIDENTE ------------------------------------->
                    
                    <!---------------VERIFICADOR, COORDINADOR Y GERENCIA  ------------------>
                    <?php if ($_SESSION["user_rol_usuario"] == 2 || $_SESSION["user_rol_usuario"] == 3 || $_SESSION["user_rol_usuario"] == 4 || $_SESSION["user_rol_usuario"] == 6 ) { ?>

                        <div class="row justify-content-center">
                            <div class="card-header d-flex p-0">
                                <ul class="nav nav-pills ml-auto p-2">
                                    <li class="nav-item"><a class="nav-link active" href="#tablas_rendimiento" data-toggle="tab">TABLAS RENDIMIENTOS</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#informe_maq" data-toggle="tab">RENDIMIENTO FRESADORA</a></li>
                                    <li class="nav-item dropdown">
                                    </li>
                                </ul>
                            </div><!-- /.card-header -->
                        </div>
                        <br />
                        <div class="tab-content">
                            <!-- TABLAS DE RENDIMINETO -->
                            <div id="tablas_rendimiento" class="tab-pane active">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">RENDIMIENTO VEHICULOS DE ASFALTO</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <select class="form-control  select2bs4" id="repdia_vehi" name="repdia_vehi"></select>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="button" id="btnver" class="btn btn-info"><span class="fas fa-search"></span></button>
                                                            <button type="button" id="btnvermas" class="btn btn-info">Ver Mas</button>
                                                            <input type="hidden" id="vehi_id_asfalto" name="vehi_id_asfalto">
                                                        </div>
                                                    </div>
                                                    <div class="card-block">
                                                        <div id="divgrafico" style="height: 250px; ">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">RENDIMIENTO VEHICULOS DE CONCRETO</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <select class="form-control  select2bs4" id="repdia_vehix" name="repdia_vehix"></select>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="button" id="btnverx" class="btn btn-info"><span class="fas fa-search"></span></button>
                                                            <button type="button" id="btnvermasx" class="btn btn-info">Ver Mas</button>
                                                            <input type="hidden" id="vehi_id_concreto" name="vehi_id_concreto" value="">
                                                        </div>
                                                    </div>
                                                    <div class="card-block">
                                                        <div id="divgraficoMixer" style="height: 250px; "></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">RENDIMIENTO MAQUINARIA</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <select class="form-control  select2bs4" id="repdia_maquinaria" name="repdia_maquinaria"></select>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="button" id="btnverMaq" class="btn btn-info"><span class="fas fa-search"></span></button>
                                                            <button type="button" id="btnvermasMaq" class="btn btn-info">Ver Mas</button>
                                                            <input type="hidden" id="vehi_id_maquinaria" name="vehi_id_maquinaria" value="">
                                                        </div>
                                                    </div>
                                                    <div class="card-block">
                                                        <div id="divgraficoMaquinaria" style="height: 250px; "></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- cierra tablas -->

                            <!-- INFORME FRESADORA -->
                            <div id="informe_maq" class="tab-pane">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">M3/HORAS TRABAJADAS</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <!-- Select de Equipo -->
                                                        <div class="col-md-3">
                                                            <label><b></b></label>
                                                            <select class="form-control select2bs4" id="repdia_fresadora" name="repdia_fresadora"></select>
                                                        </div>
                                                        <!-- Fecha Inicio -->
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><b>Del Día</b></label>
                                                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                                                            </div>
                                                        </div>
                                                        <!-- Fecha Final -->
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><b>Hasta el Día</b></label>
                                                                <input type="date" name="fecha_final" id="fecha_final" class="form-control">
                                                            </div>
                                                        </div>
                                                        <!-- Botón de búsqueda -->
                                                        <div class="col-md-3 d-flex align-items-end">
                                                            <button type="button" id="btnFresadora" class="btn btn-info btn-block" style="width: 50px;">
                                                                <span class="fas fa-search"></span>
                                                            </button>&nbsp;&nbsp;
                                                            <button type="button" id="btnvermasfrsd" class="btn btn-info">Ver Más</button>
                                                        </div>
                                                    </div>
                                                    <!-- Botón guardar el vehi_id -->
                                                    <div class="row mt-1">
                                                        <div class="col-md-12 text-right">
                                                            <input type="hidden" id="vehi_id_fresadora" name="vehi_id_fresadora" value="">
                                                        </div>
                                                    </div>
                                                    <!-- Espacio para el gráfico -->
                                                    <div class="card-block mt-3">
                                                        <div id="divgraficoFresadora" style="height: 250px;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Espacio para el card -->
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">PUNTAS/M3 </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        
                                                        <!-- Botón de búsqueda -->
                                                        <div class="col-md-3 d-flex align-items-end">
                                                            <button type="button" id="btnvermasfrsd_pnts" class="btn btn-info">Ver Más</button>
                                                        </div>
                                                    </div>
                                                    <!-- Botón guardar el vehi_id -->
                                                    <div class="row mt-1">
                                                        <div class="col-md-12 text-right">
                                                            <input type="hidden" id="vehi_id_fresadora_puntas" name="vehi_id_fresadora_puntas" value="">
                                                        </div>
                                                    </div>
                                                    <!-- Espacio para el gráfico -->
                                                    <div class="card-block mt-3">
                                                        <div id="divgraficoFresadora_Puntas" style="height: 250px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">DATOS FRESADORA</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <img src="../../public/img/fresadora_asft.png" width="150" height="150">
                                                        </div>
                                                        <div class="col">
                                                            <label for="vehi_placa">Nº Placa:</label>
                                                            <input type="text" class="form-control mt-2" name="vehi_placa" id="vehi_placa" disabled>
                                                            <div class="form-group">
                                                                <label for="tipo_nombre">Tipo Vehiculo:</label>
                                                                <input type="text" class="form-control mt-2" name="tipo_nombre" id="tipo_nombre" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">PUNTAS FRESADORA</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Espacio para card -->
                                                <div class="card-body">
                                                    <!-- Botón guardar el vehi_id -->
                                                    <div class="row mt-1">
                                                        <div class="col-md-12 text-right">
                                                            <input type="hidden" id="vehi_id_fresadora_puntas" name="vehi_id_fresadora_puntas" value="">
                                                        </div>
                                                    </div>
                                                    <!-- Espacio para el gráfico -->
                                                    <div class="card-block mt-3">
                                                        <div id="divgraficoPuntas" style="height: 430px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </div>

                    <?php  } ?>
                </div>

                <?php require_once "../MainFooter/footer.php" ?>
            </div>
            <?php require_once "../MainJS/JS.php" ?>
            <?php require_once "../Conductores/Firma.php" ?>
            <script src="../../config/config.js"></script>
            <script type="text/javascript" src="../Conductores/PreoCond.js"></script>
            <script type="text/javascript" src="../Conductores/Firma.js"></script>
            <script type="text/javascript" src="../Conductores/RepdiaCond.js"></script>
            <script type="text/javascript" src="../Alistamineto/VerAlistamiento.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
            <script src="../../public/plugins/jquery-knob/jquery.knob.min.js"></script>
            <script src="../../public/plugins/sparklines/sparkline.js"></script>
            <script type="text/javascript" src="inicio.js"></script>

        </body>

        </html>
<?php
    } else {
        header("location:" . Conectar::ruta() . "index.php");
    }
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>