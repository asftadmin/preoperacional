<?php

require_once('../config/conexion.php');
require_once('../models/TicketsSistemas.php');
require_once('curl.php');

header('Content-Type: application/json; charset=utf-8');
$modelo = new TicketsSistemas();

function responderTicketSistemas($status, $mensaje, $data = null, $codigoHttp = 200)
{
    http_response_code($codigoHttp);
    echo json_encode(array('status' => $status, 'message' => $mensaje, 'data' => $data), JSON_UNESCAPED_UNICODE);
    exit;
}

function exigirSesionTicketSistemas()
{
    if (empty($_SESSION['user_id'])) {
        responderTicketSistemas('error', 'La sesión ha expirado.', null, 401);
    }
}

function exigirCsrfTicketSistemas()
{
    $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    if (empty($_SESSION['csrf_tickets_sistemas']) || !hash_equals($_SESSION['csrf_tickets_sistemas'], $token)) {
        responderTicketSistemas('error', 'La solicitud de seguridad no es válida. Recargue la página.', null, 403);
    }
}

function textoPostTicketSistemas($campo, $maximo, $obligatorio = false)
{
    $valor = isset($_POST[$campo]) && !is_array($_POST[$campo]) ? trim((string) $_POST[$campo]) : '';
    if ($obligatorio && $valor === '') {
        responderTicketSistemas('error', 'El campo ' . $campo . ' es obligatorio.', null, 422);
    }
    if (mb_strlen($valor, 'UTF-8') > $maximo) {
        responderTicketSistemas('error', 'El campo ' . $campo . ' supera la longitud permitida.', null, 422);
    }
    return $valor;
}

function valorPermitidoTicketSistemas($valor, $permitidos, $campo)
{
    if (!in_array($valor, $permitidos, true)) {
        responderTicketSistemas('error', 'El valor de ' . $campo . ' no es válido.', null, 422);
    }
    return $valor;
}

function normalizarEmpleadoTicketSistemas($data)
{
    if (!is_object($data)) {
        throw new RuntimeException('La API devolvió un empleado con formato inválido.');
    }
    return array(
        'documento' => isset($data->documento) ? (string) $data->documento : '',
        'nombre' => isset($data->nombre) ? trim((string) $data->nombre) : '',
        'correo' => isset($data->correo) ? trim((string) $data->correo) : '',
        'cargo' => isset($data->cargo) ? trim((string) $data->cargo) : '',
        'area' => isset($data->area) ? trim((string) $data->area) : '',
        'activo' => !empty($data->activo)
    );
}

function consultarEmpleadoTicketSistemas($valor, $criterio = 'documento')
{
    $valor = trim((string) $valor);
    if ($criterio === 'documento') {
        if (!preg_match('/^[0-9]{3,20}$/D', $valor)) {
            throw new InvalidArgumentException('El documento debe contener solamente números.');
        }
    } elseif ($criterio === 'nombre') {
        if (mb_strlen($valor, 'UTF-8') < 3 || mb_strlen($valor, 'UTF-8') > 200) {
            throw new InvalidArgumentException('El nombre debe contener entre 3 y 200 caracteres.');
        }
    } else {
        throw new InvalidArgumentException('El criterio de búsqueda no es válido.');
    }
    $respuesta = CurlController::requestApiEmpleados('?' . $criterio . '=' . rawurlencode($valor), 'GET');
    if (!is_object($respuesta)) {
        throw new RuntimeException('La API de empleados no entregó una respuesta válida.');
    }
    if (empty($respuesta->success) || !isset($respuesta->data)) {
        $mensaje = isset($respuesta->message) ? (string) $respuesta->message : 'El empleado no fue encontrado o no está activo.';
        throw new RuntimeException($mensaje);
    }

    if ($criterio === 'nombre') {
        $items = is_array($respuesta->data) ? $respuesta->data : array($respuesta->data);
        $empleados = array();
        foreach ($items as $item) {
            $empleados[] = normalizarEmpleadoTicketSistemas($item);
        }
        if (count($empleados) === 0) {
            throw new RuntimeException('No se encontraron empleados activos.');
        }
        return $empleados;
    }

    return normalizarEmpleadoTicketSistemas($respuesta->data);
}

exigirSesionTicketSistemas();
if (!$modelo->tieneAcceso((int) $_SESSION['user_id'])) {
    responderTicketSistemas('error', 'No tiene permiso para utilizar la mesa de servicio.', null, 403);
}
$op = isset($_GET['op']) ? (string) $_GET['op'] : '';

try {
    switch ($op) {
        case 'buscarEmpleado':
            $termino = isset($_GET['q']) && !is_array($_GET['q']) ? trim((string) $_GET['q']) : '';
            $criterio = preg_match('/^[0-9]+$/D', $termino) ? 'documento' : 'nombre';
            $encontrados = consultarEmpleadoTicketSistemas($termino, $criterio);
            $empleados = $criterio === 'nombre' ? $encontrados : array($encontrados);
            $resultados = array();
            foreach ($empleados as $empleado) {
                $resultados[] = array(
                    'id' => $empleado['documento'],
                    'text' => $empleado['documento'] . ' - ' . $empleado['nombre'],
                    'empleado' => $empleado
                );
            }
            responderTicketSistemas('success', 'Empleados encontrados.', array('results' => $resultados));
            break;

        case 'categorias':
            responderTicketSistemas('success', 'Categorías consultadas.', $modelo->listarCategorias());
            break;

        case 'responsables':
            responderTicketSistemas('success', 'Responsables consultados.', $modelo->listarResponsables());
            break;

        case 'crear':
            exigirCsrfTicketSistemas();
            $documento = textoPostTicketSistemas('empleado_documento', 20, true);
            $empleado = consultarEmpleadoTicketSistemas($documento);
            if (!$empleado['activo'] || $empleado['documento'] !== $documento) {
                responderTicketSistemas('error', 'El empleado seleccionado no es válido.', null, 422);
            }
            $categoriaId = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
            if (!$categoriaId) {
                responderTicketSistemas('error', 'Debe seleccionar una categoría.', null, 422);
            }
            if (!$modelo->categoriaActivaExiste($categoriaId)) {
                responderTicketSistemas('error', 'La categoría seleccionada no está disponible.', null, 422);
            }
            $datos = array(
                'empleado_documento' => $empleado['documento'],
                'empleado_nombre' => $empleado['nombre'],
                'empleado_correo' => $empleado['correo'],
                'empleado_cargo' => $empleado['cargo'],
                'empleado_area' => $empleado['area'],
                'tipo' => valorPermitidoTicketSistemas(textoPostTicketSistemas('tipo', 20, true), array('SOLICITUD', 'INCIDENTE', 'REQUERIMIENTO'), 'tipo'),
                'categoria_id' => $categoriaId,
                'asunto' => textoPostTicketSistemas('asunto', 150, true),
                'descripcion' => textoPostTicketSistemas('descripcion', 4000, true),
                'prioridad' => valorPermitidoTicketSistemas(textoPostTicketSistemas('prioridad', 10, true), array('BAJA', 'MEDIA', 'ALTA', 'CRITICA'), 'prioridad'),
                'canal' => valorPermitidoTicketSistemas(textoPostTicketSistemas('canal', 20, true), array('LLAMADA', 'CORREO', 'MENSAJE', 'PRESENCIAL', 'SISTEMAS'), 'canal'),
                'ubicacion' => textoPostTicketSistemas('ubicacion', 150),
                'equipo' => textoPostTicketSistemas('equipo', 150)
            );
            $creado = $modelo->crearTicket($datos);
            responderTicketSistemas('success', 'Ticket ' . $creado['ticket_numero'] . ' registrado correctamente.', $creado, 201);
            break;

        case 'listar':
            $estado = isset($_GET['estado']) && !is_array($_GET['estado']) ? trim((string) $_GET['estado']) : '';
            $documento = isset($_GET['documento']) && !is_array($_GET['documento']) ? trim((string) $_GET['documento']) : '';
            $buscar = isset($_GET['buscar']) && !is_array($_GET['buscar']) ? trim((string) $_GET['buscar']) : '';
            if ($estado !== '') {
                valorPermitidoTicketSistemas($estado, array('ABIERTO', 'EN_PROCESO', 'EN_ESPERA', 'RESUELTO', 'CERRADO', 'CANCELADO'), 'estado');
            }
            if ($documento !== '' && !preg_match('/^[0-9]{3,20}$/D', $documento)) {
                responderTicketSistemas('error', 'El filtro de documento no es válido.', null, 422);
            }
            responderTicketSistemas('success', 'Tickets consultados.', $modelo->listarTickets(array(
                'estado' => $estado, 'documento' => $documento,
                'buscar' => mb_substr($buscar, 0, 100, 'UTF-8')
            )));
            break;

        case 'detalle':
            $ticketId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$ticketId) responderTicketSistemas('error', 'El identificador del ticket no es válido.', null, 422);
            $ticket = $modelo->obtenerTicket($ticketId);
            if ($ticket === null) responderTicketSistemas('error', 'El ticket no existe.', null, 404);
            $empleadoApi = null;
            $mensajeApi = '';
            try {
                $empleadoApi = consultarEmpleadoTicketSistemas($ticket['empleado_documento']);
            } catch (Throwable $errorApi) {
                $mensajeApi = $errorApi->getMessage();
            }
            responderTicketSistemas('success', 'Detalle consultado.', array(
                'ticket' => $ticket, 'empleado_api' => $empleadoApi, 'mensaje_api' => $mensajeApi,
                'seguimientos' => $modelo->listarSeguimientos($ticketId),
                'responsables' => $modelo->listarResponsables()
            ));
            break;

        case 'actualizarGestion':
            exigirCsrfTicketSistemas();
            $ticketId = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
            if (!$ticketId) responderTicketSistemas('error', 'El identificador del ticket no es válido.', null, 422);
            $estado = valorPermitidoTicketSistemas(textoPostTicketSistemas('estado', 20, true), array('ABIERTO', 'EN_PROCESO', 'EN_ESPERA', 'RESUELTO', 'CERRADO', 'CANCELADO'), 'estado');
            $prioridad = valorPermitidoTicketSistemas(textoPostTicketSistemas('prioridad', 10, true), array('BAJA', 'MEDIA', 'ALTA', 'CRITICA'), 'prioridad');
            $responsableTexto = textoPostTicketSistemas('responsable_id', 20);
            $responsableId = $responsableTexto === '' ? null : filter_var($responsableTexto, FILTER_VALIDATE_INT);
            if ($responsableTexto !== '' && !$responsableId) responderTicketSistemas('error', 'El responsable seleccionado no es válido.', null, 422);
            if ($responsableId !== null && !$modelo->responsableExiste($responsableId)) {
                responderTicketSistemas('error', 'El responsable seleccionado no existe.', null, 422);
            }
            $solucion = textoPostTicketSistemas('solucion', 4000);
            if (in_array($estado, array('RESUELTO', 'CERRADO'), true) && $solucion === '') {
                responderTicketSistemas('error', 'Debe documentar la solución antes de resolver o cerrar el ticket.', null, 422);
            }
            $modelo->actualizarGestion($ticketId, array(
                'estado' => $estado, 'prioridad' => $prioridad, 'responsable_id' => $responsableId,
                'solucion' => $solucion, 'comentario' => textoPostTicketSistemas('comentario_gestion', 2000)
            ));
            responderTicketSistemas('success', 'La gestión del ticket fue actualizada.');
            break;

        case 'agregarSeguimiento':
            exigirCsrfTicketSistemas();
            $ticketId = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
            $comentario = textoPostTicketSistemas('comentario', 2000, true);
            if (!$ticketId || !$modelo->agregarSeguimiento($ticketId, $comentario)) {
                responderTicketSistemas('error', 'No fue posible registrar el seguimiento.', null, 404);
            }
            responderTicketSistemas('success', 'Seguimiento registrado correctamente.');
            break;

        default:
            responderTicketSistemas('error', 'Operación no válida.', null, 404);
    }
} catch (InvalidArgumentException $error) {
    responderTicketSistemas('error', $error->getMessage(), null, 422);
} catch (RuntimeException $error) {
    responderTicketSistemas('error', $error->getMessage(), null, 422);
} catch (Throwable $error) {
    error_log('TicketsSistemas: ' . $error->getMessage());
    responderTicketSistemas('error', 'Se presentó un error interno al procesar la solicitud.', null, 500);
}
