<?php

require_once __DIR__ . '/../../config/conexion.php';

class TicketsSistemasApi extends Conectar
{
    /**
     * Listar categorías activas disponibles para crear tickets.
     */
    public function listarCategorias()
    {
        try {
            $conexion = parent::Conexion();

            $sql = '
                SELECT
                    categoria_id,
                    nombre
                FROM public.tickets_sistemas_categorias
                WHERE activo = TRUE
                ORDER BY nombre ASC
            ';

            $sentencia = $conexion->prepare($sql);
            $sentencia->execute();

            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log(
                'API Tickets - Error listando categorías: '
                . $e->getMessage()
            );

            return [];
        }
    }

    /**
     * Validar que la categoría exista y esté activa.
     */
    private function validarCategoria($conexion, $categoriaId)
    {
        $sql = '
            SELECT
                categoria_id
            FROM public.tickets_sistemas_categorias
            WHERE categoria_id = :categoria_id
              AND activo = TRUE
            LIMIT 1
        ';

        $sentencia = $conexion->prepare($sql);

        $sentencia->execute([
            ':categoria_id' => $categoriaId
        ]);

        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Generar número consecutivo del ticket.
     *
     * Ejemplo:
     * TS-2026-000058
     */
    private function generarNumeroTicket($conexion)
    {
        $sql = "
            SELECT nextval(
                'public.tickets_sistemas_consecutivo_seq'
            ) AS consecutivo
        ";

        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();

        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

        if (!$resultado || empty($resultado['consecutivo'])) {
            return false;
        }

        $consecutivo = str_pad(
            $resultado['consecutivo'],
            6,
            '0',
            STR_PAD_LEFT
        );

        return 'TS-' . date('Y') . '-' . $consecutivo;
    }

    /**
     * Registrar seguimiento inicial del ticket.
     */
    private function registrarSeguimientoCreacion(
        $conexion,
        $ticketId
    ) {
        $sql = "
            INSERT INTO public.tickets_sistemas_seguimientos
            (
                ticket_id,
                tipo,
                comentario,
                estado_anterior,
                estado_nuevo,
                responsable_id
            )
            VALUES
            (
                :ticket_id,
                'CREACION',
                :comentario,
                NULL,
                'ABIERTO',
                NULL
            )
        ";

        $sentencia = $conexion->prepare($sql);

        return $sentencia->execute([
            ':ticket_id' => $ticketId,
            ':comentario' =>
                'Ticket creado por el usuario desde Control de Personal.'
        ]);
    }

    /**
     * Generar notificaciones internas para los usuarios
     * autorizados para gestionar Tickets de Sistemas.
     */
    private function registrarNotificacionesNuevoTicket(
        $conexion,
        $ticketId,
        $ticketNumero,
        $asunto
    ) {
        $mensaje =
            $ticketNumero
            . ' - '
            . $asunto;

        $sql = "
        INSERT INTO public.tickets_sistemas_notificaciones
        (
            ticket_id,
            usuario_id,
            tipo,
            titulo,
            mensaje
        )

        SELECT
            :ticket_id,
            u.user_id,
            'NUEVO_TICKET',
            'Nuevo ticket registrado',
            :mensaje

        FROM public.usuarios u

        INNER JOIN public.permiso p
            ON p.permiso_rol = u.user_rol_usuario

        INNER JOIN public.menu m
            ON m.menu_id = p.permiso_menu

        WHERE m.menu_identi = 'ticketsSistemas'

          AND p.permiso = 'Si'

          AND COALESCE(
                p.permiso_estado,
                1
              ) = 1

        GROUP BY
            u.user_id
    ";

        $sentencia =
            $conexion->prepare($sql);

        return $sentencia->execute([
            ':ticket_id' => $ticketId,
            ':mensaje' => $mensaje
        ]);
    }

    /**
     * Crear ticket desde la API.
     */
    public function crearTicket($datos)
    {
        $conexion = null;

        try {
            /*
             * Validaciones básicas.
             */
            $documento = trim(
                isset($datos['empleado_documento'])
                    ? $datos['empleado_documento']
                    : ''
            );

            $nombre = trim(
                isset($datos['empleado_nombre'])
                    ? $datos['empleado_nombre']
                    : ''
            );

            $correo = trim(
                isset($datos['empleado_correo'])
                    ? $datos['empleado_correo']
                    : ''
            );

            $cargo = trim(
                isset($datos['empleado_cargo'])
                    ? $datos['empleado_cargo']
                    : ''
            );

            $area = trim(
                isset($datos['empleado_area'])
                    ? $datos['empleado_area']
                    : ''
            );

            $tipo = strtoupper(
                trim(
                    isset($datos['tipo'])
                        ? $datos['tipo']
                        : ''
                )
            );

            $categoriaId = isset($datos['categoria_id'])
                ? (int) $datos['categoria_id']
                : 0;

            $asunto = trim(
                isset($datos['asunto'])
                    ? $datos['asunto']
                    : ''
            );

            $descripcion = trim(
                isset($datos['descripcion'])
                    ? $datos['descripcion']
                    : ''
            );

            $prioridad = strtoupper(
                trim(
                    isset($datos['prioridad'])
                        ? $datos['prioridad']
                        : 'MEDIA'
                )
            );

            $ubicacion = trim(
                isset($datos['ubicacion'])
                    ? $datos['ubicacion']
                    : ''
            );

            $equipo = trim(
                isset($datos['equipo'])
                    ? $datos['equipo']
                    : ''
            );

            /*
             * Documento obligatorio y numérico.
             */
            if ($documento === '' || !preg_match('/^[0-9]+$/', $documento)) {
                return [
                    'success' => false,
                    'message' => 'El documento del empleado no es válido.'
                ];
            }

            /*
             * Nombre obligatorio.
             */
            if ($nombre === '') {
                return [
                    'success' => false,
                    'message' => 'El nombre del empleado es obligatorio.'
                ];
            }

            /*
             * Tipo permitido según constraint de PostgreSQL.
             */
            $tiposPermitidos = [
                'SOLICITUD',
                'INCIDENTE',
                'REQUERIMIENTO'
            ];

            if (!in_array($tipo, $tiposPermitidos, true)) {
                return [
                    'success' => false,
                    'message' => 'El tipo de ticket no es válido.'
                ];
            }

            /*
             * Prioridad permitida según constraint de PostgreSQL.
             */
            $prioridadesPermitidas = [
                'BAJA',
                'MEDIA',
                'ALTA',
                'CRITICA'
            ];

            if (!in_array(
                $prioridad,
                $prioridadesPermitidas,
                true
            )) {
                return [
                    'success' => false,
                    'message' => 'La prioridad seleccionada no es válida.'
                ];
            }

            /*
             * Datos obligatorios.
             */
            if ($categoriaId <= 0) {
                return [
                    'success' => false,
                    'message' => 'Debe seleccionar una categoría.'
                ];
            }

            if ($asunto === '') {
                return [
                    'success' => false,
                    'message' => 'El asunto es obligatorio.'
                ];
            }

            if ($descripcion === '') {
                return [
                    'success' => false,
                    'message' => 'La descripción es obligatoria.'
                ];
            }

            /*
             * Correo opcional.
             */
            if (
                $correo !== '' &&
                !filter_var($correo, FILTER_VALIDATE_EMAIL)
            ) {
                return [
                    'success' => false,
                    'message' => 'El correo electrónico no es válido.'
                ];
            }

            /*
             * Abrir conexión.
             */
            $conexion = parent::Conexion();

            $conexion->beginTransaction();

            /*
             * Validar categoría.
             */
            $categoria = $this->validarCategoria(
                $conexion,
                $categoriaId
            );

            if (!$categoria) {
                $conexion->rollBack();

                return [
                    'success' => false,
                    'message' =>
                        'La categoría seleccionada no existe o está inactiva.'
                ];
            }

            /*
             * Generar consecutivo.
             */
            $ticketNumero = $this->generarNumeroTicket(
                $conexion
            );

            if (!$ticketNumero) {
                throw new Exception(
                    'No fue posible generar el consecutivo del ticket.'
                );
            }

            /*
             * Crear ticket.
             *
             * canal y estado no vienen desde Control de Personal.
             * Son controlados internamente por la API.
             */
            $sql = "
                INSERT INTO public.tickets_sistemas
                (
                    ticket_numero,
                    empleado_documento,
                    empleado_nombre,
                    empleado_correo,
                    empleado_cargo,
                    empleado_area,
                    tipo,
                    categoria_id,
                    asunto,
                    descripcion,
                    prioridad,
                    canal,
                    ubicacion,
                    equipo,
                    estado,
                    responsable_id
                )
                VALUES
                (
                    :ticket_numero,
                    :empleado_documento,
                    :empleado_nombre,
                    :empleado_correo,
                    :empleado_cargo,
                    :empleado_area,
                    :tipo,
                    :categoria_id,
                    :asunto,
                    :descripcion,
                    :prioridad,
                    'SISTEMAS',
                    :ubicacion,
                    :equipo,
                    'ABIERTO',
                    NULL
                )
                RETURNING ticket_id
            ";

            $sentencia = $conexion->prepare($sql);

            $sentencia->execute([
                ':ticket_numero' => $ticketNumero,
                ':empleado_documento' => $documento,
                ':empleado_nombre' => $nombre,
                ':empleado_correo' =>
                    $correo !== '' ? $correo : null,
                ':empleado_cargo' =>
                    $cargo !== '' ? $cargo : null,
                ':empleado_area' =>
                    $area !== '' ? $area : null,
                ':tipo' => $tipo,
                ':categoria_id' => $categoriaId,
                ':asunto' => $asunto,
                ':descripcion' => $descripcion,
                ':prioridad' => $prioridad,
                ':ubicacion' =>
                    $ubicacion !== '' ? $ubicacion : null,
                ':equipo' =>
                    $equipo !== '' ? $equipo : null
            ]);

            $ticketId = $sentencia->fetchColumn();

            if (!$ticketId) {
                throw new Exception(
                    'No fue posible obtener el ID del ticket.'
                );
            }

            /*
             * Registrar trazabilidad inicial.
             */
            $seguimiento =
                $this->registrarSeguimientoCreacion(
                    $conexion,
                    $ticketId
                );

            if (!$seguimiento) {
                throw new Exception(
                    'No fue posible registrar el seguimiento inicial.'
                );
            }

            /*
             * Generar notificaciones internas
             * para los usuarios autorizados.
             */
            $notificaciones =
                $this->registrarNotificacionesNuevoTicket(
                    $conexion,
                    $ticketId,
                    $ticketNumero,
                    $asunto
                );

            if (!$notificaciones) {
                throw new Exception(
                    'No fue posible registrar las notificaciones del ticket.'
                );
            }

            /*
             * Confirmar ticket, seguimiento
             * y notificaciones.
             */
            $conexion->commit();

            return [
                'success' => true,
                'message' => 'Ticket creado correctamente.',
                'data' => [
                    'ticket_id' => (int) $ticketId,
                    'ticket_numero' => $ticketNumero,
                    'estado' => 'ABIERTO'
                ]
            ];
        } catch (Throwable $e) {
            if (
                $conexion !== null &&
                $conexion->inTransaction()
            ) {
                $conexion->rollBack();
            }

            error_log(
                'API Tickets - Error creando ticket: '
                . $e->getMessage()
            );

            return [
                'success' => false,
                'message' =>
                    'No fue posible crear el ticket. Intente nuevamente.'
            ];
        }
    }

    /**
     * Obtener información completa de un ticket.
     *
     * Se utiliza, entre otros casos, para generar
     * la notificación por correo después de crearlo.
     */
    public function obtenerTicket($ticketId)
    {
        try {
            $conexion = parent::Conexion();

            $sql = "
            SELECT
                t.*,
                c.nombre AS categoria,
                NULLIF(
                    TRIM(
                        COALESCE(u.user_nombre, '')
                        || ' ' ||
                        COALESCE(u.user_apellidos, '')
                    ),
                    ''
                ) AS responsable
            FROM public.tickets_sistemas t

            INNER JOIN public.tickets_sistemas_categorias c
                ON c.categoria_id = t.categoria_id

            LEFT JOIN public.usuarios u
                ON u.user_id = t.responsable_id

            WHERE t.ticket_id = :ticket_id
        ";

            $sentencia = $conexion->prepare($sql);

            $sentencia->execute([
                ':ticket_id' => $ticketId
            ]);

            $ticket = $sentencia->fetch(PDO::FETCH_ASSOC);

            return $ticket === false
                ? null
                : $ticket;
        } catch (Throwable $e) {
            error_log(
                'API Tickets - Error obteniendo ticket: '
                . $e->getMessage()
            );

            return null;
        }
    }
}
