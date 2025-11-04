<?php require_once('../../config/conexion.php');
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"Kilometraje");
if(is_array($datos)and count($datos)>0){
?>
<!DOCTYPE html>
<html lang='es'>
<?php require_once('../MainHead/head.php');
?>
<link rel='stylesheet' href='../../public/css/inicio.css'>
<link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
<title>Cosultar Kilometraje</title>
</head>

<body class='hold-transition sidebar-mini'>
  <div class='wrapper'>
    <?php require_once('../MainNav/nav.php');
    ?>
    <?php require_once('../MainMenu/menu.php');
    ?>
    <div class='content-wrapper'>
      <!-- HEADER -->
      <div class='content-header'>
        <div class='container-fluid'>
          <div class='row mb-2'>
            <div class='col-sm-6'>
            </div>
            <div class='col-sm-6'>
              <ol class='breadcrumb float-sm-right'>
                <li class='breadcrumb-item'><a href='../inicio/inicio.php'>Inicio</a></li>
                <li class='breadcrumb-item'><a href='#'>Mantenedores</a></li>
                <li class='breadcrumb-item active'>Consultar Kilometraje</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- /.HEADER -->
      <!-- MAIN -->
      <div class='content'>
        <div class='container-fluid'>
          <div class='card'>
            <br>
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
                    <button type="button" id="btnBuscar" class="btn btn-info">Buscar</button>
                  </div>
                </div>
              </div>
            <table id='tabla_resultado' name='tabla_resultado' class='table table-bordered table-striped table-vcenter js-dataTable-full'>
              <thead class='bg-info'>
                <tr>
                  <th th class='text-center' style='width: 12%;'>PLACA</th>
                  <th th class='text-center' style='width: 10%;'>FECHA DE REALIZACION </th>
                  <th th class='text-center' style='width: 10%;'>CODIGO </th>
                  <th th class='text-center' style='width: 6%;'>KILOMETRAJE</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>

          <br>

        </div>
      </div>
    </div>

    <!-- MAIN -->
    <aside class='control-sidebar control-sidebar-dark'>
    </aside>
  
  <?php require_once('../MainFooter/footer.php') ?>


  <!-- MODAL DE CALIFICAR -->
  <?php require_once('../MainJS/JS.php') ?>
  <script type='text/javascript' src='Kilometraje.js'></script>

</body>

</html>
<?php
}else {
  header("Location:".Conectar::ruta()."Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>