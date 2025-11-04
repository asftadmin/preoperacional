<?php require_once ("../../config/conexion.php");?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Cerrar Actividades</title>
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
              <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item active"><a href="../ReportesDiarios/ReportesDiarios.php">Reporte Diario</a></li>
                <li class="breadcrumb-item active">Cerrar Actividad</li>
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

              <input type="hidden" id="repdia_recib" name="repdia_recib" style="font-size:14px;" value="<?php echo date("Ymd").''.$_SESSION["user_id"]; ?>" >
              <button type="button" id="btnbuscar"  class="btn btn-info">Buscar</button><br/><br/>

              <table id="cerrarAct_data" name="cerrarAct_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    
                    <th th class="text-center"  style="width: 9%;"># RECIBO</th>
                    <th th class="text-center"  style="width: 8%;">ACTIVIDAD</th>
                    <th th class="text-center"  style="width: 8%;">PLACA</th>
                    <th th class="text-center"  style="width: 8%;">KM / HR INICIAL</th>
                    <th th class="text-center"  style="width: 8%;">OBRA</th>
                    <th th class="text-center"  style="width: 4%;">ACCIONES</th>
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
    <?php require_once ("modalKiloFinal.php")?>
    <script type="text/javascript" src="CerrarActividad.js"></script>
</body>
</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>