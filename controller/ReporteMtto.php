<?php
/*CONTROLADOR REPORTE MTTO*/

require_once("../config/conexion.php");
require_once("../models/ReporteMtto.php");
require_once("curl.php");

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

    case 'detalleReporte':

        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        $reporteID = $_POST['id'];
        $detalle = $reporte->get_reporte_detalle($reporteID);

        if ($detalle === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al consultar la base de datos'
            ]);
            exit;
        }

        if (empty($detalle)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron detalles para el reporte: ' . $reporteID
            ]);
            exit;
        }

        $informe  = $detalle["reporte"];
        $equipo   = $detalle["equipo"];
        $ot       = $detalle["ot"];
        $solicitud    = $detalle["solicitud"];
        $items    = $detalle["repuestos"];

        // ================================
        // ARMAR HTML IGUAL A TU EJEMPLO
        // ================================

        $html = '';

        $estado = intval($informe["repo_mtto_estado"]);

        if ($estado === 1) {
            $badgeEstado = '<span class="badge bg-success">REPORTE ABIERTO</span>';
        } elseif ($estado === 2) {
            $badgeEstado = '<span class="badge bg-danger">REPORTE CERRADO</span>';
        } else {
            $badgeEstado = '<span class="badge bg-secondary">ESTADO DESCONOCIDO</span>';
        }

        // ENCABEZADO
        $html .= '<div class="mailbox-read-info border-bottom pb-2 mb-3">';

        $html .= '<div class="d-flex justify-content-between align-items-center">';
        $html .= '    <h3 class="mb-0"><b>Número de Reporte: ' . htmlspecialchars($informe["repo_mtto_num_reporte"]) . '</b></h3>';
        $html .= '    <div>' . $badgeEstado . '</div>';

        $html .= '</div>';
        $html .= '<h6 class="mb-1 mt-2">Equipo: ' . htmlspecialchars($equipo["vehi_marca"] . " " . $equipo["vehi_placa"]) .
            '<span class="mailbox-read-time float-right">' .
            date('d/m/Y H:i', strtotime($informe["repo_mtto_fecha_creacion"])) .
            '</span></h6>';

        $html .= '<p class="mb-0 mt-2">Código del equipo: ' . htmlspecialchars($equipo["vehi_codigo"]) . '</p>';
        $html .= '</div>';

        $html .= '<input type="hidden" id="id_reporte" value="' . $informe["repo_mtto_id"] . '">';
        $html .= '<input type="hidden" id="id_vehiculo" value="' . $equipo["vehi_costo"] . '">';

        // ESTADO FINAL

        $estadoTexto = [
            1 => "Operativo",
            2 => "No operativo",
            3 => "Operativo con pendientes"
        ];
        $informe["estado_texto"] = $estadoTexto[$informe["repo_mtto_estado"]] ?? "Sin definir";
        $html .= '<div class="mailbox-read-message mb-0">';
        $html .= '<h5><b>Estado y/o diagnostico inicial:</b></h5>';
        $html .= '<p>' . nl2br(htmlspecialchars($solicitud["desc_soli"])) . '</p>';
        $html .= '<h5><b>Descripcion del mantenimiento:</b></h5>';
        $html .= '<p>' . nl2br(htmlspecialchars($ot["desc_atcv_otm"])) . '</p>';
        $html .= '<h5><b>Estado Final:</b></h5>';
        //$html .= '<p>' . nl2br(htmlspecialchars($reporte["estado_texto"])) . '</p>';
        $html .= '</div>';


        $items = $reporte->get_repuestos_por_reporte($reporteID);

        /*         echo json_encode($items);
        exit; */



        // ===========================================
        // FUNCIÓN PARA LIMPIAR VALORES VACÍOS
        // ===========================================

        function na($v) {
            return ($v === null || $v === "" || $v === "0" || $v == 0)
                ? "N/A"
                : htmlspecialchars($v);
        }

        // REPUESTOS E INSUMOS
        $html .= '<div class="mailbox-read-message"><hr>';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
        $html .= '    <h4 class="mb-0"><b>Repuestos e Insumos</b></h4>';
        $html .= '    <button type="button" onclick="importarRepuestos()" class="btn btn-dark btn-md" title="Importar desde SIESA">
                    <i class="fas fa-file-import"></i> Importar desde SIESA 
                </button>';
        $html .= '</div>';

        $html .= '<table class="table table-striped table-bordered table-sm" id="tablaRepuestos">';
        $html .= '<thead class="thead-light">';
        $html .= '<tr>
                <th>Nombre</th>
                <th>Referencia</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serial</th>
                <th>Cant</th>
                <th>Costo</th>
                <th>OC</th>
                <th class="text-center">Acciones</th>
              </tr>';
        $html .= '</thead><tbody>';


        $total = 0;

        // ===========================================
        // SI NO HAY ITEMS → MOSTRAR MENSAJE AMIGABLE
        // ===========================================
        if (empty($items)) {

            $html .= '
        <tr>
            <td colspan="8" class="text-center text-muted">
                No hay repuestos registrados para este reporte.<br>
                <small>Use el botón "<b>Importar desde SIESA</b>" para agregar insumos.</small>
            </td>
        </tr>
    ';
        } else {

            // ===========================================
            // RENDER DE LOS ITEMS
            // ===========================================
            foreach ($items as $it) {

                $idItem = isset($it["repo_rpts_id"]) ? $it["repo_rpts_id"] : 0;

                // evitar errores si falta algún índice                
                $descripcion = na($it["rpts_refr"] ?? "N/A");
                $referencia  = na($it["repo_item"] ?? "N/A");
                $cant        = na($it["rpts_cant"] ?? "N/A");
                $costo       = floatval($it["rpts_vlr_neto"] ?? 0);
                $documento   = na($it["rpts_docu"] ?? "N/A");

                $total += $costo;

                $html .= "<tr>
                        <td>$descripcion</td>
                        <td>$referencia</td>
                        <td>N/A</td>         <!-- Marca no existe -->
                        <td>N/A</td>         <!-- Modelo no existe -->
                        <td>N/A</td>         <!-- Serial no existe -->
                        <td>$cant</td>
                        <td>$" . number_format($costo, 0, ',', '.') . "</td>
                        <td>$documento</td>
                        <td class='text-center'>
                            <button class='btn btn-danger btn-sm' 
                                    onclick=\"eliminarRepuesto('$idItem')\">
                                    <i class=\"fas fa-trash\"></i>
                            </button>
                        </td>
                        </tr>";
            }
        }

        $html .= '</tbody></table>';
        $html .= '</div>'; // cierre mailbox-read-message

        $html .= '<h4 class="text-right"><b>Total Repuestos:</b> $' . number_format($total, 0, ',', '.') . '</h4>';

        // ENTREGA DE TRABAJO
        $html .= '<hr>';
        $html .= '<h4><b>Entrega del Trabajo</b></h4>';

        $html .= '<p><b>Horas programadas:</b> ' . $informe["repo_mtto_horas_programadas"] . '</p>';
        $html .= '<p><b>Horas ejecutadas:</b></p>';

        // FIN
        echo json_encode(['status' => 'success', 'html' => $html]);

        break;


    // ============================================================
    // 4. IMPORTAR DESDE API SIESA (asfaltart_ordenes_compra)
    // ============================================================
    case "importar_siesa":

        $vehiculo = $_POST["vehiculo"];
        $fechas   = $_POST["fechas"];

        if (!$vehiculo || !$fechas) {
            echo json_encode(["status" => "error", "message" => "Faltan parámetros"]);
            exit;
        }

        // ---------------------------------------
        // Preparar rango de fechas
        // ---------------------------------------
        $rango = explode(" / ", $fechas);
        $f_ini = $rango[0];
        $f_fin = $rango[1];

        // ---------------------------------------
        // Construcción URL API SIESA
        // ---------------------------------------
        $parametros = urlencode("fechainicio=$f_ini|fechafin=$f_fin|ccosto=$vehiculo");

        $url = "idCompania=6026";
        $url .= "&descripcion=asfaltart_ordenes_compra";
        $url .= "&parametros=" . $parametros;
        $url .= "&paginacion=" . urlencode("numPag=1|tamPag=200");

        // ---------------------------------------
        // Llamado vía CURL al ERP SIESA
        // ---------------------------------------

        $response = CurlController::requestEstandarV2($url, "GET");

        if (!isset($response->codigo) || $response->codigo != 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Error consultando SIESA",
                "debug"   => $response
            ]);
            exit;
        }

        if (!isset($response->detalle->Datos) || !is_array($response->detalle->Datos)) {
            echo json_encode(["status" => "error", "message" => "Sin datos para mostrar"]);
            exit;
        }

        $items = [];

        foreach ($response->detalle->Datos as $row) {
            // Filtrar solo documentos OCC
            if (trim($row->f420_id_tipo_docto ?? "") !== "OCC") {
                continue;
            }
            $items[] = [
                "fecha"       => $row->f421_fecha ?? 0,
                "documento"   => trim(($row->f420_id_tipo_docto ?? "") . "-" . ($row->f420_consec_docto ?? "")),
                "descripcion" => $row->f120_descripcion ?? "",
                "cantidad"    => $row->f421_cant_pedida ?? 0,
                "valor"       => $row->f421_vlr_neto ?? 0,
                "notas"       => $row->f420_notas ?? 0,
                "proveedor"   => $row->f202_descripcion_sucursal ?? 0,
                "referencia"   => $row->f120_referencia ?? 0,
                "id"          => uniqid()
            ];
        }

        echo json_encode([
            "status" => "success",
            "items"  => $items
        ]);

        break;

    // ============================================================
    // 3. GUARDAR LOS ÍTEMS IMPORTADOS EN LA BASE DE DATOS
    // ============================================================
    case "guardar_insumos":

        $reporteID = $_POST["reporte"];
        $items     = json_decode($_POST["items"], true);

        if (!$reporteID || empty($items)) {
            echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
            exit;
        }

        $save = $reporte->insertar_insumos($reporteID, $items);

        if ($save) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudieron guardar los insumos", "reload" => true]);
        }

        break;

    case 'delete_item':

        if (!isset($_POST["id"])) {
            echo json_encode(["status" => "error", "message" => "ID no recibido"]);
            exit;
        }

        $id = $_POST["id"];
        $result = $reporte->delete_item($id);

        if ($result) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo eliminar", "reload" => true]);
        }

        break;
}
