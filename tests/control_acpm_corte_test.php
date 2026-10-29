<?php
// Regression checks use session-local tables; application data is never modified.
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Despachos.php';

class DespachosCorteTest extends Despachos {
    public function __construct() {
        $this->dbh = parent::Conexion();
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    protected function conexion_control_acpm() {
        return $this->dbh;
    }

    public function verificar() {
        $db = $this->dbh;
        $db->beginTransaction();
        try {
            $db->exec("CREATE TEMP TABLE despachos_acpm AS
                SELECT * FROM public.despachos_acpm WHERE desp_galones > 0 AND desp_estado = 0 LIMIT 1");
            self::check((int) $db->query('SELECT COUNT(*) FROM despachos_acpm')->fetchColumn() === 1, 'Fixture source');
            $db->exec("CREATE TEMP TABLE control_acpm_historicos (desp_id BIGINT PRIMARY KEY);
                UPDATE despachos_acpm SET desp_id = -1, desp_fech = CURRENT_DATE,
                    desp_galones = 10, desp_documento_siesa = NULL;
                INSERT INTO control_acpm_historicos VALUES (-1)");
            foreach ([-2 => 20, -3 => 15, -4 => 30, -5 => 5] as $id => $galones) {
                $db->exec('INSERT INTO despachos_acpm SELECT * FROM despachos_acpm WHERE desp_id = -1');
                // Two copies of -1 exist only between these statements.
                $db->exec("UPDATE despachos_acpm SET desp_id = $id, desp_galones = $galones
                    WHERE ctid = (SELECT ctid FROM despachos_acpm WHERE desp_id = -1 ORDER BY ctid DESC LIMIT 1)");
            }
            $db->exec("UPDATE despachos_acpm SET desp_fech = CURRENT_DATE - 1 WHERE desp_id = -3;
                UPDATE despachos_acpm SET desp_documento_siesa = 'TEST-REGISTRADO' WHERE desp_id = -4;
                UPDATE despachos_acpm SET desp_galones = NULL WHERE desp_id = -5");
            $antes = $this->get_resumen_control_acpm_hoy();
            self::check((float) $antes['total_galones'] === 50.0, 'Historical gallons excluded from daily control');
            self::check((float) $antes['galones_pendientes_acumulados'] === 35.0, 'Previous-day pending remains deducted');
            // An authorization already present at the cut becomes a new actual dispatch.
            $db->exec('UPDATE despachos_acpm SET desp_galones = 5 WHERE desp_id = -5');
            $resumen = $this->get_resumen_control_acpm_hoy();
            self::check((float) $resumen['galones_pendientes_acumulados'] === 40.0, 'Newly completed authorization counts');
            self::check((float) $resumen['galones_pendientes_siesa'] === 25.0, 'Today pending separated from cumulative');
            self::check((float) $resumen['galones_registrados_siesa'] === 30.0, 'Documented gallons excluded from pending');
            $ids = array_column($this->get_despachos_pendientes_siesa(), 'desp_id');
            self::check(count($ids) === 3 && !in_array(-1, $ids), 'Pending list agrees with summary');
            self::check(!$this->update_documento_siesa(-1, 'NO-CAMBIAR'), 'Historical document cannot be changed');
            self::check($this->update_documento_siesa(-2, 'TEST-NUEVO'), 'New dispatch can be reconciled');
            self::check((float) $this->get_resumen_control_acpm_hoy()['galones_pendientes_acumulados'] === 20.0, 'Reconciliation removes pending once');
            foreach (['historico' => 1, 'pendiente' => 2, 'cargado' => 2, '' => 5] as $estado => $cantidad) {
                $filas = $this->get_historial_control_acpm(date('Y-m-d', strtotime('-2 days')), date('Y-m-d'), '', '', $estado);
                self::check(count($filas) === $cantidad, 'History filter: ' . $estado);
            }
            echo "All cut regression checks passed. Application data unchanged.\n";
        } finally {
            $db->rollBack();
        }
    }

    private static function check($condition, $label) {
        if (!$condition) {
            throw new RuntimeException($label);
        }
    }
}

(new DespachosCorteTest())->verificar();
