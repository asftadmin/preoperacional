<?php

    /* CONTROLADOR VEHICULO */
    require_once ("../config/conexion.php");
    require_once("../models/Fallas.php");

    $falla = new Falla();
    switch ($_GET["op"]){

        /* SELECT FALLAS */
        case 'comboFalla':
            $datos = $falla->get_falla();
            if (is_array($datos) == true and count($datos)>0) {
                $html = "<option disabled selected>--Selecciona una falla--</option>";
                foreach ($datos as $row) {
                    $html.="<option value='".$row['id_fallas']."'>".$row['fallas_nombre'] ."</option>";
                }
                echo $html;
            }
            break;

        /* GUARDAR Y EDITAR */
        case 'guardaryeditarfalla':
            $id_fallas= $_POST["id_fallas"];
            if(empty($id_fallas) && $falla ->fallaExiste($_POST["fallas_nombre"])){
                echo json_encode(array("status" => "error", "message" => "La falla ya se encuentra registrado \n Por Favor,  Verifica los campos"));
            }else{
                if(empty($id_fallas)){
                    $falla->insert_falla($_POST["fallas_nombre"],$_POST["fallasid_oper"]);
                }else{
                    $falla ->update_fallas($_POST["id_fallas"],$_POST["fallas_nombre"],$_POST["fallasid_oper"] );
                }
                echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
            }
            break;

        /* LISTAR FALLAS */
        case 'listarFalla':
            $datos = $falla->get_falla();
            $data = Array();
            foreach ($datos as $row){
                $sub_array= array();
                $sub_array[] = $row ["id_fallas"];
                $sub_array[] = $row ["fallas_nombre"];
                $sub_array[] = $row["oper_nombre"];

                $sub_array [] = '<div class="button-container text-center"  >
                                    <button type="button" onClick="editar('.$row["id_fallas"].');" id="'.$row["id_fallas"].'" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                    <button type="button" onClick="eliminar('.$row["id_fallas"].');" id="'.$row["id_fallas"].'" class="btn btn-danger btn-icon" >
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

        /* ELIMINAR  */
        case 'eliminarFalla':
            $falla->delete_falla($_POST["id_fallas"]);
            break;

        /* MOSTRAR FALLA AL EDITAR */
        case 'mostrarFallas':
            $datos=$falla->get_falla_id($_POST["id_fallas"]);
            if(is_array($datos)==true && count($datos)>0){
                foreach($datos as $row){
                    $output["id_fallas"] = $row['id_fallas'];
                    $output["fallas_nombre"] = $row['fallas_nombre'];
                    $output["fallasid_oper"] = $row['fallasid_oper'];
                }
                echo json_encode($output);
            }
            break;
    }
?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>