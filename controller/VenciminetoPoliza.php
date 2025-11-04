<?php
    require_once ("../config/conexion.php");
    require_once ("../models/VencimientoPoliza.php");

    $venciminetopoliza = new VenciminetoPoliza();

    switch ($_GET["op"]){

        case "vencimiento_poliza":
            
            $venciminetopoliza -> vencimineto_poliza($_POST["vehi_id"]);
            break;
    }

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA 
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>