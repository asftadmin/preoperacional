<?php

require_once __DIR__ . '/../../config/conexion.php';

// Persistencia de soportes en PostgreSQL y almacenamiento privado de la API.
class TicketsSoportesApi extends Conectar
{
    // La ruta física nunca se acepta desde el navegador ni se expone en JSON.
    private function directorio()
    {
        $ruta = getenv('TICKETS_SOPORTES_DIR') ?: dirname(__DIR__, 4) . '/storage/tickets_sistemas';
        if (!is_dir($ruta) && !mkdir($ruta, 0700, true) && !is_dir($ruta)) {
            throw new RuntimeException('No fue posible preparar el almacenamiento de soportes.');
        }
        return $ruta;
    }

    // Serializa cargas, eliminación y asociación del mismo formulario.
    public static function bloquear($conexion, $documento, $token)
    {
        $stmt = $conexion->prepare('SELECT pg_advisory_xact_lock(hashtext(:documento), hashtext(:token))');
        $stmt->execute([':documento' => $documento, ':token' => $token]);
    }

    // Valida contenido real y conserva nombres únicos aun con originales repetidos.
    public function subir($documento, $token, $nombre, $base64)
    {
        $contenido = base64_decode($base64, true);
        $mime = $contenido === false ? '' : (new finfo(FILEINFO_MIME_TYPE))->buffer($contenido);
        $extensiones = ['image/jpeg' => ['jpg', 'jpeg'], 'image/png' => ['png'], 'application/pdf' => ['pdf']];
        $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        if ($contenido === false || strlen($contenido) === 0 || strlen($contenido) > 10485760
            || !isset($extensiones[$mime]) || !in_array($extension, $extensiones[$mime], true)
            || mb_strlen($nombre, 'UTF-8') > 255 || preg_match('/[\\x00-\\x1f\\x7f]/', $nombre)) {
            throw new InvalidArgumentException('Adjunte JPG, PNG o PDF válido de hasta 10 MB.');
        }
        $conexion = parent::Conexion();
        $ruta = null;
        try {
            $conexion->beginTransaction();
            self::bloquear($conexion, $documento, $token);
            // Limpia lotes abandonados del propietario al volver a cargar soportes.
            $vencidos = $conexion->prepare("DELETE FROM public.tickets_sistemas_soportes
                WHERE empleado_documento = ? AND ticket_id IS NULL
                  AND soporte_fecha < CURRENT_TIMESTAMP - INTERVAL '24 hours' RETURNING soporte_ruta");
            $vencidos->execute([$documento]);
            $rutasVencidas = $vencidos->fetchAll(PDO::FETCH_COLUMN);
            $stmt = $conexion->prepare('SELECT count(*) AS total, count(ticket_id) AS asociados
                FROM public.tickets_sistemas_soportes WHERE empleado_documento = ? AND soporte_token = ?');
            $stmt->execute([$documento, $token]);
            $estado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ((int) $estado['total'] >= 10 || (int) $estado['asociados'] > 0) {
                throw new InvalidArgumentException('El formulario ya fue enviado o alcanzó el máximo de 10 soportes.');
            }
            $clave = bin2hex(random_bytes(24));
            $ruta = $this->directorio() . '/' . $clave;
            if (file_put_contents($ruta, $contenido, LOCK_EX) !== strlen($contenido)) {
                throw new RuntimeException('No fue posible almacenar el soporte.');
            }
            $stmt = $conexion->prepare('INSERT INTO public.tickets_sistemas_soportes
                (soporte_token, empleado_documento, soporte_nombre, soporte_ruta, soporte_mime, soporte_tamano)
                VALUES (?, ?, ?, ?, ?, ?) RETURNING soporte_id, soporte_nombre, soporte_mime, soporte_tamano');
            $stmt->execute([$token, $documento, $nombre, $clave, $mime, strlen($contenido)]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $conexion->commit();
            foreach ($rutasVencidas as $claveVencida) {
                $rutaVencida = $this->directorio() . '/' . $claveVencida;
                if (is_file($rutaVencida) && !unlink($rutaVencida)) {
                    error_log('Tickets: archivo temporal vencido pendiente de limpieza.');
                }
            }
            return $resultado;
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) { $conexion->rollBack(); }
            if ($ruta !== null && is_file($ruta)) { unlink($ruta); }
            throw $e;
        }
    }

    // Solo se retiran soportes temporales del propietario y formulario indicados.
    public function eliminar($documento, $token, $id)
    {
        $conexion = parent::Conexion();
        try {
            $conexion->beginTransaction();
            self::bloquear($conexion, $documento, $token);
            $stmt = $conexion->prepare('DELETE FROM public.tickets_sistemas_soportes
                WHERE soporte_id = ? AND empleado_documento = ? AND soporte_token = ? AND ticket_id IS NULL
                RETURNING soporte_ruta');
            $stmt->execute([$id, $documento, $token]);
            $clave = $stmt->fetchColumn();
            if (!$clave) { throw new InvalidArgumentException('El soporte temporal no está disponible.'); }
            $conexion->commit();
            $ruta = $this->directorio() . '/' . $clave;
            if (is_file($ruta) && !unlink($ruta)) { error_log('Tickets: soporte sin referencia pendiente de limpieza.'); }
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) { $conexion->rollBack(); }
            throw $e;
        }
    }

    // Consulta solo soportes de tickets pertenecientes al empleado autenticado.
    public function listar($documento, $ticketId)
    {
        $conexion = parent::Conexion();
        $stmt = $conexion->prepare('SELECT s.soporte_id, s.soporte_nombre, s.soporte_mime,
            s.soporte_tamano, s.soporte_fecha FROM public.tickets_sistemas_soportes s
            INNER JOIN public.tickets_sistemas t ON t.ticket_id = s.ticket_id
            WHERE s.ticket_id = ? AND s.empleado_documento = ? AND t.empleado_documento = ?
            ORDER BY s.soporte_id');
        $stmt->execute([$ticketId, $documento, $documento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna contenido para una descarga AJAX autorizada, sin publicar archivos.
    public function descargar($documento, $id)
    {
        $conexion = parent::Conexion();
        $stmt = $conexion->prepare('SELECT s.* FROM public.tickets_sistemas_soportes s
            INNER JOIN public.tickets_sistemas t ON t.ticket_id = s.ticket_id
            WHERE s.soporte_id = ? AND s.empleado_documento = ? AND t.empleado_documento = ?');
        $stmt->execute([$id, $documento, $documento]);
        $soporte = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$soporte) { throw new InvalidArgumentException('El soporte no está disponible.'); }
        $ruta = $this->directorio() . '/' . $soporte['soporte_ruta'];
        if (!is_file($ruta) || !is_readable($ruta)) {
            throw new RuntimeException('El archivo del soporte no está disponible.');
        }
        $contenido = file_get_contents($ruta);
        if ($contenido === false) { throw new RuntimeException('No fue posible leer el soporte.'); }
        return ['soporte_nombre' => $soporte['soporte_nombre'], 'soporte_mime' => $soporte['soporte_mime'],
            'contenido' => base64_encode($contenido)];
    }
}
