<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"Reporte_fallas");
if(is_array($datos)and count($datos)>0){?>
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
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Mantenedores</a></li>
                <li class="breadcrumb-item active">Reporte Fallas</li>
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
              <table id="formulario_data" name="formulario_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
              <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">REPORTES FALLAS</caption>
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 12%;">CÓDIGO</th>
                    <th th class="text-center"  style="width: 10%;">FECHA DE REALIZACION </th>
                    <th th class="text-center"  style="width: 10%;">PLACA VEHICULO </th>
                    <th th class="text-center"  style="width: 6%;">ACCIONES</th>
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
<script type="text/javascript" src="Reporte_fallas.js"></script>

</body>
</html>
<?php
}else {
  header("Location:".Conectar::ruta()."Pagina404.php");
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE:JACKSON DANIEL BORJA 
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>