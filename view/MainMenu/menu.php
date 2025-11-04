<head>
    <link rel="stylesheet" href="../../public/css/inicio.css">
</head>
<?php
require_once("../../models/Menu.php");
$menu = new Menu();
$datos = $menu->listar_menu_xrol($_SESSION["user_rol_usuario"]);

$mostrarItems = false;
$mostrarReporteObra = false;
$mostrarPreoperacionales = false;
$mostrarReportesDiarios = false;
$mostrarLab = false;
$mostrarUsu = false;
$mostrarVehi = false;
$mostrarAlista = false;
$mostrarDespachos = false;
$hayReportes = false;
$hayConsultasAlista = false; // Variable adicional
$hayConsultasLab = false;
$hayRteObra = false;
$mostrarGestionMtto = false;

foreach ($datos as $row) {
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "items") {
        $mostrarItems = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RteObra") {
        $mostrarReporteObra = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Preoperacional") {
        $mostrarPreoperacionales = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Repdia") {
        $mostrarReportesDiarios = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "usuarios") {
        $mostrarUsu = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "vehi") {
        $mostrarVehi = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Alista") {
        $mostrarAlista = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Despachos") {
        $mostrarDespachos = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Lab") {
        $mostrarLab = true;
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RepConRD") {
        $hayReportes = true; // Hay reportes habilitados
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCAlista") {
        $hayConsultasAlista = true; // Hay consultas para alistamientos   
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCLab") {
        $hayConsultasLab = true; // Hay consultas para alistamientos   
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCPreo") {
        $hayReportes = true; // Hay reportes habilitados
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RObra") {
        $hayRteObra = true; // Hay reportes habilitados
    }
    if ($row["permiso"] == "Si" && $row["menu_grupo"] == "mtto") {
        $mostrarGestionMtto = true; 
    }
}
?>
<aside class="main-sidebar elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-5 pb-5 mb-3 d-flex">
            <div class="image">
                <img src="../../public/img/logo-horizontal.svg" alt="ASFALTAR S.A.S">
            </div>
        </div>
        <!-- INICIO -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php
                foreach ($datos as $row) {
                    if ($row["permiso"] == "Si" && $row["menu_estado"] == 2) {
                ?>
                        <li class="nav-item">
                            <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                <p><?php echo $row["menu_nom"]; ?></p>
                            </a>
                        </li>
                <?php
                    }
                }
                ?>
                <!-- ITEMS -->
                <?php if ($mostrarItems): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Items <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            foreach ($datos as $row) {
                                if ($row["permiso"] == "Si" && $row["menu_grupo"] == "items") {
                            ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- USUARIOS -->
                <?php if ($mostrarUsu): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-alt"></i>
                            <p>Usuarios <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            foreach ($datos as $row) {
                                if ($row["permiso"] == "Si" && $row["menu_grupo"] == "usuarios") {
                            ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- VEHICULOS -->
                <?php if ($mostrarVehi): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-car-side"></i>
                            <p>Vehiculos <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            foreach ($datos as $row) {
                                if ($row["permiso"] == "Si" && $row["menu_grupo"] == "vehi") {
                            ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- MANTENEDORES -->
                <?php
                foreach ($datos as $row) {
                    if ($row["permiso"] == "Si" && $row["menu_grupo"] == null) {
                ?>
                        <li class="nav-item">
                            <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                <p><?php echo $row["menu_nom"]; ?></p>
                            </a>
                        </li>
                <?php
                    }
                }
                ?>
                <!-- REORTE DE OBRA -->
                <?php if ($mostrarReporteObra || $hayRteObra): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-hard-hat"></i>
                            <p>Obras <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($datos as $row): ?>
                                <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RteObra"): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($hayRteObra): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-area"></i>
                                        <p>Consultas <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php foreach ($datos as $row): ?>
                                            <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RObra"): ?>
                                                <li class="nav-item">
                                                    <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                                        <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                                        <p><?php echo $row["menu_nom"]; ?></p>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                 <!-- GESTION MANTENIMIENTO -->
                 <?php if ($mostrarGestionMtto): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Gestion Mtto <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            foreach ($datos as $row) {
                                if ($row["permiso"] == "Si" && $row["menu_grupo"] == "mtto") {
                            ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- PREOPERACIONALES -->
                <?php if ($mostrarPreoperacionales || $hayReportes): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-file-alt nav-icon"></i>
                            <p>Preoperacionales <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($datos as $row): ?>
                                <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Preoperacional"): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($hayReportes): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-area"></i>
                                        <p>Consultas <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php foreach ($datos as $row): ?>
                                            <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCPreo"): ?>
                                                <li class="nav-item">
                                                    <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                                        <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                                        <p><?php echo $row["menu_nom"]; ?></p>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- REPORTES DIARIOS -->
                <?php if ($mostrarReportesDiarios || $hayReportes): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-pen"></i>
                            <p>Reportes Diarios <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($datos as $row): ?>
                                <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Repdia"): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($hayReportes): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Consultas <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php foreach ($datos as $row): ?>
                                            <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RepConRD"): ?>
                                                <li class="nav-item">
                                                    <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                                        <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                                        <p><?php echo $row["menu_nom"]; ?></p>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- ALISTAMIENTOS -->
                <?php if ($mostrarAlista || $hayConsultasAlista): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Alistamientos <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($datos as $row): ?>
                                <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Alista"): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($hayConsultasAlista): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Consultas <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php foreach ($datos as $row): ?>
                                            <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCAlista"): ?>
                                                <li class="nav-item">
                                                    <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                                        <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                                        <p><?php echo $row["menu_nom"]; ?></p>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- DESPACHOS -->
                <?php if ($mostrarDespachos): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-gas-pump"></i>
                            <p>Despachos <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($datos as $row): ?>
                                <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Despachos"): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Consultas <i class="fas fa-angle-left right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php foreach ($datos as $row): ?>
                                        <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCDespachos"): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                                    <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                                    <p><?php echo $row["menu_nom"]; ?></p>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- LABORATORIO -->
                <?php if ($mostrarLab || $hayConsultasLab): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon 	fas fa-radiation"></i>
                            <p>Laboratorio <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($datos as $row): ?>
                                <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "Lab"): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                            <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                            <p><?php echo $row["menu_nom"]; ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($hayConsultasLab): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-area"></i>
                                        <p>Consultas <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php foreach ($datos as $row): ?>
                                            <?php if ($row["permiso"] == "Si" && $row["menu_grupo"] == "RyCLab"): ?>
                                                <li class="nav-item">
                                                    <a href="<?php echo $row["menu_ruta"]; ?>" class="nav-link">
                                                        <i class="<?php echo $row["menu_icono"]; ?>"></i>
                                                        <p><?php echo $row["menu_nom"]; ?></p>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<?php
/* DESARROLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
