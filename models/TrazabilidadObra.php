<?php

class Trazabilidad extends Conectar
{
    /**
     * Lista las obras disponibles.
     */
    public function get_obras($buscar)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $buscar = '%' . trim($buscar) . '%';

        $sql = 'SELECT
                    obras_id,
                    obras_codigo,
                    obras_nom
                FROM obras
                WHERE
                    tipo_obra = 1
                    AND (
                        obras_codigo ILIKE ?
                        OR obras_nom ILIKE ?
                    )
                ORDER BY obras_nom ASC';

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $buscar);
        $sql->bindValue(2, $buscar);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista los vehículos para los Select2.
     */
    public function get_vehiculos($buscar)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $buscar = '%' . trim($buscar) . '%';

        $sql = 'SELECT
                vehi_id,
                vehi_placa,
                vehi_codigo,
                vehi_marca,
                vehi_modelo
            FROM vehiculos
            WHERE vehi_tipo = 1
            AND (
                vehi_placa ILIKE ?
                OR vehi_codigo ILIKE ?
            )
            ORDER BY vehi_placa ASC';

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $buscar);
        $sql->bindValue(2, $buscar);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea por primera vez un formato en estado BORRADOR.
     *
     * Estado 1 = BORRADOR.
     */
    public function insertar_borrador(
        $obra_id,
        $usuario_id,
        $tipo_mezcla,
        $fecha,
        $tipo_actividad,
        $observaciones,
        $detalle,
        $llegada
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            // Cabecera.
            $sql = 'INSERT INTO trazabilidad_obra (
                        obra_trazabilidad,
                        usuario_trazabilidad,
                        tipo_mezcla_trazabilidad,
                        fecha_trazabilidad,
                        tipo_actividad_trazabilidad,
                        estado_trazabilidad,
                        observaciones_trazabilidad,
                        fecha_creacion_trazabilidad,
                        fecha_actualizacion_trazabilidad
                    )
                    VALUES (?, ?, ?, ?, ?, 1, ?, NOW(), NOW())
                    RETURNING id_trazabilidad';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(1, $obra_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(3, $tipo_mezcla);
            $stmt->bindValue(4, $fecha);
            $stmt->bindValue(5, $tipo_actividad);
            $stmt->bindValue(6, $observaciones);

            $stmt->execute();

            $trazabilidad_id = $stmt->fetchColumn();

            // Guarda las filas del detalle.
            $this->insertar_detalle(
                $conectar,
                $trazabilidad_id,
                $detalle
            );

            // Guarda el control de llegada.
            $this->insertar_llegadas(
                $conectar,
                $trazabilidad_id,
                $llegada
            );

            $conectar->commit();

            return array(
                'success' => true,
                'trazabilidad_id' => $trazabilidad_id
            );
        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }

            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Actualiza un formato existente mientras permanezca
     * en estado BORRADOR.
     */
    public function actualizar_borrador(
        $trazabilidad_id,
        $obra_id,
        $usuario_id,
        $tipo_mezcla,
        $fecha,
        $tipo_actividad,
        $observaciones,
        $detalle,
        $llegada
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            // Solo permite modificar borradores del mismo usuario.
            $sql = 'UPDATE trazabilidad_obra
                    SET
                        obra_trazabilidad = ?,
                        tipo_mezcla_trazabilidad = ?,
                        fecha_trazabilidad = ?,
                        tipo_actividad_trazabilidad = ?,
                        observaciones_trazabilidad = ?,
                        fecha_actualizacion_trazabilidad = NOW()
                    WHERE
                        id_trazabilidad = ?
                        AND usuario_trazabilidad = ?
                        AND estado_trazabilidad = 1';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(1, $obra_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $tipo_mezcla);
            $stmt->bindValue(3, $fecha);
            $stmt->bindValue(4, $tipo_actividad);
            $stmt->bindValue(5, $observaciones);
            $stmt->bindValue(6, $trazabilidad_id, PDO::PARAM_INT);
            $stmt->bindValue(7, $usuario_id, PDO::PARAM_INT);

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception(
                    'El formato no está disponible para edición.'
                );
            }

            /*
             * Para los borradores reemplazamos el detalle completo.
             * Esto facilita agregar y eliminar filas desde el frontend.
             */
            $sql = 'DELETE FROM trazabilidad_obra_detalle
                    WHERE trazabilidad_traz_det = ?';

            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $trazabilidad_id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = 'DELETE FROM trazabilidad_obra_llegada
                    WHERE trazabilidad_traz_lleg = ?';

            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $trazabilidad_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->insertar_detalle(
                $conectar,
                $trazabilidad_id,
                $detalle
            );

            $this->insertar_llegadas(
                $conectar,
                $trazabilidad_id,
                $llegada
            );

            $conectar->commit();

            return array(
                'success' => true,
                'trazabilidad_id' => $trazabilidad_id
            );
        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }

            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Inserta las filas de Detalle de aplicación.
     */
    private function insertar_detalle(
        $conectar,
        $trazabilidad_id,
        $detalle
    ) {
        if (!is_array($detalle)) {
            return;
        }

        $sql = 'INSERT INTO trazabilidad_obra_detalle (
                    trazabilidad_traz_det,
                    vehiculo_traz_det,
                    orden_traz_det,
                    pr_inicial_traz_det,
                    pr_final_traz_det,
                    numero_caja_traz_det,
                    longitud_traz_det,
                    ancho_traz_det,
                    espesor_demolido_traz_det,
                    espesor_excavacion_traz_det,
                    espesor_base_traz_det,
                    espesor_mezcla_traz_det,
                    volumen_demolido_traz_det,
                    volumen_excavado_traz_det,
                    volumen_base_traz_det,
                    capas_imprimacion_traz_det,
                    imprimacion_traz_det,
                    temperatura_aplicacion_traz_det,
                    volumen_mezcla_traz_det
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )';

        $stmt = $conectar->prepare($sql);

        foreach ($detalle as $indice => $row) {
            $fila_vacia =
                empty($row['vehi_id']) &&
                empty($row['pr_inicial']) &&
                empty($row['pr_final']) &&
                empty($row['numero_caja']) &&
                empty($row['longitud']) &&
                empty($row['ancho']) &&
                empty($row['espesor_demolido']) &&
                empty($row['espesor_excavacion']) &&
                empty($row['espesor_base']) &&
                empty($row['espesor_mezcla']) &&
                empty($row['capas_imprimacion']) &&
                empty($row['temperatura_aplicacion']) &&
                empty($row['volumen_mezcla']);

            if ($fila_vacia) {
                continue;
            }

            if (empty($row['vehi_id'])) {
                throw new Exception(
                    'Debe seleccionar la volqueta en la fila '
                    . ($indice + 1)
                    . ' del detalle de aplicación.'
                );
            }

            $longitud = $this->numero($row['longitud'] ?? null);
            $ancho = $this->numero($row['ancho'] ?? null);

            $espesor_demolido = $this->numero(
                $row['espesor_demolido'] ?? null
            );

            $espesor_excavacion = $this->numero(
                $row['espesor_excavacion'] ?? null
            );

            $espesor_base = $this->numero(
                $row['espesor_base'] ?? null
            );

            $capas_imprimacion = $this->numero(
                $row['capas_imprimacion'] ?? null
            );

            if (
                $capas_imprimacion !== null &&
                $capas_imprimacion <= 0
            ) {
                $capas_imprimacion = null;
            }

            /*
             * Los valores calculados se generan nuevamente
             * en backend y no se confía en los enviados por JS.
             */
            $volumen_demolido = $this->multiplicar(
                $longitud,
                $ancho,
                $espesor_demolido
            );

            $volumen_excavado = $this->multiplicar(
                $longitud,
                $ancho,
                $espesor_excavacion
            );

            $volumen_base = $this->multiplicar(
                $longitud,
                $ancho,
                $espesor_base
            );

            $imprimacion = $this->multiplicar(
                $longitud,
                $ancho,
                $capas_imprimacion
            );

            $stmt->execute(array(
                $trazabilidad_id,
                $row['vehi_id'],
                $indice + 1,
                $this->texto($row['pr_inicial'] ?? null),
                $this->texto($row['pr_final'] ?? null),
                $this->numero($row['numero_caja'] ?? null),
                $longitud,
                $ancho,
                $espesor_demolido,
                $espesor_excavacion,
                $espesor_base,
                $this->numero(
                    $row['espesor_mezcla'] ?? null
                ),
                $volumen_demolido,
                $volumen_excavado,
                $volumen_base,
                $capas_imprimacion,
                $imprimacion,
                $this->numero(
                    $row['temperatura_aplicacion'] ?? null
                ),
                $this->numero(
                    $row['volumen_mezcla'] ?? null
                )
            ));
        }
    }

    /**
     * Inserta las filas correspondientes al
     * control de llegada de mezcla.
     */
    private function insertar_llegadas(
        $conectar,
        $trazabilidad_id,
        $llegada
    ) {
        if (!is_array($llegada)) {
            return;
        }

        $sql = 'INSERT INTO trazabilidad_obra_llegada (
                    trazabilidad_traz_lleg,
                    vehiculo_traz_lleg,
                    orden_traz_lleg,
                    temperatura_llegada_traz_lleg,
                    volumen_llegada_traz_lleg,
                    volumen_aplicado_traz_lleg,
                    factor_compactacion_traz_lleg
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)';

        $stmt = $conectar->prepare($sql);

        foreach ($llegada as $indice => $row) {
            $fila_vacia =
                empty($row['vehi_id']) &&
                empty($row['temperatura_llegada']) &&
                empty($row['volumen_llegada']) &&
                empty($row['volumen_aplicado']);

            if ($fila_vacia) {
                continue;
            }

            if (empty($row['vehi_id'])) {
                throw new Exception(
                    'Debe seleccionar la volqueta en la fila '
                    . ($indice + 1)
                    . ' del control de llegada.'
                );
            }

            $volumen_llegada = $this->numero(
                $row['volumen_llegada'] ?? null
            );

            $volumen_aplicado = $this->numero(
                $row['volumen_aplicado'] ?? null
            );

            $factor_compactacion = null;

            if (
                $volumen_llegada !== null &&
                $volumen_aplicado !== null &&
                $volumen_aplicado > 0
            ) {
                $factor_compactacion =
                    $volumen_llegada / $volumen_aplicado;
            }

            $stmt->execute(array(
                $trazabilidad_id,
                $row['vehi_id'],
                $indice + 1,
                $this->numero(
                    $row['temperatura_llegada'] ?? null
                ),
                $volumen_llegada,
                $volumen_aplicado,
                $factor_compactacion
            ));
        }
    }

    /**
     * Obtiene el encabezado de un formato.
     */
    public function get_trazabilidad(
        $trazabilidad_id,
        $usuario_id
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                    t.id_trazabilidad,
                    t.obra_trazabilidad,
                    t.usuario_trazabilidad,
                    t.tipo_mezcla_trazabilidad,
                    t.fecha_trazabilidad,
                    t.tipo_actividad_trazabilidad,
                    t.estado_trazabilidad,
                    t.observaciones_trazabilidad,
                    t.fecha_creacion_trazabilidad,
                    t.fecha_actualizacion_trazabilidad,
                    t.fecha_envio_trazabilidad,
                    o.obras_codigo,
                    o.obras_nom
                FROM trazabilidad_obra t
                INNER JOIN obras o
                    ON o.obras_id = t.obra_trazabilidad
                WHERE
                    t.id_trazabilidad = ?
                    AND t.usuario_trazabilidad = ?';

        $stmt = $conectar->prepare($sql);

        $stmt->bindValue(
            1,
            $trazabilidad_id,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            2,
            $usuario_id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle de aplicación.
     */
    public function get_detalle($trazabilidad_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                    d.id_traz_det,
                    d.trazabilidad_traz_det,
                    d.vehiculo_traz_det,
                    d.orden_traz_det,
                    d.pr_inicial_traz_det,
                    d.pr_final_traz_det,
                    d.numero_caja_traz_det,
                    d.longitud_traz_det,
                    d.ancho_traz_det,
                    d.espesor_demolido_traz_det,
                    d.espesor_excavacion_traz_det,
                    d.espesor_base_traz_det,
                    d.espesor_mezcla_traz_det,
                    d.volumen_demolido_traz_det,
                    d.volumen_excavado_traz_det,
                    d.volumen_base_traz_det,
                    d.capas_imprimacion_traz_det,
                    d.imprimacion_traz_det,
                    d.temperatura_aplicacion_traz_det,
                    d.volumen_mezcla_traz_det,
                    v.vehi_placa
                FROM trazabilidad_obra_detalle d
                INNER JOIN vehiculos v
                    ON v.vehi_id = d.vehiculo_traz_det
                WHERE d.trazabilidad_traz_det = ?
                ORDER BY d.orden_traz_det ASC';

        $stmt = $conectar->prepare($sql);

        $stmt->bindValue(
            1,
            $trazabilidad_id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el control de llegada de mezcla.
     */
    public function get_llegadas($trazabilidad_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                    l.id_traz_lleg,
                    l.trazabilidad_traz_lleg,
                    l.vehiculo_traz_lleg,
                    l.orden_traz_lleg,
                    l.temperatura_llegada_traz_lleg,
                    l.volumen_llegada_traz_lleg,
                    l.volumen_aplicado_traz_lleg,
                    l.factor_compactacion_traz_lleg,
                    v.vehi_placa
                FROM trazabilidad_obra_llegada l
                INNER JOIN vehiculos v
                    ON v.vehi_id = l.vehiculo_traz_lleg
                WHERE l.trazabilidad_traz_lleg = ?
                ORDER BY l.orden_traz_lleg ASC';

        $stmt = $conectar->prepare($sql);

        $stmt->bindValue(
            1,
            $trazabilidad_id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista los formatos creados por el usuario
     * para la bandeja de trazabilidad.
     */
    public function get_trazabilidades_usuario($usuario_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                    t.id_trazabilidad,
                    t.fecha_trazabilidad,
                    t.tipo_mezcla_trazabilidad,
                    t.tipo_actividad_trazabilidad,
                    t.estado_trazabilidad,
                    t.fecha_creacion_trazabilidad,
                    t.fecha_actualizacion_trazabilidad,
                    o.obras_codigo,
                    o.obras_nom
                FROM trazabilidad_obra t
                INNER JOIN obras o
                    ON o.obras_id = t.obra_trazabilidad
                WHERE t.usuario_trazabilidad = ?
                ORDER BY
                    t.fecha_creacion_trazabilidad DESC';

        $stmt = $conectar->prepare($sql);

        $stmt->bindValue(
            1,
            $usuario_id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Envía una trazabilidad a aprobación.
     */
    public function enviar_aprobacion(
        $trazabilidad_id
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            /*
             * Solo puede enviarse una trazabilidad
             * que se encuentre en estado BORRADOR.
             */
            $sql = 'UPDATE trazabilidad_obra
                SET
                    estado_trazabilidad = 2,
                    fecha_envio_trazabilidad = NOW(),
                    fecha_actualizacion_trazabilidad = NOW()
                WHERE
                    id_trazabilidad = ?
                    AND estado_trazabilidad = 1';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(
                1,
                $trazabilidad_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception(
                    'El formato no está disponible para ser enviado.'
                );
            }

            /*
             * Crea el registro pendiente de aprobación.
             *
             * El responsable se asignará cuando
             * un usuario autorizado apruebe o rechace.
             */
            $sql = 'INSERT INTO trazabilidad_obra_aprobacion (
                    trazabilidad_traz_apro,
                    responsable_traz_apro,
                    estado_traz_apro,
                    fecha_asignacion_traz_apro
                )
                VALUES (?, NULL, 1, NOW())';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(
                1,
                $trazabilidad_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $conectar->commit();

            return array(
                'success' => true,
                'message' => 'El formato fue enviado a aprobación correctamente.'
            );
        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }

            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Lista los formatos pendientes de aprobación.
     */
    public function get_pendientes_aprobacion()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                    t.id_trazabilidad,
                    t.fecha_trazabilidad,
                    t.tipo_mezcla_trazabilidad,
                    t.tipo_actividad_trazabilidad,
                    t.fecha_envio_trazabilidad,
                    o.obras_codigo,
                    o.obras_nom,
                    u.user_nombre,
                    u.user_apellidos
                FROM trazabilidad_obra t
                INNER JOIN obras o
                    ON o.obras_id = t.obra_trazabilidad
                INNER JOIN usuarios u
                    ON u.user_id = t.usuario_trazabilidad
                WHERE t.estado_trazabilidad = 2
                ORDER BY
                    t.fecha_envio_trazabilidad ASC';

        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta un formato pendiente para su aprobación.
     */
    public function get_trazabilidad_aprobacion(
        $trazabilidad_id
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                t.*,
                o.obras_codigo,
                o.obras_nom,
                u.user_nombre,
                u.user_apellidos
            FROM trazabilidad_obra t
            INNER JOIN obras o
                ON o.obras_id = t.obra_trazabilidad
            INNER JOIN usuarios u
                ON u.user_id = t.usuario_trazabilidad
            WHERE t.id_trazabilidad = ?
            AND t.estado_trazabilidad = 2';

        $stmt = $conectar->prepare($sql);

        $stmt->bindValue(
            1,
            $trazabilidad_id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Aprueba una trazabilidad pendiente.
     */
    public function aprobar_trazabilidad(
        $trazabilidad_id,
        $usuario_id,
        $observaciones
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            /*
             * Registrar quién aprobó.
             */
            $sql = 'UPDATE trazabilidad_obra_aprobacion
                SET
                    responsable_traz_apro = ?,
                    estado_traz_apro = 2,
                    fecha_respuesta_traz_apro = NOW(),
                    observaciones_traz_apro = ?
                WHERE
                    trazabilidad_traz_apro = ?
                    AND estado_traz_apro = 1';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(
                1,
                $usuario_id,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                2,
                $observaciones
            );

            $stmt->bindValue(
                3,
                $trazabilidad_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception(
                    'El formato ya no se encuentra pendiente de aprobación.'
                );
            }

            /*
             * Cambiar estado general a APROBADO.
             */
            $sql = 'UPDATE trazabilidad_obra
                SET
                    estado_trazabilidad = 3,
                    fecha_actualizacion_trazabilidad = NOW()
                WHERE
                    id_trazabilidad = ?
                    AND estado_trazabilidad = 2';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(
                1,
                $trazabilidad_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception(
                    'No fue posible aprobar el formato.'
                );
            }

            $conectar->commit();

            return array(
                'success' => true,
                'message' => 'El formato fue aprobado correctamente.'
            );
        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }

            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Rechaza una trazabilidad pendiente.
     */
    public function rechazar_trazabilidad(
        $trazabilidad_id,
        $usuario_id,
        $observaciones
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            $sql = 'UPDATE trazabilidad_obra_aprobacion
                SET
                    responsable_traz_apro = ?,
                    estado_traz_apro = 3,
                    fecha_respuesta_traz_apro = NOW(),
                    observaciones_traz_apro = ?
                WHERE
                    trazabilidad_traz_apro = ?
                    AND estado_traz_apro = 1';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(
                1,
                $usuario_id,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                2,
                $observaciones
            );

            $stmt->bindValue(
                3,
                $trazabilidad_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception(
                    'El formato ya no se encuentra pendiente de aprobación.'
                );
            }

            $sql = 'UPDATE trazabilidad_obra
                SET
                    estado_trazabilidad = 4,
                    fecha_actualizacion_trazabilidad = NOW()
                WHERE
                    id_trazabilidad = ?
                    AND estado_trazabilidad = 2';

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(
                1,
                $trazabilidad_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception(
                    'No fue posible rechazar el formato.'
                );
            }

            $conectar->commit();

            return array(
                'success' => true,
                'message' => 'El formato fue rechazado.'
            );
        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }

            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Lista los formatos enviados a aprobación
     * para consulta desde la bandeja del residente.
     */
    public function get_bandeja_aprobacion()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'SELECT
                t.id_trazabilidad,
                t.fecha_trazabilidad,
                t.tipo_mezcla_trazabilidad,
                t.tipo_actividad_trazabilidad,
                t.estado_trazabilidad,
                t.fecha_envio_trazabilidad,
                o.obras_codigo,
                o.obras_nom,
                u.user_nombre,
                u.user_apellidos
            FROM trazabilidad_obra t
            INNER JOIN obras o
                ON o.obras_id = t.obra_trazabilidad
            INNER JOIN usuarios u
                ON u.user_id = t.usuario_trazabilidad
            WHERE t.estado_trazabilidad IN (2, 3, 4)
            ORDER BY
                t.fecha_envio_trazabilidad DESC,
                t.id_trazabilidad DESC';

        $stmt = $conectar->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Convierte los valores numéricos vacíos en NULL.
     */
    private function numero($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return (float) $valor;
    }

    /**
     * Convierte textos vacíos en NULL.
     */
    private function texto($valor)
    {
        $valor = trim((string) $valor);

        return $valor === ''
            ? null
            : $valor;
    }

    /**
     * Multiplica tres valores únicamente cuando
     * todos están disponibles.
     */
    private function multiplicar($valor1, $valor2, $valor3)
    {
        if (
            $valor1 === null ||
            $valor2 === null ||
            $valor3 === null
        ) {
            return null;
        }

        return $valor1 * $valor2 * $valor3;
    }
}