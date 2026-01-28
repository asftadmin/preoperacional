<?php
/*CONTROLADOR REPORTE MTTO*/

require_once("../config/conexion.php");
require_once("../models/ReporteMtto.php");
require_once("curl.php");

$reporte = new ReporteMtto();


function ftp_mksubdirs_safe($ftp, $path) {
    $parts = explode('/', trim($path, '/'));
    $fullpath = "";

    foreach ($parts as $part) {

        if ($part == "") continue;

        $fullpath .= "/" . $part;

        // Intentar cambiar
        if (@ftp_chdir($ftp, $fullpath)) {
            // Existe → regresar a raíz y seguir
            ftp_chdir($ftp, "/");
            continue;
        }

        // Si no existe → intentar crearlo
        if (!@ftp_mkdir($ftp, $fullpath)) {
            return false; // No se pudo crear
        }
    }

    return true;
}


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

        function na($v, $esReferencia = false) {
            // Si es null o vacío
            if ($v === null || $v === "") {
                return "N/A";
            }

            // Si es string, trim y verificar
            if (is_string($v)) {
                $v = trim($v);
                if ($v === "" || $v === "0" || strtoupper($v) === "N/A" || strtoupper($v) === "NA") {
                    return "N/A";
                }
            }

            // Si es numérico cero
            if (is_numeric($v) && floatval($v) == 0) {
                return "N/A";
            }

            // Si es referencia y no es numérica después de limpiar
            if ($esReferencia && !is_numeric($v)) {
                return "N/A";
            }

            return htmlspecialchars($v);
        }

        // REPUESTOS E INSUMOS
        $html .= '<div class="mailbox-read-message"><hr>';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
        $html .= '    <h4 class="mb-0"><b>Repuestos e Insumos</b></h4>';
        $html .= '<div class="btn-group" role="group">';
        $html .= '    <button type="button" onclick="importarRepuestos()" class="btn btn-dark btn-md" title="Importar desde SIESA">
                    <i class="fas fa-file-import"></i> Importar desde SIESA 
                </button>';
        $html .= ' <button type="button" onclick="agregarFactura()" class="btn btn-primary btn-md ml-2">
            <i class="fas fa-file-invoice"></i> Ingresar Facturas / Reembolsos
          </button>';
        $html .= '</div>';
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
                <th>Factura</th>
                <th class="col-proveedor d-none">Proveedor</th>
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

                $idItem = isset($it["id"]) ? $it["id"] : 0;

                // evitar errores si falta algún índice                
                $descripcion = na(trim($it["descripcion"] ?? ''));
                $referencia  = na(trim($it["referencia"] ?? ''), true);  // true = es referencia
                $cant        = na(trim($it["cantidad"] ?? ''));
                $costo       = floatval($it["valor"] ?? 0);
                $documento   = na(trim($it["documento"] ?? ''));
                $factura     = na(trim($it["factura"] ?? ''));

                if (
                    $descripcion === "N/A" && $referencia === "N/A" &&
                    $documento === "N/A" && $factura === "N/A"
                ) {
                    continue;
                }

                $total += $costo * $cant;

                $html .= "<tr>
                        <td>$descripcion</td>
                        <td>$referencia</td>
                        <td>N/A</td>         <!-- Marca no existe -->
                        <td>N/A</td>         <!-- Modelo no existe -->
                        <td>N/A</td>         <!-- Serial no existe -->
                        <td>$cant</td>
                        <td>$" . number_format($costo, 0, ',', '.') . "</td>
                        <td>$documento</td>
                        <td>$factura</td>         <!-- Factura no existe -->
                        <td class='col-proveedor d-none'>N/A</td>
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

        $html .= '<div id="areaGuardarFacturas" class="text-center mt-3" style="display:none;">
            <button onclick="guardarFacturasEnLote()" class="btn btn-info btn-md">
                <i class="fas fa-save"></i> Guardar Facturas
            </button>
         </div>';


        $html .= '<h4 class="text-right p-2"><b>Total Repuestos:</b> $' . number_format($total, 0, ',', '.') . '</h4>';

        $horasEjec = $informe["repo_mtto_horas_ejec"];
        if ($horasEjec === null || $horasEjec === "") {
            $horasEjec = "0.00";
        }

        // ENTREGA DE TRABAJO
        $html .= '<hr>';
        $html .= '<div class="mb-3 p-2">';

        $html .= '<h4 class="mb-3"><b>Entrega del Trabajo</b></h4>';

        $html .= '<p class="mb-2">
            <b class="mr-2">Horas programadas:</b> 
            ' . $informe["repo_mtto_horas_programadas"] . '
         </p>';

        $html .= '<p class="mb-2 d-flex align-items-center">
            <b class="mr-2">Horas ejecutadas:</b> 
            <span id="txtHorasEjecutadas" class="mr-2">' . $horasEjec . '</span>

            <button class="btn btn-sm btn-dark" onclick="editarHoras()">
                <i class="fas fa-edit"></i> Editar
            </button>
         </p>';

        $html .= '<input type="hidden" id="id_reporte" value="' . $informe["repo_mtto_id"] . '">';

        $html .= '</div>';

        $proveedores = $reporte->get_proveedores_reporte($reporteID);


        // PROVEEDORES
        $html .= '<div class="mailbox-read-message"><hr>';
        $html .= '<h4 class="mb-3"><b>Personal involucrado en mantenimiento</b></h4>';

        $html .= '<table class="table table-striped table-bordered table-sm" id="tablaProveedores">';
        $html .= '<thead class="thead-light">';
        $html .= '<tr>
                <th>Proveedor</th>
                <th>N° Orden de Trabajo</th>
                <th>N° Orden de Compras</th>
                <th>Factura</th>
              </tr>';
        $html .= '</thead><tbody>';

        // ===========================================
        // SI NO HAY PROVEEDORES → MOSTRAR MENSAJE AMIGABLE
        // ===========================================
        if (empty($proveedores)) {

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
            foreach ($proveedores as $pv) {

                //$idItem = isset($it["repo_rpts_id"]) ? $it["repo_rpts_id"] : 0;

                // evitar errores si falta algún índice                
                $nombre = na($pv["rpts_prov"] ?? "N/A");
                $orden_trabajo  = na($pv["num_otm"] ?? "N/A");
                $orden_comp        = na($pv["rpts_docu"] ?? "N/A");
                $factura       = na($pv["rpts_fact"] ?? "N/A");

                $html .= "<tr>
                            <td>$nombre</td>
                            <td>$orden_trabajo</td>
                            <td>$orden_comp  </td>
                            <td>$factura </td>
                        </tr>";
            }
        }

        $html .= '</tbody></table>';

        $html .= '</div>';
        $html .= '<div class="mailbox-read-message"><hr>';
        $html .= '<h4 class="mb-3"><b>Soportes Facturas</b></h4>';
        $html .= '<ul id="listaFacturas" class="list-group mt-3 mb-3"></ul>';
        $html .= '</div>';

        $html .= '<div class="mailbox-read-message"><hr>';

        $html .= '<h4 class="mb-3"><b>Cargar Facturas</b></h4>';

        $html .= '<form action="" class="dropzone" id="uploadZona">';
        $html .= '<div class="dz-default dz-message">';
        $html .= '<button class="dz-button" type="button">';
        $html .= '<i class="fas fa-cloud-upload-alt icon-super-upload"></i>';
        $html .= '<div style="font-size: 18px; font-weight: bold; margin-top: 10px; text-aling: center;"> Arrastra tus archivos o haz clic aquí </div>';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '</form>';
        $html .= '</div>';


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

    /****************************************************
     * GUARDAR FACTURAS / REEMBOLSOS EN LOTE
     ****************************************************/
    case "guardar_facturas_lote":

        if (!isset($_POST["id"]) || !isset($_POST["items"])) {
            echo json_encode([
                "status" => "error",
                "message" => "Parámetros incompletos"
            ]);
            exit;
        }

        $idReporte = $_POST["id"];
        $items = $_POST["items"];

        // Validación básica
        if (!is_array($items) || count($items) === 0) {
            echo json_encode([
                "status" => "error",
                "message" => "No hay facturas para registrar"
            ]);
            exit;
        }

        $resp = $reporte->insertar_facturas_lote($idReporte, $items);

        if ($resp === true) {
            echo json_encode([
                "status" => "success",
                "message" => "Facturas guardadas correctamente"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => $resp   // mensaje de error del modelo
            ]);
        }

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

    case "guardar_horas_ejecutadas":

        $id   = $_POST["id"];
        $hora = $_POST["horas"];


        $ok = $reporte->actualizar_horas_ejecutadas($id, $hora);

        if ($ok) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar"]);
        }

        break;

    case "subirFacturas":

        $reporte_id   = $_POST["reporte_id"];

        $datosReporte = $reporte->get_reporte_by_id($reporte_id);

        if (!$datosReporte) {
            echo json_encode([
                "success" => false,
                "message" => "Permiso no encontrado."
            ]);
            exit;
        }

        $placa    = str_replace(" ", "_", trim($datosReporte["vehi_placa"]));

        // ===========================
        // 2. DATOS DEL ARCHIVO
        // ===========================

        $tmpFile      = $_FILES["file"]["tmp_name"];
        $fileName     = $_FILES["file"]["name"];

        $fecha        = date("Y-m-d");

        // Ruta remota final donde irá el archivo
        $remotePath     = "rpts01/vehiculos/$placa/$fecha";
        $remoteFullPath = "/$remotePath/$fileName";

        // ===========================
        // 3. CREAR CARPETAS VÍA FTP
        // ===========================

        $ftp_server   = "172.16.5.3";
        $ftp_user     = "asfaltart_admin";
        $ftp_pass     = "s1st3m4s19..";

        $ftp = ftp_connect($ftp_server);
        ftp_login($ftp, $ftp_user, $ftp_pass);

        // Modo pasivo recomendado
        ftp_pasv($ftp, true);

        // Crear recursivamente: data01/permisos/{empleado}/{fecha}
        // Crear recursivamente: data01/permisos/{empleado}/{fecha}
        if (!ftp_mksubdirs_safe($ftp, "rpts01/vehiculos/$placa/$fecha")) {
            echo json_encode([
                "success" => false,
                "message" => "No fue posible crear las carpetas en el NAS."
            ]);
            ftp_close($ftp);
            exit;
        }

        ftp_close($ftp);


        // ===========================
        // 2. SUBIR ARCHIVO CON WinSCP
        // ===========================

        $scriptPath = "C:\\xampp\\htdocs\\preoperacional\\public\\winscp\\script_temp.txt";
        $winscpCom  = "C:\\xampp\\htdocs\\preoperacional\\public\\winscp\\WinSCP.com";

        $scriptContent =
            "open ftp://$ftp_user:$ftp_pass@$ftp_server\n" .
            "put \"$tmpFile\" \"$remoteFullPath\"\n" .
            "exit\n";

        file_put_contents($scriptPath, $scriptContent);

        $cmd = "\"$winscpCom\" /ini=nul /script=\"$scriptPath\"";
        exec($cmd . " 2>&1", $output, $resultCode);

        unlink($scriptPath);


        if ($resultCode === 0) {

            $reporte->registrar_soporte_factura(
                $reporte_id,
                $fileName,
                $remoteFullPath
            );

            echo json_encode([
                "success" => true,
                "message" => "Soporte subido correctamente"
            ]);
        } else {

            echo json_encode([
                "success" => false,
                "message" => "Error subiendo archivo",
                "debug"   => $output
            ]);
        }

        break;

    case "listarFacturas":

        $reporte_id = $_POST["reporte_id"];
        $data = $reporte->get_soportes_factura($reporte_id);

        echo json_encode($data);
        break;

    case "descargarFactura":

        if (!isset($_GET["file"])) {
            echo "Archivo no especificado";
            exit;
        }

        $ruta_remota = urldecode($_GET["file"]);
        $ruta_remota = ltrim($ruta_remota, '/');

        $directorio_remoto = dirname($ruta_remota);
        $archivo_remoto    = basename($ruta_remota);

        $nombre_archivo = $archivo_remoto;

        $temp_local = "C:\\xampp\\htdocs\\preoperacional\\public\\temp\\";
        if (!is_dir($temp_local)) {
            mkdir($temp_local, 0777, true);
        }

        $ruta_local = $temp_local . $nombre_archivo;

        $scriptPath = "C:\\xampp\\htdocs\\preoperacional\\public\\winscp\\script_descarga.txt";
        $winscpCom  = "C:\\xampp\\htdocs\\preoperacional\\public\\winscp\\WinSCP.com";

        $scriptContent =
            "open ftp://asfaltart_admin:s1st3m4s19..@172.16.5.3\n" .
            "option transfer binary\n" .
            "cd \"$directorio_remoto\"\n" .
            "get \"$archivo_remoto\" \"$ruta_local\"\n" .
            "exit\n";

        file_put_contents($scriptPath, $scriptContent);

        $cmd = "\"$winscpCom\" /ini=nul /script=\"$scriptPath\"";
        exec($cmd . " 2>&1", $output, $result);

        unlink($scriptPath);

        if (!file_exists($ruta_local)) {
            echo "<pre>";
            print_r($output);
            echo "</pre>";
            exit;
        }

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
        header("Content-Length: " . filesize($ruta_local));

        readfile($ruta_local);
        unlink($ruta_local);


        break;
}
