<?php

require_once("../config/conexion.php");
require_once("../models/VerDespachos.php");

$verdespachos = new VerDespachos();

switch ($_GET["op"]) {

    case "graficoRendimiento":

        $datos = $verdespachos->grafico_despachos();
        echo json_encode($datos);
        break;
    case "graficoDetalle":

        $datos = $verdespachos->detalle_rendimiento($_POST['desp_vehi']);
        echo json_encode($datos);
        break;

    case "detalle_tabla":
        $datos = $verdespachos->detalle_rendimiento($_POST['desp_vehi']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["desp_fech"]), 'd/m/Y');
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["desp_galones"];
            $sub_array[]  = $row["operador"];

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;
}

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>