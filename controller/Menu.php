<?php
/* CONTROLADOR ROL */
require_once("../config/conexion.php");
require_once("../models/Menu.php");

$menu = new Menu();

switch ($_GET["op"]) {

    case 'comboVistas':
        $datos = $menu->listar_menu();
        /*Preguntamos prinmero que hayan datos*/
        if (is_array($datos)==true and count($datos)>0) {
            $html = "<option disabled selected required>--Selecciona una Vista--</option>";
            foreach ($datos as $row) {
                $html.="<option value='".$row['menu_id']."'>".$row['menu_id'] . " - ". $row['menu_nom'] ."</option>";
            }
            echo $html;
        }
        break;

        /* LISTAR X LOS ROLES */
    case 'listar':
        $datoslista = $menu->listar_menu();
        $data = array();
        foreach ($datoslista as $row) {
            $sub_array = array();
            $sub_array[] = $row["menu_id"];
            $sub_array[] = $row["menu_nom"];
            $sub_array[] = $row["menu_ruta"];
            $sub_array[] = $row["menu_icono"];
            $sub_array[] = $row["menu_identi"];
            $sub_array[] = $row["menu_grupo"];
            $sub_array[] = '<div class="button-container text-center"  >
                                    <button type="button" onClick="editar(' . $row["menu_id"] . ');" id="' . $row["menu_id"] . '" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                    <button type="button" onClick="eliminar(' . $row["menu_id"] . ');" id="' . $row["menu_id"] . '" class="btn btn-danger btn-icon" >
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
        $menu_id=$_POST["menu_id"];
        if (empty($menu_id) && $menu->menuExiste($_POST["menu_nom"])) {
            echo json_encode(array("status" => "error", "message" => "La vista con ese nombre ya existe \n Por Favor,  Verifica los campos"));
        } else {
            if (empty($menu_id)) {
                $menu->insert_menu($_POST["menu_nom"], $_POST["menu_ruta"], $_POST["menu_estado"], $_POST["menu_icono"], $_POST["menu_identi"], $_POST["menu_grupo"]);
            } else {
                $menu->update_menu($_POST["menu_id"], $_POST["menu_nom"], $_POST["menu_ruta"], $_POST["menu_estado"], $_POST["menu_icono"], $_POST["menu_identi"], $_POST["menu_grupo"]);
            }
            echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
        }
        break;

    case "eliminar":
        $menu->delete_menu($_POST["menu_id"]);
        break;

    case "mostrar":
        $datos = $menu->get_menu_id($_POST["menu_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["menu_id"] = $row["menu_id"];
                $output["menu_nom"] = $row["menu_nom"];
                $output["menu_ruta"] = $row["menu_ruta"];
                $output["menu_estado"] = $row["menu_estado"];
                $output["menu_icono"] = $row["menu_icono"];
                $output["menu_identi"] = $row["menu_identi"];
                $output["menu_grupo"] = $row["menu_grupo"];

            }
            echo json_encode($output);
        }
        break;

        /* LISTAR X LOS ROLES */
    case 'listarxrol':
        $datoslista = $menu->listar_menu_xrol($_POST["rol_id"]);
        $data = array();
        foreach ($datoslista as $row) {
            $sub_array = array();
            $sub_array[] = $row["menu_nom"];
            if ($row["permiso"] == 'Si') {
                $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="deshabilitar(' . $row["permiso_id"] . ');" id="' . $row["permiso_id"] . '" class="btn btn-success btn-icon" >
                                                <div><i class="fa fa-check">&nbsp;&nbsp;</i>' . $row["permiso"] . '</div>
                                            </button>
                                        </div>';
            } else {
                $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="habilitar(' . $row["permiso_id"] . ');" id="' . $row["permiso_id"] . '" class="btn btn-danger btn-icon" >
                                                <div><i class="fa fa-times">&nbsp;&nbsp;</i>' . $row["permiso"] . '</div>
                                            </button>
                                        </div>';
            };

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

        /* ELIMINAR */
    case 'habilitar':
        $menu->update_habilitar($_POST["permiso_id"]);
        break;

        /* ELIMINAR */
    case 'deshabilitar':
        $menu->update_deshabilitar($_POST["permiso_id"]);
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