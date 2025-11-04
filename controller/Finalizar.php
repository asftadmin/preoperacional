<?php

require_once("../config/conexion.php");
require_once("../models/ReportesDiarios.php");

$finalizar = new ReportesDiarios();

switch ($_GET["op"]) {

    case 'finalizar':
        $finalizar->repEstado($_POST["repdia_recib"]);
        echo json_encode(array("status" => "success", "message" => "Se actualizo correctamente"));
        break;

    case "graficoVolqueta":
        $datos = $finalizar->get_repdia_grafico($_POST['repdia_vehi']);
        echo json_encode($datos);
        break;

    case "graficoMixer":
        $repdia_vehi = $_POST['repdia_vehix'];

        $datos = $finalizar->get_repdia_grafico_hrs($repdia_vehi);
        echo json_encode($datos);
        break;

    case "graficoMaquinaria":
        $repdia_vehi = $_POST['repdia_maquinaria'];

        $datos = $finalizar->get_repdia_grafico_hrs($repdia_vehi);
        echo json_encode($datos);
        break;

    case "tablaGraficoVolqueta":
        $repdia_vehi = $_POST['vehi_id_asfalto'];
        $datos = $finalizar->get_repdia_tabla_grafico($repdia_vehi);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[] = $row["kilometraje"];
            $sub_array[] = $row["consumo"];
            $sub_array[] = number_format($row["rendimiento"], 2);

            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="ver(\'' . $row["repdia_placa"] . '\');" id="' . $row["repdia_placa"] . '" class="btn btn-info btn-icon">
                                        <div><i class="fa fa-eye"></i></div>
                                        </button></div>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "detalleGraficoVolqueta":
        $datos = $finalizar->detalle_tabla_grafico($_POST["repdia_placa"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];
            $sub_array[]  = $row["kilometraje"];
            $sub_array[]  = $row["repdia_gaso"];
            $sub_array[]  = $row["repdia_acpm"];
            $sub_array[]  = $row["consumo"];
            $sub_array[]  = number_format($row["rendimiento"], 2);

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

    case "datos":
        $datos = $finalizar->detalle_tabla_grafico($_POST["repdia_placa"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {

                $output["vehi_placa"] = $row['vehi_placa'];
                $output["repdia_fech"] = $row['repdia_fech'];
                $output["vehi_id"] = $row['vehi_id'];
            }
            echo json_encode($output);
        }
        break;

    case "tablaGraficoMixer":
        $repdia_vehi = $_POST['vehi_id_concreto'];
        $datos = $finalizar->get_repdia_tabla_grafico_hrs($repdia_vehi);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[] = $row["kilometraje"];
            $sub_array[] = $row["consumo"];
            $sub_array[] = number_format($row["rendimiento"], 2);

            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="verHRS(\'' . $row["repdia_placa"] . '\');" id="' . $row["repdia_placa"] . '" class="btn btn-info btn-icon">
                                        <div><i class="fa fa-eye"></i></div>
                                        </button></div>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "detalleGraficoHRS":
        $datos = $finalizar->detalle_tabla_grafico_hrs($_POST["repdia_placa"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];
            $sub_array[]  = $row["kilometraje"];
            $sub_array[]  = $row["repdia_gaso"];
            $sub_array[]  = $row["repdia_acpm"];
            $sub_array[]  = $row["consumo"];
            $sub_array[]  = number_format($row["rendimiento"], 2);

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

    case "datosHrs":
        $datos = $finalizar->detalle_tabla_grafico_hrs($_POST["repdia_placa"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {

                $output["vehi_placa"] = $row['vehi_placa'];
                $output["repdia_fech"] = $row['repdia_fech'];
                $output["vehi_id"] = $row['vehi_id'];
            }
            echo json_encode($output);
        }
        break;

    case "tablaGraficoMaquinaria":
        $repdia_vehi = $_POST['vehi_id_maquinaria'];
        $datos = $finalizar->get_repdia_tabla_grafico_hrs($repdia_vehi);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[] = $row["kilometraje"];
            $sub_array[] = $row["consumo"];
            $sub_array[] = number_format($row["rendimiento"], 2);

            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="verHRS(\'' . $row["repdia_placa"] . '\');" id="' . $row["repdia_placa"] . '" class="btn btn-info btn-icon">
                                            <div><i class="fa fa-eye"></i></div>
                                            </button></div>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

        /* ---------------------------------GRAFICO FRESADORA------------------------------------ */

    case "graficoFresadora":
        $repdia_vehi = $_POST['repdia_fresadora'];

        $datos = $finalizar->get_repdia_grafico_fresadora($repdia_vehi);
        echo json_encode($datos);
        break;

    case "datosFresadora":
        $repdia_vehi = $_POST['repdia_fresadora'];

        $datos = $finalizar->get_repdia_grafico_fresadora($repdia_vehi);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {

                $output["vehi_placa"] = $row['vehi_placa'];
                $output["tipo_nombre"] = $row['tipo_nombre'];
            }
            echo json_encode($output);
        }
        break;

    case "tablaGraficaFresadora":
        $repdia_vehi = $_POST['vehi_id_fresadora'];
        $datos = $finalizar->get_repdia_tabla_grafico_frsd($repdia_vehi);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[] = $row["total_volumen"];
            $sub_array[] = $row["total_kilometraje"];
            $sub_array[] = number_format($row["rendimiento"], 2);

            $sub_array[] = '<div class="button-container text-center" >
                                                <button type="button" onClick="verFrsd(\'' . $row["repdia_placa"] . '\');" id="' . $row["repdia_placa"] . '" class="btn btn-info btn-icon">
                                                <div><i class="fa fa-eye"></i></div>
                                                </button></div>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "detalleGraficoFrsd":
        $datos = $finalizar->detalle_tabla_grafica_frsd($_POST["repdia_placa"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];
            $sub_array[]  = $row["total_kilometraje"];
            $sub_array[]  = $row["repdia_volu"];
            $sub_array[]  = $row["repdia_volu"];
            $sub_array[]  = number_format($row["rendimiento"], 2);

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

    case "datosFrsd":
        $datos = $finalizar->detalle_tabla_grafica_frsd($_POST["repdia_placa"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {

                $output["vehi_placa"] = $row['vehi_placa'];
                $output["repdia_fech"] = $row['repdia_fech'];
                $output["tipo_nombre"] = $row['tipo_nombre'];
                $output["repdia_placa"] = $row['repdia_placa'];
            }
            echo json_encode($output);
        }
        break;

    case "graficoFresadora_Puntas":
        $repdia_vehi = $_POST['repdia_fresadora'];

        $datos = $finalizar->get_repdia_grafico_frsd_pnts($repdia_vehi);
        echo json_encode($datos);
        break;
    case "tablaGraficaFresadoraPuntas":
        $repdia_vehi = $_POST['vehi_id_fresadora'];
        $datos = $finalizar->get_repdia_tabla_grafico_frsd_pnts($repdia_vehi);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[] = $row["total_puntas"];
            $sub_array[] = $row["total_volumen"];
            $sub_array[] = number_format($row["rendimiento"], 2);

            
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    

    case "graficoPuntas":
        $repdia_vehi = $_POST['repdia_fresadora'];

        $datos = $finalizar->get_repdia_grafico_frsd_pnts($repdia_vehi);
        echo json_encode($datos);
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