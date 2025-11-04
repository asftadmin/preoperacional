<?php require_once ("../../config/conexion.php");?>

<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Detalle Asignamiento</title>
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
                    <div class="col-md-2">
                      <div class="form-group">
                          <label for="alista_fecha">Fecha:</label>
                          <input type="text" id="alista_fecha" name="alista_fecha" style="font-size:14px;" disabled>
                      </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="obras_nom">Obra</label>
                            <input type="text" id="obras_nom" name="obras_nom" size="40" style="font-size:14px; width: 80%;"  disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="residente_nombre_completo">Nombre Residente:</label>
                            <input type="text" id="residente_nombre_completo" name="residente_nombre_completo" size="40" style="font-size:14px;" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inspector_nombre_completo">Nombre Inspector:</label>
                            <input type="text" id="inspector_nombre_completo" name="inspector_nombre_completo" size="40" style="font-size:14px;" disabled>
                        </div>
                    </div>
                    
                  </div>
            </div>
            
          <div id="" class="card-body" >
            <div class="box-typical box-typical-padding">
            <button class="btn btn-info" onclick="goBack()">Volver</button><br><br>
              <table id="detalle_data" name="pre_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 8%;">TIPO</th>
                    <th th class="text-center"  style="width: 10%;">PLACA</th>
                    <th th class="text-center"  style="width: 8%;">ESTADO</th>
                    <th th class="text-center"  style="width: 8%;">MARCA</th>

                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <br> 
              <div class="col-md-12">
              <div class="form-group">
                  <label for="conductor_nombre_completo">Conductor:</label>
                  <input type="text" id="conductor_nombre_completo" name="conductor_nombre_completo" size="40" style="font-size:14px;" disabled>
                </div>
              </div>  
              <br>
              <div class="col-md-12">
              <div class="form-group">
                  <label for="alista_observaciones">Observaciones:</label><br>
                  <textarea id="alista_observaciones" name="alista_observaciones" style="font-size:14px; resize: none; width: 100%;" rows="10"  autocapitalize="sentences" spellcheck="true" disabled></textarea>
                </div>
              </div>

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
<script type="text/javascript" src="Detalle.js"></script>
</body>
</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>