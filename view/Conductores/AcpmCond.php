<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "AcpmCond");
if (is_array($datos) and count($datos) > 0) {
?>
  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Distribucion ACPM</title>
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
                  <li class="breadcrumb-item active">Distribucion ACPM</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.HEADER -->

        <!-- MAIN -->
        <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
        <div class="col-md-3">
          <div class="content">
            <div class="container-fluid">
              <div class="row">
                <div class="col-12">
                  <div class="card" style="max-width: 400px; max-height: 400px;">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-xs-12 col-md-6 text-center" style="margin: 0 auto; display:inline; max-width: 400px;">
                          <label>GALONES DISPONIBLES</label>

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

        <div class="content">
          <div class="container-fluid">
            <ul class="nav nav-tabs" id="reporteTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="reporte-tab" data-toggle="tab" href="#ssc" role="tab">Solicitudes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="otra-tab" data-toggle="tab" href="#dss" role="tab">Distribuciones</a>
              </li>
            </ul>

            <!-- CONTENIDO DE LAS PESTAÑAS -->
            <div class="tab-content" id="reporteTabsContent">
              <!-- CUMPLIMIENTO INSPECTORES -->
              <div class="tab-pane fade show active" id="ssc" role="tabpanel">
                <div class="card">
                  <div class="box-typical box-typical-padding">
                    <br><br>
                    <table id="ssc_data" name="ssc_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                      <thead class="bg-info">
                        <tr>
                          <th th class="text-center" style="width: 8%;">FECHA</th>
                          <th th class="text-center" style="width: 8%;"># GALONES AUTORIZADOS</th>
                          <th th class="text-center" style="width: 12%;">OBRA</th>
                          <th th class="text-center" style="width: 12%;">ESTACION DE SERVICIO</th>
                          <th th class="text-center" style="width: 9%;">ESTADO</th>
                          <th th class="text-center" style="width: 6%;">ACCIONES</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- DISTRIBUCION -->
              <div class="tab-pane fade" id="dss" role="tabpanel">
                <div class="card">
                  <div class="box-typical box-typical-padding">
                    <br><br>
                    <table id="dss_data" name="dss_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                      <thead class="bg-info">
                        <tr>
                          <th th class="text-center" style="width: 8%;">FECHA</th>
                          <th th class="text-center" style="width: 8%;"># GALONES</th>
                          <th th class="text-center" style="width: 8%;">VEHICULO</th>
                          <th th class="text-center" style="width: 12%;">OPERADOR</th>
                          <th th class="text-center" style="width: 9%;">ESTADO</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div><!-- Cierra tab-content -->

          </div>
        </div>
        <!-- MAIN -->
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
      </div>
      <?php require_once("../MainFooter/footer.php") ?>
    </div>
    <?php require_once("crudAcpmCond.php") ?>
    <?php require_once("../MainJS/JS.php") ?>
    <script src="../../public/plugins/jquery-knob/jquery.knob.min.js"></script>
    <script type="text/javascript" src="AcpmCond.js"></script>

  </body>

  </html>
<?php
} else {
  header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>