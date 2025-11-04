<?php require_once("../../config/conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
<title>Detalle Reporte Consumibles</title>
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
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="../ConsultarDespachos/CnsltDespachos.php">Consultar Despachos</a></li>
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
          <div class="card" id="container">
            <br>
            <div class="box-typical box-typical-padding">
              <div class="row mt-2 ">
                <div class="col-md-3">
                  <div class="form-group">
                    <div class="form-group d-flex align-items-center">
                    &nbsp;&nbsp;&nbsp;<select class="form-control select2bs4" id="desp_vehi" name="desp_vehi" style="width: 100%;" required>
                      </select>&nbsp;&nbsp;&nbsp;
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group d-flex align-items-center">
                    <select class="form-control select2bs4" id="desp_cond" name="desp_cond" style="width: 100%;" required>
                    </select>&nbsp;&nbsp;&nbsp;
                  </div>
                </div>
              </div>

              <div class="row">&nbsp;&nbsp;&nbsp;
                <div class="col-md-3">
                  <div class="form-group">
                  <label><b>Del Dia</b></label>
                   <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><b> Hasta el Dia</b></label>
                    <input type="date" name="fecha_final" id="fecha_final" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><b>&nbsp;</b></label><br>
                    <button type="button" id="btnbuscar" class="btn btn-info">Buscar</button>
                    <button type="button" id="btnLimpiar" class="btn btn-info">Limpiar</button>
                  </div>
                </div>
              </div>
              <div style="overflow-x: hidden; overflow-y: hidden;">
                <table id="despachos_data" name="despachos_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                  <thead class="bg-info">
                    <tr>
                      <th class="text-center" style="width: 7%;">FECHA CREACION</th>
                      <th class="text-center" style="width: 2%;">GALONES AUTORIZADOS</th>
                      <th class="text-center" style="width: 5%;"># RECIBO</th>
                      <th class="text-center" style="width: 8%;">TIPO</th>
                      <th class="text-center" style="width: 6%;">PLACA</th>
                      <th class="text-center" style="width: 6%;">FECHA</th>
                      <th class="text-center" style="width: 6%;">HORA</th>
                      <th class="text-center" style="width: 10%;">OBRA</th>
                      <th class="text-center" style="width: 2%;">GALONES DESPACHADOS</th>
                      <th class="text-center" style="width: 7%;">KM/HR</th>
                      <th class="text-center" style="width: 15%;">CONDUCTOR</th>
                      <th class="text-center" style="width: 15%;">DESPACHADOR</th>
                      <th class="text-center" style="width: 15%;">OBSERVACIONES</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <br><br><br>
            </div>
          </div>
        </div>
      </div>

            <aside class="control-sidebar control-sidebar-dark">
            </aside>
        </div>
        <?php require_once("../MainFooter/footer.php") ?>
    </div>




    <?php require_once("../MainJS/JS.php") ?>
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="detalle.js"></script>
</body>

</html>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>