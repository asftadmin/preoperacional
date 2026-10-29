<?php

class ControlAcpmOperativo extends Conectar {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?: parent::conexion();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function hoy() {
        return $this->db->query("SELECT (CURRENT_TIMESTAMP AT TIME ZONE 'America/Bogota')::date")->fetchColumn();
    }

    public function resumen($fecha) {
        $sql = "WITH parametros AS (SELECT CAST(? AS date) AS dia),
            base AS (
                SELECT c.saldo_apertura AS saldo_corte,
                    COALESCE(ultimo.saldo_final, c.saldo_apertura) AS saldo_base,
                    COALESCE(ultimo.fecha, (c.fecha_corte AT TIME ZONE 'America/Bogota')::date - 1) AS fecha_base,
                    CASE WHEN ultimo.fecha IS NULL THEN 'corte' ELSE 'cierre' END AS origen_apertura
                FROM control_acpm_corte c CROSS JOIN parametros p
                LEFT JOIN LATERAL (
                    SELECT fecha, saldo_final FROM control_acpm_cierres
                    WHERE fecha < p.dia ORDER BY fecha DESC LIMIT 1
                ) ultimo ON true
                WHERE c.corte_id = 1
            ), despachos AS (
                SELECT COALESCE(SUM(desp_galones::numeric) FILTER (WHERE desp_fech < p.dia), 0) AS anteriores,
                    COALESCE(SUM(desp_galones::numeric) FILTER (WHERE desp_fech = p.dia), 0) AS despachado_dia,
                    COALESCE(SUM(desp_galones::numeric), 0) AS desde_base
                FROM despachos_acpm d CROSS JOIN parametros p CROSS JOIN base b
                WHERE desp_fech > b.fecha_base AND desp_fech <= p.dia AND desp_galones > 0 AND desp_estado = 0
                    AND NOT EXISTS (SELECT 1 FROM control_acpm_historicos h WHERE h.desp_id = d.desp_id)
            ), documentos AS (
                SELECT COUNT(*) AS pendientes_documento FROM despachos_acpm d CROSS JOIN parametros p
                WHERE desp_fech <= p.dia AND desp_galones > 0 AND desp_estado = 0
                    AND COALESCE(TRIM(desp_documento_siesa), '') = ''
                    AND NOT EXISTS (SELECT 1 FROM control_acpm_historicos h WHERE h.desp_id = d.desp_id)
            )
            SELECT b.saldo_corte, b.fecha_base, b.origen_apertura,
                b.saldo_base - d.anteriores AS apertura,
                0 AS entradas_dia, 0 AS ajustes_dia, d.despachado_dia,
                b.saldo_base - d.desde_base AS saldo_final, doc.pendientes_documento
            FROM base b CROSS JOIN despachos d CROSS JOIN documentos doc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fecha]);
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$resumen) {
            throw new RuntimeException('No existe el corte inicial de ACPM.');
        }
        $resumen['fecha'] = $fecha;
        $resumen['firma'] = hash('sha256', json_encode(array_intersect_key($resumen,
            array_flip(['fecha', 'apertura', 'entradas_dia', 'ajustes_dia', 'despachado_dia', 'saldo_final']))));
        return $resumen;
    }

    private function siguienteFecha() {
        return $this->db->query("SELECT COALESCE((SELECT MAX(fecha) + 1 FROM control_acpm_cierres),
            (fecha_corte AT TIME ZONE 'America/Bogota')::date) FROM control_acpm_corte WHERE corte_id = 1")->fetchColumn();
    }

    public function estado() {
        $hoy = $this->hoy();
        $siguiente = $this->siguienteFecha();
        return [
            'hoy' => $hoy,
            'resumen' => $this->resumen($hoy),
            'proximo_cierre' => $siguiente <= $hoy ? $this->resumen($siguiente) : null,
            'cierres' => $this->db->query("SELECT c.*, concat(u.user_nombre, ' ', u.user_apellidos) AS responsable
                FROM control_acpm_cierres c JOIN usuarios u ON u.user_id = c.usuario_id
                ORDER BY c.fecha DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    public function cerrar($fecha, $usuario, $firma) {
        $this->db->beginTransaction();
        try {
            $this->db->query('SELECT corte_id FROM control_acpm_corte WHERE corte_id = 1 FOR UPDATE');
            if ($fecha !== $this->siguienteFecha() || $fecha > $this->hoy()) {
                throw new InvalidArgumentException('Debe cerrar el siguiente dia pendiente, sin saltar fechas.');
            }
            $r = $this->resumen($fecha);
            if (!hash_equals($r['firma'], $firma)) {
                throw new InvalidArgumentException('El saldo cambio. Actualice y revise el cierre antes de confirmarlo.');
            }
            $stmt = $this->db->prepare('INSERT INTO control_acpm_cierres
                (fecha, apertura, entradas, ajustes, despachado, saldo_final, pendientes_documento, usuario_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$fecha, $r['apertura'], $r['entradas_dia'], $r['ajustes_dia'],
                $r['despachado_dia'], $r['saldo_final'], $r['pendientes_documento'], $usuario]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
