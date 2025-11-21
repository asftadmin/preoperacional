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

    public function get_tickets_revision($coordinador_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto 
				INNER JOIN vehiculos ON codi_vehi_soli = vehi_id 
                INNER JOIN usuarios ON user_id = codi_cond_soli 
                INNER JOIN ordenes_trabajo ON ordenes_trabajo.codi_solc_otm = solicitudes_mtto.codi_soli
                INNER JOIN tipos_mantenimiento ON tipos_mantenimiento.codigo_tipo_mantenimiento = ordenes_trabajo.mtto_otm
                WHERE esta_soli = '2' AND asig_soli = ?
                ";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $coordinador_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_ordenes($codi_otm) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM solicitudes_mtto 
				INNER JOIN vehiculos ON codi_vehi_soli = vehi_id 
                INNER JOIN usuarios ON user_id = codi_cond_soli 
                INNER JOIN ordenes_trabajo ON ordenes_trabajo.codi_solc_otm = solicitudes_mtto.codi_soli
                INNER JOIN tipos_mantenimiento ON tipos_mantenimiento.codigo_tipo_mantenimiento = ordenes_trabajo.mtto_otm
                WHERE codi_otm = ?
                "; //esta_soli = '2' AND
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $codi_otm, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
