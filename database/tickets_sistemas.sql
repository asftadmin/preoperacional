BEGIN;

CREATE SEQUENCE IF NOT EXISTS tickets_sistemas_consecutivo_seq START WITH 1;

CREATE TABLE IF NOT EXISTS tickets_sistemas_categorias (
    categoria_id SMALLSERIAL PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS tickets_sistemas (
    ticket_id BIGSERIAL PRIMARY KEY,
    ticket_numero VARCHAR(25) NOT NULL UNIQUE,
    empleado_documento VARCHAR(20) NOT NULL,
    empleado_nombre VARCHAR(150) NOT NULL,
    empleado_correo VARCHAR(150),
    empleado_cargo VARCHAR(120),
    empleado_area VARCHAR(120),
    tipo VARCHAR(20) NOT NULL,
    categoria_id SMALLINT NOT NULL REFERENCES tickets_sistemas_categorias(categoria_id),
    asunto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad VARCHAR(10) NOT NULL DEFAULT 'MEDIA',
    canal VARCHAR(20) NOT NULL,
    ubicacion VARCHAR(150),
    equipo VARCHAR(150),
    estado VARCHAR(20) NOT NULL DEFAULT 'ABIERTO',
    responsable_id INTEGER REFERENCES usuarios(user_id) ON DELETE SET NULL,
    solucion TEXT,
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP WITHOUT TIME ZONE,
    CONSTRAINT ck_tickets_sistemas_documento CHECK (empleado_documento ~ '^[0-9]+$'),
    CONSTRAINT ck_tickets_sistemas_tipo CHECK (tipo IN ('SOLICITUD', 'INCIDENTE', 'REQUERIMIENTO')),
    CONSTRAINT ck_tickets_sistemas_prioridad CHECK (prioridad IN ('BAJA', 'MEDIA', 'ALTA', 'CRITICA')),
    CONSTRAINT ck_tickets_sistemas_canal CHECK (canal IN ('LLAMADA', 'CORREO', 'MENSAJE', 'PRESENCIAL', 'SISTEMAS')),
    CONSTRAINT ck_tickets_sistemas_estado CHECK (estado IN ('ABIERTO', 'EN_PROCESO', 'EN_ESPERA', 'RESUELTO', 'CERRADO', 'CANCELADO'))
);

CREATE TABLE IF NOT EXISTS tickets_sistemas_seguimientos (
    seguimiento_id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets_sistemas(ticket_id) ON DELETE CASCADE,
    tipo VARCHAR(20) NOT NULL DEFAULT 'COMENTARIO',
    comentario TEXT NOT NULL,
    estado_anterior VARCHAR(20),
    estado_nuevo VARCHAR(20),
    responsable_id INTEGER REFERENCES usuarios(user_id) ON DELETE SET NULL,
    fecha_creacion TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ck_tickets_sistemas_seguimiento_tipo CHECK (tipo IN ('CREACION', 'COMENTARIO', 'GESTION', 'CIERRE'))
);

CREATE INDEX IF NOT EXISTS idx_tickets_sistemas_documento ON tickets_sistemas(empleado_documento);
CREATE INDEX IF NOT EXISTS idx_tickets_sistemas_estado ON tickets_sistemas(estado);
CREATE INDEX IF NOT EXISTS idx_tickets_sistemas_responsable ON tickets_sistemas(responsable_id);
CREATE INDEX IF NOT EXISTS idx_tickets_sistemas_fecha ON tickets_sistemas(fecha_creacion DESC);
CREATE INDEX IF NOT EXISTS idx_tickets_sistemas_seguimientos_ticket ON tickets_sistemas_seguimientos(ticket_id, fecha_creacion DESC);

INSERT INTO tickets_sistemas_categorias (nombre) VALUES
    ('Accesos y contraseñas'), ('Correo electrónico'), ('Hardware'),
    ('Impresoras y periféricos'), ('Red e internet'),
    ('Software y aplicaciones'), ('Telefonía'), ('Otro')
ON CONFLICT (nombre) DO NOTHING;

DO $$
DECLARE nuevo_menu_id INTEGER;
BEGIN
    SELECT menu_id INTO nuevo_menu_id FROM menu
    WHERE menu_identi = 'ticketsSistemas' ORDER BY menu_id LIMIT 1;
    IF nuevo_menu_id IS NULL THEN
        INSERT INTO menu (menu_nom, menu_ruta, menu_estado, menu_icono, menu_identi, menu_grupo)
        VALUES ('Mesa de servicio', '../TicketsSistemas/tickets.php', 1, 'nav-icon fas fa-headset', 'ticketsSistemas', NULL)
        RETURNING menu_id INTO nuevo_menu_id;
    END IF;
    IF EXISTS (SELECT 1 FROM roles WHERE rol_id = 4)
       AND NOT EXISTS (SELECT 1 FROM permiso WHERE permiso_menu = nuevo_menu_id AND permiso_rol = 4) THEN
        INSERT INTO permiso (permiso_menu, permiso_rol, permiso, permiso_estado)
        VALUES (nuevo_menu_id, 4, 'Si', 1);
    END IF;
END $$;

COMMIT;
