<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "consultar_check");
if (is_array($datos) and count($datos) > 0) {
?>
  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Consultar Checkeos</title>
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
                  <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                  <li class="breadcrumb-item active">Consultar Checkeos</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.HEADER -->

        <!-- MAIN -->
        <div class="content">
          <div class="container-fluid">
            <div class="card">
              <br>
              <div class="box-typical box-typical-padding">


                <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group">
                      <div class="form-group d-flex align-items-center">
                      &nbsp;&nbsp;<select class="form-control select2bs4" id="pre_equipo" name="pre_equipo" style="width: 100%;" required>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group d-flex align-items-center">
                      <button type="button" id="btnBuscar" class="btn btn-info mr-2">Buscar</button>
                      <button type="button" id="btnlimpiar" class="btn btn-info mr-2">Ver Todo</button>
                    </div>
                  </div>
                </div>
                <table id="check_data" name="check_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                  <thead class="bg-info">
                    <tr>
                      <th th class="text-center" style="width: 8%;">EQUIPO</th>
                      <th th class="text-center" style="width: 8%;">FECHA DE CREACIÓN</th>
                      <th th class="text-center" style="width: 8%;">CODIGO INTERNO</th>
                      <th th class="text-center" style="width: 8%;">ESTADO</th>
                      <th th class="text-center" style="width: 8%;">FECHA DE REVISIÓN</th>
                      <th th class="text-center" style="width: 8%;">OPERARIO</th>
                      <th th class="text-center" style="width: 8%;">ACCIONES</th>
                    </tr>
                  </thead>
                  <tbody class="text-center">
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
  
    <!-- The Modal -->
    

    <!-- MODAL DE CALIFICAR -->
    <?php require_once("Calificar.php") ?>
    <?php require_once("../MainJS/JS.php") ?>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script src="../../config/config.js"></script>
    <script type="text/javascript" src="CnsltarCheckeo.js"></script>

  </body>

  </html>
<?php
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