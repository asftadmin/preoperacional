<?php
    /* CONTROLADOR MATERIA PRMA */
    require_once("../config/conexion.php");
    require_once("../models/MateriaPrima.php");

    $materiaprima = new MateriaPrima();

    switch($_GET["op"]){

        case  "guardaryeditar":
            $mtprm_id=$_POST["mtprm_id"];
            if(empty($mtprm_id) && $materiaprima->mtprmExiste($_POST["mtprm_id"] )){
                echo json_encode(array("status" => "error", "message" => "La materia prima ya se encuentra registrado \n Por Favor,  Verifica los campos"));
            }else{
                if(empty($mtprm_id)){
                    $materiaprima->insert_mtprm($_POST["mtprm_nombre"],$_POST["mtprm_linea"]); 
                }else{
                    $materiaprima->update_mtprm($_POST["mtprm_id"], $_POST["mtprm_nombre"], $_POST["mtprm_linea"]);                
                }
                echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
            }
            break;

        case "listar":
                $datos = $materiaprima->get_mtprm();
                $data= Array();
                foreach ($datos as $row){
                    $sub_array = array();
                    $sub_array[] = $row["mtprm_id"];
                    $sub_array[] = $row["mtprm_nombre"];
                    if ($row["mtprm_linea"] === "CONCRETO") {
                        $sub_array[] = '<div style="text-align: center;"><span style="background-color:#929392; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">CONCRETO</span></div>';
                    } else if ($row["mtprm_linea"] === "ASFALTO") {
                        $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ASFALTO</span></div>';
                    } else if ($row["mtprm_linea"] === "OBRA") {
                        $sub_array[] = '<div style="text-align: center;"><span style="background-color: #e98b07; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">OBRA</span></div>';
                    }
                    $sub_array [] = '<div class="button-container  text-center" >
                                        <button type="button" onClick="editar('.$row["mtprm_id"].');" id="'.$row["mtprm_id"].'" class="btn btn-warning btn-icon " >
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar('.$row["mtprm_id"].');" id="'.$row["mtprm_id"].'" class="btn btn-danger btn-icon" >
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
            $materiaprima -> delete_mtprm($_POST["mtprm_id"]);
            break;

        case "mostrar":
            $datos = $materiaprima ->get_mtprm_id($_POST["mtprm_id"]);
            if (is_array($datos) == true and count($datos)>0) {
                foreach ($datos as $row){
                    $output["mtprm_id"] = $row["mtprm_id"];
                    $output["mtprm_nombre"] = $row["mtprm_nombre"];
                    $output["mtprm_linea"] = $row["mtprm_linea"];
                }
                echo json_encode($output);
            }
            break;

        case 'comboMateriaPrima':
            $datos = $materiaprima->get_mtprm_combo();
            /*Preguntamos prinmero que hayan datos*/
            if (is_array($datos)==true and count($datos)>0) {
                $html = "<option disabled selected required>--Selecciona una Materia Prima--</option>";
                foreach ($datos as $row) {
                    $html.="<option value='".$row['mtprm_id']."'>".$row['mtprm_id'] . " - ". $row['mtprm_nombre'] ."</option>";
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