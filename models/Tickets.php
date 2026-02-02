<?php

class Tickets extends Conectar {

    public function get_tickets($coordinador_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto INNER JOIN vehiculos ON codi_vehi_soli = vehi_id WHERE asig_soli = ? AND esta_soli = '1' ORDER BY fech_creac_soli DESC";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $coordinador_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detalle_solicitud($ticketID) {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto INNER JOIN vehiculos ON codi_vehi_soli = vehi_id INNER JOIN usuarios ON user_id = codi_cond_soli WHERE codi_soli = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ticketID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_revision($coordinador_id, $placa = "", $fechaIni = "", $fechaFin = "") {
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
                    ON ot.codi_solc_otm = soli.codi_soli
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

    public function get_tickets_cerrados($coordinador_id, $placa = "", $fechaIni = "", $fechaFin = "") {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "        SELECT 
                            repo.repo_mtto_num_reporte,
                            repo.repo_mtto_estado,
                            repo.repo_mtto_fecha_creacion,
                            repo.repo_mtto_id,
                            ot.num_otm,
                            veh.vehi_placa
                        FROM solicitudes_mtto soli
                        INNER JOIN vehiculos veh 
                            ON soli.codi_vehi_soli = veh.vehi_id
                        INNER JOIN usuarios usu 
                            ON usu.user_id = soli.codi_cond_soli
                        INNER JOIN ordenes_trabajo ot 
                            ON ot.codi_solc_otm = soli.codi_soli
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
            $sql .= " AND repo.repo_mtto_fecha_creacion BETWEEN :fini AND :ffin ";
        }

        $sql .= " ORDER BY repo.repo_mtto_fecha_creacion DESC";
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

    public function get_tickets_ordenes($codi_otm) {
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
                    AND sm2.codi_soli < solicitudes_mtto.codi_soli
                    ORDER BY sm2.codi_soli DESC
                    LIMIT 1
                ) AS lectura_anterior

            FROM solicitudes_mtto
            INNER JOIN vehiculos 
                ON codi_vehi_soli = vehi_id
            INNER JOIN usuarios 
                ON user_id = codi_cond_soli
            INNER JOIN ordenes_trabajo 
                ON ordenes_trabajo.codi_solc_otm = solicitudes_mtto.codi_soli
            INNER JOIN tipos_mantenimiento 
                ON tipos_mantenimiento.codigo_tipo_mantenimiento = ordenes_trabajo.mtto_otm
            WHERE codi_otm = ?;

                "; //esta_soli = '2' AND
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $codi_otm, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
