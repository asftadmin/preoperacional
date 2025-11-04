<?php require_once("../../config/conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
<title>Detalle Reporte Diario</title>
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
                <li class="breadcrumb-item"><a href="#">Ver Reportes Diarios</a></li>
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
          <div class="card">
            <div class="card-header bg-gray mt-0">
              <div class="row mt-2">
              </div>
              <div class="row mt-2">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="user_cedula">Cédula:</label>
                    <input type="text" id="user_cedula" name="user_cedula" style="font-size:14px;" disabled>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="conductor_nombre_completo">Nombre:</label>
                    <input type="text" id="conductor_nombre_completo" name="conductor_nombre_completo" size="40" style="font-size:14px;" disabled>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="repdia_fech">Fecha:</label>
                    <input type="text" id="repdia_fech" name="repdia_fech" style="font-size:14px;" disabled>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="repdia_recib">#</label>
                    <input type="text" id="repdia_recib" name="repdia_recib" style="font-size:14px;" disabled>
                  </div>
                </div>
              </div>
            </div>
            <div id="" class="card-body">
              <button class="btn btn-info" onclick="goBack()">Volver</button>
              <div class="box-typical box-typical-padding">
                <br><br>
                <table id="detalle_data" name="detalle_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                  <thead class="bg-info">
                    <tr>
                      <th th class="text-center" style="width: 3%;">HORA INICIO</th>
                      <th th class="text-center" style="width: 3%;">HORA FINAL</th>
                      <th th class="text-center" style="width: 8%;">ACTIVIDAD</th>
                      <th th class="text-center" style="width: 2%;">GASOLINA</th>
                      <th th class="text-center" style="width: 2%;">ACPM</th>
                      <th th class="text-center" style="width: 2%;">A. MOTOR</th>
                      <th th class="text-center" style="width: 2%;">A. HIDRAULICO</th>
                      <th th class="text-center" style="width: 2%;">A. TRASMICION</th>
                      <th th class="text-center" style="width: 2%;">GRASA</th>
                      <th th class="text-center" style="width: 2%;">VOLUMEN</th>
                      <th th class="text-center" style="width: 2%;">PLACA</th>
                      <th th class="text-center" style="width: 2%;">TIPO</th>
                      <th th class="text-center" style="width: 2%;">KM/HR INICIAL</th>
                      <th th class="text-center" style="width: 2%;">KM/HR FINAL</th>
                      <th th class="text-center" style="width: 4%;">OBRA</th>
                      <th th class="text-center" style="width: 4%;">ING RESIDENTE</th>
                      <th th class="text-center" style="width: 4%;">INSPECTOR</th>
                      <th th class="text-center" style="width: 4%;"># PUNTAS</th>
                      <th th class="text-center" style="width: 4%;">CAMBIO ACEITE</th>
                      <th th class="text-center" style="width: 4%;">KM/HR A LA FECHA DE CAMBIO</th>
                      <th th class="text-center" style="width: 10%;">OBSERVACIONES</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
                <br><br><br>
              </div>
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
  <script type="text/javascript" src="Detalle.js"></script>
</body>

</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>