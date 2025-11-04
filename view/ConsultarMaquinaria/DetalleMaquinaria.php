<?php require_once ("../../config/conexion.php");?>

<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Detalle Maquinaria</title>
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
                <li class="breadcrumb-item"><a href="#">Revision Maquinaria</a></li>
                <li class="breadcrumb-item active">Detalle</li>
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
            <div class="card-header bg-gray mt-0">
                <div class="row mt-2">
                </div>
                  <div class="row mt-2">
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="tipo_nombre">Tipo:</label>
                          <input type="text" id="tipo_nombre" name="tipo_nombre" style="font-size:14px;" disabled>
                      </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vehi_placa">Placa:</label>
                            <input type="text" id="vehi_placa" name="vehi_placa" size="40" style="font-size:14px;" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vehi_estado">Estado:</label>
                            <input type="text" id="vehi_estado" name="vehi_estado" size="40" style="font-size:14px;" disabled>
                        </div>
                    </div>
                    
                  </div>
            </div>
            
          <div id="" class="card-body" >
          <button class="btn btn-info" onclick="goBack()">Volver</button>
            <div class="box-typical box-typical-padding">
            <br><br>
              <table id="detalle_data" name="pre_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 5%;">FECHA ALISTAMIENTO</th>
                    <th th class="text-center"  style="width: 5%;">CODIGO</th>
                    <th th class="text-center"  style="width: 10%;">ESTADO</th>
                    <th th class="text-center"  style="width: 8%;">OBRA</th>
                    <th th class="text-center"  style="width: 8%;">RESIDENTE</th>
                    <th th class="text-center"  style="width: 10%;">OBSERVACIONES RESIDENTE</th>
                    <th th class="text-center"  style="width: 8%;">INSPECTOR</th>
                    <th th class="text-center"  style="width: 5%;">FECHA RECIBIMIENTO</th>
                    <th th class="text-center"  style="width: 10%;">OBSERVACIONES INSPECTOR</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <br>   
              <br>
              

            </div>
          </div>
        </div>
      </div>
    </div>

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    </div>
    <?php require_once ("../MainFooter/footer.php")?>
  </div>




<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="DetalleMaquinaria.js"></script>
</body>
</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>