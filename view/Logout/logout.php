
<?php
    require_once ("../../config/conexion.php");
    session_destroy();
    header("Location:" .Conectar::ruta(). "index.php");
    exit();
?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE:JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>