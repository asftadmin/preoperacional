<?php

/* CLASE DESPACHOS ACPM */
class  Despachos extends Conectar
{
    /* GUARDAR LOS DESPACHOS */
    public function guardar_despacho($desp_vehi, $desp_galones, $desp_user,$desp_obra)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'INSERT INTO despachos_acpm (desp_fech_crea,desp_vehi,desp_galones_autorizados,desp_estado,desp_user,desp_obra) VALUES (NOW(),?,?,0,?,?)';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_vehi, PDO::PARAM_INT);
        $sql->bindValue(2, $desp_galones, PDO::PARAM_INT);
        $sql->bindValue(3, $desp_user, PDO::PARAM_INT);
        $sql->bindValue(4, $desp_obra, PDO::PARAM_INT);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR */
    public function update_despacho($desp_id, $desp_galones, $desp_recibo, $desp_km_hr, $desp_cond, $desp_observaciones,$desp_despachador)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE despachos_acpm SET desp_galones = ?,
        desp_recibo = ?,
        desp_fech = NOW(),
        desp_km_hr = ?,
        desp_cond = ?,
        desp_hora = now(),
        desp_observaciones = ?,
        desp_despachador = ?
        WHERE desp_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_galones);
        $sql->bindValue(2, $desp_recibo);
        $sql->bindValue(3, $desp_km_hr);
        $sql->bindValue(4, $desp_cond);
        $sql->bindValue(5, $desp_observaciones);
        $sql->bindValue(6, $desp_despachador);
        $sql->bindValue(7, $desp_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR DESPACHOS */
    public function get_despachos($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CASE
        WHEN desp_estado = 0 THEN 'Activo'
        WHEN desp_estado = 1 THEN 'Anulado'
        ELSE NULL 
        END AS desp_estado FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id
        WHERE desp_user=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR DESPACHOS ACTIVOS */
    public function get_despachos_activos()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *
        FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id 
        WHERE desp_estado=0 and desp_galones is null";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VALIDACION DE EXISTENCIA */
    public function despExiste($desp_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'SELECT COUNT(*) AS count FROM despachos_acpm WHERE desp_vehi = ? and desp_estado =3 ';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_vehi);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /* ACTUALIZAR EL ESTADO A ANULADO*/
    public function CambioEstado($desp_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'UPDATE despachos_acpm SET desp_estado = 1  WHERE desp_id = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_despacho_id($desp_id){
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id  WHERE desp_id=?";
        $sql = $conectar->prepare($sql);
        $sql -> bindValue(1, $desp_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
    /* REPORTE DESPACHOS */
    public function RpteDespachos($desp_vehi, $desp_cond, $fecha_inicio, $fecha_final)
    {

        // Convertir cadena vacía en NULL si es necesario
        if ($desp_vehi === '') {
            $desp_vehi = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($desp_cond === '') {
            $desp_cond = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_inicio === '') {
            $fecha_inicio = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_final === '') {
            $fecha_final = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM filtro_despachos(?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_vehi);
        $sql->bindValue(2, $desp_cond);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
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
2024 */
?>