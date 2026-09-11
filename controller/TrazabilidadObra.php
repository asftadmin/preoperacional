<?php

ob_start();

require_once ('../config/conexion.php');
require_once ('../models/TrazabilidadObra.php');

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$trazabilidad = new Trazabilidad();

/**
 * Retorna una respuesta JSON limpia.
 */
function responder_json($data, $codigo = 200)
{
    http_response_code($codigo);

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/**
 * Convierte la fecha mostrada por daterangepicker
 * DD-MM-YYYY al formato YYYY-MM-DD de PostgreSQL.
 */
function normalizar_fecha($fecha)
{
    $fecha = trim($fecha);

    if ($fecha === '') {
        return null;
    }

    // Ya viene en formato PostgreSQL.
    $fecha_pg = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    if (
        $fecha_pg &&
        $fecha_pg->format('Y-m-d') === $fecha
    ) {
        return $fecha;
    }

    // Formato utilizado en el formulario.
    $fecha_formulario = DateTime::createFromFormat(
        'd-m-Y',
        $fecha
    );

    if (!$fecha_formulario) {
        return null;
    }

    return $fecha_formulario->format('Y-m-d');
}

/** Todas las operaciones requieren una sesión válida. */
if (!isset($_SESSION['user_id'])) {
    responder_json(
        array(
            'success' => false,
            'message' => 'La sesión no es válida.'
        ),
        401
    );
}

$usuario_id = (int) $_SESSION['user_id'];
$op = isset($_GET['op'])
    ? trim($_GET['op'])
    : '';

try {
    switch ($op) {
        /** Lista obras para Select2. */
        case 'listar_obras':
            $buscar = isset($_POST['buscar'])
                ? trim($_POST['buscar'])
                : '';

            $datos = $trazabilidad->get_obras(
                $buscar
            );

            $data = array();

            foreach ($datos as $row) {
                $texto = trim(
                    $row['obras_codigo']
                    . ' - '
                    . $row['obras_nom']
                );

                $data[] = array(
                    'id' => $row['obras_id'],
                    'text' => $texto
                );
            }

            responder_json(
                array(
                    'success' => true,
                    'data' => $data
                )
            );

            break;

        /**
         * Lista vehículos para los Select2
         * utilizados como volqueta.
         */
        case 'listar_vehiculos':
            $buscar = isset($_POST['buscar'])
                ? trim($_POST['buscar'])
                : '';

            $datos = $trazabilidad->get_vehiculos(
                $buscar
            );

            $data = array();

            foreach ($datos as $row) {
                $texto = trim($row['vehi_placa']);

                if (
                    isset($row['vehi_codigo']) &&
                    trim($row['vehi_codigo']) !== ''
                ) {
                    $texto .= ' - '
                        . trim($row['vehi_codigo']);
                }

                $data[] = array(
                    'id' => $row['vehi_id'],
                    'text' => $texto
                );
            }

            responder_json(
                array(
                    'success' => true,
                    'data' => $data
                )
            );

            break;

        /**
         * Guarda un borrador.
         *
         * Si no existe trazabilidad_id:
         * INSERT.
         *
         * Si existe trazabilidad_id:
         * UPDATE del mismo borrador.
         */
        case 'guardar_borrador':
            $trazabilidad_id =
                isset($_POST['trazabilidad_id']) &&
                $_POST['trazabilidad_id'] !== ''
                    ? (int) $_POST['trazabilidad_id']
                    : null;

            $obra_id = isset($_POST['obra_id'])
                ? (int) $_POST['obra_id']
                : 0;

            $tipo_mezcla =
                isset($_POST['tipo_mezcla'])
                    ? trim($_POST['tipo_mezcla'])
                    : '';

            $fecha =
                isset($_POST['fecha_trazabilidad'])
                    ? normalizar_fecha(
                        $_POST['fecha_trazabilidad']
                    )
                    : null;

            $tipo_actividad =
                isset($_POST['tipo_actividad'])
                    ? trim($_POST['tipo_actividad'])
                    : '';

            $observaciones =
                isset($_POST['observaciones'])
                    ? trim($_POST['observaciones'])
                    : '';

            $detalle =
                isset($_POST['detalle']) &&
                is_array($_POST['detalle'])
                    ? $_POST['detalle']
                    : array();

            $llegada =
                isset($_POST['llegada']) &&
                is_array($_POST['llegada'])
                    ? $_POST['llegada']
                    : array();

            /*
             * Datos mínimos requeridos para poder
             * guardar el encabezado en PostgreSQL.
             */
            if ($obra_id <= 0) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'Debe seleccionar una obra.'
                    ),
                    422
                );
            }

            if ($tipo_mezcla === '') {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'Debe seleccionar el tipo de mezcla.'
                    ),
                    422
                );
            }

            if ($fecha === null) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'La fecha ingresada no es válida.'
                    ),
                    422
                );
            }

            if ($tipo_actividad === '') {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'Debe seleccionar el tipo de actividad.'
                    ),
                    422
                );
            }

            /*
             * Si no existe ID se crea el borrador.
             */
            if ($trazabilidad_id === null) {
                $respuesta =
                    $trazabilidad->insertar_borrador(
                        $obra_id,
                        $usuario_id,
                        $tipo_mezcla,
                        $fecha,
                        $tipo_actividad,
                        $observaciones,
                        $detalle,
                        $llegada
                    );

                responder_json($respuesta);
            }

            /*
             * Si existe ID se actualiza el borrador.
             */
            $respuesta =
                $trazabilidad->actualizar_borrador(
                    $trazabilidad_id,
                    $obra_id,
                    $usuario_id,
                    $tipo_mezcla,
                    $fecha,
                    $tipo_actividad,
                    $observaciones,
                    $detalle,
                    $llegada
                );

            responder_json($respuesta);

            break;

        /**
         * Carga un borrador perteneciente
         * al usuario de la sesión.
         */
        case 'cargar_borrador':
            $trazabilidad_id =
                isset($_POST['trazabilidad_id'])
                    ? (int) $_POST['trazabilidad_id']
                    : 0;

            if ($trazabilidad_id <= 0) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'El formato solicitado no es válido.'
                    ),
                    422
                );
            }

            /*
             * get_trazabilidad valida que el formato
             * pertenezca al usuario conectado.
             */
            $encabezado =
                $trazabilidad->get_trazabilidad(
                    $trazabilidad_id,
                    $usuario_id
                );

            if (!$encabezado) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'No se encontró el formato o no tiene acceso.'
                    ),
                    404
                );
            }

            $detalle =
                $trazabilidad->get_detalle(
                    $trazabilidad_id
                );

            $llegada =
                $trazabilidad->get_llegadas(
                    $trazabilidad_id
                );

            responder_json(
                array(
                    'success' => true,
                    'data' => array(
                        'encabezado' => $encabezado,
                        'detalle' => $detalle,
                        'llegada' => $llegada
                    )
                )
            );

            break;

        /**
         * Lista los formatos creados por
         * el usuario para el inbox.
         */
        case 'listar':
            $datos =
                $trazabilidad
                    ->get_trazabilidades_usuario(
                        $usuario_id
                    );

            responder_json(
                array(
                    'success' => true,
                    'data' => $datos
                )
            );

            break;

        /** Envía un formato a aprobación. */
        case 'enviar_aprobacion':
            $trazabilidad_id =
                isset($_POST['trazabilidad_id'])
                    ? (int) $_POST['trazabilidad_id']
                    : 0;

            if ($trazabilidad_id <= 0) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'El formato solicitado no es válido.'
                    ),
                    422
                );
            }

            $respuesta =
                $trazabilidad->enviar_aprobacion(
                    $trazabilidad_id
                );

            responder_json(
                $respuesta
            );

            break;

        case 'listar_pendientes_aprobacion':
            $datos =
                $trazabilidad->get_pendientes_aprobacion();

            responder_json(
                array(
                    'success' => true,
                    'data' => $datos
                )
            );

            break;

        /** Cargar formato para aprobación. */
        case 'cargar_aprobacion':
            $trazabilidad_id =
                isset($_POST['trazabilidad_id'])
                    ? (int) $_POST['trazabilidad_id']
                    : 0;

            if ($trazabilidad_id <= 0) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'El formato solicitado no es válido.'
                    ),
                    422
                );
            }

            $encabezado =
                $trazabilidad->get_trazabilidad_aprobacion(
                    $trazabilidad_id
                );

            if (!$encabezado) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'El formato no se encuentra disponible para aprobación.'
                    ),
                    404
                );
            }

            $detalle =
                $trazabilidad->get_detalle(
                    $trazabilidad_id
                );

            $llegada =
                $trazabilidad->get_llegadas(
                    $trazabilidad_id
                );

            responder_json(
                array(
                    'success' => true,
                    'data' => array(
                        'encabezado' => $encabezado,
                        'detalle' => $detalle,
                        'llegada' => $llegada
                    )
                )
            );

            break;

        /** Aprobar formato. */
        case 'aprobar_trazabilidad':
            $trazabilidad_id =
                isset($_POST['trazabilidad_id'])
                    ? (int) $_POST['trazabilidad_id']
                    : 0;

            $observaciones =
                isset($_POST['observaciones'])
                    ? trim($_POST['observaciones'])
                    : '';

            if ($trazabilidad_id <= 0) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'El formato solicitado no es válido.'
                    ),
                    422
                );
            }

            $respuesta =
                $trazabilidad->aprobar_trazabilidad(
                    $trazabilidad_id,
                    $usuario_id,
                    $observaciones
                );

            responder_json(
                $respuesta
            );

            break;

        /** Rechazar formato. */
        case 'rechazar_trazabilidad':
            $trazabilidad_id =
                isset($_POST['trazabilidad_id'])
                    ? (int) $_POST['trazabilidad_id']
                    : 0;

            $observaciones =
                isset($_POST['observaciones'])
                    ? trim($_POST['observaciones'])
                    : '';

            if ($trazabilidad_id <= 0) {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'El formato solicitado no es válido.'
                    ),
                    422
                );
            }

            if ($observaciones === '') {
                responder_json(
                    array(
                        'success' => false,
                        'message' => 'Debe indicar el motivo del rechazo.'
                    ),
                    422
                );
            }

            $respuesta =
                $trazabilidad->rechazar_trazabilidad(
                    $trazabilidad_id,
                    $usuario_id,
                    $observaciones
                );

            responder_json(
                $respuesta
            );

            break;

        /** Lista la bandeja completa de aprobación. */
        case 'listar_bandeja_aprobacion':
            $datos =
                $trazabilidad->get_bandeja_aprobacion();

            responder_json(
                array(
                    'success' => true,
                    'data' => $datos
                )
            );

            break;

        /** Operación no reconocida. */
        default:
            responder_json(
                array(
                    'success' => false,
                    'message' => 'Operación no válida.'
                ),
                400
            );

            break;
    }
} catch (Exception $e) {
    responder_json(
        array(
            'success' => false,
            'message' => 'Se presentó un error al procesar la solicitud.'
        ),
        500
    );
}