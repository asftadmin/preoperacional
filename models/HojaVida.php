<?php

class HojaVida extends Conectar {
    public function get_hoja_vida($id_vehiculo, $fechaIni = '', $fechaFin = '', $tipo_mtto = '') {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
                    rm.*,
                    ot.codi_otm,
                    ot.mtto_otm,
                    ot.num_otm,
                    tm.tipo_mantenimiento,
                    sm.lect_soli,
                    sm.fech_creac_soli,
                    sm.id_soli,
                    o.obras_nom
                FROM reporte_mtto rm
                INNER JOIN ordenes_trabajo ot 
                    ON ot.codi_otm = rm.repo_mtto_orden
                INNER JOIN solicitudes_mtto sm 
                    ON sm.id_soli = ot.codi_solc_otm
                INNER JOIN tipos_mantenimiento tm 
                    ON tm.codigo_tipo_mantenimiento = ot.mtto_otm
                LEFT JOIN obras o 
                    ON o.obras_id = rm.repo_mtto_obra_id
                WHERE sm.codi_vehi_soli = :id_vehiculo";

        if ($fechaIni !== '' && $fechaFin !== '') {
            $sql .= " AND sm.fech_creac_soli BETWEEN :fechaIni AND :fechaFin";
        }

        if ($tipo_mtto !== '') {
            $sql .= " AND ot.mtto_otm = :tipo_mtto";
        }

        $sql .= " ORDER BY sm.fech_creac_soli ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id_vehiculo', $id_vehiculo, PDO::PARAM_INT);

        if ($fechaIni !== '' && $fechaFin !== '') {
            $stmt->bindValue(':fechaIni', $fechaIni);
            $stmt->bindValue(':fechaFin', $fechaFin);
        }

        if ($tipo_mtto !== '') {
            $stmt->bindValue(':tipo_mtto', $tipo_mtto, PDO::PARAM_INT);
        }

        $stmt->execute();
        $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* actividades y lectura anterior por reporte */
        foreach ($reportes as &$rep) {
            $sql2 = "SELECT sm2.lect_soli
                 FROM solicitudes_mtto sm2
                 WHERE sm2.codi_vehi_soli = :id_vehiculo
                 AND sm2.id_soli < :id_soli
                 ORDER BY sm2.id_soli DESC
                 LIMIT 1";
            $stmt2 = $conectar->prepare($sql2);
            $stmt2->bindValue(':id_vehiculo', $id_vehiculo, PDO::PARAM_INT);
            $stmt2->bindValue(':id_soli',     $rep['id_soli'], PDO::PARAM_INT);
            $stmt2->execute();
            $rep['lectura_anterior'] = $stmt2->fetchColumn();

            $sql3 = "SELECT rr.*, ot.*
                    FROM reportes_repuestos rr
                    INNER JOIN reporte_mtto mt ON mt.repo_mtto_id = rr.repo_mtto_id
                    INNER JOIN ordenes_trabajo ot ON ot.codi_otm = mt.repo_mtto_orden
                    WHERE ot.codi_otm = :id_otm
                    ORDER BY ot.codi_otm ASC";
            $stmt3 = $conectar->prepare($sql3);
            $stmt3->bindValue(':id_otm', $rep['codi_otm'], PDO::PARAM_INT);
            $stmt3->execute();
            $rep['actividades'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);

            /* valor total repuestos por OTM */
            $sql4 = "SELECT COALESCE(SUM(rr.rpts_vlr_neto * rr.rpts_cant), 0) AS total_valor
                    FROM reportes_repuestos rr
                    INNER JOIN reporte_mtto mt ON mt.repo_mtto_id = rr.repo_mtto_id
                    INNER JOIN ordenes_trabajo ot ON ot.codi_otm = mt.repo_mtto_orden
                    WHERE ot.codi_otm = :id_otm";

            $stmt4 = $conectar->prepare($sql4);
            $stmt4->bindValue(':id_otm', $rep['codi_otm'], PDO::PARAM_INT);
            $stmt4->execute();
            $rep['total_valor_repuestos'] = $stmt4->fetchColumn();
        }
        unset($rep);

        /* datos del equipo */
        $sqlEq = "SELECT * FROM vehiculos INNER JOIN tipo_vehiculo ON tipo_vehiculo.tipo_id = vehiculos.vehi_tipo  WHERE vehi_id = :id_vehiculo";
        $stmtEq = $conectar->prepare($sqlEq);
        $stmtEq->bindValue(':id_vehiculo', $id_vehiculo, PDO::PARAM_INT);
        $stmtEq->execute();
        $equipo = $stmtEq->fetch(PDO::FETCH_ASSOC);

        return [
            'equipo'   => $equipo,
            'reportes' => $reportes
        ];
    }
}
