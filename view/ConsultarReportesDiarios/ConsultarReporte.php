<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "ConsultarReporte");
if (is_array($datos) and count($datos) > 0) {
?>
  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Consultar Reportes Diarios</title>
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
                  <li class="breadcrumb-item active">Consultar Reporte Diario</li>
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
                <div class="col-md-3">
                  <div class="form-group">
                    <div class="form-group d-flex align-items-center">
                    &nbsp;&nbsp;<select class="form-control select2bs4" id="repdia_vehi" name="repdia_vehi" style="width: 100%;" required>
                      </select>&nbsp;&nbsp;&nbsp;
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <div class="form-group d-flex align-items-center">
                    <select class="form-control select2bs4" id="repdia_user" name="repdia_user" required>
                    </select>&nbsp;&nbsp;&nbsp;
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">

                <div class="col-md-3">
                  <div class="form-group">
                  &nbsp;&nbsp;<label><b>Del Dia</b></label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><b> Hasta el Dia</b></label>
                    <input type="date" name="fecha_final" id="fecha_final" class="form-control">
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label><b>&nbsp;</b></label><br>
                    <button type="button" id="btnBuscar" class="btn btn-info">Buscar</button>
                    <button type="button" id="btnLimpiar" class="btn btn-info">Ver Todo</button>
                    <button type="button" id="btnCorreo" class="btn btn-info" >Enviar &nbsp;
                    <span class="fa fa-envelope"></span>
                    </button>
                    <button type="button" id="btnGenerarPDF" class="btn btn-danger" >
                    <span class="fa fa-file-pdf"></span>
                    </button>
                    </a>
                  </div>
                </div>
              </div>
                <table id="reporte_data" name="reporte_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                  <thead class="bg-info">
                    <tr>
                      <th th class="text-center" style="width: 12%;"># RECIBO</th>
                      <th th class="text-center" style="width: 10%;">FECHA DE REALIZACION </th>
                      <th th class="text-center" style="width: 10%;">USUARIO </th>
                      <th th class="text-center" style="width: 10%;">PLACA </th>
                      <th th class="text-center" style="width: 6%;">ACCIONES</th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- MAIN -->

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
      </div>
      <?php require_once("../MainFooter/footer.php") ?>
    </div>


    <!-- MODAL DE CALIFICAR -->
    <?php require_once("../MainJS/JS.php") ?>
    <?php require_once("Correo.php") ?>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script src="../../config/config.js"></script>
    <script type="text/javascript" src="ConsultarReporte.js"></script>

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