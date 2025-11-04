<?php require_once ("../../config/conexion.php");?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Consultar Preoperacionales</title>
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <?php require_once("../MainNav/nav.php");?>
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
                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
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
              <table id="pre_data" name="pre_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 5%;">PLACA</th>
                    <th th class="text-center"  style="width: 5%;">FECHA DE REALIZACIÓN</th>
                    <th th class="text-center"  style="width: 5%;">FECHA DE REVISIÓN</th>
                    <th th class="text-center"  style="width: 8%;">ESTADO</th>
                    <th th class="text-center"  style="width: 18%;">OBSERVACIONES VERIFICADOR</th>
                    <th th class="text-center"  style="width: 3%;">ACCIONES</th>
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
    <?php require_once ("../MainFooter/footer.php")?>
  </div>


<!-- MODAL DE CALIFICAR -->
<?php require_once ("../MainJS/JS.php")?>
<script src="../../config/config.js"></script>
<script type="text/javascript" src="PreoCond.js"></script>

</body>
</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>