<?php
// Only session-local temporary tables are changed. No application rows are written.
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ControlAcpmOperativo.php';

function verificarOperativo($ok, $message) {
    if (!$ok) throw new RuntimeException($message);
}
function rechazarOperativo($call, $message) {
    try { $call(); } catch (Throwable $e) { return; }
    throw new RuntimeException($message);
}

$db = (new Conectar())->getConexion();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$usuario = $db->query('SELECT user_id FROM usuarios ORDER BY user_id LIMIT 1')->fetchColumn();
$db->exec("CREATE TEMP TABLE control_acpm_corte (LIKE public.control_acpm_corte INCLUDING ALL);
    INSERT INTO control_acpm_corte VALUES (1,
        ((CURRENT_TIMESTAMP AT TIME ZONE 'America/Bogota')::date - 1)::timestamp AT TIME ZONE 'America/Bogota',
        1050.38, 'TEST');
    CREATE TEMP TABLE control_acpm_historicos (desp_id BIGINT PRIMARY KEY);
    CREATE TEMP TABLE control_acpm_cierres (LIKE public.control_acpm_cierres INCLUDING ALL);
    CREATE TEMP TABLE acpm_fixture AS SELECT * FROM public.despachos_acpm
        WHERE desp_galones > 0 AND desp_estado = 0 LIMIT 1;
    CREATE TEMP TABLE despachos_acpm AS SELECT * FROM acpm_fixture WHERE false");
verificarOperativo((int) $db->query('SELECT COUNT(*) FROM acpm_fixture')->fetchColumn() === 1, 'Fixture source');
foreach ([-1 => 500, -2 => 20, -3 => 30, -4 => null] as $id => $galones) {
    $db->exec('INSERT INTO despachos_acpm SELECT * FROM acpm_fixture');
    $stmt = $db->prepare("UPDATE despachos_acpm SET desp_id = ?, desp_galones = ?,
        desp_fech = (CURRENT_TIMESTAMP AT TIME ZONE 'America/Bogota')::date - CAST(? AS integer), desp_documento_siesa = NULL
        WHERE desp_id NOT IN (-1, -2, -3, -4)");
    $stmt->execute([$id, $galones, $id === -3 ? 0 : 1]);
}
$db->exec("INSERT INTO control_acpm_historicos VALUES (-1);
    CREATE TRIGGER test_proteger_despacho BEFORE INSERT OR UPDATE OR DELETE ON despachos_acpm
        FOR EACH ROW EXECUTE FUNCTION public.control_acpm_proteger_despacho();
    CREATE TRIGGER test_conservar_cierre BEFORE UPDATE OR DELETE ON control_acpm_cierres
        FOR EACH ROW EXECUTE FUNCTION public.control_acpm_conservar_cierre()");
$op = new ControlAcpmOperativo($db);
$hoy = $op->hoy();
$ayer = $db->query("SELECT (CURRENT_TIMESTAMP AT TIME ZONE 'America/Bogota')::date - 1")->fetchColumn();
$r = $op->resumen($hoy);
verificarOperativo((float) $r['saldo_final'] === 1000.38, 'Opening minus both days, historical excluded');
$db->exec("UPDATE despachos_acpm SET desp_documento_siesa = 'DOC-30' WHERE desp_id = -3");
verificarOperativo((float) $op->resumen($hoy)['saldo_final'] === 1000.38, 'Document association must not restore fuel');
rechazarOperativo(function () use ($op, $hoy, $usuario) { $op->cerrar($hoy, $usuario, $op->resumen($hoy)['firma']); }, 'Must not skip yesterday');
$cierre = $op->resumen($ayer);
$op->cerrar($ayer, $usuario, $cierre['firma']);
verificarOperativo((float) $op->resumen($hoy)['apertura'] === (float) $cierre['saldo_final'], 'Next day opens with previous close');
rechazarOperativo(function () use ($db) { $db->exec('UPDATE despachos_acpm SET desp_galones = 25 WHERE desp_id = -2'); }, 'Closed dispatch must not change');
rechazarOperativo(function () use ($db) { $db->exec('UPDATE despachos_acpm SET desp_estado = 1 WHERE desp_id = -2'); }, 'Closed dispatch must not be cancelled');
rechazarOperativo(function () use ($db) { $db->exec('DELETE FROM despachos_acpm WHERE desp_id = -2'); }, 'Closed dispatch must not be deleted');
rechazarOperativo(function () use ($db) { $db->exec('DELETE FROM control_acpm_cierres'); }, 'Close must remain immutable');
$db->exec("UPDATE despachos_acpm SET desp_documento_siesa = 'DOC-20-AFTER-CLOSE' WHERE desp_id = -2");
verificarOperativo((float) $op->resumen($hoy)['saldo_final'] === 1000.38, 'Late document keeps closing balance unchanged');
verificarOperativo((int) $db->query('SELECT pendientes_documento FROM control_acpm_cierres')->fetchColumn() === 1, 'Pending count is a closing snapshot');
$anterior = $op->resumen($hoy);
$db->exec("UPDATE despachos_acpm SET desp_galones = 5,
    desp_fech = (CURRENT_TIMESTAMP AT TIME ZONE 'America/Bogota')::date WHERE desp_id = -4");
rechazarOperativo(function () use ($op, $hoy, $usuario, $anterior) { $op->cerrar($hoy, $usuario, $anterior['firma']); }, 'Stale preview cannot close');
verificarOperativo((float) $op->resumen($hoy)['saldo_final'] === 995.38, 'Authorization completed after cut counts once');
$op->cerrar($hoy, $usuario, $op->resumen($hoy)['firma']);
verificarOperativo($op->estado()['proximo_cierre'] === null, 'No double close or future close');
$manana = (new DateTimeImmutable($hoy))->modify('+1 day')->format('Y-m-d');
$siguiente = $op->resumen($manana);
verificarOperativo((float) $siguiente['apertura'] === 995.38 && (float) $siguiente['saldo_final'] === 995.38, 'Saved closing balance must become next opening without subtracting earlier dispatches again');
verificarOperativo($siguiente['origen_apertura'] === 'cierre' && $siguiente['fecha_base'] === $hoy, 'Opening must explicitly reference the last saved close');
echo "All operational balance, document, saved opening, rollover and closing protection checks passed. Application data unchanged.\n";
