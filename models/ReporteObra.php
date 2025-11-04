<?php

/* CLASE REPORTE OBRAS */
class ReporteObra extends Conectar
{

    /* AGREGAR UN REPORTE DE OBRA */
    public function rpte_obra_add($fecha, $inspector, $obra, $operador,$hr_inicio, $actividad) {
        $conectar = parent::conexion();
        $sql = "INSERT INTO reporte_obra (ro_fecha,ro_id_inspector,ro_id_obra, ro_id_operador, ro_hr_inicio, ro_actv) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(1, $fecha);
        $stmt->bindParam(2, $inspector);
        $stmt->bindParam(3, $obra);
        $stmt->bindParam(4, $operador);
        $stmt->bindParam(5, $hr_inicio);
        $stmt->bindParam(6, $actividad);
        return $stmt->execute();
    }
    /* CERRAR REPORTES OBRAS */
    public function get_cerrar_ro($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT ro_id,CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS inspector,CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS operador, 
        ro_fecha, obras_nom, ro_actv, ro_hr_inicio,ro_hr_final
        FROM reporte_obra ro 
        INNER JOIN usuarios u1 ON u1.user_id = ro.ro_id_inspector 
        INNER JOIN usuarios u2 ON u2.user_id = ro.ro_id_operador
        INNER JOIN obras o ON o.obras_id = ro.ro_id_obra
		WHERE ro_hr_inicio is not null and ro_id_inspector =?and ro_hr_final is null";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    
    /* LISTAR REPORTES OBRAS */
    public function get_ro()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            ro_id,
            CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS inspector,
            CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS operador,
            ro_fecha,
            obras_nom,
            ro_actv,
            ro_hr_inicio,
            ro_hr_final,
            -- Calcular horas trabajadas considerando cambio de día
            CASE 
                WHEN ro_hr_final >= ro_hr_inicio 
                THEN ro_hr_final - ro_hr_inicio
                ELSE (ro_hr_final - ro_hr_inicio) + INTERVAL '24 hours'
            END AS horas_trabajadas
        FROM reporte_obra ro 
        INNER JOIN usuarios u1 ON u1.user_id = ro.ro_id_inspector 
        INNER JOIN usuarios u2 ON u2.user_id = ro.ro_id_operador
        INNER JOIN obras o ON o.obras_id = ro.ro_id_obra
        WHERE ro_hr_final IS NOT NULL;";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* LISTAR REPORTES OBRAS */
    public function filtro_ro($ro_id_inspector, $ro_id_operador, $fecha_inicio, $fecha_final)
    {
        // Convertir cadena vacía en NULL si es necesario
        if ($ro_id_inspector === '') {
            $ro_id_inspector = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($ro_id_operador === '') {
            $ro_id_operador = null;
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
        $sql = "SELECT * FROM Rte_Obra(?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ro_id_inspector);
        $sql->bindValue(2, $ro_id_operador);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* UPDATE HORA FINAL */
    public function update_ro($ro_hr_final,$ro_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE reporte_obra SET ro_hr_final = ? WHERE ro_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ro_hr_final);
        $sql->bindValue(2, $ro_id);
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