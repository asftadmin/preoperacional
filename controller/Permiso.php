<?php
/* CONTROLADOR ROL */
require_once("../config/conexion.php");
require_once("../models/Permiso.php");

$permiso = new Permiso();

switch ($_GET["op"]) {

        /* LISTAR X LOS ROLES */
    case 'listar':
        $datoslista = $permiso->listar_permisos();
        $data = array();
        foreach ($datoslista as $row) {
            $sub_array = array();
            $sub_array[] = $row["permiso_id"];
            $sub_array[] = $row["menu_nom"];
            $sub_array[] = $row["rol_cargo"];
            $sub_array[] = '<div class="button-container text-center"  >
                                <button type="button" onClick="editar(' . $row["permiso_id"] . ');" id="' . $row["permiso_id"] . '" class="btn btn-warning btn-icon " >
                                    <div><i class="fa fa-edit"></i></div>
                                </button>
                                <button type="button" onClick="eliminar(' . $row["permiso_id"] . ');" id="' . $row["permiso_id"] . '" class="btn btn-danger btn-icon" >
                                    <div><i class="fa fa-trash"></i></div>
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

    case  "guardaryeditar":
        $permiso_id = $_POST["permiso_id"];
        if (empty($permiso_id) && $permiso->permisoExiste($_POST["permiso_menu"],$_POST["permiso_rol"])) {
            echo json_encode(array("status" => "error", "message" => "El Permiso ya existe \n Por Favor,  Verifica los campos"));
        } else {
            if (empty($permiso_id)) {
                $permiso->insert_permiso($_POST["permiso_menu"], $_POST["permiso_rol"], $_POST["permiso"]);
            } else {
                $permiso->update_permiso($_POST["permis_id"], $_POST["permiso_menu"], $_POST["permiso_rol"], $_POST["permiso"]);
            }
            echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
        }
        break;

    case "eliminar":
        $permiso->delete_permiso($_POST["permiso_id"]);
        break;

    case "mostrar":
        $datos = $permiso->get_permiso_id($_POST["permiso_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["permiso_id"] = $row["permiso_id"];
                $output["permiso_menu"] = $row["permiso_menu"];
                $output["permiso_rol"] = $row["permiso_rol"];
                $output["permiso"] = $row["permiso"];
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
2025 */
?>