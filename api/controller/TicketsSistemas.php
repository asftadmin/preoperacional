<?php

require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../model/TicketsSistemas.php';

/*
 * Reutilizamos el servicio de correo
 * existente en Control de Equipos.
 */
require_once __DIR__ . '/../../models/EmailTickets.php';

class TicketsSistemasController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new TicketsSistemasApi();
    }

    /** Crear un nuevo ticket desde Control de Personal. */

    /**
     * Crear un nuevo ticket desde Control de Personal.
     */
    public function crear()
    {
        try {
            /*
             * Recibir JSON enviado en el body.
             */
            $contenido = file_get_contents('php://input');

            if (
                $contenido === false ||
                trim($contenido) === ''
            ) {
                ApiResponse::json(
                    false,
                    'No se recibió información para crear el ticket.',
                    null,
                    400
                );
            }

            /*
             * Convertir JSON a arreglo.
             */
            $datos = json_decode(
                $contenido,
                true
            );

            if (
                json_last_error() !== JSON_ERROR_NONE ||
                !is_array($datos)
            ) {
                ApiResponse::json(
                    false,
                    'La información enviada no tiene un formato JSON válido.',
                    null,
                    400
                );
            }

            /*
             * Campos mínimos obligatorios.
             */
            $camposObligatorios = [
                'empleado_documento',
                'empleado_nombre',
                'tipo',
                'categoria_id',
                'asunto',
                'descripcion'
            ];

            foreach (
                $camposObligatorios as $campo
            ) {
                if (
                    !isset($datos[$campo]) ||
                    trim(
                        (string) $datos[$campo]
                    ) === ''
                ) {
                    ApiResponse::json(
                        false,
                        'El campo '
                            . $campo
                            . ' es obligatorio.',
                        null,
                        400
                    );
                }
            }

            /*
             * Crear ticket.
             */
            $respuesta =
                $this->modelo->crearTicket(
                    $datos
                );

            /*
             * Validar resultado del modelo.
             */
            if (
                !isset($respuesta['success']) ||
                $respuesta['success'] !== true
            ) {
                ApiResponse::json(
                    false,
                    isset($respuesta['message'])
                        ? $respuesta['message']
                        : 'No fue posible crear el ticket.',
                    null,
                    400
                );
            }

            /*
             * =====================================================
             * ENVÍO DE CORREO
             * =====================================================
             *
             * El ticket ya está creado en este punto.
             * Si falla el correo NO se revierte la creación.
             */

            $correoEnviado = false;

            try {
                $ticketId =
                    isset($respuesta['data']['ticket_id'])
                        ? (int) $respuesta['data']['ticket_id']
                        : 0;

                if ($ticketId > 0) {
                    /*
                     * Consultar información completa
                     * del ticket para la plantilla.
                     */
                    $ticket =
                        $this->modelo->obtenerTicket(
                            $ticketId
                        );

                    if ($ticket !== null) {
                        $correoTickets =
                            new EmailTickets();

                        $correoTickets
                            ->enviarNuevoTicket(
                                $ticket
                            );

                        $correoEnviado = true;
                    }
                }
            } catch (Throwable $errorCorreo) {
                /*
                 * El error del correo se registra,
                 * pero no afecta la creación del ticket.
                 */
                error_log(
                    'API Tickets - Error enviando correo del ticket '
                    . (
                        isset(
                            $respuesta['data']['ticket_numero']
                        )
                            ? $respuesta['data']['ticket_numero']
                            : ''
                    )
                    . ': '
                    . $errorCorreo->getMessage()
                );
            }

            /*
             * Informar estado del correo.
             */
            $respuesta['data']['correo_enviado'] =
                $correoEnviado;

            /*
             * Respuesta final.
             */
            ApiResponse::json(
                true,
                isset($respuesta['message'])
                    ? $respuesta['message']
                    : 'Ticket creado correctamente.',
                $respuesta['data'],
                201
            );
        } catch (Throwable $e) {
            error_log(
                'API Tickets - Error en controlador crear: '
                . $e->getMessage()
            );

            ApiResponse::json(
                false,
                'Se presentó un error al procesar la solicitud.',
                null,
                500
            );
        }
    }

    /**
     * Consultar categorías activas.
     */
    public function categorias()
    {
        try {
            $categorias = $this->modelo->listarCategorias();

            ApiResponse::json(
                true,
                'Categorías consultadas correctamente.',
                $categorias,
                200
            );
        } catch (Throwable $e) {
            error_log(
                'API Tickets - Error consultando categorías: '
                . $e->getMessage()
            );

            ApiResponse::json(
                false,
                'No fue posible consultar las categorías.',
                null,
                500
            );
        }
    }
}
