<?php
/* CLASE TIPO DE VEHICULO */
class Liquidaciones extends Conectar {

    public function consultarTipoVehiculo() {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tipo_vehiculo";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insertLiquidacion($nombre, $fecha_inicio, $fecha_fin, $usuario) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO liquidaciones (liq_descripcion, liq_fecha_inicio, liq_fecha_fin, liq_estado, liq_total, liq_fecha_creacion, liq_user_codigo)
                VALUES (?, ?, ?, 1, 0 ,NOW(), ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre);
        $sql->bindValue(2, $fecha_inicio);
        $sql->bindValue(3, $fecha_fin);
        $sql->bindValue(4, $usuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_liquidacion() {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM liquidaciones";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
	
    public function get_liquidacion_cerrada($liquidacion_id) {

        $id = (int)$liquidacion_id;

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT liq_codigo,repdia_fech AS FECHA_REPORTE, liq_descripcion AS DESCRIP_LIQUIDACION, liq_fecha_inicio AS FECHA_INICIO, 
                liq_fecha_fin AS FECHA_FIN, liq_total AS TOTAL, act_nombre AS ACTIVIDAD,tipo_nombre AS tipo , vehi_costo AS CCOSTO, vehi_marca AS MARCA, vehi_placa AS PLACA,
				CONCAT(user_nombre, ' ', user_apellidos) AS CONDUCTOR, obras_nom AS OBRA, detliq_km_inicial AS KM_HM_INICIAL,
				detliq_km_final AS KM_HM_FINAL, detliq_km_total AS KM_HM_TOTAL, detliq_tarifa AS TARIFA, repdia_volu AS VOLUMEN,
				detliq_subtotal AS SUB_TOTAL, repdia_observa AS OBSERVACIONES
                FROM liquidaciones 
                INNER JOIN detalle_liquidacion ON detalle_liquidacion.detliq_liquidacion_id = liquidaciones.liq_codigo
				INNER JOIN tipo_vehiculo ON tipo_vehiculo.tipo_id = detalle_liquidacion.detliq_tipo_vehiculo_id
                INNER JOIN reportes_diarios ON reportes_diarios.repdia_id = detalle_liquidacion.detliq_repdia_id
                INNER JOIN vehiculos ON vehiculos.vehi_id = reportes_diarios.repdia_vehi
                INNER JOIN obras ON obras.obras_id = reportes_diarios.repdia_obras
                INNER JOIN actividades ON actividades.act_id = reportes_diarios.repdia_actv
				INNER JOIN usuarios ON usuarios.user_id = reportes_diarios.repdia_cond
                WHERE liq_codigo = :id 
                ORDER BY placa";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_obras_liquidacion($fecha_inicio, $fecha_fin, $tipo_vehic) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *
                FROM get_obras_liquidacion_por_obra (?,?,?)
                ORDER BY repdia_obras";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fecha_inicio);
        $sql->bindValue(2, $fecha_fin);
        $sql->bindValue(3, $tipo_vehic);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_actividades($tipo_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM actividades INNER JOIN tipo_vehiculo on tipo_vehiculo.tipo_id = actividades.act_tipo WHERE tipo_id =?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tipo_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_detalle_liquid($liquidacion_id) {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM liquidaciones WHERE liq_codigo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $liquidacion_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_read_liquidacion(
        $tipo_vehi,
        $actividad,
        $obra,
        $fecha_inicio,
        $fecha_fin
    ) {

        // Convertir cadena vacía en NULL si es necesario
        if ($tipo_vehi === '') {
            $tipo_vehi = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($actividad === '') {
            $actividad = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($obra === '') {
            $obra = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM liquidaciones_mensuales(?, ?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tipo_vehi);
        $sql->bindValue(2, $actividad);
        $sql->bindValue(3, $obra);
        $sql->bindValue(4, $fecha_inicio);
        $sql->bindValue(5, $fecha_fin);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insertDetalle($liqId, $tipoVeh, array $detalles) {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            $conectar->beginTransaction();
            //Actualizar el estado de la liquidacion
            $sqlUpdLiq = "UPDATE liquidaciones SET liq_estado = 2 WHERE liq_codigo = :liq";
            $stmt_upd_liqu = $conectar->prepare($sqlUpdLiq);
            $stmt_upd_liqu->execute([':liq' => $liqId]);

            // 3) Prepara INSERT y UPDATE
            $sqlIns = "INSERT INTO detalle_liquidacion
                (detliq_liquidacion_id, detliq_repdia_id, detliq_tipo_vehiculo_id,
                 detliq_km_inicial, detliq_km_final, detliq_km_total,
                 detliq_tarifa, detliq_subtotal)
             VALUES
                (:liq, :repdia, :tipo, :kmin, :kmfi, :kmtot, :tarifa, :sub)";
            $stmt_insert = $conectar->prepare($sqlIns);

            //Update
            $sqlUpd = "UPDATE reportes_diarios
                       SET liqu_codi = :liq
                       WHERE repdia_id = :repdia";
            $stmt_update = $conectar->prepare($sqlUpd);

            // 4) Itera y ejecuta
            foreach ($detalles as $d) {
                $stmt_insert->execute([
                    ':liq'    => $liqId,
                    ':repdia' => $d['repdia_id'],
                    ':tipo'   => $tipoVeh,
                    ':kmin'   => $d['km_inicial'],
                    ':kmfi'   => $d['km_final'],
                    ':kmtot'  => $d['km_total'],
                    ':tarifa' => $d['tarifa'],
                    ':sub'    => $d['subtotal']
                ]);

                $stmt_update->execute([
                    ':liq'    => $liqId,
                    ':repdia' => $d['repdia_id']
                ]);
            }



            $conectar->commit();
            return ['success' => true, 'liq' => $liqId];
        } catch (Exception $e) {
            //Revertir transacción en caso de error
            $conectar->rollBack();
            error_log("Error al guardar el reporte: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function anularDetalle($liquiId) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            // 1) Iniciar transacción
            $conectar->beginTransaction();

            // 2) Marcar la liquidación como anulada (estado = 4)
            $sqlUpdLiq = "
            UPDATE liquidaciones
               SET liq_estado = 4
             WHERE liq_codigo = :liq
            ";
            $stmt_upd_liq = $conectar->prepare($sqlUpdLiq);
            $stmt_upd_liq->execute([':liq' => $liquiId]);

            // 3) Desvincular todos los reportes diarios de esta liquidación
            $sqlUpdRpt = "
            UPDATE reportes_diarios
               SET liqu_codi = NULL
             WHERE liqu_codi = :liq
            ";
            $stmt_upd_rpt = $conectar->prepare($sqlUpdRpt);
            $stmt_upd_rpt->execute([':liq' => $liquiId]);

            // 4) Borrar los detalles asociados
            $sqlDelDet = "
            DELETE FROM detalle_liquidacion
             WHERE detliq_liquidacion_id = :liq
            ";
            $stmt_del_det = $conectar->prepare($sqlDelDet);
            $stmt_del_det->execute([':liq' => $liquiId]);

            // 5) Commit
            $conectar->commit();

            return ['success' => true];
        } catch (Exception $e) {
            // Rollback en caso de error
            $conectar->rollBack();
            error_log("Error al anular detalles de liquidación: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function liquidarLiqu($liquiId) {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            $conectar->beginTransaction();

            // 1) Calcular la suma de subtotales de detalle_liquidacion
            $sqlSum = "
            SELECT COALESCE(SUM(detliq_subtotal),0) AS total
              FROM detalle_liquidacion
             WHERE detliq_liquidacion_id = :liq
        ";
            $stmtSum = $conectar->prepare($sqlSum);
            $stmtSum->execute([':liq' => $liquiId]);
            $total = $stmtSum->fetch(PDO::FETCH_ASSOC)['total'];

            // 2) Actualizar la liquidación: estado = 3 y liq_total = suma anterior
            $sqlUpd = "
            UPDATE liquidaciones
               SET liq_estado = 3,
                   liq_total  = :total
             WHERE liq_codigo = :liq
        ";
            $stmtUpd = $conectar->prepare($sqlUpd);
            $stmtUpd->execute([
                ':total' => $total,
                ':liq'   => $liquiId
            ]);

            $conectar->commit();
            return ['success' => true, 'total' => $total];
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al liquidar $liquiId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function getComisiones($liqId, $tipos = []) {
        $conectar = parent::conexion();
        parent::set_names();

        $params = [];
        $sql = "
        SELECT
          CONCAT(u.user_nombre,' ',u.user_apellidos)                                   AS conductor,
          v.vehi_placa                                                                 AS placa,
          STRING_AGG(DISTINCT tv.tipo_nombre, ' / ' ORDER BY tv.tipo_nombre)           AS tipos,
          COALESCE(SUM(dl.detliq_subtotal), 0)                                         AS subtotal_liquidado,
          liq_fecha_inicio,
          liq_fecha_fin
        FROM liquidaciones             l
        JOIN detalle_liquidacion       dl ON dl.detliq_liquidacion_id = l.liq_codigo
        JOIN tipo_vehiculo             tv ON tv.tipo_id = dl.detliq_tipo_vehiculo_id
        JOIN reportes_diarios          rd ON rd.repdia_id = dl.detliq_repdia_id
        JOIN vehiculos                 v  ON v.vehi_id   = rd.repdia_vehi
        JOIN usuarios                  u  ON u.user_id   = rd.repdia_cond
        WHERE l.liq_codigo = ?
    ";

        // primer parámetro: id de liquidación
        $params[] = (int)$liqId;

        // si hay filtro de tipos, agrega placeholders y parámetros
        if (!empty($tipos)) {
            // normaliza a array de strings (o ints si aplica)
            $tipos = array_values(array_filter(array_map('strval', $tipos), fn($v) => $v !== ''));
            $placeholders = implode(',', array_fill(0, count($tipos), '?'));
            $sql .= " AND tv.tipo_id IN ($placeholders) ";
            foreach ($tipos as $t) {
                $params[] = $t; // si son ints: (int)$t
            }
        }

        $sql .= "
        GROUP BY placa, conductor, liq_fecha_inicio, liq_fecha_fin
        ORDER BY placa, conductor
    ";

        $stmt = $conectar->prepare($sql);

        // liga todos los parámetros en orden
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val); // PDO cuenta desde 1
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}