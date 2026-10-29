-- Aplicar despues de control_acpm_corte.sql. Conserva el corte existente.
BEGIN;
CREATE TABLE IF NOT EXISTS control_acpm_cierres (
    fecha DATE PRIMARY KEY,
    apertura NUMERIC NOT NULL,
    entradas NUMERIC NOT NULL,
    ajustes NUMERIC NOT NULL,
    despachado NUMERIC NOT NULL,
    saldo_final NUMERIC NOT NULL,
    pendientes_documento INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT clock_timestamp(),
    CHECK (saldo_final = apertura + entradas + ajustes - despachado)
);

-- Todas las escrituras fisicas y el cierre toman el mismo bloqueo.
-- Asociar un documento sigue permitido despues del cierre.
CREATE OR REPLACE FUNCTION control_acpm_proteger_despacho() RETURNS trigger AS $$
DECLARE limite DATE; inicio DATE;
BEGIN
    IF TG_OP = 'UPDATE' AND
       ROW(OLD.desp_id, OLD.desp_fech, OLD.desp_galones, OLD.desp_estado)
       IS NOT DISTINCT FROM ROW(NEW.desp_id, NEW.desp_fech, NEW.desp_galones, NEW.desp_estado) THEN
        RETURN NEW;
    END IF;
    SELECT (fecha_corte AT TIME ZONE 'America/Bogota')::date INTO inicio
        FROM control_acpm_corte WHERE corte_id = 1 FOR UPDATE;
    SELECT MAX(fecha) INTO limite FROM control_acpm_cierres;
    IF TG_OP <> 'INSERT' AND OLD.desp_galones > 0 AND OLD.desp_fech <= limite
       AND NOT EXISTS (SELECT 1 FROM control_acpm_historicos WHERE desp_id = OLD.desp_id) THEN
        RAISE EXCEPTION 'El despacho pertenece a un dia cerrado y no se puede modificar.';
    END IF;
    IF TG_OP <> 'DELETE' AND NEW.desp_galones > 0 AND NEW.desp_fech <= limite
       AND NOT EXISTS (SELECT 1 FROM control_acpm_historicos WHERE desp_id = NEW.desp_id) THEN
        RAISE EXCEPTION 'No se permiten despachos en un dia cerrado.';
    END IF;
    IF TG_OP <> 'DELETE' AND NEW.desp_galones > 0
       AND (NEW.desp_fech IS NULL OR NEW.desp_fech < inicio OR
            NEW.desp_fech > (CURRENT_TIMESTAMP AT TIME ZONE 'America/Bogota')::date)
       AND NOT EXISTS (SELECT 1 FROM control_acpm_historicos WHERE desp_id = NEW.desp_id) THEN
        RAISE EXCEPTION 'El despacho debe tener fecha desde el corte hasta hoy.';
    END IF;
    IF TG_OP = 'DELETE' THEN RETURN OLD; END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
DROP TRIGGER IF EXISTS control_acpm_proteger_despacho ON despachos_acpm;
CREATE TRIGGER control_acpm_proteger_despacho
BEFORE INSERT OR UPDATE OR DELETE ON despachos_acpm
FOR EACH ROW EXECUTE FUNCTION control_acpm_proteger_despacho();

CREATE OR REPLACE FUNCTION control_acpm_conservar_cierre() RETURNS trigger AS $$
BEGIN
    RAISE EXCEPTION 'El cierre es definitivo y no se modifica al conciliar documentos.';
END;
$$ LANGUAGE plpgsql;
DROP TRIGGER IF EXISTS control_acpm_conservar_cierre ON control_acpm_cierres;
CREATE TRIGGER control_acpm_conservar_cierre BEFORE UPDATE OR DELETE ON control_acpm_cierres
FOR EACH ROW EXECUTE FUNCTION control_acpm_conservar_cierre();
COMMIT;
