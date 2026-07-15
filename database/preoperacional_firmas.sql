CREATE TABLE IF NOT EXISTS preoperacional_firmas (
    firma_id SERIAL PRIMARY KEY,
    pre_formulario VARCHAR(100) NOT NULL UNIQUE,
    pre_user INTEGER NOT NULL,
    pre_firma TEXT NOT NULL,
    firma_fecha TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_preoperacional_firmas_pre_user
    ON preoperacional_firmas (pre_user);

