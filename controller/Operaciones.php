<?php
    /* CONTROLADOR OPERACIONES */
    require_once("../config/conexion.php");
    require_once("../models/Operaciones.php");

    $operaciones = new Operaciones();

    switch($_GET["op"]){

        case  "guardaryeditar":
            $oper_id=$_POST["oper_id"];
            if(empty($oper_id) && $operaciones->operExiste($_POST["oper_id"] )){
                echo json_encode(array("status" => "error", "message" => "La operacion ya se encuentra registrado \n Por Favor,  Verifica los campos"));
            }else{
                if(empty($oper_id)){
                    $operaciones->insert_oper($_POST["oper_nombre"]); 
                }else{
                    $operaciones->update_oper($_POST["oper_id"], $_POST["oper_nombre"], $_POST["oper_estado"]);                
                }
                echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
            }
            break;

        case "listar":
                $datos = $operaciones->get_oper();
                $data= Array();
                foreach ($datos as $row){
                    $sub_array = array();
                    $sub_array[] = $row["oper_id"];
                    $sub_array[] = $row["oper_nombre"];
                    $sub_array[] = $row["oper_estado"];
                    $sub_array [] = '<div class="button-container  text-center" >
                                        <button type="button" onClick="editar('.$row["oper_id"].');" id="'.$row["oper_id"].'" class="btn btn-warning btn-icon " >
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar('.$row["oper_id"].');" id="'.$row["oper_id"].'" class="btn btn-danger btn-icon" >
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
        
        case "eliminar":
            $operaciones -> delete_oper($_POST["oper_id"]);
            break;

        case "mostrar":
            $datos = $operaciones ->get_oper_id($_POST["oper_id"]);
            if (is_array($datos) == true and count($datos)>0) {
                foreach ($datos as $row){
                    $output["oper_id"] = $row["oper_id"];
                    $output["oper_nombre"] = $row["oper_nombre"];
                    $output["oper_estado"] = $row["oper_estado"];
                }
                echo json_encode($output);
            }
            break;

        case 'comboOperaciones':
            $datos = $operaciones->get_oper_combo();
            /*Preguntamos prinmero que hayan datos*/
            if (is_array($datos)==true and count($datos)>0) {
                $html = "<option disabled selected required>--Selecciona una Operacion--</option>";
                foreach ($datos as $row) {
                    $html.="<option value='".$row['oper_id']."'>".$row['oper_id'] . " - ". $row['oper_nombre'] ."</option>";
                }
                echo $html;
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