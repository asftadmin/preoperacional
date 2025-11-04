<?php

/* CONTROLADOR VEHICULO */
require_once("../config/conexion.php");
require_once("../models/Vehiculo.php");

$vehiculo = new Vehiculo();
switch ($_GET["op"]) {

        /* SELECT VEHICULOS */
    case 'comboVehiculo':
        $datos = $vehiculo->get_car();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Vehiculo--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['vehi_id'] . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;

        /* SELECT VEHICULOS DATA */
    case 'comboVehiculoPreop':
        $datos = $vehiculo->get_car_preo();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Vehiculo--</option>";
            foreach ($datos as $row) {
                $vehi_id = $row['vehi_id'];
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-vehi-id='" . $vehi_id . "' data-tipo-id='" . $tipo_id . "'' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
    break;
    /* SELECT VEHICULOS DESPACHOS EXTERNOS */
    case 'comboEquiposDesp':
        $datos = $vehiculo->get_equipo_desp();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Vehiculo--</option>";
            foreach ($datos as $row) {
                $vehi_id = $row['vehi_id'];
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-vehi-id='" . $vehi_id . "' data-tipo-id='" . $tipo_id . "'' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
    break;

    /* SELECT VEHICULOS DATA */
    case 'comboEquiposLab':
        $datos = $vehiculo->get_equipos_lab();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Equipo--</option>";
            foreach ($datos as $row) {
                $vehi_id = $row['vehi_id'];
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-vehi-id='" . $vehi_id . "' data-tipo-id='" . $tipo_id . "'' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
    break;

    case 'comboVehiculoVolqueta':
        $datos = $vehiculo->get_car_volqueta();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Vehiculo--</option>";
            foreach ($datos as $row) {
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-tipo-id='" . $tipo_id . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;

    case 'comboVehiculoMaquinaria':
        $datos = $vehiculo->get_car_maquinaria();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Equipo--</option>";
            foreach ($datos as $row) {
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-tipo-id='" . $tipo_id . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;
    case 'comboHerramientaMenor':
        $datos = $vehiculo->listar_herramienta_menor();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-tipo-id='" . $tipo_id . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;
    case 'comboFresadoras':
        $datos = $vehiculo->get_car_fresadora();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Equipo--</option>";
            foreach ($datos as $row) {
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-tipo-id='" . $tipo_id . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;

    case 'comboMaquinariaAsignada':
        $datos = $vehiculo->get_car_maquinaria_asignada($_POST['alista_codigo']);
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Equipo--</option>";
            foreach ($datos as $row) {
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-tipo-id='" . $tipo_id . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;

    case 'comboVehiculoMixer':
        $datos = $vehiculo->get_car_mixer();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona una Mixer--</option>";
            foreach ($datos as $row) {
                $tipo_id = $row['tipo_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_id'] . "' data-tipo-id='" . $tipo_id . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . " - " . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;

    case 'comboVehiculoPreopUser':
        $user_id = $_POST['user_id'];
        echo '<pre>';
        print_r($user_id);
        echo '</pre>';
        $datos = $vehiculo->get_car_user($user_id);
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona un Vehiculo--</option>";
            foreach ($datos as $row) {
                $vehi_id = $row['vehi_id'];
                $vehi_placa = $row['vehi_placa'];
                $html .= "<option value='" . $row['vehi_placa'] . "' data-vehi-placa='" . $vehi_placa . "'>" . $row['vehi_placa'] . "</option>";
            }
            echo $html;
        }
        break;

        /* GUARDAR Y EDITAR */
    case 'guardaryeditarvehiculo':
        $vehi_id = $_POST["vehi_id"];
        if (empty($vehi_id) && $vehiculo->carExiste($_POST["vehi_placa"])) {
            echo json_encode(array("status" => "error", "message" => "El Vehiculo ya se encuentra registrado \n Por Favor,  Verifica los campos"));
        } else {
            if (empty($vehi_id)) {
                $vehiculo->insert_car($_POST["vehi_marca"], $_POST["vehi_placa"], $_POST["vehi_modelo"], $_POST["vehi_soat_vence"], $_POST["vehi_tecnicomecanica"], $_POST["vehi_tarjeta_propiedad"], $_POST["vehi_poliza"], $_POST["vehi_poliza_vence"], $_POST["vehi_tipo"]);
            } else {
                $vehiculo->update_car($_POST["vehi_id"], $_POST["vehi_marca"], $_POST["vehi_placa"], $_POST["vehi_modelo"], $_POST["vehi_soat_vence"], $_POST["vehi_tecnicomecanica"], $_POST["vehi_tarjeta_propiedad"], $_POST["vehi_poliza"], $_POST["vehi_poliza_vence"], $_POST["vehi_tipo"], $_POST["vehi_costo"]);
            }
            echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
        }
        break;

        /* LISTAR VEHICULOS */
    case 'listarVehiculo':
        $datos = $vehiculo->get_car();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_id"];
            $sub_array[] = $row["vehi_marca"];
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = $row["vehi_modelo"];
            $sub_array[] = $row["vehi_soat_vence"];
            $sub_array[] = $row["vehi_tecnicomecanica"];
            $sub_array[] = $row["vehi_tarjeta_propiedad"];
            $sub_array[] = $row["vehi_poliza"];
            $sub_array[] = $row["vehi_poliza_vence"];
            $sub_array[] = $row["tipo_nombre"];
            $sub_array[] = $row["vehi_estado"];

            $sub_array[] = '<div class="button-container text-center"  >
                                    <button type="button" onClick="editar(' . $row["vehi_id"] . ');" id="' . $row["vehi_id"] . '" class="btn btn-warning btn-icon " >
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                    <button type="button" onClick="eliminar(' . $row["vehi_id"] . ');" id="' . $row["vehi_id"] . '" class="btn btn-danger btn-icon" >
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

        /* ELIMINAR  */
    case 'eliminarVehiculo':
        $vehiculo->delete_car($_POST["vehi_id"]);
        break;

        /* MOSTRAR VEHICULO AL EDITAR */
    case 'mostrarVehiculo':
        $datos = $vehiculo->get_car_id($_POST["vehi_id"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["vehi_id"] = $row['vehi_id'];
                $output["vehi_marca"] = $row['vehi_marca'];
                $output["vehi_placa"] = $row['vehi_placa'];
                $output["vehi_modelo"] = $row['vehi_modelo'];
                $output["vehi_soat_vence"] = $row['vehi_soat_vence'];
                $output["vehi_tecnicomecanica"] = $row['vehi_tecnicomecanica'];
                $output["vehi_tarjeta_propiedad"] = $row['vehi_tarjeta_propiedad'];
                $output["vehi_poliza"] = $row['vehi_poliza'];

                $output["vehi_poliza_vence"] = $row['vehi_poliza_vence'];
                $output["vehi_tipo"] = $row['vehi_tipo'];
				$output["vehi_costo"] = $row['vehi_costo'];
            }
            echo json_encode($output);
        }
        break;

    case 'listarMaquinaria':
        $datos = $vehiculo->get_car_maquinaria();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["vehi_marca"];

            if ($row["vehi_estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["vehi_estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["vehi_estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            } else if ($row["vehi_estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["vehi_estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #00b0cc; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            }
            $sub_array[] = $row["tipo_nombre"];
            $sub_array[] = '<div class="button-container text-center" >
                                                <button type="button" onClick="ver(\'' . $row["vehi_id"] . '\');" id="' . $row["vehi_id"] . '" class="btn btn-info btn-icon">
                                                <div><i class="fa fa-eye"></i></div>
                                                </button>
                                            </div>';
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case 'listarHerramientaMenor':
        $datos = $vehiculo->get_herramienta_menor();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["vehi_marca"];

            if ($row["vehi_estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["vehi_estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["vehi_estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            } else if ($row["vehi_estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["vehi_estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #00b0cc; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            }
            $sub_array[] = $row["tipo_nombre"];
            $sub_array[] = '<div class="button-container text-center" >
                                                    <button type="button" onClick="ver(\'' . $row["vehi_id"] . '\');" id="' . $row["vehi_id"] . '" class="btn btn-info btn-icon">
                                                    <div><i class="fa fa-eye"></i></div>
                                                    </button>
                                                </div>';
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;


        /* MOSTRAR DETALLE MAQUINARIA */
    case "detalleMaquinaria":
        $datos = $vehiculo->detalle($_POST["vehi_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');
            $sub_array[]  = $row["alista_id"];

            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Traslado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Traslado</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["residente_nombre_completo"];
            $sub_array[]  = $row["alista_observaciones"];
            $sub_array[]  = $row["inspector_nombre_completo"];
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[]  = $row["observaciones_inspe"];
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;

        /* MOSTRAR DATOS DE LA MAQUINARIA*/
    case "datos":
        $datos = $vehiculo->detalle($_POST["vehi_id"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["tipo_nombre"] = $row['tipo_nombre'];
                $output["vehi_placa"] = $row['vehi_placa'];
                $output["vehi_estado"] = $row['vehi_estado'];
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