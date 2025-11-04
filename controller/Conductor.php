<?php 

    /* CONTROLADOR CONDUCTOR */
    require_once ("../config/conexion.php");
    require_once ("../models/Conductor.php");

    $conductor = new Conductor();
    switch($_GET['op']){

        case 'guardaryeditarConductor':
            $cond_id = $_POST["cond_id"];
            if (empty($cond_id)) {
                if ($conductor->condExiste($_POST["conductor_usuario"])) {
                    echo json_encode(array("status" => "error", "message" => "El conductor y/o vehiculo  se encuentra asignado."));
                } else {
                    $conductor->insert_driver($_POST["conductor_usuario"], $_POST["cond_expedicion_licencia"],$_POST["cond_categoria_licencia"],$_POST["cond_vencimiento_licencia"],$_POST["rolcond"]);
                    echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
                }
            } else {
                $result = $conductor->update_driver($_POST["cond_id"], $_POST["conductor_usuario"], $_POST["cond_expedicion_licencia"],$_POST["cond_vencimiento_licencia"],$_POST["cond_categoria_licencia"],$_POST["rolcond"]);
                echo json_encode($result);
            }
            break;

        case 'listarConductor':

            $datos = $conductor -> get_driver_all();
            $data = Array();

            foreach ($datos as $row){
                $sub_array = array();
                $sub_array[]  = $row["cedula"];
                $sub_array[]  = $row["user_nombre_com"];
                $sub_array[]  = $row["cond_expedicion_licencia"];
                $sub_array[]  = $row["cond_vencimiento_licencia"];
                $sub_array[]  = $row["cond_categoria_licencia"];
                $sub_array[]  = $row["rolcond_nombre"];

                $sub_array [] = '<div class="button-container text-center" >
                                    <button type="button" onClick="editar('.$row["cond_id"].');" id="'.$row["cond_id"].'" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>

                                    <button type="button" onClick="eliminar('.$row["cond_id"].');" id="'.$row["cond_id"].'" class="btn btn-danger btn-icon" >
                                        <div><i class="fa fa-trash"></i></div>
                                    </button>
                                </div>';
                $data[] = $sub_array;
            }
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" =>count($data),
                "iTotalDisplayRecords" =>count($data),
                "aaData" => $data);
                echo json_encode($results);
            break;

        case 'eliminarConductor':
                $conductor->delete_driver($_POST["cond_id"]);
            break;

        case 'mostrarConductor':
            $datos=$conductor->get_driver_id($_POST["cond_id"]);
            if(is_array($datos)==true && count($datos)>0){
                foreach($datos as $row){
                    $output["cond_id"] = $row['cond_id'];
                    $output["cond_expedicion_licencia"] = $row['cond_expedicion_licencia'];
                    $output["cond_vencimiento_licencia"] = $row['cond_vencimiento_licencia'];
                    $output["cond_categoria_licencia"] = $row['cond_categoria_licencia'];
                    $output["conductor_usuario"] = $row['conductor_usuario'];
                    $output["rolcond"] = $row['rolcond'];

                }
                echo json_encode($output);
            }
            break;

        case 'editarLicencia':
                $conductor->update_driver_licencia($_POST["cond_id"],$_POST["cond_expedicion_licencia"], $_POST["cond_vencimiento_licencia"], $_POST["cond_categoria_licencia"]);
                echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
            break;

        case 'listarLicencia':
                $datos = $conductor -> get_driver_all();
                $data = Array();
                foreach ($datos as $row){
                    $sub_array = array();
                    $sub_array[]  = $row["cedula"];
                    $sub_array[]  = $row["user_nombre_com"];
                    $sub_array[]  = $row["cond_expedicion_licencia"];
                    $sub_array[]  = $row["cond_vencimiento_licencia"];
                    $sub_array[]  = $row["cond_categoria_licencia"];
                    $sub_array[]  = $row["vehi_placa"];
                    
                    $sub_array [] = '<div class="button-container text-center" >   
                                        <button type="button" onClick="editarLicencia('.$row["cond_id"].');" id="'.$row["cond_id"].'" class="btn btn-info btn-icon " >
                                            <div><i class="	far fa-id-card"></i></div>
                                        </button>
                                    </div>';
                   
    
                    $data[] = $sub_array;
                }
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" =>count($data),
                    "iTotalDisplayRecords" =>count($data),
                    "aaData" => $data);
                    echo json_encode($results);
                break;
        
        case 'mostrarLicencia':
            $datos=$conductor->get_driver_id($_POST["cond_id"]);
            if(is_array($datos)==true && count($datos)>0){
                foreach($datos as $row){
                    $output["cond_id"] = $row['cond_id'];
                    $output["cond_expedicion_licencia"] = $row['cond_expedicion_licencia'];
                    $output["cond_vencimiento_licencia"] = $row['cond_vencimiento_licencia'];
                    $output["cond_categoria_licencia"] = $row['cond_categoria_licencia'];
                    $output["conductor_usuario"] = $row['conductor_usuario'];
                }
                echo json_encode($output);
            }
            break;

        
    }
    


?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>