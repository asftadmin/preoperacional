<?php

/* CLASE DESPACHOS ACPM */
class  DespachosExternos extends Conectar
{
    /* GUARDAR LOS DESPACHOS EXTERNO*/
    public function guardar_ssc($ae_cond, $ae_eds, $ae_obra, $ae_galones_aut, $ae_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'INSERT INTO ssc_acpm_externos (ae_cond,ae_eds,ae_obra,ae_galones_aut,ae_fecha_solicitud,ae_codigo,ae_estado) VALUES (?,?,?,?,NOW(),?,1)';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ae_cond, PDO::PARAM_INT);
        $sql->bindValue(2, $ae_eds, PDO::PARAM_STR);
        $sql->bindValue(3, $ae_obra, PDO::PARAM_INT);
        $sql->bindValue(4, $ae_galones_aut, PDO::PARAM_INT);
        $sql->bindValue(5, $ae_codigo, PDO::PARAM_STR);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* GUARDAR DISTURIBUCION*/
    public function guardar_dss($dss_operador, $dss_galones, $dss_vehi, $dss_ae, $dss_cond)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'INSERT INTO dss_acpm_externos (dss_operador,dss_galones,dss_vehi,dss_ae,dss_fecha_desp,dss_ssc,dss_cond) VALUES (?,?,?,?,NOW(),1,?)';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $dss_operador, PDO::PARAM_INT);
        $sql->bindValue(2, $dss_galones, PDO::PARAM_INT);
        $sql->bindValue(3, $dss_vehi, PDO::PARAM_INT);
        $sql->bindValue(4, $dss_ae, PDO::PARAM_INT);
        $sql->bindValue(5, $dss_cond, PDO::PARAM_INT);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* LISTAR SOLICITUD DE DESPACHOS EXTERNOS*/
    public function get_ssc()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CASE
        WHEN ae_estado = 0 THEN 'Anulado'
        WHEN ae_estado = 1 THEN 'Activo'
        WHEN ae_estado = 2 THEN 'Distribuido'
        ELSE NULL 
        END AS ae_estado, CONCAT(user_nombre, ' ', user_apellidos) AS conductor
        FROM ssc_acpm_externos INNER JOIN obras ON ssc_acpm_externos.ae_obra = obras.obras_id 
        INNER JOIN usuarios ON usuarios.user_id = ssc_acpm_externos.ae_cond";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR SOLICITUDES DE DESPACHOS EXTERNOS ACTIVOS */
    public function get_ssc_activos($ae_cond)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *,CASE
        WHEN ae_estado = 2 THEN 'Distribuido'
        WHEN ae_estado = 1 THEN 'Activo'
        ELSE NULL 
        END AS ae_estado
        FROM ssc_acpm_externos INNER JOIN obras ON ssc_acpm_externos.ae_obra = obras.obras_id 
        INNER JOIN usuarios ON usuarios.user_id = ssc_acpm_externos.ae_cond 
        WHERE ae_estado IN (1,2) AND ae_cond=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ae_cond);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* LISTAR DISTRIBUCIONES HECHA POR CADA CONDUCTOR */
    public function get_dss_cond($ae_cond)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *,CASE
        WHEN dss_ssc = 0 THEN 'Recibido'
        WHEN dss_ssc = 1 THEN 'Distribuido'
        ELSE NULL 
        END AS dss_ssc, CONCAT(user_nombre, ' ', user_apellidos) AS operador
        FROM dss_acpm_externos dss INNER JOIN vehiculos v ON dss.dss_vehi = v.vehi_id 
        INNER JOIN usuarios ON usuarios.user_id = dss.dss_operador 
        WHERE dss_cond=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ae_cond);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VALIDACION DE EXISTENCIA CSS*/
    public function sscExiste($ae_cond)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'SELECT COUNT(*) AS count FROM ssc_acpm_externos WHERE ae_cond = ? and ae_estado =5';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ae_cond);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    /* VALIDACION DE EXISTENCIA DE GALONES DSS */
    public function dssExiste($dss_ae, $dss_galones)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'SELECT 
        ssc.ae_galones_aut AS galones_autorizados,
        COALESCE(SUM(dss.dss_galones), 0) AS galones_asignados,
        (ssc.ae_galones_aut - COALESCE(SUM(dss.dss_galones), 0)) AS galones_disponibles
        FROM ssc_acpm_externos ssc
        LEFT JOIN dss_acpm_externos dss ON ssc.ae_id = dss.dss_ae
        WHERE ssc.ae_id = ?
        GROUP BY ssc.ae_galones_aut;';

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $dss_ae);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);

        // Si no hay registros, significa que no hay autorización para asignar galones
        if (!$result) {
            return false;
        }

        // Verificar que los galones disponibles sean suficientes para la nueva asignación
        return $dss_galones  > $result['galones_disponibles'];
    }

    public function actualizarEstadoAE($dss_ae)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Obtener los galones disponibles después de la asignación
        $sql = 'SELECT 
        (ssc.ae_galones_aut - COALESCE(SUM(dss.dss_galones), 0)) AS galones_disponibles
        FROM ssc_acpm_externos ssc
        LEFT JOIN dss_acpm_externos dss ON ssc.ae_id = dss.dss_ae
        WHERE ssc.ae_id = ?
        GROUP BY ssc.ae_galones_aut;';

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $dss_ae);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['galones_disponibles'] <= 0) {
            // Si los galones disponibles son 0 o menos, actualizar ae_estado a 2
            $sqlUpdate = 'UPDATE ssc_acpm_externos SET ae_estado = 2 WHERE ae_id = ?';
            $stmtUpdate = $conectar->prepare($sqlUpdate);
            $stmtUpdate->bindValue(1, $dss_ae);
            $stmtUpdate->execute();
        }
    }


    /* ACTUALIZAR EL ESTADO A ANULADO*/
    public function ssc_anulado($ae_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'UPDATE ssc_acpm_externos SET ae_estado = 0  WHERE ae_id = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ae_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function grafico_galones($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            (SELECT COALESCE(SUM(ssc.ae_galones_aut), 0) 
            FROM ssc_acpm_externos ssc 
            WHERE ssc.ae_cond = ?) AS galones_autorizados,
            COALESCE(SUM(dss.dss_galones), 0) AS galones_despachados,
            ((SELECT COALESCE(SUM(ssc.ae_galones_aut), 0) 
            FROM ssc_acpm_externos ssc 
            WHERE ssc.ae_cond = ?) - COALESCE(SUM(dss.dss_galones), 0)) 
            AS galones_disponibles
        FROM dss_acpm_externos dss
        LEFT JOIN ssc_acpm_externos ssc ON ssc.ae_id = dss.dss_ae
        WHERE ssc.ae_cond = ?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->bindValue(2, $user_id);
        $sql->bindValue(3, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}



?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2025 */
?>