<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "ReporteCumplimiento");
if (is_array($datos) and count($datos) > 0) {
?>
  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Reportes Cumplimiento</title>
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
                  <li class="breadcrumb-item active">Reporte Cumplimiento</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.HEADER -->

        <!-- MAIN -->
        <div class="content">
          <div class="container-fluid">
            <!-- NAV TABS -->
            <ul class="nav nav-tabs" id="reporteTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="reporte-tab" data-toggle="tab" href="#reporte" role="tab">Cumplimiento Inspectores</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="otra-tab" data-toggle="tab" href="#otra" role="tab">Cumplimiento Operadores</a>
              </li>
            </ul>

            <!-- CONTENIDO DE LAS PESTAÑAS -->
            <div class="tab-content" id="reporteTabsContent">

              <!-- CUMPLIMIENTO INSPECTORES -->
              <div class="tab-pane fade show active" id="reporte" role="tabpanel">
                <div class="card">
                  <br>
                  <div class="box-typical box-typical-padding">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          &nbsp;&nbsp;<label><b>Mes</b></label>
                          <select name="mes" id="mes" class="form-control">
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label><b>Año</b></label>
                          <input type="number" name="anio" id="anio" class="form-control" value="<?= date('Y') ?>">
                        </div>
                      </div>
                      <div class="col-md-5">
                        <div class="form-group">
                          <label><b>&nbsp;</b></label><br>
                          <button type="button" id="btnBuscar" class="btn btn-info">Buscar</button>
                        </div>
                      </div>
                    </div>
                    <table id="reporte_data" name="reporte_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                      <thead class="bg-info">
                        <tr>
                          <th class="text-center" style="width: 12%;">INSPECTOR</th>
                          <th class="text-center" style="width: 10%;"># OPERADORES A CARGO</th>
                          <th class="text-center" style="width: 8%;">% CUMPLIMIENTO</th>
                          <th class="text-center" style="width: 6%;">ACCIONES</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- CUMPLIMIENTO OPERADORES -->
              <div class="tab-pane fade" id="otra" role="tabpanel">
                <div class="card">
                  <br>
                  <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                          &nbsp;<label><b>Del Dia</b></label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label><b> Hasta el Dia</b></label>
                            <input type="date" name="fecha_final" id="fecha_final" class="form-control">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label><b>&nbsp;</b></label><br>
                            <button type="button" id="btnconsultar" class="btn btn-info">Buscar</button>
                          </div>
                        </div>
                      </div>
                  <div class="box-typical box-typical-padding">
                    <table id="cmpcond_data" name="cmpcond_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                      <thead class="bg-info">
                        <tr>
                          <th class="text-center" style="width: 12%;">CONDUCTOR</th>
                          <th class="text-center" style="width: 10%;">% CUMPLIMIENTO PREOPERACIONALES</th>
                          <th class="text-center" style="width: 10%;">% CUMPLIMIENTO REPORTES DIARIOS</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div> <!-- Cierra tab-content -->

          </div>
        </div>


        <!-- MAIN -->

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
      </div>
      <?php require_once("../MainFooter/footer.php") ?>
    </div>


    <!-- MODAL DE CALIFICAR -->
    <?php require_once("DetalleCumplimiento.php") ?>
    <?php require_once("../MainJS/JS.php") ?>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script src="../../config/config.js"></script>
    <script type="text/javascript" src="ReporteCumplimiento.js"></script>

  </body>

  </html>
<?php
} else {
  header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE:JACKSON DANIEL BORJA 
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>