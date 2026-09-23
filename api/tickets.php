<?php

/*
 * Punto de entrada API - Tickets de Sistemas.
 *
 * Todas las respuestas deben ser JSON.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/middleware/Auth.php';
require_once __DIR__ . '/controller/TicketsSistemas.php';

try {
    /*
     * Validar autenticación de la API.
     */
    ApiAuth::validar();

    /*
     * Obtener operación solicitada.
     */
    $op = isset($_GET['op'])
        ? trim($_GET['op'])
        : '';

    if ($op === '') {
        ApiResponse::json(
            false,
            'Debe indicar una operación.',
            null,
            400
        );
    }

    /*
     * Instanciar controlador.
     */
    $controller = new TicketsSistemasController();

    /*
     * Enrutamiento.
     */
    switch ($op) {
        /*
         * Crear nuevo ticket.
         *
         * POST /api/tickets.php?op=crear
         */
        case 'crear':
            validarMetodo('POST');

            $controller->crear();

            break;

        /*
         * Consultar categorías activas.
         *
         * GET /api/tickets.php?op=categorias
         */
        case 'categorias':
            validarMetodo('GET');

            $controller->categorias();

            break;

        /*
         * Operación no encontrada.
         */
        default:
            ApiResponse::json(
                false,
                'La operación solicitada no existe.',
                null,
                404
            );

            break;
    }
} catch (Throwable $e) {
    /*
     * Registrar error real únicamente en el servidor.
     */
    error_log(
        'API Tickets - Error general: '
        . $e->getMessage()
    );

    /*
     * Nunca exponer detalles internos al consumidor.
     */
    ApiResponse::json(
        false,
        'Se presentó un error interno al procesar la solicitud.',
        null,
        500
    );
}

/**
 * Validar método HTTP permitido.
 */
function validarMetodo($metodoPermitido)
{
    $metodoActual = isset($_SERVER['REQUEST_METHOD'])
        ? strtoupper($_SERVER['REQUEST_METHOD'])
        : '';

    if ($metodoActual !== strtoupper($metodoPermitido)) {
        header(
            'Allow: ' . strtoupper($metodoPermitido)
        );

        ApiResponse::json(
            false,
            'Método HTTP no permitido para esta operación.',
            null,
            405
        );
    }
}
