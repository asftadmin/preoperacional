<?php

/* CLASE PREOPERACIONAL */
class  Alistamiento extends Conectar
{

    /* LISTAR MAQUINARIA X ID */
    public function listar_maquinaria()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id  where tipo_id IN (5,6,7,8,9,11,12,14,15) and vehi_estado='stock' order by tipo_nombre";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* FUNCION PARA VERIFICAR SI YA SE REGISTRO EL ALISTAMIENTO */
    public function alistaExiste($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'SELECT COUNT(*) AS count FROM alistamiento WHERE alista_codigo = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_codigo);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    /* GUARDAR EL ALISTAMIENTO */
    public function guardar_preguntas($alista_obras, $alista_inspec, $alista_residente, $alista_observaciones, $alista_codigo, $alista_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
    
        // Verificar si el array de vehículos no está vacío
        if (empty($alista_vehi)) {
            throw new Exception('El array de vehículos está vacío.');
        }
    
        // Iterar sobre el array de vehículos y ejecutar la consulta para cada uno
        foreach ($alista_vehi as $vehi_id) {
            // Preparar la consulta para insertar en la tabla alistamiento
            $sql_insert = "INSERT INTO alistamiento (alista_obras, alista_inspec, alista_residente, alista_observaciones, alista_codigo, alista_vehi, alista_fecha, alista_estado_alm, alista_estado_mtn)
                VALUES (:alista_obras, :alista_inspec, :alista_residente, :alista_observaciones, :alista_codigo, :alista_vehi, NOW(), null, null)";
            $stmt_insert = $conectar->prepare($sql_insert);
    
            // Ejecutar la consulta de inserción
            $stmt_insert->execute([
                ':alista_obras' => $alista_obras,
                ':alista_inspec' => $alista_inspec,
                ':alista_residente' => $alista_residente,
                ':alista_observaciones' => $alista_observaciones,
                ':alista_codigo' => $alista_codigo,
                ':alista_vehi' => $vehi_id,
            ]);
    
            // Preparar la consulta para actualizar el estado del vehículo
            $sql_update = "UPDATE vehiculos SET vehi_estado = 'solicitado' WHERE vehi_id = :vehi_id";
            $stmt_update = $conectar->prepare($sql_update);
    
            // Ejecutar la consulta de actualización
            $stmt_update->execute([
                ':vehi_id' => $vehi_id,
            ]);
        }
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