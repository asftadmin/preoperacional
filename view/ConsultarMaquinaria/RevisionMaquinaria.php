<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"RevisionMaquinaria");
if(is_array($datos)and count($datos)>0){
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Consultar Maquinaria</title>
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
                <li class="breadcrumb-item active">Consultar Maquinaria</li>
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

              
              <table id="maquinaria_data" name="maquinaria_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
              <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">REPORTES MAQUINARIA</caption>  
              <thead class="bg-info" >
                  <tr>
                  <th th class="text-center"  style="width: 8%;">PLACA</th>
                    <th th class="text-center"  style="width: 8%;">MARCA</th>
                    <th th class="text-center"  style="width: 8%;">ESTADO</th>
                    <th th class="text-center"  style="width: 8%;">TIPO</th>
                    <th th class="text-center"  style="width: 8%;">ACCIONES</th>
                  </tr>
                </thead>
                <tbody class="text-center">

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
<script src="../../public/plugins/select2/js/select2.full.min.js"></script>
<script src="../../config/config.js"></script>
<script type="text/javascript" src="RevisionMaquinaria.js"></script>
</body>
</html>
<?php
}else {
  print "¡ERROR 404 NO TIENE ACCESO A ESTA VISTA!";
}
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>