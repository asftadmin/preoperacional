<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {

    ?>

<!DOCTYPE html>
<html lang="es">

<?php require_once "../MainHead/head.php";?>

<title>Inicio</title>
</head>


<body class="hold-transition sidebar-mini">

    <div class="wrapper">

        <!-- Navbar -->
        <?php require_once "../MainNav/nav.php";?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php require_once "../MainMenu/menu.php";?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">PREOPERACIONAL</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Preoperacionales</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-12">
                           
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        </div>


            <!-- Main Footer -->
            <footer class="main-footer">
            <?php require_once "../MainFooter/footer.php"?>
            </footer>
        
        <!-- ./wrapper -->
        </div>

        <?php require_once "../MainJS/JS.php"?>

        
       
</body>

</html>

<?php
} else {
    header("location:" . Conectar::ruta() . "index.php");
}
?>