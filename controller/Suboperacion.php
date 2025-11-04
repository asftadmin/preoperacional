<?php
    /* CONTROLADOR SUBOPERACIONES */
    require_once("../config/conexion.php");
    require_once("../models/Suboperacion.php");

    $suboperacion = new Suboperacion();

    switch($_GET["op"]){
        /* GUARDAR Y EDITAR */
        case  "guardaryeditar":
            $suboper_id=$_POST["suboper_id"];
                if(empty($suboper_id)){
                    $suboperacion->insert_suboper($_POST["suboper_nombre"],$_POST["suboper_oper"],$_POST["suboper_vehi"]); 
                }else{
                    $suboperacion->update_suboper($_POST["suboper_id"], $_POST["suboper_nombre"], $_POST["suboper_estado"], $_POST["suboper_oper"],$_POST["suboper_vehi"]);                
                }
            break;
        /* LISTAR */
        case "listar":
            $datos = $suboperacion->get_suboper();
            $data= Array();
                foreach ($datos as $row){
                    $sub_array = array();
                    $sub_array[] = $row["suboper_id"];
                    $sub_array[] = $row["oper_nombre"];
                    $sub_array[] = $row["suboper_nombre"];
                    
                    $sub_array[] = $row["vehiculo_tipo_nombre"];
                    $sub_array[] = $row["suboper_estado"];
                    $sub_array [] = '<div class="button-container  text-center" >
                                        <button type="button" onClick="editar('.$row["suboper_id"].');" id="'.$row["suboper_id"].'" class="btn btn-warning btn-icon " >
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar('.$row["suboper_id"].');" id="'.$row["suboper_id"].'" class="btn btn-danger btn-icon" >
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
        case "eliminar":
                $suboperacion-> delete_suboper($_POST["suboper_id"]);
            break;

        /* MOSTRAR DATOS AL EDITAR */
        case "mostrar":
            $datos = $suboperacion ->get_suboper_id($_POST["suboper_id"]);
            if (is_array($datos) == true and count($datos)>0) {
                foreach ($datos as $row){
                    $output["suboper_id"] = $row["suboper_id"];
                    $output["suboper_nombre"] = $row["suboper_nombre"];
                    $output["suboper_oper"] = $row["suboper_oper"];
                    $output["suboper_vehi"] = $row["suboper_vehi"];
                    $output["suboper_estado"] = $row["suboper_estado"];
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