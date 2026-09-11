-- ============================================================
-- MODULO: TRAZABILIDAD DE MEZCLA EN OBRA
-- FORMATO: CT-F-21
-- MOTOR: PostgreSQL
-- CONVENCION:
--   snake_case
--   campos finalizan con nombre de tabla o abreviatura
-- ============================================================

BEGIN;

-- ============================================================
-- 1. ENCABEZADO DEL FORMATO
-- Tabla: trazabilidad_obra
-- Sufijo de campos: _trazabilidad
-- ============================================================

CREATE TABLE IF NOT EXISTS public.trazabilidad_obra (
    id_trazabilidad SERIAL PRIMARY KEY,
    obra_trazabilidad INTEGER NOT NULL,
    usuario_trazabilidad INTEGER NOT NULL,
    tipo_mezcla_trazabilidad VARCHAR(10) NOT NULL,
    fecha_trazabilidad DATE NOT NULL,
    tipo_actividad_trazabilidad VARCHAR(20) NOT NULL,
    estado_trazabilidad INTEGER NOT NULL DEFAULT 1,
    observaciones_trazabilidad TEXT,
    fecha_creacion_trazabilidad TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    fecha_actualizacion_trazabilidad TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    fecha_envio_trazabilidad TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT fk_trazabilidad_obra
        FOREIGN KEY (obra_trazabilidad)
        REFERENCES public.obras(obras_id)
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,

    CONSTRAINT fk_trazabilidad_usuario
        FOREIGN KEY (usuario_trazabilidad)
        REFERENCES public.usuarios(user_id)
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,

    CONSTRAINT chk_tipo_mezcla_trazabilidad
        CHECK (
            tipo_mezcla_trazabilidad IN ('MDC-19', 'MDC-25')
        ),

    CONSTRAINT chk_tipo_actividad_trazabilidad
        CHECK (
            tipo_actividad_trazabilidad IN (
                'CONTINUA',
                'BACHEO',
                'PARCHEO'
            )
        ),

    CONSTRAINT chk_estado_trazabilidad
        CHECK (
            estado_trazabilidad IN (1, 2, 3, 4)
        )
);

COMMENT ON TABLE public.trazabilidad_obra IS
'Encabezado del formato CT-F-21 de trazabilidad de mezcla en obra.';

COMMENT ON COLUMN public.trazabilidad_obra.estado_trazabilidad IS
'1=BORRADOR, 2=PENDIENTE_APROBACION, 3=APROBADO, 4=RECHAZADO';


-- ============================================================
-- 2. DETALLE DE APLICACION
-- Tabla: trazabilidad_obra_detalle
-- Sufijo de campos: _traz_det
-- ============================================================

CREATE TABLE IF NOT EXISTS public.trazabilidad_obra_detalle (
    id_traz_det SERIAL PRIMARY KEY,
    trazabilidad_traz_det INTEGER NOT NULL,
    vehiculo_traz_det INTEGER NOT NULL,
    orden_traz_det INTEGER NOT NULL,

    pr_inicial_traz_det VARCHAR(100),
    pr_final_traz_det VARCHAR(100),
    numero_caja_traz_det INTEGER,

    longitud_traz_det NUMERIC(12,3),
    ancho_traz_det NUMERIC(12,3),
    espesor_demolido_traz_det NUMERIC(12,3),
    espesor_excavacion_traz_det NUMERIC(12,3),
    espesor_base_traz_det NUMERIC(12,3),
    espesor_mezcla_traz_det NUMERIC(12,3),

    volumen_demolido_traz_det NUMERIC(14,3),
    volumen_excavado_traz_det NUMERIC(14,3),
    volumen_base_traz_det NUMERIC(14,3),

    capas_imprimacion_traz_det INTEGER,
    imprimacion_traz_det NUMERIC(14,3),

    temperatura_aplicacion_traz_det NUMERIC(6,2),
    volumen_mezcla_traz_det NUMERIC(14,3),

    CONSTRAINT fk_detalle_trazabilidad
        FOREIGN KEY (trazabilidad_traz_det)
        REFERENCES public.trazabilidad_obra(id_trazabilidad)
        ON UPDATE NO ACTION
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_vehiculo
        FOREIGN KEY (vehiculo_traz_det)
        REFERENCES public.vehiculos(vehi_id)
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,

    CONSTRAINT chk_orden_traz_det
        CHECK (orden_traz_det > 0),

    CONSTRAINT chk_numero_caja_traz_det
        CHECK (
            numero_caja_traz_det IS NULL
            OR numero_caja_traz_det > 0
        ),

    CONSTRAINT chk_capas_imprimacion_traz_det
        CHECK (
            capas_imprimacion_traz_det IS NULL
            OR capas_imprimacion_traz_det > 0
        )
);

COMMENT ON TABLE public.trazabilidad_obra_detalle IS
'Detalle de aplicacion del formato CT-F-21. Contiene las casillas [1] a [16].';


-- ============================================================
-- 3. CONTROL DE LLEGADA DE MEZCLA
-- Tabla: trazabilidad_obra_llegada
-- Sufijo de campos: _traz_lleg
-- ============================================================

CREATE TABLE IF NOT EXISTS public.trazabilidad_obra_llegada (
    id_traz_lleg SERIAL PRIMARY KEY,
    trazabilidad_traz_lleg INTEGER NOT NULL,
    vehiculo_traz_lleg INTEGER NOT NULL,
    orden_traz_lleg INTEGER NOT NULL,

    temperatura_llegada_traz_lleg NUMERIC(6,2),
    volumen_llegada_traz_lleg NUMERIC(14,3),
    volumen_aplicado_traz_lleg NUMERIC(14,3),
    factor_compactacion_traz_lleg NUMERIC(12,6),

    CONSTRAINT fk_llegada_trazabilidad
        FOREIGN KEY (trazabilidad_traz_lleg)
        REFERENCES public.trazabilidad_obra(id_trazabilidad)
        ON UPDATE NO ACTION
        ON DELETE CASCADE,

    CONSTRAINT fk_llegada_vehiculo
        FOREIGN KEY (vehiculo_traz_lleg)
        REFERENCES public.vehiculos(vehi_id)
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,

    CONSTRAINT chk_orden_traz_lleg
        CHECK (orden_traz_lleg > 0)
);

COMMENT ON TABLE public.trazabilidad_obra_llegada IS
'Control de llegada de mezcla y factor de compactacion del formato CT-F-21.';


-- ============================================================
-- 4. APROBACION DEL FORMATO
-- Tabla: trazabilidad_obra_aprobacion
-- Sufijo de campos: _traz_apro
-- ============================================================

CREATE TABLE IF NOT EXISTS public.trazabilidad_obra_aprobacion (
    id_traz_apro SERIAL PRIMARY KEY,
    trazabilidad_traz_apro INTEGER NOT NULL,
    responsable_traz_apro INTEGER NOT NULL,
    estado_traz_apro INTEGER NOT NULL DEFAULT 1,
    fecha_asignacion_traz_apro TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    fecha_respuesta_traz_apro TIMESTAMP WITHOUT TIME ZONE,
    observaciones_traz_apro TEXT,

    CONSTRAINT fk_aprobacion_trazabilidad
        FOREIGN KEY (trazabilidad_traz_apro)
        REFERENCES public.trazabilidad_obra(id_trazabilidad)
        ON UPDATE NO ACTION
        ON DELETE CASCADE,

    CONSTRAINT fk_aprobacion_usuario
        FOREIGN KEY (responsable_traz_apro)
        REFERENCES public.usuarios(user_id)
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,

    CONSTRAINT chk_estado_traz_apro
        CHECK (
            estado_traz_apro IN (1, 2, 3)
        )
);

COMMENT ON TABLE public.trazabilidad_obra_aprobacion IS
'Gestion de aprobacion por residente o responsable de obra.';

COMMENT ON COLUMN public.trazabilidad_obra_aprobacion.estado_traz_apro IS
'1=PENDIENTE, 2=APROBADO, 3=RECHAZADO';


-- ============================================================
-- 5. INDICES
-- ============================================================

CREATE INDEX IF NOT EXISTS idx_obra_trazabilidad
    ON public.trazabilidad_obra (obra_trazabilidad);

CREATE INDEX IF NOT EXISTS idx_usuario_trazabilidad
    ON public.trazabilidad_obra (usuario_trazabilidad);

CREATE INDEX IF NOT EXISTS idx_fecha_trazabilidad
    ON public.trazabilidad_obra (fecha_trazabilidad);

CREATE INDEX IF NOT EXISTS idx_estado_trazabilidad
    ON public.trazabilidad_obra (estado_trazabilidad);

CREATE INDEX IF NOT EXISTS idx_trazabilidad_traz_det
    ON public.trazabilidad_obra_detalle (trazabilidad_traz_det);

CREATE INDEX IF NOT EXISTS idx_vehiculo_traz_det
    ON public.trazabilidad_obra_detalle (vehiculo_traz_det);

CREATE INDEX IF NOT EXISTS idx_trazabilidad_traz_lleg
    ON public.trazabilidad_obra_llegada (trazabilidad_traz_lleg);

CREATE INDEX IF NOT EXISTS idx_vehiculo_traz_lleg
    ON public.trazabilidad_obra_llegada (vehiculo_traz_lleg);

CREATE INDEX IF NOT EXISTS idx_trazabilidad_traz_apro
    ON public.trazabilidad_obra_aprobacion (trazabilidad_traz_apro);

CREATE INDEX IF NOT EXISTS idx_responsable_traz_apro
    ON public.trazabilidad_obra_aprobacion (responsable_traz_apro);

CREATE INDEX IF NOT EXISTS idx_estado_traz_apro
    ON public.trazabilidad_obra_aprobacion (estado_traz_apro);

COMMIT;

-- ============================================================
-- ESTADOS
-- ============================================================
-- trazabilidad_obra.estado_trazabilidad
-- 1 = BORRADOR
-- 2 = PENDIENTE_APROBACION
-- 3 = APROBADO
-- 4 = RECHAZADO
--
-- trazabilidad_obra_aprobacion.estado_traz_apro
-- 1 = PENDIENTE
-- 2 = APROBADO
-- 3 = RECHAZADO
-- ============================================================
