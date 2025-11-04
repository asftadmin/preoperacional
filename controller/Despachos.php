<?php

require_once("../config/conexion.php");
require_once("../models/Despachos.php");

$despachos = new Despachos();

switch ($_GET["op"]) {

    case "listar":
        $datos = $despachos->RpteDespachos($_POST['desp_vehi'], $_POST['desp_cond'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["desp_fech_crea"]), 'd/m/Y');
            $sub_array[]  = $row["desp_galones_autorizados"];
            $sub_array[]  = $row["desp_recibo"];
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["desp_fech"]), 'd/m/Y');
            $desp_hora = new DateTime($row["desp_hora"]);
            $sub_array[] = $desp_hora->format('H:i:s');
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["desp_galones"];
            $sub_array[]  = $row["desp_km_hr"];
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["despachador"];
            $sub_array[]  = $row["desp_observaciones"];

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
            $desp_id = $_POST["desp_id"];
            $recibo = $fecha . $desp_id;
        
            if (empty($desp_id) && $despachos->despExiste($_POST["desp_vehi"])) {
                echo json_encode(array("status" => "error", "message" => "El Despacho ya se encuentra registrado \n Por Favor,  Verifica los campos"));
            } else {
                if (empty($desp_id)) {
                    // Guardar un nuevo despacho
                    $desp_id = $despachos->guardar_despacho($_POST["desp_vehi"], $_POST["desp_galones_autorizados"], $_POST["desp_user"],$_POST["desp_obra"]);
                } else {
                    // Actualizar un despacho existente
                    $despachos->update_despacho($_POST["desp_id"], $_POST["desp_galones"], $recibo, $_POST["desp_km_hr"], $_POST["desp_cond"], $_POST["desp_observaciones"], $_POST["desp_despachador"]);
                }
        
                // Incluir el desp_id en la respuesta JSON
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Datos guardados correctamente",
                    "desp_id" => $desp_id // Retornar el ID del despacho
                ));
            }
            break;
        

    case "consultar":
        $datos = $despachos->RpteDespachos($_POST['desp_vehi'], $_POST['desp_cond'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["desp_fech_crea"]), 'd/m/Y');
            $sub_array[]  = $row["desp_recibo"];
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["desp_galones_autorizados"];
            $sub_array[] = date_format(new DateTime($row["desp_fech"]), 'd/m/Y');
            $desp_hora = new DateTime($row["desp_hora"]);
            $sub_array[] = $desp_hora->format('H:i:s');
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["desp_galones"];
            $sub_array[]  = $row["desp_km_hr"];
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["desp_observaciones"];

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

        /* UPDATE - ESTADO TRASLADO */
    case "cambioestado":
        $datos = $despachos->CambioEstado($_POST["desp_id"]);
        break;

    /* MOSTRAR DESPACHOS AL EDITAR */
    case 'mostrarDespachos':
        $datos=$despachos->get_despacho_id($_POST["desp_id"]);
            if(is_array($datos)==true && count($datos)>0){
                foreach($datos as $row){
                    $output["desp_id"] = $row['desp_id'];
                    $output["vehi_placa"] = $row['vehi_placa'];
                    $output["desp_cond"] = $row['desp_cond'];
                    $output["desp_obra"] = $row['desp_obra'];
                    $output["desp_galones"] = $row['desp_galones'];
                    $output["desp_recibo"] = $row['desp_recibo'];
                    $output["desp_fech"] = $row['desp_fech'];
                    $output["desp_hora"] = $row['desp_hora'];
                    $output["desp_km_hr"] = $row['desp_km_hr'];
                    $output["desp_observaciones"] = $row['desp_observaciones'];

                }
                echo json_encode($output);
            }
        break;

        /* LISTAR DESPACHOS X AUTORIZADO*/
    case 'listarDespacho':
        $datos = $despachos->get_despachos($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["desp_id"];
            $sub_array[] = date_format(new DateTime($row["desp_fech_crea"]), 'd/m/Y');
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = $row["desp_galones_autorizados"];
            if ($row["desp_estado"] === "Activo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ACTIVO</span></div>';
            } else if ($row["desp_estado"] === "Anulado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #7c8788; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ANULADO</span></div>';
            }
            $sub_array[] = '<div class="button-container text-center" >
                                <button type="button" onClick="cambio_estado(' . $row["desp_id"] . ');" id="' . $row["desp_id"] . '" class="btn btn-danger btn-icon " >
                                    <div><i class="fas fa-minus-circle"></i></div>
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
    /* LISTAR DESPACHOS ACTIVOS*/
    case 'ListarACPM':
        $datos = $despachos->get_despachos_activos();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["desp_id"];
            $sub_array[] = date_format(new DateTime($row["desp_fech_crea"]), 'd/m/Y');
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = $row["desp_galones_autorizados"];
            
            $sub_array[] = '<div class="button-container text-center" >
                                <button type="button" onClick="Despacho(' . $row["desp_id"] . ');" id="' . $row["desp_id"] . '" class="btn btn-info btn-icon " >
                                    <div><i class="fas fa-gas-pump"></i></div>
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
}

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>