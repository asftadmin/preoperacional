<?php require_once ("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validacion_acceso($_SESSION["user_id"],"RevisionAlistaActivos");
if(is_array($datos)and count($datos)>0){
  ?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Consultar Alistamientos Activos Inspector</title>
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
                <li class="breadcrumb-item active">Consultar Alistamientos Inspector</li>
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
              <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>"  >
              <table id="inspe_data" name="inspe_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
              <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">ALISTAMIENTOS ASIGNADOS</caption>
                <thead class="bg-info" >
                  <tr>
                    <th th class="text-center"  style="width: 8%;">FECHA DE CREACIÓN</th>
                    <th th class="text-center"  style="width: 6%;">CODIGO</th>
                    <th th class="text-center"  style="width: 8%;">ESTADO</th>
                    <th th class="text-center"  style="width: 8%;">OBSERVACIONES RESIDENTE</th>
                    <th th class="text-center"  style="width: 10%;">CONDUCTOR</th>
                    <th th class="text-center"  style="width: 8%;">OBRA</th>
                    <th th class="text-center"  style="width: 8%;">FECHA DE RECIBIDO</th>
                    <th th class="text-center"  style="width: 8%;">OBSERVACIONES INSPECTOR</th>
                    <th th class="text-center"  style="width: 10%;">ACCIONES</th>
                  </tr>
                </thead>
                <tbody class="text-center">

                </tbody>
              </table>
            </div>
          </div>
      </div>
    </div>

    <div class="content">
        <div class="container-fluid">
          <div class="card">
            <br>
            <div class="box-typical box-typical-padding">
              <table id="HM_Inspec" name="HM_Inspec" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <caption style="caption-side: top; font-size: 1.5em; font-weight: bold; text-align:center;">EQUIPOS A CARGO</caption>
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center" style="width: 8%;">CODIGO</th>
                    <th th class="text-center" style="width: 8%;">FECHA DE ASIGNACION</th>
                    <th th class="text-center" style="width: 8%;">ALISTAMIENTO</th>
                    <th th class="text-center" style="width: 8%;">EQUIPO</th>
                    <th th class="text-center" style="width: 8%;">ESTADO</th>
                    <th th class="text-center" style="width: 8%;">OBRA</th>
                    <th th class="text-center" style="width: 8%;">OBSERVACION</th>
                    <th th class="text-center" style="width: 8%;">ACCIONES</th>
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
<?php require_once ("Calificar.php")?>
<?php require_once ("crudRealistacion.php")?>
<script src="../../public/plugins/select2/js/select2.full.min.js"></script>
<script src="../../config/config.js"></script>
<script type="text/javascript" src="RevisionAlistaInspe.js"></script>
<script type="text/javascript" src="Calificar.js"></script>
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