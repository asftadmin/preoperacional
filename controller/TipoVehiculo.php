<?php
    /*CONTOLADOR TIPO DE VEHICULO*/
    require_once ("../config/conexion.php");
    require_once("../models/TipoVehiculo.php");

    $tipovehiculo = new TipoVehiculo();

    switch ($_GET["op"]){
        /* SELECT TIPO DE VEHICULOS  */
        case 'combotipovehi':
                   $datos = $tipovehiculo->get_typecar();
                   if (is_array($datos)==true and count($datos)>0) {
                       $html = "<option disabled selected>--Selecciona un Tipo Vehiculo--</option>";
                       foreach ($datos as $row) {
                           $html.="<option value='".$row['tipo_id']."'>".$row['tipo_nombre']."</option>";
                       }
                       echo $html;
                   }
            break; 
        /* GUARDAR Y EDITAR */
        case 'guardaryeditartipovehi': 
                $tipo_id= $_POST["tipo_id"];
                if(empty($tipo_id) && $tipovehiculo ->typecarExiste($_POST["tipo_nombre"])){
                    echo json_encode(array("status" => "error", "message" => "El Tipo de Vehiculo ya se encuentra registrado \n Por Favor,  Verifica los campos"));
                }else{
                    if(empty($tipo_id)){
                        $tipovehiculo->insert_typecar($_POST["tipo_nombre"]);
                    }else{
                        $tipovehiculo ->update_typecar($_POST["tipo_id"],$_POST["tipo_nombre"] );
                    }
                    echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
                }
            break;
        /* LISTADO */
        case 'listarTipoVehiculo':
            $datos=$tipovehiculo->get_typecar();
            $data = Array();
            foreach ($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["tipo_id"];
                $sub_array[] = $row["tipo_nombre"];

                $sub_array [] = '<div class="button-container text-center" >
                                    <button type="button" onClick="editar('.$row["tipo_id"].');" id="'.$row["tipo_id"].'" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                    <button type="button" onClick="eliminar('.$row["tipo_id"].');" id="'.$row["tipo_id"].'" class="btn btn-danger btn-icon" >
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
        case 'eliminartipovehi':
                $tipovehiculo->delete_typecar($_POST["tipo_id"]);
            break;
        
        /* MOSTRAR VEHICULOS AL EDITAR */
        case 'mostrartipovehi':
            $datos=$tipovehiculo->get_typecar_id($_POST["tipo_id"]);
                if(is_array($datos)==true && count($datos)>0){
                    foreach($datos as $row){
                        $output["tipo_id"] = $row['tipo_id'];
                        $output["tipo_nombre"] = $row['tipo_nombre'];
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