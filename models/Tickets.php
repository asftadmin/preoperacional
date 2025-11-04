<?php

class Tickets extends Conectar {

    public function get_tickets($coordinador_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto INNER JOIN vehiculos ON codi_vehi_soli = vehi_id WHERE asig_soli = ? AND esta_soli = 'abierto' ORDER BY fech_creac_soli DESC";
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

    public function get_tickets_revision($coordinador_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto INNER JOIN vehiculos ON codi_vehi_soli = vehi_id 
                INNER JOIN usuarios ON user_id = codi_cond_soli 
                INNER JOIN reporte_mantenimiento ON reporte_mantenimiento.repo_vehi = vehiculos.vehi_id
                INNER JOIN tipos_mantenimiento ON tipos_mantenimiento.codigo_tipo_mantenimiento = reporte_mantenimiento.repo_tipo_mtto
                WHERE esta_soli = 'en_revision' AND asig_soli = ?
                ";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $coordinador_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
