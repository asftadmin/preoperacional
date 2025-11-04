<?php
    /*CONTOLADOR TIPO DE VEHICULO*/
    require_once ("../config/conexion.php");
    require_once("../models/Obras.php");

    $obras = new Obras();

    switch ($_GET["op"]){
        /* SELECT OBRAS  */
        case 'comboObras':
                   $datos = $obras->get_obras_activas();
                   if (is_array($datos)==true and count($datos)>0) {
                       $html = "<option value='' disabled selected>--Selecciona una Obra--</option>";
                       foreach ($datos as $row) {
                           $html.="<option value='".$row['obras_id']."'>".$row['obras_codigo'] ."-".$row['obras_nom'] ."</option>";
                       }
                       echo $html;
                   }
            break; 
        /* GUARDAR Y EDITAR */
        case 'guardaryeditarobras': 
                $obras_id= $_POST["obras_id"];
                if(empty($obras_id) && $obras ->obraExiste($_POST["obras_codigo"])){
                    echo json_encode(array("status" => "error", "message" => "La Obra ya se encuentra registrado \n Por Favor,  Verifica los campos"));
                }else{
                    if(empty($obras_id)){
                        $obras->insert_obras($_POST["obras_codigo"],$_POST["obras_nom"],$_POST["obra_estado"],$_POST["tipo_obra"]);
                    }else{
                        $obras ->update_obras($_POST["obras_id"],$_POST["obras_codigo"],$_POST["obras_nom"],$_POST["obra_estado"],$_POST["tipo_obra"]);
                    }
                    echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
                }
            break;
        /* LISTADO */
        case 'listarobras':
            $datos=$obras->get_obras();
            $data = Array();
            foreach ($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["obras_id"];
                $sub_array[] = $row["obras_codigo"];
                $sub_array[] = $row["obras_nom"];
                if ($row["obra_estado"] === "Activa") {
                    $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ACTIVA</span></div>';
                } else if ($row["obra_estado"] === "No activa") {
                    $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">NO ACTIVA</span></div>';
                } 
                $sub_array[] = $row["tipo_obra"];
                $sub_array [] = '<div class="button-container text-center" >
                                    <button type="button" onClick="editar('.$row["obras_id"].');" id="'.$row["obras_id"].'" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                    <button type="button" onClick="eliminar('.$row["obras_id"].');" id="'.$row["obras_id"].'" class="btn btn-danger btn-icon" >
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
        case 'eliminarobras':
                $obras->delete_obras($_POST["obras_id"]);
            break;
        
        /* MOSTRAR OBRAS AL EDITAR */
        case 'mostrarobras':
            $datos=$obras->get_obras_id($_POST["obras_id"]);
                if(is_array($datos)==true && count($datos)>0){
                    foreach($datos as $row){
                        $output["obras_id"] = $row['obras_id'];
                        $output["obras_codigo"] = $row['obras_codigo'];
                        $output["obras_nom"] = $row['obras_nom'];
                        $output["obra_estado"] = $row['obra_estado'];
                        $output["tipo_obra"] = $row['tipo_obra'];                     
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