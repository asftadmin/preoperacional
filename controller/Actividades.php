<?php
    /*CONTOLADOR TIPO DE VEHICULO*/
    require_once ("../config/conexion.php");
    require_once("../models/Actividades.php");

    $actividades = new Actividades();

    switch ($_GET["op"]){
        /* SELECT TIPO DE VEHICULOS  */
        case 'comboActividad':
                   $datos = $actividades->get_actividad();
                   if (is_array($datos)==true and count($datos)>0) {
                       $html = "<option disabled selected>--Selecciona una Actividad--</option>";
                       foreach ($datos as $row) {
                           $html.="<option value='".$row['act_id']."'>".$row['act_nombre'] ."</option>";
                       }
                       echo $html;
                   }
            break; 
        /* GUARDAR Y EDITAR */
        case 'guardaryeditaractividad': 
                $act_id= $_POST["act_id"];
                if(empty($act_id) && $actividades ->actividadExiste($_POST["act_nombre"])){
                    echo json_encode(array("status" => "error", "message" => "La actividad ya se encuentra registrado \n Por Favor,  Verifica los campos"));
                }else{
                    if(empty($act_id)){
                        $actividades->insert_actividad($_POST["act_nombre"],$_POST["act_tarifa"],$_POST["act_tipo"],$_POST["act_unidades"]);
                    }else{
                        $actividades ->update_actividad($_POST["act_id"],$_POST["act_nombre"],$_POST["act_tarifa"],$_POST["act_tipo"],$_POST["act_unidades"] );
                    }
                    echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
                }
            break;
        /* LISTADO */
        case 'listarActividad':
            $datos=$actividades->get_actividad();
            $data = Array();
            foreach ($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["act_id"];
                $sub_array[] = $row["act_nombre"];
                $sub_array[] = $row["act_tarifa"];
                $sub_array[] = strtoupper( $row["act_unidades"]);
                $sub_array[] = $row["tipo_nombre"];
                

                $sub_array [] = '<div class="button-container text-center" >
                                    <button type="button" onClick="editar('.$row["act_id"].');" id="'.$row["act_id"].'" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                    <button type="button" onClick="eliminar('.$row["act_id"].');" id="'.$row["act_id"].'" class="btn btn-danger btn-icon" >
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
        
        /* ELIMINAR */
        case 'eliminarActividad':
                $actividades->delete_actividad($_POST["act_id"]);
            break;
        
        /* MOSTRAR VEHICULOS AL EDITAR */
        case 'mostrarActividad':
            $datos=$actividades->get_actividad_id($_POST["act_id"]);
                if(is_array($datos)==true && count($datos)>0){
                    foreach($datos as $row){
                        $output["act_id"] = $row['act_id'];
                        $output["act_nombre"] = $row['act_nombre'];
                        $output["act_tarifa"] = $row['act_tarifa'];
                        $output["act_unidades"] = $row['act_unidades'];
                        $output["act_tipo"] = $row['act_tipo'];
                    }
                    echo json_encode($output);
                }
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