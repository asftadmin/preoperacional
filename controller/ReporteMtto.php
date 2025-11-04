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
    case 'listaRerporte':
        $datos = $reporte->listaReporte();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["repo_fech"]), 'd/m/Y');
            $sub_array[] = $row["repo_numb"];
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = '$ ' . number_format($row["deta_total_mtto"], 0, ',', '.');
            if ($row["estado"] === "APROBADO") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">APROBADO</span></div>';
            } else if ($row["estado"] === "ANULADO") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ANULADO</span></div>';
            }
            $sub_array[] = '<div class="button-container text-center" >
                    <button type="button" onClick="anular(' . $row["repo_codi"] . ');" id="' . $row["repo_codi"] . '" class="btn btn-danger btn-icon" >
                        <div><i class="fas fa-minus-square"></i></div>
                    </button>
                     <button type="button" onClick="ver(\'' . $row["repo_numb"] . '\');" id="' . $row["repo_numb"] . '" class="btn btn-info btn-icon">
                        <div><i class="fa fa-eye"></i></div>
                    </button>
                    <button type="button" onClick="pdf(\'' . $row["repo_numb"] . '\');" id="' . $row["repo_numb"] . '" class="btn btn-danger btn-icon">
                        <div><i class="fa fa-file-pdf"></i></div>
                    </button></div>';
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
    case 'guardar':

        // Obtener los datos de la solicitud
        $inputJSON = file_get_contents('php://input');
        error_log("Contenido de php://input: " . $inputJSON);

        // Decodificar JSON
        $datos = json_decode($inputJSON, true);

        // Verificar si el JSON es válido
        if ($datos === null) {
            error_log("Error al decodificar JSON: " . json_last_error_msg());
            echo json_encode(['status' => 'error', 'message' => 'Error al decodificar JSON']);
            exit;
        }


        //Extraer los datos

        $reporteJS = $datos['reporte'] ?? [];
        $detalle = $datos['detalle'] ?? [];
        $proveedores = $datos['proveedores'] ?? [];
        $insumos = $datos['insumos'] ?? [];

        //Validar si los datos no este vacios
        if (empty($reporteJS) || empty($detalle)) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
            return;
        }

        // Registrar los datos extraídos en el log del servidor
        error_log("Reporte: " . print_r($reporte, true));
        error_log("Detalle: " . print_r($detalle, true));
        error_log("Proveedores: " . print_r($proveedores, true));
        error_log("Insumos: " . print_r($insumos, true));

        // Llamar al modelo para guardar los datos
        $resultado = $reporte->guardarReporte($reporteJS, $detalle, $proveedores, $insumos);


        // Devolver la respuesta al frontend
        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => 'Reporte guardado correctamente', 'repo_numb' => $reporteJS['numb_reporte']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el reporte']);
        }

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
