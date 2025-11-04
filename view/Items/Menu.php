<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"Menu");
if(is_array($datos)and count($datos)>0){
?>

<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="stylesheet" href="../../public/css/tablas.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>MENU</title>
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
            <h1 class="m-0">Vistas</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                <li class="breadcrumb-item"><a href="#">Items</a></li>
                <li class="breadcrumb-item active">Vistas</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- /HEADER -->

      <!-- MAIN -->
      <div class="content">
      
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
            <button type="button" id="btnnuevavista"  class="btn btn-info">Nueva Vista</button>
            <br><br>
            <table id="menu_data" name="oper_data" class="table table-bordered table-striped table-vcenter js-dataTable-full" >
              <thead class="bg-info text-center">
                <tr>
                  <th th class="text-center"  style="width: 4%;">CÓDIGO</th>
                  <th th class="text-center"  style="width: 10%;">NOMBRE DE LA VISTA</th>
                  <th th class="text-center"  style="width: 8%;">RUTA</th>
                  <th th class="text-center"  style="width: 12%;">ICONO</th>
                  <th th class="text-center"  style="width: 5%;">IDENTIFICADOR</th>
                  <th th class="text-center"  style="width: 6%;">GRUPO</th>
                  <th th class="text-center"  style="width: 4%;">ACCIONES</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
      </div>
      <!-- /MAIN -->

      </div>

      <aside class="control-sidebar control-sidebar-dark">
      </aside>
  
    </div>
    <!-- Main Footer -->
    <?php require_once ("../MainFooter/footer.php")?>

  </div>
  <!-- ./wrapper -->
  <?php require_once ("crudMenu.php")?>
  <?php require_once ("../MainJS/JS.php")?>
  <script type="text/javascript" src="crudMenu.js"></script>
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
2025 */
?>