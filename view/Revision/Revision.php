<?php require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"], "Revision");
if (is_array($datos) and count($datos) > 0) {
?>
  <!DOCTYPE html>
  <html lang="es">
  <?php require_once("../MainHead/head.php"); ?>
  <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="../../public/css/inicio.css">
  <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
  <title>Consultar Preoperacionales</title>
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
                  <li class="breadcrumb-item active">Consultar Preoperacional</li>
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

                <div class="row mt-2 ">
                  <div class="col-md-3">
                    <div class="form-group">
                      <div class="form-group d-flex align-items-center">
                      &nbsp;&nbsp;<select class="form-control select2bs4" id="pre_vehiculo" name="pre_vehiculo" style="width: 100%;" required>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <div class="form-group d-flex align-items-center">
                        <select class="form-control select2bs4" id="operador" name="operador" style="width: 100%;" required>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row align-items-end">
                  <div class="col-md-3">
                    <div class="form-group">
                    &nbsp;&nbsp;<label><b>Del Dia</b></label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label><b>Hasta el Dia</b></label>
                      <input type="date" name="fecha_final" id="fecha_final" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group d-flex align-items-center">
                      <button type="button" id="btnBuscar" class="btn btn-info mr-2">Buscar</button>
                      <button type="button" id="btnlimpiar" class="btn btn-info mr-2">Ver Todo</button>
                      <button type="button" id="Acum" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i>&nbsp;&nbsp;Informes</button>
                    </div>
                  </div>
                </div>

                <table id="pre_data" name="pre_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                  <thead class="bg-info">
                    <tr>
                      <th th class="text-center" style="width: 8%;">PLACA</th>
                      <th th class="text-center" style="width: 8%;">FECHA DE CREACIÓN</th>
                      <th th class="text-center" style="width: 8%;">TIPO</th>
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
    <div id="myModal" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header bg-info text-center">
            <h3 class="modal-title w-100" id="mdltitulo"></h3>
            <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
          </div>
          <form method="post" id="informe_form">
            <div class="modal-body">
              <div class="row mt-2">
                <div class="col-md-10">
                  <div class="form-group">
                    <select id="pdfSelect" class="form-control">
                    <option value="Autobmba">Autobomba</option>
                    <option value="BmbaEstn">Bomba Estacionaria</option>
                    <option value="Cargador">Cargador</option>
                    <option value="Fresadora">Fresadora</option>
                    <option value="Fresadora">Finisher</option>
                    <option value="Mixer">Mixer</option>
                    <option value="Mtnvdra">Motoniveladora</option>
                    <option value="Vehiculo">Vehiculo</option>
                    <option value="Vibro">VibroCompactador</option>
                    <option value="Volquetas">Volqueta</option>
                    <option value="Tracto">Tracto</option>
                    <option value="PlntaAsft">Planta Asfalto</option>
                    <option value="PlntaCrto">Planta Concreto</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <button type="button" id="myBtn" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i></button>
                  </div>
                </div>
              </div>
            </div>
        </div>
        </form>
      </div>
    </div>

    <!-- MODAL DE CALIFICAR -->
    <?php require_once("Calificar.php") ?>
    <?php require_once("../MainJS/JS.php") ?>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script src="../../config/config.js"></script>
    <script type="text/javascript" src="VerPreoperacional.js"></script>
    <script type="text/javascript" src="Calificar.js"></script>
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