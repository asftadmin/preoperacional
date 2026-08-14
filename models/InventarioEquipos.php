<?php

/**
 * Modelo del inventario de equipos de cómputo.
 * Todo acceso SQL del módulo se concentra en esta clase.
 */
class InventarioEquipos extends Conectar
{
    private function db()
    {
        $conexion = parent::Conexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    }

    /**
     * Ejecuta una sentencia enlazando explícitamente booleanos.
     * PDO PostgreSQL convierte false en cadena vacía cuando se usa execute(array),
     * valor que PostgreSQL no reconoce como BOOLEAN.
     */
    private function ejecutarTipado(PDOStatement $sentencia, $parametros, $booleanos = array())
    {
        foreach ($parametros as $clave => $valor) {
            if (in_array($clave, $booleanos, true)) {
                $sentencia->bindValue($clave, (bool) $valor, PDO::PARAM_BOOL);
            } elseif ($valor === null) {
                $sentencia->bindValue($clave, null, PDO::PARAM_NULL);
            } elseif (is_int($valor)) {
                $sentencia->bindValue($clave, $valor, PDO::PARAM_INT);
            } else {
                $sentencia->bindValue($clave, $valor, PDO::PARAM_STR);
            }
        }
        return $sentencia->execute();
    }

    /** Comprueba el permiso del usuario contra el menú del módulo. */
    public function tieneAcceso($usuarioId)
    {
        $sql = "SELECT 1
                FROM usuarios u
                INNER JOIN permiso p ON p.permiso_rol = u.user_rol_usuario
                INNER JOIN menu m ON m.menu_id = p.permiso_menu
                WHERE u.user_id = :usuario
                  AND m.menu_identi = 'inventarioEquipos'
                  AND p.permiso = 'Si'
                  AND COALESCE(p.permiso_estado, 1) = 1
                LIMIT 1";
        $sentencia = $this->db()->prepare($sql);
        $sentencia->execute(array(':usuario' => $usuarioId));
        return (bool) $sentencia->fetchColumn();
    }

    /** Devuelve todos los catálogos requeridos por los formularios. */
    public function obtenerCatalogos()
    {
        $db = $this->db();
        $catalogos = array(
            'tipos_equipo' => 'SELECT tipo_equipo_id AS id, nombre, codigo FROM inventario_tipos_equipo WHERE activo ORDER BY nombre',
            'estados_equipo' => 'SELECT estado_equipo_id AS id, nombre, codigo, permite_asignacion FROM inventario_estados_equipo WHERE activo ORDER BY nombre',
            'ubicaciones' => "SELECT ubicacion_id AS id, nombre || COALESCE(' · ' || sede, '') AS nombre FROM inventario_ubicaciones WHERE activo ORDER BY nombre",
            'tipos_componente' => 'SELECT tipo_componente_id AS id, nombre, codigo FROM inventario_tipos_componente WHERE activo ORDER BY nombre',
            'estados_componente' => 'SELECT estado_componente_id AS id, nombre, codigo FROM inventario_estados_componente WHERE activo ORDER BY nombre',
            'motivos_devolucion' => 'SELECT motivo_devolucion_id AS id, nombre, codigo FROM inventario_motivos_devolucion WHERE activo ORDER BY nombre',
            'estados_recepcion' => 'SELECT estado_recepcion_id AS id, nombre, codigo, genera_novedad FROM inventario_estados_recepcion WHERE activo ORDER BY estado_recepcion_id',
            'estados_serial' => 'SELECT estado_verificacion_serial_id AS id, nombre, codigo, genera_novedad FROM inventario_estados_verificacion_serial WHERE activo ORDER BY estado_verificacion_serial_id',
            'tipos_mantenimiento' => 'SELECT tipo_mantenimiento_id AS id, nombre, codigo FROM inventario_tipos_mantenimiento WHERE activo ORDER BY nombre'
        );
        $resultado = array();
        foreach ($catalogos as $clave => $sql) {
            $resultado[$clave] = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        return $resultado;
    }

    /** Lista usuarios internos habilitados para entrega, recepción y custodia. */
    public function listarResponsables()
    {
        $sql = "SELECT u.user_id AS id,
                       TRIM(u.user_nombre || ' ' || u.user_apellidos) AS nombre,
                       r.rol_cargo AS cargo
                FROM usuarios u
                LEFT JOIN roles r ON r.rol_id = u.user_rol_usuario
                ORDER BY u.user_nombre, u.user_apellidos";
        return $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Verifica que una clave de catálogo activa exista. */
    private function existeActivo(PDO $db, $tabla, $campo, $valor)
    {
        $permitidos = array(
            'inventario_tipos_equipo' => 'tipo_equipo_id',
            'inventario_estados_equipo' => 'estado_equipo_id',
            'inventario_ubicaciones' => 'ubicacion_id',
            'inventario_tipos_componente' => 'tipo_componente_id',
            'inventario_estados_componente' => 'estado_componente_id',
            'inventario_motivos_devolucion' => 'motivo_devolucion_id',
            'inventario_estados_recepcion' => 'estado_recepcion_id',
            'inventario_estados_verificacion_serial' => 'estado_verificacion_serial_id',
            'inventario_tipos_mantenimiento' => 'tipo_mantenimiento_id'
        );
        if (!isset($permitidos[$tabla]) || $permitidos[$tabla] !== $campo) {
            return false;
        }
        $sql = "SELECT 1 FROM {$tabla} WHERE {$campo} = :id AND activo = TRUE";
        $sentencia = $db->prepare($sql);
        $sentencia->execute(array(':id' => $valor));
        return (bool) $sentencia->fetchColumn();
    }

    /** Consulta la bandeja con filtros combinables. */
    public function listarEquipos($filtros)
    {
        $condiciones = array('e.activo = TRUE');
        $parametros = array();
        $camposExactos = array(
            'estado_id' => 'e.estado_equipo_id',
            'tipo_id' => 'e.tipo_equipo_id',
            'ubicacion_id' => 'e.ubicacion_id',
            'responsable_id' => 'e.custodio_id'
        );
        foreach ($camposExactos as $filtro => $columna) {
            if (!empty($filtros[$filtro])) {
                $condiciones[] = $columna . ' = :' . $filtro;
                $parametros[':' . $filtro] = $filtros[$filtro];
            }
        }
        foreach (array('marca', 'serial', 'codigo_siesa', 'codigo_activo_fijo') as $campo) {
            if (!empty($filtros[$campo])) {
                $condiciones[] = 'e.' . $campo . ' ILIKE :' . $campo;
                $parametros[':' . $campo] = '%' . $filtros[$campo] . '%';
            }
        }
        foreach (array('adquisicion' => 'fecha_adquisicion', 'mantenimiento' => 'fecha_mantenimiento') as $prefijo => $columna) {
            if (!empty($filtros[$prefijo . '_desde'])) {
                $condiciones[] = 'e.' . $columna . ' >= :' . $prefijo . '_desde';
                $parametros[':' . $prefijo . '_desde'] = $filtros[$prefijo . '_desde'];
            }
            if (!empty($filtros[$prefijo . '_hasta'])) {
                $condiciones[] = 'e.' . $columna . ' <= :' . $prefijo . '_hasta';
                $parametros[':' . $prefijo . '_hasta'] = $filtros[$prefijo . '_hasta'];
            }
        }
        $sql = "SELECT e.equipo_id, e.nombre, te.nombre AS tipo, e.marca, e.modelo, e.serial,
                       e.codigo_siesa, e.codigo_activo_fijo, ee.nombre AS estado, ee.codigo AS estado_codigo,
                       iu.nombre AS ubicacion,
                       TRIM(u.user_nombre || ' ' || u.user_apellidos) AS responsable,
                       TO_CHAR(e.fecha_mantenimiento, 'YYYY-MM-DD') AS fecha_mantenimiento,
                       TO_CHAR(e.fecha_adquisicion, 'YYYY-MM-DD') AS fecha_adquisicion,
                       (SELECT a0.asignacion_id
                        FROM inventario_asignaciones a0
                        WHERE a0.equipo_id = e.equipo_id
                        ORDER BY a0.fecha_creacion DESC, a0.asignacion_id DESC LIMIT 1) AS ultima_asignacion_id,
                       (SELECT d0.devolucion_id
                        FROM inventario_devoluciones d0
                        INNER JOIN inventario_asignaciones a1 ON a1.asignacion_id = d0.asignacion_id
                        WHERE a1.equipo_id = e.equipo_id
                        ORDER BY d0.fecha_creacion DESC, d0.devolucion_id DESC LIMIT 1) AS ultima_devolucion_id,
                       EXISTS (
                           SELECT 1 FROM inventario_asignaciones a
                           WHERE a.equipo_id = e.equipo_id AND a.estado = 'ACTIVA'
                       ) AS asignacion_activa
                FROM inventario_equipos e
                INNER JOIN inventario_tipos_equipo te ON te.tipo_equipo_id = e.tipo_equipo_id
                INNER JOIN inventario_estados_equipo ee ON ee.estado_equipo_id = e.estado_equipo_id
                INNER JOIN inventario_ubicaciones iu ON iu.ubicacion_id = e.ubicacion_id
                INNER JOIN usuarios u ON u.user_id = e.custodio_id
                WHERE " . implode(' AND ', $condiciones) . "
                ORDER BY e.fecha_actualizacion DESC, e.equipo_id DESC";
        $sentencia = $this->db()->prepare($sql);
        $sentencia->execute($parametros);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Obtiene el equipo y todos sus accesorios activos. */
    public function obtenerEquipo($equipoId)
    {
        $db = $this->db();
        $sql = "SELECT e.*, te.nombre AS tipo, ee.nombre AS estado, ee.codigo AS estado_codigo,
                       iu.nombre AS ubicacion,
                       TRIM(u.user_nombre || ' ' || u.user_apellidos) AS custodio
                FROM inventario_equipos e
                INNER JOIN inventario_tipos_equipo te ON te.tipo_equipo_id = e.tipo_equipo_id
                INNER JOIN inventario_estados_equipo ee ON ee.estado_equipo_id = e.estado_equipo_id
                INNER JOIN inventario_ubicaciones iu ON iu.ubicacion_id = e.ubicacion_id
                INNER JOIN usuarios u ON u.user_id = e.custodio_id
                WHERE e.equipo_id = :equipo AND e.activo = TRUE";
        $sentencia = $db->prepare($sql);
        $sentencia->execute(array(':equipo' => $equipoId));
        $equipo = $sentencia->fetch(PDO::FETCH_ASSOC);
        if (!$equipo) {
            return null;
        }
        $componentes = $db->prepare(
            "SELECT c.*, tc.nombre AS tipo, ec.nombre AS estado
             FROM inventario_componentes c
             INNER JOIN inventario_tipos_componente tc ON tc.tipo_componente_id = c.tipo_componente_id
             INNER JOIN inventario_estados_componente ec ON ec.estado_componente_id = c.estado_componente_id
             WHERE c.equipo_id = :equipo AND c.activo = TRUE
             ORDER BY tc.nombre, c.componente_id"
        );
        $componentes->execute(array(':equipo' => $equipoId));
        $equipo['componentes'] = $componentes->fetchAll(PDO::FETCH_ASSOC);
        $actas = $db->prepare(
            "SELECT ac.acta_id, ac.asignacion_id, ac.tipo, ac.numero, ac.fecha_generacion
             FROM inventario_actas ac
             INNER JOIN inventario_asignaciones a ON a.asignacion_id = ac.asignacion_id
             WHERE a.equipo_id = :equipo
             ORDER BY ac.fecha_generacion DESC"
        );
        $actas->execute(array(':equipo' => $equipoId));
        $equipo['actas'] = $actas->fetchAll(PDO::FETCH_ASSOC);
        return $equipo;
    }

    /** Valida duplicados antes de insertar o actualizar. */
    private function validarDuplicados(PDO $db, $datos, $equipoId)
    {
        $campos = array(
            'serial' => 'serial',
            'codigo_siesa' => 'código SIESA',
            'codigo_activo_fijo' => 'código de activo fijo'
        );
        foreach ($campos as $campo => $etiqueta) {
            if (empty($datos[$campo])) {
                continue;
            }
            $sql = "SELECT 1 FROM inventario_equipos
                    WHERE LOWER(BTRIM({$campo})) = LOWER(BTRIM(:valor))
                      AND equipo_id <> :equipo LIMIT 1";
            $sentencia = $db->prepare($sql);
            $sentencia->execute(array(':valor' => $datos[$campo], ':equipo' => $equipoId ?: 0));
            if ($sentencia->fetchColumn()) {
                throw new RuntimeException('Ya existe un equipo con el mismo ' . $etiqueta . '.');
            }
        }
    }

    /** Registra o actualiza un equipo y deja trazabilidad del cambio. */
    public function guardarEquipo($equipoId, $datos, $componentes, $usuarioId)
    {
        $db = $this->db();
        $db->beginTransaction();
        try {
            foreach (array(
                array('inventario_tipos_equipo', 'tipo_equipo_id', $datos['tipo_equipo_id']),
                array('inventario_estados_equipo', 'estado_equipo_id', $datos['estado_equipo_id']),
                array('inventario_ubicaciones', 'ubicacion_id', $datos['ubicacion_id'])
            ) as $catalogo) {
                if (!$this->existeActivo($db, $catalogo[0], $catalogo[1], $catalogo[2])) {
                    throw new RuntimeException('Uno de los catálogos seleccionados no está disponible.');
                }
            }
            $usuario = $db->prepare('SELECT 1 FROM usuarios WHERE user_id = :id');
            $usuario->execute(array(':id' => $datos['custodio_id']));
            if (!$usuario->fetchColumn()) {
                throw new RuntimeException('El responsable de custodia no existe.');
            }
            $this->validarDuplicados($db, $datos, $equipoId);

            $columnas = array(
                'nombre', 'tipo_equipo_id', 'estado_equipo_id', 'ubicacion_id', 'custodio_id',
                'codigo_siesa', 'marca', 'modelo', 'serial', 'disco_duro', 'ram', 'procesador',
                'serial_cargador', 'mac_wlan', 'mac_lan', 'serial_teclado', 'serial_mouse',
                'sistema_operativo', 'licencia_windows', 'office', 'licencia_office',
                'fecha_mantenimiento', 'observaciones', 'fecha_adquisicion', 'codigo_activo_fijo'
            );
            $anteriores = null;
            if ($equipoId) {
                $bloqueo = $db->prepare('SELECT * FROM inventario_equipos WHERE equipo_id = :id AND activo = TRUE FOR UPDATE');
                $bloqueo->execute(array(':id' => $equipoId));
                $anteriores = $bloqueo->fetch(PDO::FETCH_ASSOC);
                if (!$anteriores) {
                    throw new RuntimeException('El equipo no existe.');
                }
                $asignaciones = $db->prepare("SELECT 1 FROM inventario_asignaciones WHERE equipo_id = :id AND estado = 'ACTIVA'");
                $asignaciones->execute(array(':id' => $equipoId));
                if ($asignaciones->fetchColumn() && (int) $anteriores['estado_equipo_id'] !== (int) $datos['estado_equipo_id']) {
                    throw new RuntimeException('El estado de un equipo asignado se controla desde la devolución.');
                }
                $asignacionesSql = array();
                $parametros = array(':equipo_id' => $equipoId, ':actualizado_por' => $usuarioId);
                foreach ($columnas as $columna) {
                    $asignacionesSql[] = $columna . ' = :' . $columna;
                    $parametros[':' . $columna] = $datos[$columna];
                }
                $sql = "UPDATE inventario_equipos SET " . implode(', ', $asignacionesSql) . ",
                        actualizado_por = :actualizado_por, fecha_actualizacion = CURRENT_TIMESTAMP
                        WHERE equipo_id = :equipo_id";
                $db->prepare($sql)->execute($parametros);
                $tipoMovimiento = 'ACTUALIZACION';
            } else {
                $nombres = implode(', ', $columnas);
                $marcas = ':' . implode(', :', $columnas);
                $parametros = array(':creado_por' => $usuarioId);
                foreach ($columnas as $columna) {
                    $parametros[':' . $columna] = $datos[$columna];
                }
                $sql = "INSERT INTO inventario_equipos ({$nombres}, creado_por)
                        VALUES ({$marcas}, :creado_por) RETURNING equipo_id";
                $sentencia = $db->prepare($sql);
                $sentencia->execute($parametros);
                $equipoId = (int) $sentencia->fetchColumn();
                $tipoMovimiento = 'REGISTRO_INICIAL';
                $custodia = $db->prepare(
                    "INSERT INTO inventario_custodias
                     (equipo_id, custodio_nuevo_id, ubicacion_id, estado_equipo_id,
                      funcionario_recibe_id, motivo, observaciones, creado_por)
                     VALUES (:equipo, :custodio, :ubicacion, :estado, :recibe,
                             'Custodia inicial', :observaciones, :creado)"
                );
                $custodia->execute(array(
                    ':equipo' => $equipoId, ':custodio' => $datos['custodio_id'],
                    ':ubicacion' => $datos['ubicacion_id'], ':estado' => $datos['estado_equipo_id'],
                    ':recibe' => $datos['custodio_id'], ':observaciones' => $datos['observaciones'],
                    ':creado' => $usuarioId
                ));
            }

            if (!$anteriores) {
                foreach ($componentes as $componente) {
                    $this->insertarComponente($db, $equipoId, $componente, $usuarioId);
                }
            }
            $movimiento = $db->prepare(
                "INSERT INTO inventario_movimientos
                 (equipo_id, tipo_movimiento, descripcion, responsable_anterior_id,
                  responsable_nuevo_id, datos_anteriores, datos_nuevos, usuario_id)
                 VALUES (:equipo, :tipo, :descripcion, :anterior, :nuevo,
                         CAST(:datos_anteriores AS JSONB), CAST(:datos_nuevos AS JSONB), :usuario)"
            );
            $movimiento->execute(array(
                ':equipo' => $equipoId, ':tipo' => $tipoMovimiento,
                ':descripcion' => $anteriores ? 'Se actualizaron los datos del equipo.' : 'Se registró el equipo en el inventario.',
                ':anterior' => $anteriores ? $anteriores['custodio_id'] : null,
                ':nuevo' => $datos['custodio_id'],
                ':datos_anteriores' => $anteriores ? json_encode($anteriores, JSON_UNESCAPED_UNICODE) : null,
                ':datos_nuevos' => json_encode($datos, JSON_UNESCAPED_UNICODE),
                ':usuario' => $usuarioId
            ));
            $db->commit();
            return $equipoId;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    /** Inserta un componente validado dentro de una transacción. */
    private function insertarComponente(PDO $db, $equipoId, $datos, $usuarioId)
    {
        if (!$this->existeActivo($db, 'inventario_tipos_componente', 'tipo_componente_id', $datos['tipo_componente_id'])
            || !$this->existeActivo($db, 'inventario_estados_componente', 'estado_componente_id', $datos['estado_componente_id'])) {
            throw new RuntimeException('El tipo o estado de un componente no está disponible.');
        }
        if (!empty($datos['serial'])) {
            $duplicado = $db->prepare(
                "SELECT 1 FROM inventario_componentes
                 WHERE LOWER(BTRIM(serial)) = LOWER(BTRIM(:serial)) AND activo = TRUE LIMIT 1"
            );
            $duplicado->execute(array(':serial' => $datos['serial']));
            if ($duplicado->fetchColumn()) {
                throw new RuntimeException('Ya existe un componente con el serial ' . $datos['serial'] . '.');
            }
        }
        $sql = "INSERT INTO inventario_componentes
                (equipo_id, tipo_componente_id, estado_componente_id, marca, modelo,
                 serial, observacion, creado_por)
                VALUES (:equipo, :tipo, :estado, :marca, :modelo, :serial, :observacion, :usuario)";
        $db->prepare($sql)->execute(array(
            ':equipo' => $equipoId, ':tipo' => $datos['tipo_componente_id'],
            ':estado' => $datos['estado_componente_id'], ':marca' => $datos['marca'],
            ':modelo' => $datos['modelo'], ':serial' => $datos['serial'],
            ':observacion' => $datos['observacion'], ':usuario' => $usuarioId
        ));
    }

    /** Agrega un accesorio a un equipo existente y registra el movimiento. */
    public function agregarComponente($equipoId, $datos, $usuarioId)
    {
        $db = $this->db();
        $db->beginTransaction();
        try {
            $equipo = $db->prepare('SELECT 1 FROM inventario_equipos WHERE equipo_id = :id AND activo = TRUE FOR UPDATE');
            $equipo->execute(array(':id' => $equipoId));
            if (!$equipo->fetchColumn()) {
                throw new RuntimeException('El equipo no existe.');
            }
            $this->insertarComponente($db, $equipoId, $datos, $usuarioId);
            $db->prepare(
                "INSERT INTO inventario_movimientos (equipo_id, tipo_movimiento, descripcion, datos_nuevos, usuario_id)
                 VALUES (:equipo, 'COMPONENTE_AGREGADO', 'Se agregó un componente al equipo.',
                         CAST(:datos AS JSONB), :usuario)"
            )->execute(array(
                ':equipo' => $equipoId,
                ':datos' => json_encode($datos, JSON_UNESCAPED_UNICODE),
                ':usuario' => $usuarioId
            ));
            $db->commit();
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    /** Busca equipos asignables o equipos con asignación activa. */
    public function buscarEquipos($termino, $soloActivos, $todos = false)
    {
        $parametros = array(':q' => '%' . $termino . '%');
        if ($soloActivos) {
            $sql = "SELECT e.equipo_id AS id,
                           e.nombre || ' · ' || e.serial || ' · ' || a.empleado_nombre AS text
                    FROM inventario_equipos e
                    INNER JOIN inventario_asignaciones a ON a.equipo_id = e.equipo_id AND a.estado = 'ACTIVA'
                    WHERE e.activo = TRUE AND (
                        e.nombre ILIKE :q OR e.serial ILIKE :q OR e.codigo_siesa ILIKE :q
                        OR e.codigo_activo_fijo ILIKE :q OR a.empleado_documento ILIKE :q
                        OR a.empleado_nombre ILIKE :q
                    ) ORDER BY e.nombre LIMIT 30";
        } elseif ($todos) {
            $sql = "SELECT e.equipo_id AS id, e.nombre || ' · ' || e.serial AS text
                    FROM inventario_equipos e
                    WHERE e.activo = TRUE
                      AND (e.nombre ILIKE :q OR e.serial ILIKE :q
                           OR e.codigo_siesa ILIKE :q OR e.codigo_activo_fijo ILIKE :q)
                    ORDER BY e.nombre LIMIT 30";
        } else {
            $sql = "SELECT e.equipo_id AS id, e.nombre || ' · ' || e.serial AS text
                    FROM inventario_equipos e
                    INNER JOIN inventario_estados_equipo ee ON ee.estado_equipo_id = e.estado_equipo_id
                    WHERE e.activo = TRUE AND ee.permite_asignacion = TRUE
                      AND NOT EXISTS (
                          SELECT 1 FROM inventario_asignaciones a
                          WHERE a.equipo_id = e.equipo_id AND a.estado = 'ACTIVA'
                      )
                      AND (e.nombre ILIKE :q OR e.serial ILIKE :q
                           OR e.codigo_siesa ILIKE :q OR e.codigo_activo_fijo ILIKE :q)
                    ORDER BY e.nombre LIMIT 30";
        }
        $sentencia = $this->db()->prepare($sql);
        $sentencia->execute($parametros);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Crea una asignación completa con elementos y software en una transacción. */
    public function crearAsignacion($datos, $componentesIds, $software, $usuarioId)
    {
        $db = $this->db();
        $db->beginTransaction();
        try {
            $equipoSql = "SELECT e.*, ee.permite_asignacion
                          FROM inventario_equipos e
                          INNER JOIN inventario_estados_equipo ee ON ee.estado_equipo_id = e.estado_equipo_id
                          WHERE e.equipo_id = :equipo AND e.activo = TRUE FOR UPDATE";
            $sentencia = $db->prepare($equipoSql);
            $sentencia->execute(array(':equipo' => $datos['equipo_id']));
            $equipo = $sentencia->fetch(PDO::FETCH_ASSOC);
            if (!$equipo || !$equipo['permite_asignacion']) {
                throw new RuntimeException('El equipo no está disponible para asignación.');
            }
            $activa = $db->prepare("SELECT 1 FROM inventario_asignaciones WHERE equipo_id = :equipo AND estado = 'ACTIVA'");
            $activa->execute(array(':equipo' => $datos['equipo_id']));
            if ($activa->fetchColumn()) {
                throw new RuntimeException('El equipo ya tiene una asignación activa.');
            }
            $campos = array(
                'equipo_id', 'empleado_documento', 'empleado_nombre', 'empleado_correo',
                'empleado_cargo', 'empleado_area', 'jefe_nombre', 'jefe_cargo',
                'funcionario_entrega_id', 'fecha_entrega', 'diagnostico_entrega',
                'datos_empleado_verificados', 'equipo_fisico_verificado', 'seriales_verificados',
                'componentes_entregados_verificados', 'diagnostico_diligenciado',
                'software_verificado', 'software_no_aplica'
            );
            $parametros = array(':creado_por' => $usuarioId);
            foreach ($campos as $campo) {
                $parametros[':' . $campo] = $datos[$campo];
            }
            $sql = "INSERT INTO inventario_asignaciones (" . implode(', ', $campos) . ", creado_por)
                    VALUES (:" . implode(', :', $campos) . ", :creado_por) RETURNING asignacion_id";
            $insertar = $db->prepare($sql);
            $this->ejecutarTipado($insertar, $parametros, array(
                ':datos_empleado_verificados', ':equipo_fisico_verificado',
                ':seriales_verificados', ':componentes_entregados_verificados',
                ':diagnostico_diligenciado', ':software_verificado', ':software_no_aplica'
            ));
            $asignacionId = (int) $insertar->fetchColumn();

            $elemento = $db->prepare(
                "INSERT INTO inventario_asignacion_componentes
                 (asignacion_id, componente_id, es_equipo_principal, tipo, marca, modelo,
                  serial_original, entregado, observacion)
                 VALUES (:asignacion, :componente, :principal, :tipo, :marca, :modelo,
                         :serial, TRUE, :observacion)"
            );
            $this->ejecutarTipado($elemento, array(
                ':asignacion' => $asignacionId, ':componente' => null, ':principal' => true,
                ':tipo' => 'Equipo principal', ':marca' => $equipo['marca'], ':modelo' => $equipo['modelo'],
                ':serial' => $equipo['serial'], ':observacion' => null
            ), array(':principal'));
            if ($componentesIds) {
                $marcas = implode(',', array_fill(0, count($componentesIds), '?'));
                $consulta = $db->prepare(
                    "SELECT c.componente_id, tc.nombre AS tipo, c.marca, c.modelo, c.serial, c.observacion
                     FROM inventario_componentes c
                     INNER JOIN inventario_tipos_componente tc ON tc.tipo_componente_id = c.tipo_componente_id
                     WHERE c.equipo_id = ? AND c.activo = TRUE AND c.componente_id IN ({$marcas})"
                );
                $consulta->execute(array_merge(array($datos['equipo_id']), $componentesIds));
                $encontrados = $consulta->fetchAll(PDO::FETCH_ASSOC);
                if (count($encontrados) !== count(array_unique($componentesIds))) {
                    throw new RuntimeException('Uno de los componentes seleccionados no pertenece al equipo.');
                }
                foreach ($encontrados as $componente) {
                    $this->ejecutarTipado($elemento, array(
                        ':asignacion' => $asignacionId, ':componente' => $componente['componente_id'],
                        ':principal' => false, ':tipo' => $componente['tipo'], ':marca' => $componente['marca'],
                        ':modelo' => $componente['modelo'], ':serial' => $componente['serial'],
                        ':observacion' => $componente['observacion']
                    ), array(':principal'));
                }
            }
            $insertarSoftware = $db->prepare(
                "INSERT INTO inventario_asignacion_software
                 (asignacion_id, nombre, version, licencia, observacion)
                 VALUES (:asignacion, :nombre, :version, :licencia, :observacion)"
            );
            foreach ($software as $programa) {
                $insertarSoftware->execute(array(
                    ':asignacion' => $asignacionId, ':nombre' => $programa['nombre'],
                    ':version' => $programa['version'], ':licencia' => $programa['licencia'],
                    ':observacion' => $programa['observacion']
                ));
            }
            $estadoAsignado = $db->query(
                "SELECT estado_equipo_id FROM inventario_estados_equipo WHERE codigo = 'ASIGNADO'"
            )->fetchColumn();
            if (!$estadoAsignado) {
                throw new RuntimeException('No existe el estado ASIGNADO.');
            }
            $db->prepare(
                "UPDATE inventario_equipos SET estado_equipo_id = :estado,
                 actualizado_por = :usuario, fecha_actualizacion = CURRENT_TIMESTAMP WHERE equipo_id = :equipo"
            )->execute(array(':estado' => $estadoAsignado, ':usuario' => $usuarioId, ':equipo' => $datos['equipo_id']));
            $db->prepare(
                "INSERT INTO inventario_movimientos
                 (equipo_id, tipo_movimiento, descripcion, responsable_anterior_id,
                  datos_nuevos, usuario_id)
                 VALUES (:equipo, 'ASIGNACION', :descripcion, :anterior,
                         CAST(:datos AS JSONB), :usuario)"
            )->execute(array(
                ':equipo' => $datos['equipo_id'],
                ':descripcion' => 'Equipo entregado a ' . $datos['empleado_nombre'] . '.',
                ':anterior' => $equipo['custodio_id'],
                ':datos' => json_encode(array(
                    'asignacion_id' => $asignacionId,
                    'empleado_documento' => $datos['empleado_documento'],
                    'empleado_nombre' => $datos['empleado_nombre']
                ), JSON_UNESCAPED_UNICODE),
                ':usuario' => $usuarioId
            ));
            $db->commit();
            return $asignacionId;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    /** Carga la asignación activa con elementos, software y actas. */
    public function obtenerAsignacionActiva($equipoId)
    {
        $db = $this->db();
        $sql = "SELECT a.*, e.nombre AS equipo_nombre, e.marca AS equipo_marca,
                       e.modelo AS equipo_modelo, e.serial AS equipo_serial,
                       e.codigo_siesa, e.codigo_activo_fijo
                FROM inventario_asignaciones a
                INNER JOIN inventario_equipos e ON e.equipo_id = a.equipo_id
                WHERE a.equipo_id = :equipo AND a.estado = 'ACTIVA'";
        $sentencia = $db->prepare($sql);
        $sentencia->execute(array(':equipo' => $equipoId));
        $asignacion = $sentencia->fetch(PDO::FETCH_ASSOC);
        if (!$asignacion) {
            return null;
        }
        $elementos = $db->prepare(
            "SELECT * FROM inventario_asignacion_componentes
             WHERE asignacion_id = :asignacion AND entregado = TRUE
             ORDER BY es_equipo_principal DESC, asignacion_componente_id"
        );
        $elementos->execute(array(':asignacion' => $asignacion['asignacion_id']));
        $asignacion['componentes'] = $elementos->fetchAll(PDO::FETCH_ASSOC);
        $software = $db->prepare(
            'SELECT nombre, version, licencia, observacion FROM inventario_asignacion_software WHERE asignacion_id = :asignacion'
        );
        $software->execute(array(':asignacion' => $asignacion['asignacion_id']));
        $asignacion['software'] = $software->fetchAll(PDO::FETCH_ASSOC);
        $actas = $db->prepare(
            'SELECT acta_id, tipo, numero, nombre_archivo, fecha_generacion FROM inventario_actas WHERE asignacion_id = :asignacion'
        );
        $actas->execute(array(':asignacion' => $asignacion['asignacion_id']));
        $asignacion['actas'] = $actas->fetchAll(PDO::FETCH_ASSOC);
        return $asignacion;
    }

    /** Registra la devolución y cierra la asignación de forma atómica. */
    public function crearDevolucion($datos, $elementos, $revision, $usuarioId)
    {
        $db = $this->db();
        $db->beginTransaction();
        try {
            $sql = "SELECT a.*, e.custodio_id
                    FROM inventario_asignaciones a
                    INNER JOIN inventario_equipos e ON e.equipo_id = a.equipo_id
                    WHERE a.asignacion_id = :asignacion AND a.estado = 'ACTIVA' FOR UPDATE OF a, e";
            $sentencia = $db->prepare($sql);
            $sentencia->execute(array(':asignacion' => $datos['asignacion_id']));
            $asignacion = $sentencia->fetch(PDO::FETCH_ASSOC);
            if (!$asignacion) {
                throw new RuntimeException('La asignación ya no se encuentra activa.');
            }
            foreach (array(
                array('inventario_motivos_devolucion', 'motivo_devolucion_id', $datos['motivo_devolucion_id']),
                array('inventario_ubicaciones', 'ubicacion_id', $datos['ubicacion_posterior_id']),
                array('inventario_estados_equipo', 'estado_equipo_id', $datos['estado_resultante_id'])
            ) as $catalogo) {
                if (!$this->existeActivo($db, $catalogo[0], $catalogo[1], $catalogo[2])) {
                    throw new RuntimeException('Uno de los catálogos de devolución no está disponible.');
                }
            }
            $esperados = $db->prepare(
                "SELECT asignacion_componente_id FROM inventario_asignacion_componentes
                 WHERE asignacion_id = :asignacion AND entregado = TRUE ORDER BY asignacion_componente_id"
            );
            $esperados->execute(array(':asignacion' => $datos['asignacion_id']));
            $idsEsperados = array_map('intval', $esperados->fetchAll(PDO::FETCH_COLUMN));
            $idsRecibidos = array();
            $hayNovedad = false;
            $elementosValidados = array();
            foreach ($elementos as $elemento) {
                $idsRecibidos[] = (int) $elemento['asignacion_componente_id'];
                $recepcion = $db->prepare(
                    'SELECT codigo, genera_novedad FROM inventario_estados_recepcion WHERE estado_recepcion_id = :id AND activo'
                );
                $recepcion->execute(array(':id' => $elemento['estado_recepcion_id']));
                $estadoRecepcion = $recepcion->fetch(PDO::FETCH_ASSOC);
                $serial = $db->prepare(
                    'SELECT codigo, genera_novedad FROM inventario_estados_verificacion_serial WHERE estado_verificacion_serial_id = :id AND activo'
                );
                $serial->execute(array(':id' => $elemento['estado_verificacion_serial_id']));
                $estadoSerial = $serial->fetch(PDO::FETCH_ASSOC);
                if (!$estadoRecepcion || !$estadoSerial) {
                    throw new RuntimeException('Todos los elementos deben tener verificaciones válidas.');
                }
                $novedad = !empty($estadoRecepcion['genera_novedad']) || !empty($estadoSerial['genera_novedad']);
                if ($novedad && trim($elemento['observacion']) === '') {
                    throw new RuntimeException('Cada componente con novedad debe incluir una observación.');
                }
                $elemento['tiene_novedad'] = $novedad;
                $hayNovedad = $hayNovedad || $novedad;
                $elementosValidados[] = $elemento;
            }
            sort($idsEsperados);
            sort($idsRecibidos);
            if ($idsEsperados !== array_values(array_unique($idsRecibidos))) {
                throw new RuntimeException('Debe verificar individualmente todos los elementos entregados.');
            }
            $camposRevision = array(
                'estado_fisico', 'encendido', 'funcionamiento', 'pantalla', 'bateria',
                'teclado', 'mouse', 'cargador', 'puertos', 'conectividad', 'limpieza'
            );
            $revisionConNovedad = false;
            foreach ($camposRevision as $campo) {
                if (!in_array($revision[$campo], array('BUENO', 'NOVEDAD', 'NO_APLICA'), true)) {
                    throw new RuntimeException('La revisión física está incompleta.');
                }
                $revisionConNovedad = $revisionConNovedad || $revision[$campo] === 'NOVEDAD';
            }
            if ($revisionConNovedad && trim($revision['observacion']) === '') {
                throw new RuntimeException('La revisión física con novedad requiere observación.');
            }
            $hayNovedad = $hayNovedad || $revisionConNovedad
                || trim($revision['danos_visibles']) !== '' || trim($revision['elementos_faltantes']) !== '';
            $completa = !$hayNovedad;
            $insertar = $db->prepare(
                "INSERT INTO inventario_devoluciones
                 (asignacion_id, motivo_devolucion_id, motivo_otro, fecha_devolucion,
                  funcionario_recibe_id, custodio_posterior_id, ubicacion_posterior_id,
                  estado_resultante_id, diagnostico_devolucion, novedades,
                  confirmacion_general, completa, creado_por)
                 VALUES (:asignacion, :motivo, :motivo_otro, :fecha, :recibe, :custodio,
                         :ubicacion, :estado, :diagnostico, :novedades, TRUE, :completa, :usuario)
                 RETURNING devolucion_id"
            );
            $this->ejecutarTipado($insertar, array(
                ':asignacion' => $datos['asignacion_id'], ':motivo' => $datos['motivo_devolucion_id'],
                ':motivo_otro' => $datos['motivo_otro'], ':fecha' => $datos['fecha_devolucion'],
                ':recibe' => $datos['funcionario_recibe_id'], ':custodio' => $datos['custodio_posterior_id'],
                ':ubicacion' => $datos['ubicacion_posterior_id'], ':estado' => $datos['estado_resultante_id'],
                ':diagnostico' => $datos['diagnostico_devolucion'], ':novedades' => $datos['novedades'],
                ':completa' => $completa, ':usuario' => $usuarioId
            ), array(':completa'));
            $devolucionId = (int) $insertar->fetchColumn();
            $detalle = $db->prepare(
                "INSERT INTO inventario_devolucion_componentes
                 (devolucion_id, asignacion_componente_id, estado_recepcion_id,
                  estado_verificacion_serial_id, serial_original, serial_recibido,
                  observacion, tiene_novedad)
                 SELECT :devolucion, ac.asignacion_componente_id, :recepcion, :serial_estado,
                        ac.serial_original, :serial_recibido, :observacion, :novedad
                 FROM inventario_asignacion_componentes ac
                 WHERE ac.asignacion_componente_id = :elemento AND ac.asignacion_id = :asignacion"
            );
            foreach ($elementosValidados as $elemento) {
                $this->ejecutarTipado($detalle, array(
                    ':devolucion' => $devolucionId, ':recepcion' => $elemento['estado_recepcion_id'],
                    ':serial_estado' => $elemento['estado_verificacion_serial_id'],
                    ':serial_recibido' => $elemento['serial_recibido'],
                    ':observacion' => $elemento['observacion'], ':novedad' => $elemento['tiene_novedad'],
                    ':elemento' => $elemento['asignacion_componente_id'],
                    ':asignacion' => $datos['asignacion_id']
                ), array(':novedad'));
                if ($detalle->rowCount() !== 1) {
                    throw new RuntimeException('Un elemento devuelto no pertenece a la asignación.');
                }
            }
            $revision[':devolucion'] = $devolucionId;
            $parametrosRevision = array(':devolucion' => $devolucionId);
            foreach (array_merge($camposRevision, array('danos_visibles', 'elementos_faltantes', 'observacion')) as $campo) {
                $parametrosRevision[':' . $campo] = $revision[$campo];
            }
            $db->prepare(
                "INSERT INTO inventario_revision_devolucion
                 (devolucion_id, estado_fisico, encendido, funcionamiento, pantalla,
                  bateria, teclado, mouse, cargador, puertos, conectividad, limpieza,
                  danos_visibles, elementos_faltantes, observacion)
                 VALUES (:devolucion, :estado_fisico, :encendido, :funcionamiento, :pantalla,
                         :bateria, :teclado, :mouse, :cargador, :puertos, :conectividad,
                         :limpieza, :danos_visibles, :elementos_faltantes, :observacion)"
            )->execute($parametrosRevision);
            $db->prepare(
                "UPDATE inventario_asignaciones SET estado = 'CERRADA', fecha_cierre = CURRENT_TIMESTAMP
                 WHERE asignacion_id = :asignacion"
            )->execute(array(':asignacion' => $datos['asignacion_id']));
            $db->prepare(
                "UPDATE inventario_equipos
                 SET estado_equipo_id = :estado, ubicacion_id = :ubicacion, custodio_id = :custodio,
                     actualizado_por = :usuario, fecha_actualizacion = CURRENT_TIMESTAMP
                 WHERE equipo_id = :equipo"
            )->execute(array(
                ':estado' => $datos['estado_resultante_id'], ':ubicacion' => $datos['ubicacion_posterior_id'],
                ':custodio' => $datos['custodio_posterior_id'], ':usuario' => $usuarioId,
                ':equipo' => $asignacion['equipo_id']
            ));
            $db->prepare(
                "INSERT INTO inventario_custodias
                 (equipo_id, custodio_anterior_id, custodio_nuevo_id, ubicacion_id,
                  estado_equipo_id, funcionario_recibe_id, motivo, observaciones, creado_por)
                 VALUES (:equipo, :anterior, :nuevo, :ubicacion, :estado, :recibe,
                         'Custodia posterior a devolución', :observaciones, :usuario)"
            )->execute(array(
                ':equipo' => $asignacion['equipo_id'], ':anterior' => $asignacion['custodio_id'],
                ':nuevo' => $datos['custodio_posterior_id'], ':ubicacion' => $datos['ubicacion_posterior_id'],
                ':estado' => $datos['estado_resultante_id'], ':recibe' => $datos['funcionario_recibe_id'],
                ':observaciones' => $datos['novedades'], ':usuario' => $usuarioId
            ));
            $db->prepare(
                "INSERT INTO inventario_movimientos
                 (equipo_id, tipo_movimiento, descripcion, responsable_anterior_id,
                  responsable_nuevo_id, datos_nuevos, usuario_id)
                 VALUES (:equipo, 'DEVOLUCION', :descripcion, :anterior, :nuevo,
                         CAST(:datos AS JSONB), :usuario)"
            )->execute(array(
                ':equipo' => $asignacion['equipo_id'],
                ':descripcion' => $completa ? 'Devolución completa registrada.' : 'Devolución incompleta o con novedad registrada.',
                ':anterior' => $asignacion['custodio_id'], ':nuevo' => $datos['custodio_posterior_id'],
                ':datos' => json_encode(array('devolucion_id' => $devolucionId, 'completa' => $completa), JSON_UNESCAPED_UNICODE),
                ':usuario' => $usuarioId
            ));
            $db->commit();
            return array('devolucion_id' => $devolucionId, 'completa' => $completa);
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    /** Consulta el historial de mantenimiento exclusivo de un equipo. */
    public function listarMantenimientos($equipoId)
    {
        $sql = "SELECT m.mantenimiento_id, m.fecha, tm.nombre AS tipo,
                       TRIM(u.user_nombre || ' ' || u.user_apellidos) AS responsable,
                       m.diagnostico, m.actividad_realizada, m.observaciones,
                       ee.nombre AS estado_resultante, m.proxima_fecha,
                       COALESCE((
                           SELECT JSON_AGG(JSON_BUILD_OBJECT(
                               'descripcion', r.descripcion, 'referencia', r.referencia,
                               'serial', r.serial, 'cantidad', r.cantidad, 'observacion', r.observacion
                           ) ORDER BY r.mantenimiento_repuesto_id)
                           FROM inventario_mantenimiento_repuestos r
                           WHERE r.mantenimiento_id = m.mantenimiento_id
                       ), '[]'::JSON) AS repuestos
                FROM inventario_mantenimientos m
                INNER JOIN inventario_tipos_mantenimiento tm
                        ON tm.tipo_mantenimiento_id = m.tipo_mantenimiento_id
                INNER JOIN usuarios u ON u.user_id = m.responsable_id
                INNER JOIN inventario_estados_equipo ee
                        ON ee.estado_equipo_id = m.estado_resultante_id
                WHERE m.equipo_id = :equipo
                ORDER BY m.fecha DESC, m.mantenimiento_id DESC";
        $sentencia = $this->db()->prepare($sql);
        $sentencia->execute(array(':equipo' => $equipoId));
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Registra mantenimiento, repuestos, estado resultante y trazabilidad en una transacción. */
    public function crearMantenimiento($datos, $repuestos, $usuarioId)
    {
        $db = $this->db();
        $db->beginTransaction();
        try {
            $equipo = $db->prepare(
                'SELECT estado_equipo_id FROM inventario_equipos WHERE equipo_id = :equipo AND activo = TRUE FOR UPDATE'
            );
            $equipo->execute(array(':equipo' => $datos['equipo_id']));
            $estadoAnterior = $equipo->fetchColumn();
            if (!$estadoAnterior) {
                throw new RuntimeException('El equipo no existe.');
            }
            if (!$this->existeActivo($db, 'inventario_tipos_mantenimiento', 'tipo_mantenimiento_id', $datos['tipo_mantenimiento_id'])
                || !$this->existeActivo($db, 'inventario_estados_equipo', 'estado_equipo_id', $datos['estado_resultante_id'])) {
                throw new RuntimeException('El tipo de mantenimiento o estado resultante no está disponible.');
            }
            $insertar = $db->prepare(
                "INSERT INTO inventario_mantenimientos
                 (equipo_id, tipo_mantenimiento_id, fecha, responsable_id, diagnostico,
                  actividad_realizada, observaciones, estado_resultante_id, proxima_fecha, creado_por)
                 VALUES (:equipo, :tipo, :fecha, :responsable, :diagnostico, :actividad,
                         :observaciones, :estado, :proxima, :usuario)
                 RETURNING mantenimiento_id"
            );
            $insertar->execute(array(
                ':equipo' => $datos['equipo_id'], ':tipo' => $datos['tipo_mantenimiento_id'],
                ':fecha' => $datos['fecha'], ':responsable' => $datos['responsable_id'],
                ':diagnostico' => $datos['diagnostico'], ':actividad' => $datos['actividad_realizada'],
                ':observaciones' => $datos['observaciones'], ':estado' => $datos['estado_resultante_id'],
                ':proxima' => $datos['proxima_fecha'], ':usuario' => $usuarioId
            ));
            $mantenimientoId = (int) $insertar->fetchColumn();
            $insertarRepuesto = $db->prepare(
                "INSERT INTO inventario_mantenimiento_repuestos
                 (mantenimiento_id, descripcion, referencia, serial, cantidad, observacion)
                 VALUES (:mantenimiento, :descripcion, :referencia, :serial, :cantidad, :observacion)"
            );
            foreach ($repuestos as $repuesto) {
                $insertarRepuesto->execute(array(
                    ':mantenimiento' => $mantenimientoId, ':descripcion' => $repuesto['descripcion'],
                    ':referencia' => $repuesto['referencia'], ':serial' => $repuesto['serial'],
                    ':cantidad' => $repuesto['cantidad'], ':observacion' => $repuesto['observacion']
                ));
            }
            $db->prepare(
                "UPDATE inventario_equipos SET fecha_mantenimiento = :fecha,
                 estado_equipo_id = :estado, actualizado_por = :usuario,
                 fecha_actualizacion = CURRENT_TIMESTAMP WHERE equipo_id = :equipo"
            )->execute(array(
                ':fecha' => $datos['fecha'], ':estado' => $datos['estado_resultante_id'],
                ':usuario' => $usuarioId, ':equipo' => $datos['equipo_id']
            ));
            $db->prepare(
                "INSERT INTO inventario_movimientos
                 (equipo_id, tipo_movimiento, descripcion, datos_anteriores, datos_nuevos, usuario_id)
                 VALUES (:equipo, 'MANTENIMIENTO', :descripcion,
                         CAST(:anterior AS JSONB), CAST(:nuevo AS JSONB), :usuario)"
            )->execute(array(
                ':equipo' => $datos['equipo_id'], ':descripcion' => 'Se registró un mantenimiento del equipo.',
                ':anterior' => json_encode(array('estado_equipo_id' => $estadoAnterior)),
                ':nuevo' => json_encode(array(
                    'mantenimiento_id' => $mantenimientoId,
                    'estado_equipo_id' => $datos['estado_resultante_id'],
                    'fecha' => $datos['fecha']
                ), JSON_UNESCAPED_UNICODE),
                ':usuario' => $usuarioId
            ));
            $db->commit();
            return $mantenimientoId;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    /** Devuelve únicamente los movimientos del equipo solicitado. */
    public function obtenerTrazabilidad($equipoId)
    {
        $sql = "SELECT m.movimiento_id, m.tipo_movimiento, m.descripcion, m.datos_anteriores,
                       m.datos_nuevos, m.fecha_creacion,
                       TRIM(u.user_nombre || ' ' || u.user_apellidos) AS usuario,
                       TRIM(COALESCE(ua.user_nombre, '') || ' ' || COALESCE(ua.user_apellidos, '')) AS responsable_anterior,
                       TRIM(COALESCE(un.user_nombre, '') || ' ' || COALESCE(un.user_apellidos, '')) AS responsable_nuevo
                FROM inventario_movimientos m
                INNER JOIN usuarios u ON u.user_id = m.usuario_id
                LEFT JOIN usuarios ua ON ua.user_id = m.responsable_anterior_id
                LEFT JOIN usuarios un ON un.user_id = m.responsable_nuevo_id
                WHERE m.equipo_id = :equipo
                ORDER BY m.fecha_creacion DESC, m.movimiento_id DESC";
        $sentencia = $this->db()->prepare($sql);
        $sentencia->execute(array(':equipo' => $equipoId));
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Consolida pendientes sin crear una tabla duplicada. */
    public function listarPendientes()
    {
        $sql = "WITH base AS (
                    SELECT e.equipo_id, e.nombre, e.serial, ee.nombre AS estado,
                           ee.codigo AS estado_codigo, iu.nombre AS ubicacion,
                           TRIM(u.user_nombre || ' ' || u.user_apellidos) AS custodio
                    FROM inventario_equipos e
                    INNER JOIN inventario_estados_equipo ee ON ee.estado_equipo_id = e.estado_equipo_id
                    INNER JOIN inventario_ubicaciones iu ON iu.ubicacion_id = e.ubicacion_id
                    INNER JOIN usuarios u ON u.user_id = e.custodio_id
                    WHERE e.activo = TRUE
                )
                SELECT * FROM (
                    SELECT b.equipo_id, b.nombre, b.serial, b.estado, b.ubicacion,
                           a.empleado_nombre AS responsable, 'PENDIENTE_DEVOLUCION' AS tipo_pendiente,
                           'Asignación activa desde ' || TO_CHAR(a.fecha_entrega, 'YYYY-MM-DD') AS detalle
                    FROM base b
                    INNER JOIN inventario_asignaciones a ON a.equipo_id = b.equipo_id AND a.estado = 'ACTIVA'

                    UNION ALL

                    SELECT b.equipo_id, b.nombre, b.serial, b.estado, b.ubicacion,
                           b.custodio, 'DEVOLUCION_INCOMPLETA', COALESCE(d.novedades, 'Elementos pendientes')
                    FROM base b
                    INNER JOIN inventario_asignaciones a ON a.equipo_id = b.equipo_id
                    INNER JOIN inventario_devoluciones d ON d.asignacion_id = a.asignacion_id
                    WHERE NOT d.completa

                    UNION ALL

                    SELECT b.equipo_id, b.nombre, b.serial, b.estado, b.ubicacion,
                           b.custodio,
                           CASE b.estado_codigo
                               WHEN 'EN_REVISION' THEN 'EN_REVISION'
                               WHEN 'EN_MANTENIMIENTO' THEN 'EN_MANTENIMIENTO'
                               WHEN 'PENDIENTE_NOVEDAD' THEN 'CON_NOVEDAD'
                               ELSE 'INCOMPLETO'
                           END,
                           'Estado actual del equipo'
                    FROM base b
                    WHERE b.estado_codigo IN ('EN_REVISION', 'EN_MANTENIMIENTO', 'PENDIENTE_NOVEDAD', 'INCOMPLETO')

                    UNION ALL

                    SELECT b.equipo_id, b.nombre, b.serial, b.estado, b.ubicacion,
                           a.empleado_nombre, 'ACTA_ENTREGA_PENDIENTE', 'No se ha generado el acta de entrega'
                    FROM base b
                    INNER JOIN inventario_asignaciones a ON a.equipo_id = b.equipo_id
                    WHERE NOT EXISTS (
                        SELECT 1 FROM inventario_actas ac
                        WHERE ac.asignacion_id = a.asignacion_id AND ac.tipo = 'ENTREGA'
                    )

                    UNION ALL

                    SELECT b.equipo_id, b.nombre, b.serial, b.estado, b.ubicacion,
                           b.custodio, 'ACTA_DEVOLUCION_PENDIENTE', 'No se ha generado el acta de devolución'
                    FROM base b
                    INNER JOIN inventario_asignaciones a ON a.equipo_id = b.equipo_id
                    INNER JOIN inventario_devoluciones d ON d.asignacion_id = a.asignacion_id
                    WHERE NOT EXISTS (
                        SELECT 1 FROM inventario_actas ac
                        WHERE ac.asignacion_id = a.asignacion_id AND ac.tipo = 'DEVOLUCION'
                    )

                    UNION ALL

                    SELECT b.equipo_id, b.nombre, b.serial, b.estado, b.ubicacion,
                           b.custodio, 'BAJO_CUSTODIA', 'Equipo sin asignación laboral activa'
                    FROM base b
                    WHERE NOT EXISTS (
                        SELECT 1 FROM inventario_asignaciones a
                        WHERE a.equipo_id = b.equipo_id AND a.estado = 'ACTIVA'
                    )
                ) pendientes
                ORDER BY nombre, tipo_pendiente";
        return $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Obtiene todos los datos requeridos por el PDF institucional. */
    public function obtenerDatosActa($asignacionId, $tipo)
    {
        $db = $this->db();
        $sql = "SELECT a.*, e.nombre AS equipo_nombre, e.marca AS equipo_marca,
                       e.modelo AS equipo_modelo, e.serial AS equipo_serial,
                       TRIM(ue.user_nombre || ' ' || ue.user_apellidos) AS funcionario_entrega,
                       re.rol_cargo AS funcionario_entrega_cargo,
                       d.devolucion_id, d.fecha_devolucion, d.diagnostico_devolucion,
                       d.novedades, TRIM(ur.user_nombre || ' ' || ur.user_apellidos) AS funcionario_recibe,
                       rr.rol_cargo AS funcionario_recibe_cargo
                FROM inventario_asignaciones a
                INNER JOIN inventario_equipos e ON e.equipo_id = a.equipo_id
                INNER JOIN usuarios ue ON ue.user_id = a.funcionario_entrega_id
                LEFT JOIN roles re ON re.rol_id = ue.user_rol_usuario
                LEFT JOIN inventario_devoluciones d ON d.asignacion_id = a.asignacion_id
                LEFT JOIN usuarios ur ON ur.user_id = d.funcionario_recibe_id
                LEFT JOIN roles rr ON rr.rol_id = ur.user_rol_usuario
                WHERE a.asignacion_id = :asignacion";
        $sentencia = $db->prepare($sql);
        $sentencia->execute(array(':asignacion' => $asignacionId));
        $datos = $sentencia->fetch(PDO::FETCH_ASSOC);
        if (!$datos || ($tipo === 'DEVOLUCION' && !$datos['devolucion_id'])) {
            return null;
        }
        $elementos = $db->prepare(
            "SELECT ac.tipo, ac.marca, ac.modelo, ac.serial_original,
                    er.nombre AS estado_recepcion, dc.serial_recibido, dc.observacion
             FROM inventario_asignacion_componentes ac
             LEFT JOIN inventario_devolucion_componentes dc
                    ON dc.asignacion_componente_id = ac.asignacion_componente_id
             LEFT JOIN inventario_estados_recepcion er ON er.estado_recepcion_id = dc.estado_recepcion_id
             WHERE ac.asignacion_id = :asignacion AND ac.entregado
             ORDER BY ac.es_equipo_principal DESC, ac.asignacion_componente_id"
        );
        $elementos->execute(array(':asignacion' => $asignacionId));
        $datos['componentes'] = $elementos->fetchAll(PDO::FETCH_ASSOC);
        $software = $db->prepare(
            'SELECT nombre, version FROM inventario_asignacion_software WHERE asignacion_id = :asignacion'
        );
        $software->execute(array(':asignacion' => $asignacionId));
        $datos['software'] = $software->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    /** Registra de manera idempotente el archivo de un acta generada. */
    public function registrarActa($asignacionId, $devolucionId, $tipo, $numero, $nombre, $ruta, $hash, $usuarioId)
    {
        $db = $this->db();
        $db->beginTransaction();
        try {
            $equipo = $db->prepare('SELECT equipo_id FROM inventario_asignaciones WHERE asignacion_id = :asignacion');
            $equipo->execute(array(':asignacion' => $asignacionId));
            $equipoId = $equipo->fetchColumn();
            if (!$equipoId) {
                throw new RuntimeException('La asignación del acta no existe.');
            }
            $sql = "INSERT INTO inventario_actas
                    (asignacion_id, devolucion_id, tipo, numero, nombre_archivo,
                     ruta_archivo, hash_sha256, generado_por)
                    VALUES (:asignacion, :devolucion, :tipo, :numero, :nombre, :ruta, :hash, :usuario)
                    ON CONFLICT (asignacion_id, tipo) DO UPDATE
                    SET nombre_archivo = EXCLUDED.nombre_archivo,
                        ruta_archivo = EXCLUDED.ruta_archivo,
                        hash_sha256 = EXCLUDED.hash_sha256,
                        generado_por = EXCLUDED.generado_por,
                        fecha_generacion = CURRENT_TIMESTAMP
                    RETURNING acta_id";
            $sentencia = $db->prepare($sql);
            $sentencia->execute(array(
                ':asignacion' => $asignacionId, ':devolucion' => $devolucionId ?: null,
                ':tipo' => $tipo, ':numero' => $numero, ':nombre' => $nombre,
                ':ruta' => $ruta, ':hash' => $hash, ':usuario' => $usuarioId
            ));
            $actaId = (int) $sentencia->fetchColumn();
            $db->prepare(
                "INSERT INTO inventario_movimientos
                 (equipo_id, tipo_movimiento, descripcion, datos_nuevos, usuario_id)
                 VALUES (:equipo, 'ACTA_GENERADA', :descripcion, CAST(:datos AS JSONB), :usuario)"
            )->execute(array(
                ':equipo' => $equipoId, ':descripcion' => 'Se generó el acta CO-F-16 de ' . strtolower($tipo) . '.',
                ':datos' => json_encode(array('acta_id' => $actaId, 'tipo' => $tipo, 'numero' => $numero), JSON_UNESCAPED_UNICODE),
                ':usuario' => $usuarioId
            ));
            $db->commit();
            return $actaId;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }
}
