<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar Contraseña</title>
 <link rel="stylesheet" href="../../public/css/ResetPass.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../public/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../../public/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="../../public/plugins/css/adminlte.min.css">
  <link rel="stylesheet" href="../../public/plugins/css/lib/bootstrap-sweetalert/sweetalert.css">
  <link rel="stylesheet" href="../../public/plugins/css/separate/vendor/sweet-alert-animations.min.css">
  <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
  <style>
     .login-box {
    width: 60%; 
    max-width: 440px; 
   
  }
  </style>
</head>
<body class="hold-transition login-page bg-info "  >
  <div class="login-box" >
    <div class="card card-outline card-info">
      <div class="card-header text-center bg-dark" >
        <h3 ><b>Recuperar Contraseña</b></h3>
      </div>
      <div class="card-body">
        <p class="login-box-msg"></p>
        <form action="#" method="post">
          <label style="color:black;">Correo Electrónico:</label>
          <div class="input-group mb-3">
            <input type="email" class="form-control"  maxlength="50" minlength="10" id="user_email" name="user_email" placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-4"></div>
            <div class="col-12 col-md-4 text-center">
              <button type="button" id="btnenviar" class="btn btn-info btn-block btn-lg">Enviar</button>
            </div>
            <div class="col-12 col-md-4"></div>
          </div>
        </form>
        <p class="mt-3 mb-1">
          <a href="../../index.php">Regresar</a>
        </p>
      </div>
    </div>
  </div>

<!-- jQuery -->
<script src="../../public/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../public/plugins/js/adminlte.min.js"></script>
<script src="../../public/plugins/sweetalert2/sweetalert.min.js"></script>

<script type="text/javascript" src="ResetPass.js"></script>
</body>
</html>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>