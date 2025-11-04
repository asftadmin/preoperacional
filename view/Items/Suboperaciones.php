<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"SubOperaciones");
if(is_array($datos)and count($datos)>0){
?>

<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>SubOperaciones</title>
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
            <h1 class="m-0">SUB OPERACIONES</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                <li class="breadcrumb-item"><a href="#">Items</a></li>
                <li class="breadcrumb-item active">SubOperaciones</li>
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
            <button type="button" id="btnnuevosuboper"  class="btn btn-info">Nueva SubOperacion</button>
            <br><br>
            <table id="suboper_data" name="suboper_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
              <thead class="bg-info">
                <tr>
                  <th th class="text-center"  style="width: 2%;">CÓDIGO</th>
                  <th th class="text-center"  style="width: 8%;">OPERACIÓN</th>
                  <th th class="text-center"  style="width: 19%;">SUBOPERACIÓN</th>
                  <th th class="text-center"  style="width: 7%;">SUBOPERACIÓN VEHICULO</th>
                  <th th class="text-center"  style="width: 7%;">ESTADO</th>
                  <th th class="text-center"  style="width: 4%;">ACCIONES</th>
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
    <?php require_once ("../MainFooter/footer.php")?>
  </div>
  <?php require_once ("crudSuboperaciones.php")?>
  <?php require_once ("../MainJS/JS.php")?>
  <script type="text/javascript" src="crudSuboperaciones.js"></script>
</body>
</html>
<?php
}else {
  header("Location:".Conectar::ruta()."Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>