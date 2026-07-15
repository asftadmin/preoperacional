<?php

class TicketsSistemas extends Conectar
{
    public function tieneAcceso($usuarioId)
    {
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

    public function listarCategorias()
    {
        $conexion = parent::Conexion();
        return $conexion->query("SELECT categoria_id, nombre FROM tickets_sistemas_categorias WHERE activo = TRUE ORDER BY nombre")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function categoriaActivaExiste($categoriaId)
    {
        $conexion = parent::Conexion();
        $sentencia = $conexion->prepare('SELECT 1 FROM tickets_sistemas_categorias WHERE categoria_id = :categoria AND activo = TRUE');
        $sentencia->execute(array(':categoria' => $categoriaId));
        return (bool) $sentencia->fetchColumn();
    }

    public function listarResponsables()
    {
        $conexion = parent::Conexion();
        $sql = "SELECT u.user_id, TRIM(u.user_nombre || ' ' || u.user_apellidos) AS nombre,
                       COALESCE(r.rol_cargo, '') AS rol
                FROM usuarios u LEFT JOIN roles r ON r.rol_id = u.user_rol_usuario
                ORDER BY u.user_nombre, u.user_apellidos";
        return $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function responsableExiste($usuarioId)
    {
        $conexion = parent::Conexion();
        $sentencia = $conexion->prepare('SELECT 1 FROM usuarios WHERE user_id = :usuario');
        $sentencia->execute(array(':usuario' => $usuarioId));
        return (bool) $sentencia->fetchColumn();
    }

    public function crearTicket($datos)
    {
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
                ':numero' => $numero, ':documento' => $datos['empleado_documento'],
                ':nombre' => $datos['empleado_nombre'], ':correo' => $datos['empleado_correo'],
                ':cargo' => $datos['empleado_cargo'], ':area' => $datos['empleado_area'],
                ':tipo' => $datos['tipo'], ':categoria' => $datos['categoria_id'],
                ':asunto' => $datos['asunto'], ':descripcion' => $datos['descripcion'],
                ':prioridad' => $datos['prioridad'], ':canal' => $datos['canal'],
                ':ubicacion' => $datos['ubicacion'], ':equipo' => $datos['equipo']
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

    public function listarTickets($filtros = array())
    {
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

    public function obtenerTicket($ticketId)
    {
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

    public function listarSeguimientos($ticketId)
    {
        $conexion = parent::Conexion();
        $sql = "SELECT s.*, TRIM(COALESCE(u.user_nombre, '') || ' ' || COALESCE(u.user_apellidos, '')) AS responsable
                FROM tickets_sistemas_seguimientos s
                LEFT JOIN usuarios u ON u.user_id = s.responsable_id
                WHERE s.ticket_id = :ticket ORDER BY s.fecha_creacion DESC, s.seguimiento_id DESC";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(array(':ticket' => $ticketId));
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarGestion($ticketId, $datos)
    {
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

    public function agregarSeguimiento($ticketId, $comentario)
    {
        $conexion = parent::Conexion();
        $sql = "INSERT INTO tickets_sistemas_seguimientos (ticket_id, tipo, comentario)
                SELECT :ticket, 'COMENTARIO', :comentario
                WHERE EXISTS (SELECT 1 FROM tickets_sistemas WHERE ticket_id = :ticket_existe)";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(array(':ticket' => $ticketId, ':comentario' => $comentario, ':ticket_existe' => $ticketId));
        return $sentencia->rowCount() === 1;
    }
}
