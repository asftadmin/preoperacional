<?php require_once("../../config/conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
<title>Detalle Grafico Fresadora</title>
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
                <li class="breadcrumb-item" id="btnvolver">Tabla Grafica</li>
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
                    <label for="vehi_placa">Placa:</label>
                    <input type="text" id="vehi_placa" name="vehi_placa"  style="font-size:14px;" disabled>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="repdia_fech">Fecha:</label>
                    <input type="text" id="repdia_fech" name="repdia_fech" style="font-size:14px;" disabled>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                  <label for="tipo_nombre">Tipo:</label>
                  <input type="text" id="tipo_nombre" name="tipo_nombre" style="font-size:14px;" disabled>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                  <label for="repdia_placa">#</label>
                  <input type="text" id="repdia_placa" name="repdia_placa" style="font-size:14px;" disabled>
                  </div>
                </div>
              </div>
            </div>
            <div id="" class="card-body">
              <div class="box-typical box-typical-padding">
              <button class="btn btn-info" onclick="goBack()">Volver</button><br><br>
                <table id="Frsd_data" name="Frsd_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                  <thead class="bg-info">
                    <tr>
                      <th th class="text-center" style="width: 5%;">HOROMETRAJE INICIAL</th>
                      <th th class="text-center" style="width: 5%;">HOROMETRAJE FINAL</th>
                      <th th class="text-center" style="width: 8%;">HOROMETRAJE GASTADO</th>
                      <th th class="text-center" style="width: 4%;">VOLUMEN</th>
                      <th th class="text-center" style="width: 6%;">M3</th>
                      <th th class="text-center" style="width: 9%;">RENDIMINETO</th>

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
  <script type="text/javascript" src="DetalleFrsd.js"></script>
</body>

</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>