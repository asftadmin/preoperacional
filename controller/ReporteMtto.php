<?php
/*CONTROLADOR REPORTE MTTO*/

require_once("../config/conexion.php");
require_once("../models/ReporteMtto.php");

$reporte = new ReporteMtto();


switch ($_GET["op"]) {
    case 'comboTipoMtto':
        $datos = $reporte->get_tipo_mtto();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el tipo de mtto--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['codigo_tipo_mantenimiento'] . "'>" . $row['tipo_mantenimiento'] . "</option>";
            }
            echo $html;
        }
        break;
    case 'numeroReporte':
        $ultimo = $reporte->obternerUltimoConsecutivo();

        // Verifica si obtuvo un resultado correcto
        if ($ultimo && isset($ultimo['repo_numb'])) {
            // Extraer los últimos 6 dígitos del repo_numb
            preg_match('/(\d{6})$/', $ultimo['repo_numb'], $matches);

            if (isset($matches[1])) {
                $ultimoNumero = intval($matches[1]); // Convertir a número
                $nuevoNumero = str_pad($ultimoNumero + 1, 6, "0", STR_PAD_LEFT);
            } else {
                $nuevoNumero = "000001"; // Si no encuentra el número, inicia en 000001
            }
        } else {
            $nuevoNumero = "000001";
        }

        // Generar el nuevo código con el año actual
        $nuevoReporte = "MTTO-" . date("Y") . "-" . $nuevoNumero;

        echo $nuevoReporte;


        break;

    case "anulado":
        $datos = $reporte->anulado($_POST["repo_codi"]);
        break;
    case "guardarReporte":
        if (isset($_POST['ticket_id'], $_POST['num_reporte'], $_POST['horas_prog'], $_POST['fecha_asignacion'], $_POST['obra'], $_POST['mantenimiento'], $_POST['vehiculo'], $_POST['conductor'], $_POST['diagnostico_inicial'])) {

            $numeroRpte =  $_POST['num_reporte'];
            $hora =  $_POST['horas_prog'];
            $fechaAsig =  $_POST['fecha_asignacion'];
            $obra =  $_POST['obra'];
            $mantenimiento =  $_POST['mantenimiento'];
            $vehiculo =  $_POST['vehiculo'];
            $conductor =  $_POST['conductor'];
            $diagnostico_inic =  $_POST['diagnostico_inicial'];
            $ticket =  $_POST['ticket_id'];

            $resultado = $reporte->insert_reporte(
                $numeroRpte,
                $hora,
                $fechaAsig,
                $obra,
                $mantenimiento,
                $vehiculo,
                $conductor,
                $diagnostico_inic,
                $ticket
            );
            if ($resultado) {
                echo json_encode(['status' => 'success', 'message' => 'Reporte creado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Hubo un error al crear el reporte.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos.']);
        }
        break;
    
}