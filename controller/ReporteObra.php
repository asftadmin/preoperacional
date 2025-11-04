<?php
/* CONTROLADOR ROL */
require_once("../config/conexion.php");
require_once("../models/ReporteObra.php");

$reporteobra = new ReporteObra();

switch ($_GET["op"]) {

    case "guardar":
        if (isset($_POST["ro_fecha"]) && is_array($_POST["ro_fecha"])) {
            for ($i = 0; $i < count($_POST["ro_fecha"]); $i++) {
                $fecha = $_POST["ro_fecha"][$i];
                $inspector = $_POST["ro_id_inspector"][$i];
                $obra = $_POST["ro_id_obra"][$i];
                $operador = $_POST["ro_id_operador"][$i];
                $hr_inicio = $_POST["ro_hr_inicio"][$i];
                $actividad = $_POST["ro_actv"][$i];

                $reporteobra->rpte_obra_add($fecha, $inspector, $obra, $operador, $hr_inicio, $actividad);
            }
            echo json_encode(array("status" => "success", "message" => "Se envio correctamente"));
        } else {
        }
        break;


    /* LISTADO DE REPORTES DE OBRA OR INSPECTOR */
    case 'listar':
        $datos = $reporteobra->get_cerrar_ro($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["ro_fecha"]), 'd/m/Y');
            $sub_array[] = $row["operador"];
            $sub_array[] = $row["ro_hr_inicio"];
            // Campo editable para la hora final
            $sub_array[] = '<input type="time" id="hora_final_' . $row["ro_id"] . '" class="form-control" value="' . $row["ro_hr_final"] . '">';
            // Botón para guardar cambios
            $sub_array[] = '<div class="button-container text-center">
                                    <button type="button" class="btn btn-success btn-icon btn-update" data-id="' . $row["ro_id"] . '">
                                        <i class="fa fa-save"></i>
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


    /* ACTUALIZAR HR FINAL  */
    case 'update':
        $reporteobra->update_ro($_POST["ro_hr_final"], $_POST["ro_id"]);

        // Enviar una respuesta JSON al frontend
        echo json_encode([
            "status" => "success",
            "message" => "Hora final actualizada correctamente"
        ]);
        exit;
        /* LISTADO DE REPORTES DE OBRA OR INSPECTOR */
    case 'consultar':
        $datos = $reporteobra->get_ro();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["ro_fecha"]), 'd/m/Y');
            $sub_array[] = $row["inspector"];
            $sub_array[] = $row["operador"];
            $hora_inicio = new DateTime($row["ro_hr_inicio"]);
            $sub_array[] = $hora_inicio->format('H:i');
            $sub_array[] = $row["ro_actv"];
            $hora_final = new DateTime($row["ro_hr_final"]);
            $sub_array[] = $hora_final->format('H:i');
            $horas_trabajadas = new DateTime($row["horas_trabajadas"]);
            $sub_array[] = $horas_trabajadas->format('H:i');
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

    case "filtro_ro":
        $datos = $reporteobra->filtro_ro($_POST['ro_id_inspector'], $_POST['ro_id_operador'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["ro_fecha"]), 'd/m/Y');
            $sub_array[] = $row["inspector"];
            $sub_array[] = $row["operador"];
            $hora_inicio = new DateTime($row["ro_hr_inicio"]);
            $sub_array[] = $hora_inicio->format('H:i');
            $sub_array[] = $row["ro_actv"];
            $hora_final = new DateTime($row["ro_hr_final"]);
            $sub_array[] = $hora_final->format('H:i');
            $horas_trabajadas = new DateTime($row["horas_trabajadas"]);
            $sub_array[] = $horas_trabajadas->format('H:i');

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
}


?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>