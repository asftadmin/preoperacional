<?php

/* CLASE DESPACHOS ACPM */
class  VerDespachos extends Conectar
{    
    /* GRAFICO RENDIMIENTO */
    public function grafico_despachos(){
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT vehi_placa,
        SUM (desp_galones) galones
        FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id
        WHERE DATE_TRUNC('month', desp_fech_crea) = DATE_TRUNC('month', CURRENT_DATE) and desp_galones <> 0
        GROUP BY vehi_placa ";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
    /* DETALLE RENDIMIENTO */
    public function detalle_rendimiento($desp_vehi){
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT vehi_placa,desp_fech,desp_galones, obras_nom, CONCAT(user_nombre, ' ', user_apellidos) AS operador, desp_observaciones
        FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id
        INNER JOIN obras ON obras.obras_id = despachos_acpm.desp_obra
        INNER JOIN usuarios ON usuarios.user_id = despachos_acpm.desp_cond
        WHERE desp_vehi=?";
        $sql = $conectar->prepare($sql);
        $sql -> bindValue(1, $desp_vehi);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    
}

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>