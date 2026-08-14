BEGIN;

-- Catálogos propios del inventario de cómputo.
CREATE TABLE IF NOT EXISTS inventario_tipos_equipo (
    tipo_equipo_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_estados_equipo (
    estado_equipo_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    permite_asignacion BOOLEAN NOT NULL DEFAULT FALSE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_ubicaciones (
    ubicacion_id SMALLSERIAL PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE,
    sede VARCHAR(120),
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_tipos_componente (
    tipo_componente_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_estados_componente (
    estado_componente_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_motivos_devolucion (
    motivo_devolucion_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(40) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_estados_recepcion (
    estado_recepcion_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    genera_novedad BOOLEAN NOT NULL DEFAULT FALSE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_estados_verificacion_serial (
    estado_verificacion_serial_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    genera_novedad BOOLEAN NOT NULL DEFAULT FALSE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS inventario_tipos_mantenimiento (
    tipo_mantenimiento_id SMALLSERIAL PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

-- Equipo ofimático principal. No tiene relación con la tabla vehiculos.
CREATE TABLE IF NOT EXISTS inventario_equipos (
    equipo_id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    tipo_equipo_id SMALLINT NOT NULL REFERENCES inventario_tipos_equipo(tipo_equipo_id),
    estado_equipo_id SMALLINT NOT NULL REFERENCES inventario_estados_equipo(estado_equipo_id),
    ubicacion_id SMALLINT NOT NULL REFERENCES inventario_ubicaciones(ubicacion_id),
    custodio_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    codigo_siesa VARCHAR(60),
    marca VARCHAR(80) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    serial VARCHAR(120) NOT NULL,
    disco_duro VARCHAR(120),
    ram VARCHAR(80),
    procesador VARCHAR(150),
    serial_cargador VARCHAR(120),
    mac_wlan VARCHAR(50),
    mac_lan VARCHAR(50),
    serial_teclado VARCHAR(120),
    serial_mouse VARCHAR(120),
    sistema_operativo VARCHAR(120),
    licencia_windows VARCHAR(180),
    office VARCHAR(120),
    licencia_office VARCHAR(180),
    fecha_mantenimiento DATE,
    observaciones TEXT,
    fecha_adquisicion DATE,
    codigo_activo_fijo VARCHAR(60),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    creado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    actualizado_por INTEGER REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_equipos_serial
    ON inventario_equipos (LOWER(BTRIM(serial)));
CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_equipos_codigo_siesa
    ON inventario_equipos (LOWER(BTRIM(codigo_siesa)))
    WHERE codigo_siesa IS NOT NULL AND BTRIM(codigo_siesa) <> '';
CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_equipos_activo_fijo
    ON inventario_equipos (LOWER(BTRIM(codigo_activo_fijo)))
    WHERE codigo_activo_fijo IS NOT NULL AND BTRIM(codigo_activo_fijo) <> '';
CREATE INDEX IF NOT EXISTS idx_inventario_equipos_estado ON inventario_equipos(estado_equipo_id);
CREATE INDEX IF NOT EXISTS idx_inventario_equipos_ubicacion ON inventario_equipos(ubicacion_id);
CREATE INDEX IF NOT EXISTS idx_inventario_equipos_custodio ON inventario_equipos(custodio_id);

-- Accesorios individualizados relacionados con el equipo principal.
CREATE TABLE IF NOT EXISTS inventario_componentes (
    componente_id BIGSERIAL PRIMARY KEY,
    equipo_id BIGINT NOT NULL REFERENCES inventario_equipos(equipo_id) ON DELETE RESTRICT,
    tipo_componente_id SMALLINT NOT NULL REFERENCES inventario_tipos_componente(tipo_componente_id),
    estado_componente_id SMALLINT NOT NULL REFERENCES inventario_estados_componente(estado_componente_id),
    marca VARCHAR(80),
    modelo VARCHAR(100),
    serial VARCHAR(120),
    observacion TEXT,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    creado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_componentes_serial
    ON inventario_componentes (LOWER(BTRIM(serial)))
    WHERE serial IS NOT NULL AND BTRIM(serial) <> '';
CREATE INDEX IF NOT EXISTS idx_inventario_componentes_equipo
    ON inventario_componentes(equipo_id, activo);

-- Entrega del equipo a un empleado consultado en la API institucional.
CREATE TABLE IF NOT EXISTS inventario_asignaciones (
    asignacion_id BIGSERIAL PRIMARY KEY,
    equipo_id BIGINT NOT NULL REFERENCES inventario_equipos(equipo_id) ON DELETE RESTRICT,
    empleado_documento VARCHAR(20) NOT NULL,
    empleado_nombre VARCHAR(180) NOT NULL,
    empleado_correo VARCHAR(180),
    empleado_cargo VARCHAR(150),
    empleado_area VARCHAR(150),
    jefe_nombre VARCHAR(180),
    jefe_cargo VARCHAR(150),
    funcionario_entrega_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_entrega DATE NOT NULL,
    diagnostico_entrega TEXT NOT NULL,
    datos_empleado_verificados BOOLEAN NOT NULL,
    equipo_fisico_verificado BOOLEAN NOT NULL,
    seriales_verificados BOOLEAN NOT NULL,
    componentes_entregados_verificados BOOLEAN NOT NULL,
    diagnostico_diligenciado BOOLEAN NOT NULL,
    software_verificado BOOLEAN NOT NULL,
    software_no_aplica BOOLEAN NOT NULL DEFAULT FALSE,
    estado VARCHAR(15) NOT NULL DEFAULT 'ACTIVA',
    fecha_cierre TIMESTAMP WITHOUT TIME ZONE,
    creado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ck_inventario_asignaciones_documento CHECK (empleado_documento ~ '^[0-9]+$'),
    CONSTRAINT ck_inventario_asignaciones_estado CHECK (estado IN ('ACTIVA', 'CERRADA')),
    CONSTRAINT ck_inventario_asignaciones_confirmaciones CHECK (
        datos_empleado_verificados AND equipo_fisico_verificado AND seriales_verificados
        AND componentes_entregados_verificados AND diagnostico_diligenciado AND software_verificado
    )
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_asignacion_activa
    ON inventario_asignaciones(equipo_id) WHERE estado = 'ACTIVA';
CREATE INDEX IF NOT EXISTS idx_inventario_asignaciones_empleado
    ON inventario_asignaciones(empleado_documento);

-- Instantánea de cada elemento entregado, incluido el equipo principal.
CREATE TABLE IF NOT EXISTS inventario_asignacion_componentes (
    asignacion_componente_id BIGSERIAL PRIMARY KEY,
    asignacion_id BIGINT NOT NULL REFERENCES inventario_asignaciones(asignacion_id) ON DELETE RESTRICT,
    componente_id BIGINT REFERENCES inventario_componentes(componente_id) ON DELETE RESTRICT,
    es_equipo_principal BOOLEAN NOT NULL DEFAULT FALSE,
    tipo VARCHAR(80) NOT NULL,
    marca VARCHAR(80),
    modelo VARCHAR(100),
    serial_original VARCHAR(120),
    entregado BOOLEAN NOT NULL DEFAULT TRUE,
    observacion TEXT,
    CONSTRAINT ck_inventario_asignacion_elemento CHECK (
        (es_equipo_principal AND componente_id IS NULL)
        OR (NOT es_equipo_principal AND componente_id IS NOT NULL)
    )
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_asignacion_principal
    ON inventario_asignacion_componentes(asignacion_id) WHERE es_equipo_principal;
CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_asignacion_componente
    ON inventario_asignacion_componentes(asignacion_id, componente_id)
    WHERE componente_id IS NOT NULL;

CREATE TABLE IF NOT EXISTS inventario_asignacion_software (
    asignacion_software_id BIGSERIAL PRIMARY KEY,
    asignacion_id BIGINT NOT NULL REFERENCES inventario_asignaciones(asignacion_id) ON DELETE RESTRICT,
    nombre VARCHAR(150) NOT NULL,
    version VARCHAR(80),
    licencia VARCHAR(180),
    observacion TEXT
);

-- Recepción y verificación del equipo devuelto.
CREATE TABLE IF NOT EXISTS inventario_devoluciones (
    devolucion_id BIGSERIAL PRIMARY KEY,
    asignacion_id BIGINT NOT NULL UNIQUE REFERENCES inventario_asignaciones(asignacion_id) ON DELETE RESTRICT,
    motivo_devolucion_id SMALLINT NOT NULL REFERENCES inventario_motivos_devolucion(motivo_devolucion_id),
    motivo_otro VARCHAR(250),
    fecha_devolucion DATE NOT NULL,
    funcionario_recibe_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    custodio_posterior_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    ubicacion_posterior_id SMALLINT NOT NULL REFERENCES inventario_ubicaciones(ubicacion_id),
    estado_resultante_id SMALLINT NOT NULL REFERENCES inventario_estados_equipo(estado_equipo_id),
    diagnostico_devolucion TEXT NOT NULL,
    novedades TEXT,
    confirmacion_general BOOLEAN NOT NULL,
    completa BOOLEAN NOT NULL,
    cerrada BOOLEAN NOT NULL DEFAULT TRUE,
    creado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ck_inventario_devolucion_confirmacion CHECK (confirmacion_general),
    CONSTRAINT ck_inventario_devolucion_otro CHECK (
        motivo_otro IS NULL OR BTRIM(motivo_otro) <> ''
    )
);

CREATE TABLE IF NOT EXISTS inventario_devolucion_componentes (
    devolucion_componente_id BIGSERIAL PRIMARY KEY,
    devolucion_id BIGINT NOT NULL REFERENCES inventario_devoluciones(devolucion_id) ON DELETE RESTRICT,
    asignacion_componente_id BIGINT NOT NULL REFERENCES inventario_asignacion_componentes(asignacion_componente_id) ON DELETE RESTRICT,
    estado_recepcion_id SMALLINT NOT NULL REFERENCES inventario_estados_recepcion(estado_recepcion_id),
    estado_verificacion_serial_id SMALLINT NOT NULL REFERENCES inventario_estados_verificacion_serial(estado_verificacion_serial_id),
    serial_original VARCHAR(120),
    serial_recibido VARCHAR(120),
    observacion TEXT,
    tiene_novedad BOOLEAN NOT NULL DEFAULT FALSE,
    UNIQUE (devolucion_id, asignacion_componente_id)
);

CREATE TABLE IF NOT EXISTS inventario_revision_devolucion (
    revision_devolucion_id BIGSERIAL PRIMARY KEY,
    devolucion_id BIGINT NOT NULL UNIQUE REFERENCES inventario_devoluciones(devolucion_id) ON DELETE RESTRICT,
    estado_fisico VARCHAR(15) NOT NULL,
    encendido VARCHAR(15) NOT NULL,
    funcionamiento VARCHAR(15) NOT NULL,
    pantalla VARCHAR(15) NOT NULL,
    bateria VARCHAR(15) NOT NULL,
    teclado VARCHAR(15) NOT NULL,
    mouse VARCHAR(15) NOT NULL,
    cargador VARCHAR(15) NOT NULL,
    puertos VARCHAR(15) NOT NULL,
    conectividad VARCHAR(15) NOT NULL,
    limpieza VARCHAR(15) NOT NULL,
    danos_visibles TEXT,
    elementos_faltantes TEXT,
    observacion TEXT,
    CONSTRAINT ck_inventario_revision_estado_fisico CHECK (estado_fisico IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_encendido CHECK (encendido IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_funcionamiento CHECK (funcionamiento IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_pantalla CHECK (pantalla IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_bateria CHECK (bateria IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_teclado CHECK (teclado IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_mouse CHECK (mouse IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_cargador CHECK (cargador IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_puertos CHECK (puertos IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_conectividad CHECK (conectividad IN ('BUENO', 'NOVEDAD', 'NO_APLICA')),
    CONSTRAINT ck_inventario_revision_limpieza CHECK (limpieza IN ('BUENO', 'NOVEDAD', 'NO_APLICA'))
);

-- Custodia no equivale a una asignación laboral.
CREATE TABLE IF NOT EXISTS inventario_custodias (
    custodia_id BIGSERIAL PRIMARY KEY,
    equipo_id BIGINT NOT NULL REFERENCES inventario_equipos(equipo_id) ON DELETE RESTRICT,
    custodio_anterior_id INTEGER REFERENCES usuarios(user_id),
    custodio_nuevo_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    ubicacion_id SMALLINT NOT NULL REFERENCES inventario_ubicaciones(ubicacion_id),
    estado_equipo_id SMALLINT NOT NULL REFERENCES inventario_estados_equipo(estado_equipo_id),
    funcionario_recibe_id INTEGER REFERENCES usuarios(user_id),
    motivo VARCHAR(180) NOT NULL,
    observaciones TEXT,
    creado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_inventario_custodias_equipo
    ON inventario_custodias(equipo_id, fecha_creacion DESC);

-- Bitácora inmutable para la trazabilidad individual.
CREATE TABLE IF NOT EXISTS inventario_movimientos (
    movimiento_id BIGSERIAL PRIMARY KEY,
    equipo_id BIGINT NOT NULL REFERENCES inventario_equipos(equipo_id) ON DELETE RESTRICT,
    tipo_movimiento VARCHAR(40) NOT NULL,
    descripcion TEXT NOT NULL,
    responsable_anterior_id INTEGER REFERENCES usuarios(user_id),
    responsable_nuevo_id INTEGER REFERENCES usuarios(user_id),
    datos_anteriores JSONB,
    datos_nuevos JSONB,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_inventario_movimientos_equipo
    ON inventario_movimientos(equipo_id, fecha_creacion DESC, movimiento_id DESC);

CREATE TABLE IF NOT EXISTS inventario_actas (
    acta_id BIGSERIAL PRIMARY KEY,
    asignacion_id BIGINT NOT NULL REFERENCES inventario_asignaciones(asignacion_id) ON DELETE RESTRICT,
    devolucion_id BIGINT REFERENCES inventario_devoluciones(devolucion_id) ON DELETE RESTRICT,
    tipo VARCHAR(12) NOT NULL,
    numero VARCHAR(40) NOT NULL UNIQUE,
    nombre_archivo VARCHAR(220) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    hash_sha256 CHAR(64) NOT NULL,
    generado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_generacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ck_inventario_acta_tipo CHECK (tipo IN ('ENTREGA', 'DEVOLUCION')),
    CONSTRAINT ck_inventario_acta_relacion CHECK (
        (tipo = 'ENTREGA' AND devolucion_id IS NULL)
        OR (tipo = 'DEVOLUCION' AND devolucion_id IS NOT NULL)
    ),
    UNIQUE (asignacion_id, tipo)
);

-- Mantenimiento exclusivo de equipos de cómputo.
CREATE TABLE IF NOT EXISTS inventario_mantenimientos (
    mantenimiento_id BIGSERIAL PRIMARY KEY,
    equipo_id BIGINT NOT NULL REFERENCES inventario_equipos(equipo_id) ON DELETE RESTRICT,
    tipo_mantenimiento_id SMALLINT NOT NULL REFERENCES inventario_tipos_mantenimiento(tipo_mantenimiento_id),
    fecha DATE NOT NULL,
    responsable_id INTEGER NOT NULL REFERENCES usuarios(user_id),
    diagnostico TEXT NOT NULL,
    actividad_realizada TEXT NOT NULL,
    observaciones TEXT,
    estado_resultante_id SMALLINT NOT NULL REFERENCES inventario_estados_equipo(estado_equipo_id),
    proxima_fecha DATE,
    creado_por INTEGER NOT NULL REFERENCES usuarios(user_id),
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inventario_mantenimiento_repuestos (
    mantenimiento_repuesto_id BIGSERIAL PRIMARY KEY,
    mantenimiento_id BIGINT NOT NULL REFERENCES inventario_mantenimientos(mantenimiento_id) ON DELETE RESTRICT,
    descripcion VARCHAR(180) NOT NULL,
    referencia VARCHAR(120),
    serial VARCHAR(120),
    cantidad NUMERIC(12,2) NOT NULL DEFAULT 1,
    observacion TEXT,
    CONSTRAINT ck_inventario_repuesto_cantidad CHECK (cantidad > 0)
);

CREATE INDEX IF NOT EXISTS idx_inventario_mantenimientos_equipo
    ON inventario_mantenimientos(equipo_id, fecha DESC);

-- Catálogos iniciales derivados de los requisitos aprobados.
INSERT INTO inventario_tipos_equipo (codigo, nombre) VALUES
    ('PORTATIL', 'Portátil'), ('ESCRITORIO', 'Equipo de escritorio'),
    ('TODO_EN_UNO', 'Todo en uno'), ('TABLETA', 'Tableta'), ('OTRO', 'Otro')
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_estados_equipo (codigo, nombre, permite_asignacion) VALUES
    ('DISPONIBLE', 'Disponible', TRUE), ('ASIGNADO', 'Asignado', FALSE),
    ('EN_REVISION', 'En revisión', FALSE), ('EN_MANTENIMIENTO', 'En mantenimiento', FALSE),
    ('PENDIENTE_NOVEDAD', 'Pendiente por novedad', FALSE),
    ('INCOMPLETO', 'Incompleto', FALSE), ('DADO_DE_BAJA', 'Dado de baja', FALSE)
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_ubicaciones (nombre) VALUES ('Sistemas'), ('Almacén')
ON CONFLICT (nombre) DO NOTHING;

INSERT INTO inventario_tipos_componente (codigo, nombre) VALUES
    ('CARGADOR', 'Cargador'), ('TECLADO', 'Teclado'), ('MOUSE', 'Mouse'),
    ('MONITOR', 'Monitor'), ('BASE', 'Base'), ('MALETIN', 'Maletín'),
    ('ADAPTADOR', 'Adaptador'), ('OTRO', 'Otro accesorio')
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_estados_componente (codigo, nombre) VALUES
    ('BUENO', 'Buen estado'), ('CON_NOVEDAD', 'Con novedad'),
    ('EN_REVISION', 'En revisión'), ('DADO_DE_BAJA', 'Dado de baja')
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_motivos_devolucion (codigo, nombre) VALUES
    ('TERMINACION_CONTRATO', 'Terminación del contrato'), ('CAMBIO_EQUIPO', 'Cambio de equipo'),
    ('CAMBIO_CARGO', 'Cambio de cargo'), ('CAMBIO_SEDE', 'Cambio de sede'),
    ('MANTENIMIENTO', 'Mantenimiento'), ('SOLICITUD_EMPLEADOR', 'Solicitud del empleador'),
    ('OTRO', 'Otro')
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_estados_recepcion (codigo, nombre, genera_novedad) VALUES
    ('BUEN_ESTADO', 'Devuelto en buen estado', FALSE),
    ('CON_NOVEDAD', 'Devuelto con novedad', TRUE),
    ('NO_DEVUELTO', 'No devuelto', TRUE), ('NO_APLICA', 'No aplica', FALSE)
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_estados_verificacion_serial (codigo, nombre, genera_novedad) VALUES
    ('COINCIDE', 'Coincide', FALSE), ('DIFERENTE', 'Diferente', TRUE),
    ('ILEGIBLE', 'Ilegible', TRUE), ('SIN_SERIAL', 'Sin serial', TRUE),
    ('NO_APLICA', 'No aplica', FALSE)
ON CONFLICT (codigo) DO NOTHING;

INSERT INTO inventario_tipos_mantenimiento (codigo, nombre) VALUES
    ('PREVENTIVO', 'Preventivo'), ('CORRECTIVO', 'Correctivo'),
    ('DIAGNOSTICO', 'Diagnóstico'), ('MEJORA', 'Mejora')
ON CONFLICT (codigo) DO NOTHING;

-- Entrada de menú y permisos iniciales para Administrador, Almacén y Coordinador Mtto.
DO $$
DECLARE
    nuevo_menu_id INTEGER;
    rol_destino INTEGER;
BEGIN
    SELECT menu_id INTO nuevo_menu_id
    FROM menu WHERE menu_identi = 'inventarioEquipos' ORDER BY menu_id LIMIT 1;

    IF nuevo_menu_id IS NULL THEN
        INSERT INTO menu (menu_nom, menu_ruta, menu_estado, menu_icono, menu_identi, menu_grupo)
        VALUES ('Inventario de cómputo', '../InventarioEquipos/inventario.php', 1,
                'nav-icon fas fa-laptop', 'inventarioEquipos', NULL)
        RETURNING menu_id INTO nuevo_menu_id;
    END IF;

    FOREACH rol_destino IN ARRAY ARRAY[4, 9, 14]
    LOOP
        IF EXISTS (SELECT 1 FROM roles WHERE rol_id = rol_destino)
           AND NOT EXISTS (
               SELECT 1 FROM permiso
               WHERE permiso_menu = nuevo_menu_id AND permiso_rol = rol_destino
           ) THEN
            INSERT INTO permiso (permiso_menu, permiso_rol, permiso, permiso_estado)
            VALUES (nuevo_menu_id, rol_destino, 'Si', 1);
        END IF;
    END LOOP;
END $$;

COMMIT;
