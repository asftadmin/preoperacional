<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"acpm_externos");
if(is_array($datos)and count($datos)>0){
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Autorizacion Despachos Externos</title>
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
            <h1 class="m-0">Autorizacion Despachos Externos</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item active">Autorizacion Despachos Externos</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- /.HEADER -->

      <!-- MAIN -->
      <div class="content">
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
              <button type="button" id="btnnuevo_desp_ext"  class="btn btn-info">Nuevo Despacho Externo</button>
              <br><br><input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>">
              <table id="dpex_data" name="dpex_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 8%;">CODIGO</th>
                    <th th class="text-center"  style="width: 8%;">FECHA</th>
                    <th th class="text-center"  style="width: 15%;">CONDUCTOR</th>
                    <th th class="text-center"  style="width: 8%;"># GALONES AUTORIZADOS</th>
                    <th th class="text-center"  style="width: 12%;">OBRA</th>
                    <th th class="text-center"  style="width: 12%;">ESTACION DE SERVICIO</th>
                    <th th class="text-center"  style="width: 9%;">ESTADO</th>
                    <th th class="text-center"  style="width: 6%;">ACCIONES</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
        </div>
      </div>
      <!-- MAIN -->
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    </div>
    <?php require_once ("../MainFooter/footer.php")?>
  </div>
  
<?php require_once ("DespachoExterno.php")?>
<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="AcpmExternos.js"></script>
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