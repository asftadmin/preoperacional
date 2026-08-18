<?php

class TicketsSistemas extends Conectar {
    public function tieneAcceso($usuarioId) {
        $conexion = parent::Conexion();
        $sql = "SELECT 1
                FROM usuarios u
                INNER JOIN permiso p ON p.permiso_rol = u.user_rol_usuario
                INNER JOIN menu m ON m.menu_id = p.permiso_menu
                WHERE u.user_id = :usuario
                  AND m.menu_identi = 'ticketsSistemas'
                  AND p.permiso = 'Si'
                  AND COALESCE(p.permiso_estado, 1) = 1
                LIMIT 1";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(array(':usuario' => $usuarioId));
        return (bool) $sentencia->fetchColumn();
    }

    public function listarCategorias() {
        $conexion = parent::Conexion();
        return $conexion->query("SELECT categoria_id, nombre FROM tickets_sistemas_categorias WHERE activo = TRUE ORDER BY nombre")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function categoriaActivaExiste($categoriaId) {
        $conexion = parent::Conexion();
        $sentencia = $conexion->prepare('SELECT 1 FROM tickets_sistemas_categorias WHERE categoria_id = :categoria AND activo = TRUE');
        $sentencia->execute(array(':categoria' => $categoriaId));
        return (bool) $sentencia->fetchColumn();
    }

    public function listarResponsables() {
        $conexion = parent::Conexion();
        $sql = "SELECT u.user_id, TRIM(u.user_nombre || ' ' || u.user_apellidos) AS nombre,
                       COALESCE(r.rol_cargo, '') AS rol
                FROM usuarios u LEFT JOIN roles r ON r.rol_id = u.user_rol_usuario
                ORDER BY u.user_nombre, u.user_apellidos";
        return $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function responsableExiste($usuarioId) {
        $conexion = parent::Conexion();
        $sentencia = $conexion->prepare('SELECT 1 FROM usuarios WHERE user_id = :usuario');
        $sentencia->execute(array(':usuario' => $usuarioId));
        return (bool) $sentencia->fetchColumn();
    }

    public function crearTicket($datos) {
        $conexion = parent::Conexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conexion->beginTransaction();
        try {
            $consecutivo = (int) $conexion->query("SELECT nextval('tickets_sistemas_consecutivo_seq')")->fetchColumn();
            $numero = 'TS-' . date('Y') . '-' . str_pad((string) $consecutivo, 6, '0', STR_PAD_LEFT);
            $sql = "INSERT INTO tickets_sistemas
                    (ticket_numero, empleado_documento, empleado_nombre, empleado_correo,
                     empleado_cargo, empleado_area, tipo, categoria_id, asunto, descripcion,
                     prioridad, canal, ubicacion, equipo)
                    VALUES (:numero, :documento, :nombre, :correo, :cargo, :area, :tipo,
                            :categoria, :asunto, :descripcion, :prioridad, :canal, :ubicacion, :equipo)
                    RETURNING ticket_id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->execute(array(
                ':numero' => $numero,
                ':documento' => $datos['empleado_documento'],
                ':nombre' => $datos['empleado_nombre'],
                ':correo' => $datos['empleado_correo'],
                ':cargo' => $datos['empleado_cargo'],
                ':area' => $datos['empleado_area'],
                ':tipo' => $datos['tipo'],
                ':categoria' => $datos['categoria_id'],
                ':asunto' => $datos['asunto'],
                ':descripcion' => $datos['descripcion'],
                ':prioridad' => $datos['prioridad'],
                ':canal' => $datos['canal'],
                ':ubicacion' => $datos['ubicacion'],
                ':equipo' => $datos['equipo']
            ));
            $ticketId = (int) $sentencia->fetchColumn();
            $seguimiento = $conexion->prepare("INSERT INTO tickets_sistemas_seguimientos
                (ticket_id, tipo, comentario, estado_nuevo) VALUES (:ticket, 'CREACION', :comentario, 'ABIERTO')");
            $seguimiento->execute(array(':ticket' => $ticketId, ':comentario' => 'Ticket registrado para ' . $datos['empleado_nombre'] . '.'));
            $conexion->commit();
            return array('ticket_id' => $ticketId, 'ticket_numero' => $numero);
        } catch (Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    public function listarTickets($filtros = array()) {
        $conexion = parent::Conexion();
        $condiciones = array('1 = 1');
        $parametros = array();
        if (!empty($filtros['estado'])) {
            $condiciones[] = 't.estado = :estado';
            $parametros[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['documento'])) {
            $condiciones[] = 't.empleado_documento = :documento';
            $parametros[':documento'] = $filtros['documento'];
        }
        if (!empty($filtros['buscar'])) {
            $condiciones[] = '(t.ticket_numero ILIKE :buscar OR t.empleado_nombre ILIKE :buscar OR t.asunto ILIKE :buscar)';
            $parametros[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        $sql = "SELECT t.ticket_id, t.ticket_numero, t.empleado_documento, t.empleado_nombre,
                       t.asunto, t.prioridad, t.estado, t.fecha_creacion, c.nombre AS categoria,
                       NULLIF(TRIM(COALESCE(u.user_nombre, '') || ' ' || COALESCE(u.user_apellidos, '')), '') AS responsable
                FROM tickets_sistemas t
                INNER JOIN tickets_sistemas_categorias c ON c.categoria_id = t.categoria_id
                LEFT JOIN usuarios u ON u.user_id = t.responsable_id
                WHERE " . implode(' AND ', $condiciones) . " ORDER BY t.fecha_creacion DESC";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute($parametros);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTicket($ticketId) {
        $conexion = parent::Conexion();
        $sql = "SELECT t.*, c.nombre AS categoria,
                       NULLIF(TRIM(COALESCE(u.user_nombre, '') || ' ' || COALESCE(u.user_apellidos, '')), '') AS responsable
                FROM tickets_sistemas t
                INNER JOIN tickets_sistemas_categorias c ON c.categoria_id = t.categoria_id
                LEFT JOIN usuarios u ON u.user_id = t.responsable_id
                WHERE t.ticket_id = :ticket";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(array(':ticket' => $ticketId));
        $ticket = $sentencia->fetch(PDO::FETCH_ASSOC);
        return $ticket === false ? null : $ticket;
    }

    public function listarSeguimientos($ticketId) {
        $conexion = parent::Conexion();
        $sql = "SELECT s.*, TRIM(COALESCE(u.user_nombre, '') || ' ' || COALESCE(u.user_apellidos, '')) AS responsable
                FROM tickets_sistemas_seguimientos s
                LEFT JOIN usuarios u ON u.user_id = s.responsable_id
                WHERE s.ticket_id = :ticket ORDER BY s.fecha_creacion DESC, s.seguimiento_id DESC";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(array(':ticket' => $ticketId));
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarGestion($ticketId, $datos) {
        $conexion = parent::Conexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conexion->beginTransaction();
        try {
            $actual = $conexion->prepare('SELECT estado FROM tickets_sistemas WHERE ticket_id = :ticket FOR UPDATE');
            $actual->execute(array(':ticket' => $ticketId));
            $estadoAnterior = $actual->fetchColumn();
            if ($estadoAnterior === false) {
                throw new RuntimeException('El ticket no existe.');
            }
            $sql = "UPDATE tickets_sistemas SET estado = :estado, prioridad = :prioridad,
                        responsable_id = :responsable, solucion = :solucion,
                        fecha_actualizacion = CURRENT_TIMESTAMP,
                        fecha_cierre = CASE WHEN :estado_cierre = 'CERRADO' THEN CURRENT_TIMESTAMP ELSE NULL END
                    WHERE ticket_id = :ticket";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue(':estado', $datos['estado']);
            $sentencia->bindValue(':prioridad', $datos['prioridad']);
            $sentencia->bindValue(':responsable', $datos['responsable_id'], $datos['responsable_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $sentencia->bindValue(':solucion', $datos['solucion']);
            $sentencia->bindValue(':estado_cierre', $datos['estado']);
            $sentencia->bindValue(':ticket', $ticketId, PDO::PARAM_INT);
            $sentencia->execute();
            $seguimiento = $conexion->prepare("INSERT INTO tickets_sistemas_seguimientos
                (ticket_id, tipo, comentario, estado_anterior, estado_nuevo, responsable_id)
                VALUES (:ticket, :tipo, :comentario, :anterior, :nuevo, :responsable)");
            $seguimiento->bindValue(':ticket', $ticketId, PDO::PARAM_INT);
            $seguimiento->bindValue(':tipo', $datos['estado'] === 'CERRADO' ? 'CIERRE' : 'GESTION');
            $seguimiento->bindValue(':comentario', $datos['comentario'] !== '' ? $datos['comentario'] : 'Se actualizó la gestión del ticket.');
            $seguimiento->bindValue(':anterior', $estadoAnterior);
            $seguimiento->bindValue(':nuevo', $datos['estado']);
            $seguimiento->bindValue(':responsable', $datos['responsable_id'], $datos['responsable_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $seguimiento->execute();
            $conexion->commit();
            return true;
        } catch (Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    public function agregarSeguimiento($ticketId, $comentario) {
        $conexion = parent::Conexion();
        $sql = "INSERT INTO tickets_sistemas_seguimientos (ticket_id, tipo, comentario)
                SELECT :ticket, 'COMENTARIO', :comentario
                WHERE EXISTS (SELECT 1 FROM tickets_sistemas WHERE ticket_id = :ticket_existe)";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(array(':ticket' => $ticketId, ':comentario' => $comentario, ':ticket_existe' => $ticketId));
        return $sentencia->rowCount() === 1;
    }

    public function kpiControlOperacion($matrizTiempos) {
        $conexion = parent::Conexion();

        /*
     * KPI generales:
     * - recibidos hoy
     * - cerrados hoy
     * - pendientes
     * - pendientes sin responsable
     */
        $sql = "
        SELECT

            COUNT(*) FILTER (
                WHERE t.fecha_creacion::date = CURRENT_DATE
            ) AS recibidos_hoy,

            COUNT(*) FILTER (
                WHERE t.estado = 'CERRADO'
                  AND t.fecha_cierre::date = CURRENT_DATE
            ) AS cerrados_hoy,

            COUNT(*) FILTER (
                WHERE t.estado IN (
                    'ABIERTO',
                    'EN_PROCESO',
                    'EN_ESPERA'
                )
            ) AS pendientes,

            COUNT(*) FILTER (
                WHERE t.estado IN (
                    'ABIERTO',
                    'EN_PROCESO',
                    'EN_ESPERA'
                )
                  AND t.responsable_id IS NULL
            ) AS sin_asignar

        FROM tickets_sistemas t
    ";

        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();

        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);


        /*
     * Consultamos únicamente los tickets activos
     * para validar si superaron el tiempo de solución.
     */
        $sqlVencidos = "
        SELECT
            t.ticket_id,
            t.prioridad,
            EXTRACT(
                EPOCH FROM (
                    CURRENT_TIMESTAMP - t.fecha_creacion
                )
            ) / 3600 AS horas_transcurridas

        FROM tickets_sistemas t

        WHERE t.estado IN (
            'ABIERTO',
            'EN_PROCESO',
            'EN_ESPERA'
        )
    ";

        $sentenciaVencidos = $conexion->prepare($sqlVencidos);
        $sentenciaVencidos->execute();

        $ticketsActivos = $sentenciaVencidos->fetchAll(PDO::FETCH_ASSOC);


        $fueraTiempo = 0;
        $fueraTiempoPrioritario = 0;


        /*
     * Comparamos las horas transcurridas
     * contra la matriz definida en el controller.
     */
        foreach ($ticketsActivos as $ticket) {

            $prioridad = strtoupper(trim($ticket['prioridad']));

            if (!isset($matrizTiempos[$prioridad])) {
                continue;
            }

            $horasPermitidas =
                (float) $matrizTiempos[$prioridad]['solucion_horas'];

            $horasTranscurridas =
                (float) $ticket['horas_transcurridas'];


            if ($horasTranscurridas > $horasPermitidas) {

                $fueraTiempo++;

                if (
                    in_array(
                        $prioridad,
                        array('ALTA', 'CRITICA'),
                        true
                    )
                ) {
                    $fueraTiempoPrioritario++;
                }
            }
        }


        return array(

            'recibidos_hoy' =>
            (int) $resultado['recibidos_hoy'],

            'cerrados_hoy' =>
            (int) $resultado['cerrados_hoy'],

            'pendientes' =>
            (int) $resultado['pendientes'],

            'sin_asignar' =>
            (int) $resultado['sin_asignar'],

            'fuera_tiempo' =>
            $fueraTiempo,

            'fuera_tiempo_prioritario' =>
            $fueraTiempoPrioritario

        );
    }

    public function listarTicketsRequierenAtencion($matrizTiempos) {
        $conexion = parent::Conexion();

        /*
     * Consultamos únicamente tickets que todavía
     * forman parte de la operación activa.
     */
        $sql = "
        SELECT

            t.ticket_id,
            t.ticket_numero,
            t.fecha_creacion,
            t.empleado_nombre,
            t.empleado_area,
            t.asunto,
            t.prioridad,
            t.estado,
            t.responsable_id,

            c.nombre AS categoria,

            NULLIF(
                TRIM(
                    COALESCE(u.user_nombre, '')
                    || ' ' ||
                    COALESCE(u.user_apellidos, '')
                ),
                ''
            ) AS responsable,

            /*
             * Antigüedad actual expresada en horas.
             *
             * Por ahora se maneja en tiempo calendario,
             * igual que el KPI de fuera de tiempo.
             */
            EXTRACT(
                EPOCH FROM (
                    CURRENT_TIMESTAMP - t.fecha_creacion
                )
            ) / 3600 AS horas_transcurridas

        FROM tickets_sistemas t

        INNER JOIN tickets_sistemas_categorias c
            ON c.categoria_id = t.categoria_id

        LEFT JOIN usuarios u
            ON u.user_id = t.responsable_id

        WHERE t.estado IN (
            'ABIERTO',
            'EN_PROCESO',
            'EN_ESPERA'
        )

        ORDER BY t.fecha_creacion ASC
    ";

        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();

        $tickets = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        $resultado = array();

        foreach ($tickets as $ticket) {

            $prioridad = strtoupper(
                trim((string) $ticket['prioridad'])
            );

            $horasTranscurridas = (float) $ticket['horas_transcurridas'];

            /*
         * Por defecto asumimos que el ticket
         * todavía no está fuera de tiempo.
         */
            $fueraTiempo = false;

            /*
         * Si la prioridad existe en la matriz,
         * comparamos contra el tiempo máximo de solución.
         */
            if (isset($matrizTiempos[$prioridad])) {

                $horasPermitidas = (float)
                $matrizTiempos[$prioridad]['solucion_horas'];

                if ($horasTranscurridas > $horasPermitidas) {
                    $fueraTiempo = true;
                }
            }

            /*
         * Validamos las condiciones de atención.
         */
            $esCritica = $prioridad === 'CRITICA';
            $esAlta = $prioridad === 'ALTA';
            $sinAsignar = empty($ticket['responsable_id']);

            /*
         * Si no cumple ninguna condición,
         * no debe aparecer en esta bandeja.
         */
            if (
                !$fueraTiempo &&
                !$esCritica &&
                !$esAlta &&
                !$sinAsignar
            ) {
                continue;
            }

            /*
         * Construimos la situación del ticket.
         *
         * Puede tener más de una condición al mismo tiempo.
         */
            $situaciones = array();

            if ($fueraTiempo) {
                $situaciones[] = 'FUERA_TIEMPO';
            }

            if ($esCritica) {
                $situaciones[] = 'CRITICA';
            }

            if ($esAlta) {
                $situaciones[] = 'ALTA';
            }

            if ($sinAsignar) {
                $situaciones[] = 'SIN_ASIGNAR';
            }

            /*
         * Creamos un nivel de prioridad para ordenar
         * posteriormente los resultados.
         */
            $orden = 5;

            if ($esCritica) {

                $orden = 1;
            } elseif ($fueraTiempo) {

                $orden = 2;
            } elseif ($esAlta) {

                $orden = 3;
            } elseif ($sinAsignar) {

                $orden = 4;
            }

            $resultado[] = array(

                'ticket_id' =>
                (int) $ticket['ticket_id'],

                'ticket_numero' =>
                $ticket['ticket_numero'],

                'fecha_creacion' =>
                $ticket['fecha_creacion'],

                'empleado_nombre' =>
                $ticket['empleado_nombre'],

                'empleado_area' =>
                $ticket['empleado_area'],

                'asunto' =>
                $ticket['asunto'],

                'categoria' =>
                $ticket['categoria'],

                'prioridad' =>
                $prioridad,

                'responsable' =>
                $ticket['responsable']
                    ?: 'Sin asignar',

                'horas_transcurridas' =>
                round(
                    $horasTranscurridas,
                    1
                ),

                'fuera_tiempo' =>
                $fueraTiempo,

                'situaciones' =>
                $situaciones,

                'estado' =>
                $ticket['estado'],

                /*
             * Este dato se utiliza únicamente para ordenar.
             * No es una columna de base de datos.
             */
                '_orden' =>
                $orden
            );
        }

        /*
     * Orden:
     *
     * 1. CRITICA
     * 2. Fuera de tiempo
     * 3. ALTA
     * 4. Sin asignar
     *
     * Dentro de la misma condición,
     * primero aparece el ticket más antiguo.
     */
        usort(
            $resultado,
            function ($a, $b) {

                if ($a['_orden'] === $b['_orden']) {

                    return $b['horas_transcurridas']
                        <=> $a['horas_transcurridas'];
                }

                return $a['_orden'] <=> $b['_orden'];
            }
        );

        /*
     * Quitamos el dato interno utilizado para ordenar.
     */
        foreach ($resultado as &$ticket) {
            unset($ticket['_orden']);
        }

        unset($ticket);

        return $resultado;
    }

    /*
 * Este indicador mide antigüedad del backlog.
 * No corresponde al cálculo de SLA.
 */
    public function antiguedadTicketsPendientes() {
        $conexion = parent::Conexion();

        $sql = "
        SELECT

            COUNT(*) FILTER (
                WHERE
                    EXTRACT(
                        EPOCH FROM (
                            CURRENT_TIMESTAMP - t.fecha_creacion
                        )
                    ) / 3600 < 48
            ) AS rango_0_1,

            COUNT(*) FILTER (
                WHERE
                    EXTRACT(
                        EPOCH FROM (
                            CURRENT_TIMESTAMP - t.fecha_creacion
                        )
                    ) / 3600 >= 48

                    AND

                    EXTRACT(
                        EPOCH FROM (
                            CURRENT_TIMESTAMP - t.fecha_creacion
                        )
                    ) / 3600 < 96
            ) AS rango_2_3,

            COUNT(*) FILTER (
                WHERE
                    EXTRACT(
                        EPOCH FROM (
                            CURRENT_TIMESTAMP - t.fecha_creacion
                        )
                    ) / 3600 >= 96

                    AND

                    EXTRACT(
                        EPOCH FROM (
                            CURRENT_TIMESTAMP - t.fecha_creacion
                        )
                    ) / 3600 < 192
            ) AS rango_4_7,

            COUNT(*) FILTER (
                WHERE
                    EXTRACT(
                        EPOCH FROM (
                            CURRENT_TIMESTAMP - t.fecha_creacion
                        )
                    ) / 3600 >= 192
            ) AS rango_mayor_7

        FROM tickets_sistemas t

        WHERE t.estado IN (
            'ABIERTO',
            'EN_PROCESO',
            'EN_ESPERA'
        )
    ";

        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();

        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

        return array(

            'rango_0_1' =>
            (int) ($resultado['rango_0_1'] ?? 0),

            'rango_2_3' =>
            (int) ($resultado['rango_2_3'] ?? 0),

            'rango_4_7' =>
            (int) ($resultado['rango_4_7'] ?? 0),

            'rango_mayor_7' =>
            (int) ($resultado['rango_mayor_7'] ?? 0)

        );
    }

    /*
 * =====================================================
 * TIEMPO PROMEDIO DE SOLUCION VS SLA
 * =====================================================
 */

    public function tiempoPromedioSolucionVsSla($matrizTiempos) {
        $conexion = parent::Conexion();

        /*
     * Consultamos los últimos 7 días en los que
     * existan tickets cerrados.
     */
        $sql = "
        SELECT
            t.ticket_id,
            t.prioridad,
            t.fecha_creacion,
            t.fecha_cierre,
            t.fecha_cierre::date AS fecha_cierre_dia,

            EXTRACT(
                EPOCH FROM (
                    t.fecha_cierre - t.fecha_creacion
                )
            ) / 3600 AS horas_solucion

        FROM tickets_sistemas t

        WHERE t.estado = 'CERRADO'
          AND t.fecha_cierre IS NOT NULL
          AND t.fecha_cierre::date IN (

                SELECT DISTINCT fecha_cierre::date
                FROM tickets_sistemas
                WHERE estado = 'CERRADO'
                  AND fecha_cierre IS NOT NULL

                ORDER BY fecha_cierre::date DESC
                LIMIT 7
          )

        ORDER BY t.fecha_cierre ASC
    ";

        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();

        $tickets = $sentencia->fetchAll(PDO::FETCH_ASSOC);


        /*
     * Agrupamos los tickets por fecha de cierre.
     */
        $agrupados = array();

        foreach ($tickets as $ticket) {

            $fecha = $ticket['fecha_cierre_dia'];

            $prioridad = strtoupper(
                trim((string) $ticket['prioridad'])
            );

            /*
         * Si la prioridad no existe en la matriz,
         * no la utilizamos para el cálculo.
         */
            if (!isset($matrizTiempos[$prioridad])) {
                continue;
            }

            if (!isset($agrupados[$fecha])) {

                $agrupados[$fecha] = array(
                    'suma_real' => 0,
                    'suma_sla' => 0,
                    'tickets' => 0
                );
            }


            /*
         * Tiempo real del ticket:
         * fecha cierre - fecha creación.
         */
            $horasReal =
                (float) $ticket['horas_solucion'];


            /*
         * SLA máximo permitido
         * según la prioridad del ticket.
         */
            $horasSla =
                (float) $matrizTiempos[$prioridad]['solucion_horas'];


            $agrupados[$fecha]['suma_real'] +=
                $horasReal;

            $agrupados[$fecha]['suma_sla'] +=
                $horasSla;

            $agrupados[$fecha]['tickets']++;
        }


        /*
     * Construimos la serie final.
     */
        $serie = array();

        foreach ($agrupados as $fecha => $datos) {

            if ($datos['tickets'] <= 0) {
                continue;
            }

            $promedioReal =
                $datos['suma_real']
                / $datos['tickets'];

            $promedioSla =
                $datos['suma_sla']
                / $datos['tickets'];


            $serie[] = array(

                'fecha' =>
                date(
                    'd/m',
                    strtotime($fecha)
                ),

                'promedio_real' =>
                round(
                    $promedioReal,
                    2
                ),

                'promedio_sla' =>
                round(
                    $promedioSla,
                    2
                ),

                'tickets_cerrados' =>
                (int) $datos['tickets']
            );
        }


        return $serie;
    }
}
