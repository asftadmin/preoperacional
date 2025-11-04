<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "RDMensual");
if (is_array($datos) and count($datos) > 0) {
?>

  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Reporte Diario Mensual</title>
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
                  <li class="breadcrumb-item active">Detalle</li>
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
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="form-group d-flex align-items-center">
                        <label for="repdia_vehi" class="mr-2">Placa:</label>
                        <select class="form-control select2bs4" id="repdia_vehi" name="repdia_vehi" style="width: 100%;" required>
                        </select>&nbsp;&nbsp;&nbsp;
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group d-flex align-items-center">
                      <select class="form-control select2bs4" id="repdia_user" name="repdia_user" required>
                      </select>&nbsp;&nbsp;&nbsp;
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label><b>Del Dia</b></label>
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
                      <button type="button" id="btnbuscar" class="btn btn-info">Buscar</button>
                      <button type="button" id="btnLimpiar" class="btn btn-info">Limpiar</button>
                    </div>
                  </div>
                </div>
                <div style="overflow-x: hidden; overflow-y: hidden;">
                  <table id="RDmensuales_data" name="RDmensuales_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                    <thead class="bg-info">
                      <tr>
                        <th class="text-center" style="width: 3%;">FECHA</th>
                        <th class="text-center" style="width: 3%;">HORA INICIO</th>
                        <th class="text-center" style="width: 3%;">HORA FINAL</th>
                        <th class="text-center" style="width: 2%;">GASOLINA</th>
                        <th class="text-center" style="width: 2%;">ACPM</th>
                        <th class="text-center" style="width: 2%;">A. MOTOR</th>
                        <th class="text-center" style="width: 2%;">A. HIDRAULICO</th>
                        <th class="text-center" style="width: 2%;">A. TRASMISIÓN</th>
                        <th class="text-center" style="width: 2%;">CAMBIO ACEITE</th>
                        <th class="text-center" style="width: 2%;">K/HR CAMBIO DE ACEITE</th>
                        <th class="text-center" style="width: 2%;">GRASA</th>
                        <th class="text-center" style="width: 2%;"># PUNTAS</th>
                        <th class="text-center" style="width: 2%;">VOLUMEN</th>
                        <th class="text-center" style="width: 6%;">ACTIVIDAD</th>
                        <th class="text-center" style="width: 6%;">TARIFA</th>
                        <th class="text-center" style="width: 6%;"># DE VIAJES</th>
                        <th class="text-center" style="width: 6%;">KM/HR GASTADO</th>
                        <th class="text-center" style="width: 6%;">FACTURACION</th>
                        <th class="text-center" style="width: 2%;">PLACA</th>
                        <th class="text-center" style="width: 2%;">TIPO</th>
                        <th class="text-center" style="width: 2%;">KM/HM INICIAL</th>
                        <th class="text-center" style="width: 2%;">KM/HM FINAL</th>
                        <th class="text-center" style="width: 4%;">OBRA</th>
                        <th class="text-center" style="width: 4%;">RESIDENTE</th>
                        <th class="text-center" style="width: 4%;">INSPECTOR</th>
                        <th class="text-center" style="width: 4%;">CONDUCTOR</th>
                        <th class="text-center" style="width: 4%;">OBSERVACIONES</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                <br><br><br>
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
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="RDMensuales.js"></script>
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