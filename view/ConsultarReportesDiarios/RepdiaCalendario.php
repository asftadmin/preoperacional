<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "CalendarioReportes");
if (is_array($datos) and count($datos) > 0) {
?>

  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
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
  <title>Calendario Reportes Diarios</title>
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
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                  <li class="breadcrumb-item"><a href="../ConsultarReportesDiarios/ConsultarReporte.php">Ver Reportes Diarios</a></li>
                  <li class="breadcrumb-item active">Calendario</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.HEADER -->

        <!-- MAIN -->
        <div class="content">
          <div class="container-fluid">
            <div class="card" id="container">
              <br>
              <div class="box-typical box-typical-padding">
              <div class="row mt-2 ">
                    <div class="col-md-8">
                        <div class="form-group">
                      &nbsp;&nbsp;<select class="form-control select2bs4" id="user_id" name="user_id" style="width: 40%;">
                      </select>&nbsp;&nbsp;&nbsp;
                      <button type="button" id="btnbuscar" class="btn btn-info">Buscar</button>&nbsp;&nbsp;&nbsp;
                      <input type="color" id="Preoperacionales" name="Preoperacionales" value="#09aa41" />&nbsp;&nbsp;<label for="Preoperacionales">Preoperacionales</label>&nbsp;&nbsp;&nbsp;
                      <input type="color" id="Repdia" name="Repdia" value="#009BA9" />&nbsp;&nbsp;<label for="Repdia">Reportes Diarios</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <div class="content">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-12">
                              <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
                              <div class="small-box">
                                <div class="label">% CUMPLIMIENTOS DE PREOPERACIONALES</div>
                                <div class="inner">
                                  <div id="Porcentaje_preo"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div> 
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <div class="content">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-12">
                              <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
                              <div class="small-box">
                                <div class="label">% CUMPLIMIENTOS DE REPORTES DIARIOS</div>
                                <div class="inner">
                                  <div id="PORCENTAJE"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div> 
                        </div>
                    </div>    
                </div>
                <div id="Calendario"></div>

              </div>
            </div>
          </div>
        </div>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
      </div>
      <?php require_once("../MainFooter/footer.php") ?>
    </div>




    <?php require_once("../MainJS/JS.php") ?>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="RepdiaCalendario.js"></script>
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
2024 */
?>