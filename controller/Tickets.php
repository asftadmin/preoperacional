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
        $datos = $ticket->get_tickets_revision($coordinador);
        $data = array();
        //$tickets = [];
        foreach ($datos as $solicitud) {
            $sub_array = array();
            $sub_array[] = $solicitud["num_otm"];
            $sub_array[] = $solicitud["num_soli"];
            $sub_array[] = $solicitud["vehi_placa"];
            $sub_array[] = date('d-m-Y / H:i', strtotime($solicitud["fech_creac_soli"]));
            $sub_array[] = $solicitud["tipo_mantenimiento"];

            $sub_array[] = '<div class="button-container text-center" >
                    <button type="button" onClick="" id="" class="btn btn-dark btn-icon " >
                        <div><i class="fas fa-search-plus"></i></div>
                    </button>
                    <button type="button" onClick="verOTM(' . $solicitud["codi_otm"] . ');" id="' . $solicitud["codi_otm"] . '" class="btn btn-danger btn-icon " >
                        <div><i class="fas fa-file-pdf"></i></i></div>
                    </button>
                    <button type="button" onClick="cerrarOTM(' . $solicitud["codi_otm"] . ');" id="' . $solicitud["codi_otm"] . '" class="btn btn-success btn-icon " >
                        <div><i class="fas fa-lock"></i></i></div>
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
        $datos = $ticket->get_tickets_cerrados($coordinador);
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
}


?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>