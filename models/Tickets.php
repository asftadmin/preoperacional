<?php

class Tickets extends Conectar
{

    public function get_tickets($coordinador_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto INNER JOIN vehiculos ON codi_vehi_soli = vehi_id WHERE asig_soli = ? AND esta_soli = '1' ORDER BY fech_creac_soli DESC";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $coordinador_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detalle_solicitud($ticketID)
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto INNER JOIN vehiculos ON codi_vehi_soli = vehi_id INNER JOIN usuarios ON user_id = codi_cond_soli WHERE id_soli = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ticketID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_revision($coordinador_id, $placa = "", $fechaIni = "", $fechaFin = "")
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    ot.codi_otm,
                    ot.num_otm,
                    soli.num_soli,
                    soli.fech_creac_soli,
                    soli.esta_soli,
                    tm.tipo_mantenimiento,
                    veh.vehi_placa
                FROM solicitudes_mtto soli
                INNER JOIN vehiculos veh 
                    ON soli.codi_vehi_soli = veh.vehi_id
                INNER JOIN usuarios usu 
                    ON usu.user_id = soli.codi_cond_soli
                INNER JOIN ordenes_trabajo ot 
                    ON ot.codi_solc_otm = soli.id_soli
                INNER JOIN tipos_mantenimiento tm
                    ON tm.codigo_tipo_mantenimiento = ot.mtto_otm
                WHERE soli.esta_soli IN ('2','3')
                AND soli.asig_soli = :coord
                ";

        if ($placa !== "") {
            $sql .= " AND veh.vehi_id = :placa ";
        }

        if ($fechaIni !== "" && $fechaFin !== "") {
            $sql .= " AND soli.fech_creac_soli BETWEEN :fini AND :ffin ";
        }

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":coord", $coordinador_id, PDO::PARAM_INT);

        if ($placa !== "") {
            $stmt->bindValue(":placa", $placa, PDO::PARAM_STR);
        }

        if ($fechaIni !== "" && $fechaFin !== "") {
            $stmt->bindValue(":fini", $fechaIni);
            $stmt->bindValue(":ffin", $fechaFin);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_cerrados($coordinador_id, $placa = "", $fechaIni = "", $fechaFin = "")
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "        SELECT 
                            repo.repo_mtto_num_reporte,
                            repo.repo_mtto_estado,
                            repo.created_at,
                            repo.repo_mtto_id,
                            ot.num_otm,
                            veh.vehi_placa
                        FROM solicitudes_mtto soli
                        INNER JOIN vehiculos veh 
                            ON soli.codi_vehi_soli = veh.vehi_id
                        INNER JOIN usuarios usu 
                            ON usu.user_id = soli.codi_cond_soli
                        INNER JOIN ordenes_trabajo ot 
                            ON ot.codi_solc_otm = soli.id_soli
                        INNER JOIN reporte_mtto repo
                            ON repo.repo_mtto_orden = ot.codi_otm
                        INNER JOIN tipos_mantenimiento tm
                            ON tm.codigo_tipo_mantenimiento = ot.mtto_otm
                        WHERE soli.esta_soli = '3'
                        AND soli.asig_soli = :coord
                ";

        if ($placa !== "") {
            $sql .= " AND veh.vehi_id = :placa ";
        }

        if ($fechaIni !== "" && $fechaFin !== "") {
            $sql .= " AND repo.created_at BETWEEN :fini AND :ffin ";
        }

        $sql .= " ORDER BY repo.created_at DESC";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":coord", $coordinador_id, PDO::PARAM_INT);
        if ($placa !== "") {
            $stmt->bindValue(":placa", $placa, PDO::PARAM_STR);
        }

        if ($fechaIni !== "" && $fechaFin !== "") {
            $stmt->bindValue(":fini", $fechaIni);
            $stmt->bindValue(":ffin", $fechaFin);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_ordenes($codi_otm)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                solicitudes_mtto.*,
                vehiculos.*,
                usuarios.*,
                ordenes_trabajo.*,
                tipos_mantenimiento.*,

                -- Lectura nueva
                solicitudes_mtto.lect_soli AS lectura_nueva,

                -- Lectura anterior
                (
                    SELECT sm2.lect_soli
                    FROM solicitudes_mtto sm2
                    WHERE sm2.codi_vehi_soli = solicitudes_mtto.codi_vehi_soli
                    AND sm2.id_soli < solicitudes_mtto.id_soli
                    ORDER BY sm2.id_soli DESC
                    LIMIT 1
                ) AS lectura_anterior

            FROM solicitudes_mtto
            INNER JOIN vehiculos 
                ON codi_vehi_soli = vehi_id
            INNER JOIN usuarios 
                ON user_id = codi_cond_soli
            INNER JOIN ordenes_trabajo 
                ON ordenes_trabajo.codi_solc_otm = solicitudes_mtto.id_soli
            INNER JOIN tipos_mantenimiento 
                ON tipos_mantenimiento.codigo_tipo_mantenimiento = ordenes_trabajo.mtto_otm
            WHERE codi_otm = ?;

                "; //esta_soli = '2' AND
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $codi_otm, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_actividades($codi_otm)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = " SELECT 
                        actividad_programada,
                        detalle_trabajo,
                        repuesto,
                        um,
                        cantidad
                    FROM detalle_actv_otm
                    WHERE id_orden_trabajo = ?
                    ORDER BY id_detalle_actv_otm;"; //esta_soli = '2' AND
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $codi_otm, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obternerUltimoConsecutivo()
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Año actual
        $anio = date("Y");

        $sql = "SELECT num_soli 
            FROM solicitudes_mtto
            WHERE num_soli LIKE :pattern
            ORDER BY RIGHT(num_soli, 3)::INTEGER DESC
            LIMIT 1";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":pattern", "SM-$anio-%", PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function insert_solicitud($numero, $conductor, $vehiculo, $falla, $km)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO 
                solicitudes_mtto
                (fech_creac_soli, num_soli, codi_cond_soli, codi_vehi_soli, esta_soli, asig_soli, desc_soli, lect_soli, prio_soli)
                VALUES
                (NOW(), ?, ?, ?, ?, ?, ?, ?, 'Normal')";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $numero, PDO::PARAM_STR);
        $stmt->bindValue(2, $conductor,       PDO::PARAM_INT);
        $stmt->bindValue(3, $vehiculo,            PDO::PARAM_INT);
        $stmt->bindValue(4, '1',          PDO::PARAM_STR);
        $stmt->bindValue(5, 2,                  PDO::PARAM_INT); // ID del coordinador
        $stmt->bindValue(6, $falla, PDO::PARAM_STR);
        $stmt->bindValue(7, $km, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function insertar_lote($fila)
    {
        $conectar = parent::conexion();
        $sql  = "INSERT INTO detalle_actv_otm 
                    (id_orden_trabajo, actividad_programada, detalle_trabajo, repuesto, um, cantidad)
                VALUES 
                    (:id_orden_trabajo, :actividad_programada, :detalle_trabajo, :repuesto, :um, :cantidad)
                RETURNING id_detalle_actv_otm";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id_orden_trabajo',     $fila['id_orden_trabajo'], PDO::PARAM_INT);
        $stmt->bindValue(':actividad_programada', $fila['actividad'],        PDO::PARAM_STR);
        $stmt->bindValue(':detalle_trabajo',      $fila['detalle'],          PDO::PARAM_STR);
        $stmt->bindValue(':repuesto',             $fila['repuesto'],         PDO::PARAM_STR);
        $stmt->bindValue(':um',                   $fila['um'],               PDO::PARAM_STR);
        $stmt->bindValue(':cantidad',             $fila['cantidad'],         PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function actualizar_lote($fila)
    {
        $conectar = parent::conexion();

        $sql  = "UPDATE detalle_actv_otm
                SET id_orden_trabajo     = :id_orden_trabajo,
                    actividad_programada = :actividad_programada,
                    detalle_trabajo      = :detalle_trabajo,
                    repuesto             = :repuesto,
                    um                   = :um,
                    cantidad             = :cantidad
              WHERE id_detalle_actv_otm  = :id";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id_orden_trabajo',     $fila['id_orden_trabajo'], PDO::PARAM_INT);
        $stmt->bindValue(':actividad_programada', $fila['actividad'],        PDO::PARAM_STR);
        $stmt->bindValue(':detalle_trabajo',      $fila['detalle'],          PDO::PARAM_STR);
        $stmt->bindValue(':repuesto',             $fila['repuesto'],         PDO::PARAM_STR);
        $stmt->bindValue(':um',                   $fila['um'],               PDO::PARAM_STR);
        $stmt->bindValue(':cantidad',             $fila['cantidad'],         PDO::PARAM_STR);
        $stmt->bindValue(':id',                   $fila['id'],               PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function listar_lote($id_orden_trabajo)
    {
        $conectar = parent::conexion();
        $sql = "SELECT id_detalle_actv_otm,
                   id_orden_trabajo,
                   actividad_programada,
                   detalle_trabajo,
                   repuesto,
                   um,
                   cantidad
              FROM detalle_actv_otm
             WHERE id_orden_trabajo = :id_orden_trabajo
             ORDER BY id_detalle_actv_otm";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id_orden_trabajo', $id_orden_trabajo, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarUno($id)
    {
        $conectar = parent::conexion();
        $sql  = "SELECT id_detalle_actv_otm,
                    actividad_programada,
                    detalle_trabajo,
                    repuesto,
                    um,
                    cantidad
               FROM detalle_actv_otm
              WHERE id_detalle_actv_otm = :id";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminar_actividad($id)
    {
        $conectar = parent::conexion();

        $sql  = "DELETE FROM detalle_actv_otm WHERE id_detalle_actv_otm = :id";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
