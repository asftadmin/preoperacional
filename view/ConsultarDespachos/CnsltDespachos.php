<?php

require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "CnsltDespachos");
if (is_array($datos) and count($datos) > 0) {
?>

  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/css/graficos.css">
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Reporte Despachos</title>
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
                  <li class="breadcrumb-item"><a href="#">Reportes Despachos</a></li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.HEADER -->

        <!-- MAIN -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-4">
                <select class="form-control  select2bs4" id="desp_vehi" name="desp_vehi"></select>
              </div>
              <div class="col-md-3">
                <div class="form-group d-flex align-items-center">
                  <div class="input-group">
                    <input type="text" class="form-control" id="rangoFechas" placeholder="Seleccione un rango de fechas">
                    <div class="input-group-append">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-alt"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group d-flex align-items-center gap-2">
                  <button type="button" id="btnBuscar" class="btn btn-info">Buscar</button>&nbsp;&nbsp;
                  <button type="button" id="btnDetalle" class="btn btn-info">Mas a Detalle</button>
                </div>
              </div>
            </div>
            <br />
            <div class="tab-content">
              <!-- GRAFICO DE RENDIMINETO VEHICULOS -->
              <div id="tablas_rendimiento" class="tab-pane active">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card card-info">
                        <div class="card-header d-flex justify-content-center">
                          <h3 class="card-title text-center w-100">CONSUMO DE ACPM MENSUAL</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div class="row align-items-center">

                          </div>
                          <div class="card-block">
                            <div id="divgrafico" style="height: 250px; ">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- TABLAS DE RENDIMINETO VEHICULO -->
                    <div class="col-md-6">
                      <div class="card card-info">
                        <div class="card-header ">
                          <h3 class="card-title ">Tabla Rendimiento</h3>&nbsp;&nbsp;
                          <input type="text" id="placa" style="font-size:14px;"
                          disabled>
                          <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div class="box-typical box-typical-padding">
                            <table id="pre_data" name="pre_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                              <thead class="bg-info">
                                <tr>
                                  <th th class="text-center" style="width: 5%;">FECHA</th>
                                  <th th class="text-center" style="width: 12%;">OBRA</th>
                                  <th th class="text-center" style="width: 5%;">GALONES</th>
                                  <th th class="text-center" style="width: 14%;">OPERADOR</th>
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                      <!-- TABLAS DE RENDIMINETO VEHICULO -->
                    <div class="col-md-6">
                      <div class="card card-info">
                        <div class="card-header d-flex justify-content-center">
                          <h3 class="card-title text-center w-100">Detalle Rendimiento</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div class="row align-items-center">
                          </div>
                          <div class="card-block">
                            <div id="divgraficoDetalle" style="height: 250px; "></div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div><!-- cierra tablas -->
            </div>
          </div>
        </div>
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
      </div>
      <?php require_once("../MainFooter/footer.php") ?>
    </div>




    <?php require_once("../MainJS/JS.php") ?>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="../../config/config.js"></script>
    <script type="text/javascript" src="CnsltDespachos.js"></script>
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