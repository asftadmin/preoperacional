<?php

require_once("../config/conexion.php");
require_once("../models/DespachosExternos.php");

$despachosexternos = new DespachosExternos();

switch ($_GET["op"]) {

    case "listar":
        $datos = $despachosexternos->get_ssc();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["ae_codigo"];
            $sub_array[] = date_format(new DateTime($row["ae_fecha_solicitud"]), 'd/m/Y');
            $sub_array[]  = $row["conductor"];
            $sub_array[]  = $row["ae_galones_aut"];
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["ae_eds"];
            if ($row["ae_estado"] === "Activo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:  #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Activo</span></div>';
            } else if ($row["ae_estado"] === "Anulado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(246, 2, 18); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Anulada</span></div>';
            } else if ($row["ae_estado"] === "Distribuido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(133, 133, 126); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Distribuido</span></div>';
            }
            $sub_array[] = '<div class="button-container text-center" >
                            <button type="button" onClick="anulado(' . $row["ae_id"] . ');" id="' . $row["ae_id"] . '" class="btn btn-danger btn-icon " >
                                <div><i class="fas fa-minus-circle"></i></div>
                            </button></div>';

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
        case "listardssCond":
            $datos = $despachosexternos->get_dss_cond($_POST["user_id"]);
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
    
                $sub_array[] = date_format(new DateTime($row["dss_fecha_desp"]), 'd/m/Y');
                $sub_array[]  = $row["dss_galones"];
                $sub_array[]  = $row["vehi_placa"];
                $sub_array[]  = $row["operador"];
                if ($row["dss_ssc"] === "Recibido") {
                    $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(3, 184, 39); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Recibido</span></div>';
                } else if ($row["dss_ssc"] === "Distribuido") {
                    $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(255, 179, 2); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Distribuido</span></div>';
                } 
                
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

        /* GUARDAR Y EDITAR */
    case 'guardaryeditarDespacho':
        $fecha = date('Ymd');
        $ae_cond = $_POST["ae_cond"];
        $ae_obra = $_POST["ae_obra"];
        $codigo = $fecha . $ae_cond . $ae_obra;

        if (empty($ae_id) && $despachosexternos->sscExiste($_POST["ae_cond"])) {
            echo json_encode(array("status" => "error", "message" => "El Despacho ya se encuentra registrado \n Por Favor,  Verifica los campos"));
        } else {
            if (empty($ae_id)) {
                // Guardar un nuevo despacho
                $ae_id = $despachosexternos->guardar_ssc($_POST["ae_cond"], $_POST["ae_eds"], $_POST["ae_obra"], $_POST["ae_galones_aut"], $codigo);
            } else {
                // Actualizar un despacho existente
            }

            // Incluir el desp_id en la respuesta JSON
            echo json_encode(array(
                "status" => "success",
                "message" => "Datos guardados correctamente",
                "ae_id" => $ae_id // Retornar el ID del despacho
            ));
        }
        break;

        /* GUARDAR Y EDITAR  DISTRIBUCION*/
        case 'guardarDistribucion':

            if (empty($dss_id) && $despachosexternos->dssExiste($_POST["dss_ae"], $_POST["dss_galones"])) {
                echo json_encode(array("status" => "error", "message" => "El numero de Galones Disponiles no es suficiente."));
            } else {
                if (empty($dss_id)) {
                    // Guardar un nuevo despacho
                    $dss_id = $despachosexternos->guardar_dss($_POST["dss_operador"], $_POST["dss_galones"], $_POST["dss_vehi"], $_POST["dss_ae"], $_POST["dss_cond"]);
                } else {
                    // Actualizar un despacho existente
                }
        
                // Verificar si los galones disponibles llegan a 0 después de la asignación
                $despachosexternos->actualizarEstadoAE($_POST["dss_ae"]);
        
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Datos guardados correctamente",
                    "dss_id" => $dss_id // Retornar el ID del despacho
                ));
            }
            break;
        


        /* UPDATE - ESTADO TRASLADO */
    case "anulado":
        $datos = $despachosexternos->ssc_anulado($_POST["ae_id"]);
        break;

        /* LISTAR DESPACHOS ACTIVOS X CONDUCTOR*/
    case 'ListarDespActivos':
        $datos = $despachosexternos->get_ssc_activos($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["ae_fecha_solicitud"]), 'd/m/Y');
            $sub_array[] = $row["ae_galones_aut"];
            $sub_array[] = $row["obras_nom"];
            $sub_array[] = $row["ae_eds"];
            if ($row["ae_estado"] === "Activo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:  #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Activo</span></div>';
            } else if ($row["ae_estado"] === "Distribuido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(133, 133, 126); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Distribuido</span></div>';
            } 
            $sub_array[] = '<div class="button-container text-center" >
                                <button type="button" onClick="dss(' . $row["ae_id"] . ');" id="' . $row["ae_id"] . '" class="btn btn-info btn-icon " >
                                    <div><i class="fas fa-oil-can"></i></div>
                                </button>
                            </div>';
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

        /* GRAFICO GALONES DISPONIBLES */
    case 'grafico':

        $datos = $despachosexternos->grafico_galones($_POST['user_id']);
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
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