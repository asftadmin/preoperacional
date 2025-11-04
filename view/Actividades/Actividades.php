<?php require_once("../../config/conexion.php"); 
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"Actividades");
if(is_array($datos)and count($datos)>0){
?>

<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
<title>ACTIVIDADES</title>
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
            <h1 class="m-0">ACTIVIDADES</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                <li class="breadcrumb-item"><a href="#">Items</a></li>
                <li class="breadcrumb-item active">Actividades</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- /.HEADER-->

      <!-- MAIN -->
      <div class="content">
        <div class="container-fluid">
          <div class="box-typical box-typical-padding">
            <button type="button" id="btnnuevaAct" class="btn btn-info">Nueva Actividad</button>
            <br><br>
            <table id="Act_data" name="Act_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
              <thead class="bg-info">
                <tr>
                  <th th class="text-center" style="width: 2%;">CÓDIGO</th>
                  <th th class="text-center" style="width: 15%;">ACTIVIDAD</th>
                  <th th class="text-center" style="width: 9%;">TARIFA</th>
                  <th th class="text-center" style="width: 5%;">UND</th>
                  <th th class="text-center" style="width: 10%;">TIPO VEHICULO</th>
                  <th th class="text-center" style="width: 4%;">ACCIONES</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
        <!-- /.MAIN -->
      </div>


      <aside class="control-sidebar control-sidebar-dark">
      </aside>

    </div>
    <?php require_once("../MainFooter/footer.php") ?>
  </div>
  <?php require_once("crudActividades.php") ?>
  <?php require_once("../MainJS/JS.php") ?>
  <script type="text/javascript" src="crudActividades.js"></script>
</body>

</html>
<?php
}else {
  header("Location:".Conectar::ruta()."Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>