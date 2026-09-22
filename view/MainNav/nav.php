<?php

if (empty($_SESSION['csrf_notificaciones'])) {
    $_SESSION['csrf_notificaciones'] = bin2hex(random_bytes(32));
}

?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- ================================================= -->
    <!-- MENÚ LATERAL -->
    <!-- ================================================= -->

    <ul class="navbar-nav">

        <li class="nav-item">

            <a class="nav-link" data-widget="pushmenu" href="#" role="button">

                <i class="fas fa-bars"></i>

            </a>

        </li>

    </ul>


    <!-- ================================================= -->
    <!-- USUARIO -->
    <!-- ================================================= -->

    <ul class="navbar-nav">

        <li class="nav-item">

            <div class="dropdown dropdown-typical">

                <span class="fas fa-user-alt" style="color:#17a2b8"></span>

                <span class="lblcontactonomx">

                    <?php echo $_SESSION['user_nombre']; ?>

                    <?php echo $_SESSION['user_apellidos']; ?>

                </span>

            </div>

        </li>

    </ul>


    <!-- ================================================= -->
    <!-- OPCIONES DERECHA -->
    <!-- ================================================= -->

    <ul class="navbar-nav ml-auto">


        <!-- ================================================= -->
        <!-- NOTIFICACIONES MESA DE SERVICIO -->
        <!-- ================================================= -->

        <li class="nav-item dropdown">

            <a class="nav-link" data-toggle="dropdown" href="#" role="button" id="btnNotificacionesTickets"
                aria-haspopup="true" aria-expanded="false">

                <i class="far fa-bell"></i>

                <span class="badge badge-danger navbar-badge d-none" id="contadorNotificacionesTickets">
                    0
                </span>

            </a>


            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="dropdownNotificacionesTickets">

                <span class="dropdown-item dropdown-header" id="encabezadoNotificacionesTickets">
                    Notificaciones
                </span>


                <div class="dropdown-divider"></div>


                <div id="listaNotificacionesTickets">

                    <span class="dropdown-item text-center text-muted">

                        <i class="fas fa-bell-slash mr-1"></i>

                        Sin notificaciones

                    </span>

                </div>

            </div>

        </li>


        <!-- ================================================= -->
        <!-- VENCIMIENTO PÓLIZAS -->
        <!-- ================================================= -->

        <?php if ($_SESSION['user_rol_usuario'] == 3) { ?>

        <li class="nav-item">

            <a class="nav-link" href="../VenciminetoPoliza/VenPoliza.php" id="btnpoliza">

                <i class="fas fa-envelope"></i>

            </a>

        </li>

        <?php } ?>


        <!-- ================================================= -->
        <!-- MENÚ USUARIO -->
        <!-- ================================================= -->

        <li class="nav-item dropdown user-menu">

            <button class="dropdown-toggle border-0 pl-3 pr-2 pt-1 pb-1" id="dd-user-menu" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <img src="../../public/img/perfil.png" alt="usuario">

            </button>


            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">

                <a class="dropdown-item" href="../MntPerfil/Perfil.php">

                    <span class="fas fa-user"></span>

                    &nbsp;Perfil

                </a>


                <a class="dropdown-item" href="#">

                    <span class="fas fa-question-circle"></span>

                    &nbsp;Ayuda

                </a>


                <div class="dropdown-divider"></div>


                <a class="dropdown-item bg-danger" href="../Logout/logout.php">

                    <span class="fas fa-sign-out-alt"></span>

                    &nbsp;Cerrar Sesion

                </a>

            </div>

        </li>

    </ul>


    <!-- ================================================= -->
    <!-- DATOS DE SESIÓN -->
    <!-- ================================================= -->

    <input type="hidden" id="user_idx" value="<?php echo $_SESSION['user_id']; ?>">

    <input type="hidden" id="rol_idx" name="rol_idx" value="<?php echo $_SESSION['user_rol_usuario']; ?>">

    <input type="hidden" id="tipo_idx" value="<?php echo $_SESSION['tipo_id']; ?>">

    <input type="hidden" id="vehi_plac" value="<?php echo $_SESSION['vehi_placa']; ?>">

    <input type="hidden" id="csrfNotificaciones" value="<?php
echo htmlspecialchars(
    $_SESSION['csrf_notificaciones'],
    ENT_QUOTES,
    'UTF-8'
);
?>">

</nav>


<?php

/*
 * DESARROLLADO POR:
 * ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
 * UNIDADES TECNOLOGICAS DE SANTANDER
 * BUCARAMANGA-SANTANDER
 * 2023
 */

?>