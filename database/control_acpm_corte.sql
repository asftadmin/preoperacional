-- Corte inicial autorizado: los despachos ya realizados quedan históricos.
-- Ejecutar una sola vez en la misma base de datos de la aplicación.
-- Reejecutar conserva el corte original y no incorpora nuevos históricos.
BEGIN;

LOCK TABLE despachos_acpm IN SHARE ROW EXCLUSIVE MODE;

CREATE TABLE IF NOT EXISTS control_acpm_corte (
    corte_id INTEGER PRIMARY KEY CHECK (corte_id = 1),
    fecha_corte TIMESTAMPTZ NOT NULL,
    saldo_apertura NUMERIC(14, 2) NOT NULL,
    observacion TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS control_acpm_historicos (
    desp_id BIGINT PRIMARY KEY,
    corte_id INTEGER NOT NULL REFERENCES control_acpm_corte(corte_id),
    galones_corte NUMERIC NOT NULL
);

WITH nuevo_corte AS (
    INSERT INTO control_acpm_corte
        (corte_id, fecha_corte, saldo_apertura, observacion)
    VALUES (1, clock_timestamp(), 1000.38,
        'Corte desde este momento. Los despachos existentes se conservan sin conciliación; solo los posteriores afectan la disponibilidad.')
    ON CONFLICT (corte_id) DO NOTHING
    RETURNING corte_id
)
INSERT INTO control_acpm_historicos (desp_id, corte_id, galones_corte)
SELECT d.desp_id, c.corte_id, d.desp_galones
FROM despachos_acpm d CROSS JOIN nuevo_corte c
WHERE d.desp_galones > 0;

COMMIT;
