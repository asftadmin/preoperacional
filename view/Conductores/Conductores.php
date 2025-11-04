<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"Conductores");
if(is_array($datos)and count($datos)>0){
?>
<!DOCTYPE html>
<html lang="es">

    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Conductores</title>
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
      <?php require_once("../MainNav/nav.php");?> 
      <?php require_once("../MainMenu/menu.php");?>

    
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
                <li class="breadcrumb-item active">Conductores</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- HEADER -->

      <!-- MAIN-->
      <div class="content">
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
            <button type="button" id="btnnuevocond"  class="btn btn-info">Licencia Conductor</button>
            <br><br>
            <table id="cond_data" name="cond_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
            <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">CONDUCTORES</caption>
              <thead class="bg-info">
                <tr>
                  <th th class="text-center"  style="width: 8%;">CEDULA</th>
                  <th th class="text-center"  style="width: 15%;">CONDUCTOR</th>
                  <th th class="text-center"  style="width: 8%;">LICENCIA DE CONDUCCION (Expedición)</th>
                  <th th class="text-center"  style="width: 8%;">FECHA DE VENCIMIENTO (Licencia)</th>
                  <th th class="text-center"  style="width: 5%;">CATEGORIA (Licencia)</th>
                  <th th class="text-center"  style="width: 5%;">ROL CONDUCTOR</th>
                  <th th class="text-center"  style="width: 6%;">ACCIONES</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
      </div>
    </div>

    <aside class="control-sidebar control-sidebar-dark">

    </aside>

    </div><!-- / Wrapper -->
    <?php require_once ("../MainFooter/footer.php")?>
  </div>
  <?php require_once ("crudConductor.php")?>
  <?php require_once ("../MainJS/JS.php")?>
  <script type="text/javascript" src="crudConductor.js"></script>
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