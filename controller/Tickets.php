<?php
/*CONTOLADOR TIPO DE VEHICULO*/
require_once("../config/conexion.php");
require_once("../models/Tickets.php");

$ticket = new Tickets();

switch ($_REQUEST["op"]) {
    /* SELECT OBRAS  */
    case 'listarTicketsOpen':
        $coordinador = $_SESSION["user_rol_usuario"];
        $datos = $ticket->get_tickets($coordinador);
        $data = array();
        //$tickets = [];
        foreach ($datos as $solicitud) {
            $sub_array = array();
            $sub_array[] = $solicitud["num_soli"];
            $sub_array[] = $solicitud["vehi_placa"];
            $sub_array[] = date('d-m-Y / H:i', strtotime($solicitud["fech_creac_soli"]));

            $sub_array[] = '<div class="button-container text-center" >
                    <button type="button" onClick="ver(' . $solicitud["codi_soli"] . ');" id="' . $solicitud["codi_soli"] . '" class="btn btn-secondary btn-icon " >
                        <div><i class="fas fa-envelope"></i></div>
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


    case 'detalleTicket':
        //header('Content-Type: application/json'); // Añadir cabecera JSON

        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        $ticketID = $_POST['id'];
        $detalle = $ticket->get_detalle_solicitud($ticketID);

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
                'message' => 'No se encontraron detalles para el ticket ID: ' . $ticketID
            ]);
            exit;
        }

        $html = '';
        foreach ($detalle as $row) {
            $html .= '<div class="mailbox-read-info">';
            $html .= '<input type="hidden" id="ticket_id" name="ticket_id" value="' . $row['codi_soli'] . '">';
            $html .= '<h3><b>Detalle de Solicitud: ' . htmlspecialchars($row['num_soli']) . '</b></h3></br>';
            $html .= '<input type="hidden" id="codi_vehi" name="codi_vehi" value="' . $row['codi_vehi_soli'] . '">';
            $html .= '<h6>Vehículo: ' . htmlspecialchars($row['vehi_marca'] . " " . $row['vehi_placa']) .
                '<span class="mailbox-read-time float-right">' .
                date('d/m/Y H:i', strtotime($row['fech_creac_soli'])) . '</span></h6>';
            $html .= '<p> Conductor (Responsable): ' . nl2br(htmlspecialchars($row['user_nombre'] . ' ' . $row['user_apellidos'])) . '</p>';
            $html .= '</div>';

            $html .= '<div class="mailbox-read-message">';
            $html .= '<h5><b>Diagnostico Inicial:</b></h5>';
            $html .= '<input type="hidden" id="codi_cond" name="codi_cond" value="' . $row['codi_cond_soli'] . '">';
            $html .= '<input type="hidden" id="diag_rpte" name="diag_rpte" value="' . $row['desc_soli'] . '">';
            $html .= '<p>' . nl2br(htmlspecialchars($row['desc_soli'])) . '</p>';
            $html .= '</div>';
        }

        echo json_encode(['status' => 'success', 'html' => $html]);
        break;

    case 'listarTicketsRevision':

        $coordinador = $_SESSION["user_rol_usuario"];
        $placa  = isset($_POST["placa"]) ? trim($_POST["placa"]) : "";
        $fechas = isset($_POST["fechas"]) ? trim($_POST["fechas"]) : "";

        $fechaIni = "";

        $fechaFin = "";

        if ($fechas !== "") {
            // formato esperado: YYYY-MM-DD / YYYY-MM-DD
            $rango = explode(" / ", $fechas);
            $fechaIni = $rango[0];
            $fechaFin = $rango[1];
            // Si seleccionó UN SOLO DÍA → ajustar
            if ($fechaIni === $fechaFin) {
                $fechaIni = $fechaIni . " 00:00:00";
                $fechaFin = $fechaFin . " 23:59:59";
            }
        }
        $datos = $ticket->get_tickets_revision($coordinador, $placa, $fechaIni, $fechaFin);
        $data = array();
        //$tickets = [];
        foreach ($datos as $solicitud) {
            $sub_array = array();
            $sub_array[] = $solicitud["num_otm"];
            $sub_array[] = $solicitud["num_soli"];
            $sub_array[] = $solicitud["vehi_placa"];
            $sub_array[] = date('d-m-Y / H:i', strtotime($solicitud["fech_creac_soli"]));
            $sub_array[] = $solicitud["tipo_mantenimiento"];
            $estado = intval($solicitud["esta_soli"]);

            if ($estado === 2) {
                $badge = '<span class="badge bg-success">EN REVISION</span>';
            } elseif ($estado === 3) {
                $badge = '<span class="badge bg-danger">CERRADO</span>';
            } else {
                $badge = '<span class="badge bg-secondary">DESCONOCIDO</span>';
            }

            $sub_array[] = $badge;

            $sub_array[] = '<div class="button-container text-center" >
                    <button type="button" onClick="cerrarOTM(' . $solicitud["codi_otm"] . ');" id="' . $solicitud["codi_otm"] . '" class="btn btn-warning btn-icon " >
                        <div><i class="fas fa-lock"></i></i></div>
                    </button>
                    <button type="button" onClick="verOTM(' . $solicitud["codi_otm"] . ');" id="' . $solicitud["codi_otm"] . '" class="btn btn-danger btn-icon " >
                        <div><i class="fas fa-file-pdf"></i></i></div>
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

    case 'listarTicketsCerrados':

        $coordinador = $_SESSION["user_rol_usuario"];
        $placa  = isset($_POST["placa"]) ? trim($_POST["placa"]) : "";
        $fechas = isset($_POST["fechas"]) ? trim($_POST["fechas"]) : "";

        $fechaIni = "";

        $fechaFin = "";

        if ($fechas !== "") {
            // formato esperado: YYYY-MM-DD / YYYY-MM-DD
            $rango = explode(" / ", $fechas);
            $fechaIni = $rango[0];
            $fechaFin = $rango[1];
            // Si seleccionó UN SOLO DÍA → ajustar
            if ($fechaIni === $fechaFin) {
                $fechaIni = $fechaIni . " 00:00:00";
                $fechaFin = $fechaFin . " 23:59:59";
            }
        }


        $datos = $ticket->get_tickets_cerrados($coordinador, $placa, $fechaIni, $fechaFin);

        /*         echo "<pre>";
        var_dump($datos);
        echo "</pre>";
        exit; */

        $data = array();
        //$tickets = [];
        foreach ($datos as $solicitud) {

            // =====================================
            //  BADGE DE ESTADO
            // =====================================
            $estado = intval($solicitud["repo_mtto_estado"]);

            if ($estado === 1) {
                $badge = '<span class="badge bg-success">ABIERTO</span>';
            } elseif ($estado === 2) {
                $badge = '<span class="badge bg-danger">CERRADO</span>';
            } else {
                $badge = '<span class="badge bg-secondary">DESCONOCIDO</span>';
            }
            $sub_array = array();
            $sub_array[] = $solicitud["repo_mtto_num_reporte"];
            $sub_array[] = $solicitud["num_otm"];
            $sub_array[] = $solicitud["vehi_placa"];

            $sub_array[] = date('d-m-Y / H:i', strtotime($solicitud["repo_mtto_fecha_creacion"]));
            $sub_array[] = $badge;
            $sub_array[] = $estado;

            $sub_array[] = '<div class="button-container text-center" >
                    <button type="button" onClick="verReporte(' . $solicitud["repo_mtto_id"] . ');" id="' . $solicitud["repo_mtto_id"] . '" class="btn btn-warning btn-icon " >
                        <div><i class="fas fa-folder-open"></i></div>
                    </button>
                    <button type="button" onClick="verPdf(' . $solicitud["repo_mtto_id"] . ');" id="' . $solicitud["repo_mtto_id"] . '" class="btn btn-danger btn-icon btn-pdf" >
                        <div><i class="fas fa-file-pdf"></i></div>
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


    /*     case 'numeroSolicitud':
        $ultimo = $ticket->obternerUltimoConsecutivo();

        // Verifica si obtuvo un resultado correcto
        if ($ultimo && isset($ultimo['num_soli'])) {
            // Extraer los últimos 6 dígitos del repo_numb
            preg_match('/(\d{3})$/', $ultimo['num_soli'], $matches);

            if (isset($matches[1])) {
                $ultimoNumero = intval($matches[1]); // Convertir a número
                $nuevoNumero = str_pad($ultimoNumero + 1, 3, "0", STR_PAD_LEFT);
            } else {
                $nuevoNumero = "001"; // Si no encuentra el número, inicia en 000001
            }
        } else {
            $nuevoNumero = "001";
        }

        // Generar el nuevo código con el año actual
        $nuevoReporte = "SM-" . date("Y") . "-" . $nuevoNumero;

        echo $nuevoReporte;


        break; */

    case 'numeroSolicitud':

        $ultimo = $ticket->obternerUltimoConsecutivo();

        if ($ultimo && isset($ultimo['num_soli'])) {

            // Tomar los últimos 3 dígitos
            $ultimosTres = substr($ultimo['num_soli'], -3);

            if (is_numeric($ultimosTres)) {
                $nuevoNumero = str_pad(intval($ultimosTres) + 1, 3, "0", STR_PAD_LEFT);
            } else {
                $nuevoNumero = "001";
            }
        } else {
            // No hay solicitudes este año
            $nuevoNumero = "001";
        }

        $nuevoCodigo = "SM-" . date("Y") . "-" . $nuevoNumero;

        echo $nuevoCodigo;
        exit;

        break;



    case 'guardarSolicitudMtto':

        $numero     = $_POST["num_solicitud"] ?? null;
        $conductor  = $_POST["conductor"] ?? null;
        $vehiculo   = $_POST["vehiculo"] ?? null;
        $falla      = $_POST["falla"] ?? null;
        $km         = $_POST["lectura"] ?? null;

        $resultado = $ticket->insert_solicitud($numero, $conductor, $vehiculo, $falla, $km);

        if ($resultado) {
            echo json_encode([
                "status"  => "success",
                "message" => "Solicitud registrada correctamente"
            ]);
        } else {
            echo json_encode([
                "status"  => "error",
                "message" => "No se pudo guardar la solicitud"
            ]);
        }

        exit;
}


?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>