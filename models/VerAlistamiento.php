<?php
/* CLASE VER PREOPERACIONAL - ROL VERIFICADOR */
class  VerAlistamiento extends Conectar
{
    /* LISTAR ALISTAMIENTOS INDIVIDUALES*/
    public function  consultar()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT CONCAT(user_nombre, ' ', user_apellidos) AS inspector,alista_fecha, alista_id,vehi_placa,obras_nom,alista_observaciones,vehi_estado,alista_codigo,alista_fecha_recibe,
        CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Recibido'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado
        FROM alistamiento INNER JOIN usuarios ON user_id = alista_inspec
		INNER JOIN obras  ON alista_obras = obras_id
		INNER JOIN vehiculos ON alistamiento.alista_vehi = vehiculos.vehi_id
        GROUP BY  alista_id, alista_estado, alista_fecha, obras_nom,alista_observaciones,vehi_placa,vehi_estado,alista_codigo,inspector
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* LISTAR */
    public function listar()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT CONCAT(user_nombre, ' ', user_apellidos) AS inspector_nombre_completo, alista_codigo,alista_fecha,obras_nom,alista_observaciones,observaciones_inspe,
        alista_fecha_recibe,
        CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Recibido'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado
        FROM alistamiento INNER JOIN usuarios ON user_id = alista_inspec
		INNER JOIN obras  ON alista_obras = obras_id
        GROUP BY inspector_nombre_completo, alista_codigo,alista_fecha,obras_nom,alista_observaciones,observaciones_inspe, alista_fecha_recibe,alista_estado
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR ALISTAMIENTOS X RESIDENTE*/
    public function  listarAlistamientoxResidente($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT alista_codigo,alista_fecha,
        CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Recibido'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado,
        alista_observaciones
        FROM alistamiento
        WHERE alistamiento.alista_residente = ?
        GROUP BY  alista_codigo, alista_estado, alista_fecha,alista_observaciones
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* LISTAR ALISTAMIENTOS  X INSPECTOR*/
    public function  listarAlistamientoxInspector($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT CONCAT(user_nombre, ' ', user_apellidos) AS conductor_nombre_completo, alista_codigo,alista_fecha,obras_nom,alista_observaciones,observaciones_inspe,
        CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Recibido'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado, alista_fecha_recibe
        FROM alistamiento INNER JOIN usuarios ON user_id = alista_conductor
		INNER JOIN obras  ON alista_obras = obras_id
        WHERE alistamiento.alista_inspec = ? and alista_estado in (1,2,3)
        GROUP BY  alista_codigo, alista_estado, alista_fecha,conductor_nombre_completo, obras_nom,alista_observaciones,observaciones_inspe,alista_fecha_recibe
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR HERRAMIENTA MENOR  X INSPECTOR*/
    public function  listarHMxInspector($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT alista_fecha, alista_id,vehi_placa,obras_nom,alista_observaciones,observaciones_inspe,vehi_estado,alista_codigo
        FROM alistamiento INNER JOIN usuarios ON user_id = alista_conductor
		INNER JOIN obras  ON alista_obras = obras_id
		INNER JOIN vehiculos ON alistamiento.alista_vehi = vehiculos.vehi_id
        WHERE alistamiento.alista_inspec = ? and alista_estado IN (1,2,3,4) and vehi_estado = 'operativo'
        GROUP BY  alista_id, alista_estado, alista_fecha, obras_nom,alista_observaciones,observaciones_inspe,vehi_placa,vehi_estado,alista_codigo
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR ALISTAMIENTOS SOLICITADOS VISTA DE ALMACEN*/
    public function  listarAlistamientoHM()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT alista_codigo,alista_fecha,
        CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Recibido'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado,
        alista_observaciones, alista_fecha_recibe
		FROM alistamiento
        WHERE alista_estado NOT IN (4,0) OR alista_estado IS NULL
        GROUP BY  alista_codigo, alista_estado, alista_fecha,alista_observaciones,alista_fecha_recibe
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR ALISTAMIENTOS SOLICITADOS VISTA DE MANTENIMIENTO*/
    /* public function  listarAlistamientoMAQ()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT alista_codigo,alista_fecha,
        CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Recibido'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado,
        alista_observaciones, alista_fecha_recibe
		FROM alistamiento
        WHERE alista_estado_mtn is NULL
        GROUP BY  alista_codigo, alista_estado, alista_fecha,alista_observaciones,alista_fecha_recibe
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    } */


    public function  listarAlistamientoNoFuncionales()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT alista_id,alista_codigo,alista_fecha,vehi_estado,vehi_placa, alista_fecha_recibe,CONCAT(user_nombre, ' ', user_apellidos) AS inspector
		FROM vehiculos  INNER JOIN alistamiento  ON vehiculos.vehi_id=alistamiento.alista_vehi
		INNER JOIN usuarios ON alistamiento.alista_inspec = usuarios.user_id
        WHERE vehi_estado='no funcional'
        GROUP BY  alista_codigo, alista_estado, alista_fecha,alista_fecha_recibe,vehi_estado,vehi_placa,inspector,alista_id
        ORDER BY alista_fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR AL CAMBIAR EL ESTADO */
    public function  mostrarAlistamiento($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT  *,
        CASE
                    WHEN alista_estado = 0 THEN 'No Funcional'
                    WHEN alista_estado = 1 THEN 'Alistado'
                    WHEN alista_estado = 2 THEN 'Recibido'
                    WHEN alista_estado = 3 THEN 'Operativo'
                    WHEN alista_estado = 4 THEN 'Finalizado'
                    ELSE NULL 
        END AS alista_estado
        FROM alistamiento
        WHERE alista_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_codigo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR AL REALISTAR*/
    public function  mostrarAlistamientoAsignado($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *
        FROM vehiculos 
        INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id 
        INNER JOIN alistamiento ON alistamiento.alista_vehi = vehiculos.vehi_id
        INNER JOIN obras ON obras.obras_id = alistamiento.alista_obras
        WHERE alista_codigo = ? 
        order by tipo_nombre";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_codigo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ASIGNACION DE LA HERRAMIENTA MENOR */
    public function asignado_HM($alista_id, $alista_codigo,$vehi_estado)
    {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            $conectar->beginTransaction();

            // Actualizar el estado del vehículo
            $sql_update_vehiculo = 'UPDATE vehiculos
            SET vehi_estado = ?
            WHERE vehi_id IN (SELECT alista_vehi FROM alistamiento WHERE alista_id = ?)';
            $stmt_update_vehiculo = $conectar->prepare($sql_update_vehiculo);
            $stmt_update_vehiculo->bindValue(1, $vehi_estado);
            $stmt_update_vehiculo->bindValue(2, $alista_id);
            $stmt_update_vehiculo->execute();
            // Actualizar alista_estado a 1
            $sql_update_estado = 'UPDATE alistamiento SET alista_estado = 1 WHERE alista_codigo = ?';
            $stmt_update_estado = $conectar->prepare($sql_update_estado);
            $stmt_update_estado->bindValue(1, $alista_codigo);
            $stmt_update_estado->execute();


            // Si todo se ha ejecutado correctamente, confirmamos la transacción
            $conectar->commit();

            return true; // O cualquier valor que necesites para indicar éxito
        } catch (Exception $e) {
            // Si ocurre algún error, revertimos la transacción
            $conectar->rollback();
            echo "Error: " . $e->getMessage();
            return false; // O cualquier valor que necesites para indicar fallo
        }
    }

    /* ASIGNACION DE LA MAQUINARIA */
    /* public function asignado_MAQ($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            $conectar->beginTransaction();

            // Primero, actualizamos en la tabla de alistamiento
            $sql_update = 'UPDATE alistamiento SET alista_estado_mtn = 1  WHERE alista_codigo = ?';
            $sql = $conectar->prepare($sql_update);
            $sql->bindValue(1, $alista_codigo);
            $sql->execute();

            // Verificar si alista_estado_mtn y alista_estado_alm son ambos 1
            $sql_check = 'SELECT alista_estado_mtn, alista_estado_alm FROM alistamiento WHERE alista_codigo = ?';
            $stmt_check = $conectar->prepare($sql_check);
            $stmt_check->bindValue(1, $alista_codigo);
            $stmt_check->execute();
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($result['alista_estado_mtn'] == 1 && $result['alista_estado_alm'] == 1) {
                // Actualizar alista_estado a 1
                $sql_update_estado = 'UPDATE alistamiento SET alista_estado = 1 WHERE alista_codigo = ?';
                $stmt_update_estado = $conectar->prepare($sql_update_estado);
                $stmt_update_estado->bindValue(1, $alista_codigo);
                $stmt_update_estado->execute();
            }

            // Actualizar el estado del vehículo
            $sql_update_vehiculo = 'UPDATE vehiculos
            SET vehi_estado = \'asignado\'
            WHERE vehi_id IN (SELECT alista_vehi FROM alistamiento WHERE alista_codigo = ? AND alista_resp_vehi = \'A\' and vehi_tipo <> 18)';
            $stmt_update_vehiculo = $conectar->prepare($sql_update_vehiculo);
            $stmt_update_vehiculo->bindValue(1, $alista_codigo);
            $stmt_update_vehiculo->execute();

            // Si todo se ha ejecutado correctamente, confirmamos la transacción
            $conectar->commit();

            return true; // O cualquier valor que necesites para indicar éxito
        } catch (Exception $e) {
            // Si ocurre algún error, revertimos la transacción
            $conectar->rollback();
            echo "Error: " . $e->getMessage();
            return false; // O cualquier valor que necesites para indicar fallo
        }
    } */

    /* ACTUALIZAR EL ESTADO FINAL */
    public function calificar($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            // Primero, actualizamos en la tabla de alistamiento
            $sql_update = 'UPDATE alistamiento SET alista_estado = 2,  alista_fecha_recibe=NOW() WHERE alista_codigo = ?';
            $sql = $conectar->prepare($sql_update);
            $sql->bindValue(1, $alista_codigo);
            $sql->execute();

            // Si todo se ha ejecutado correctamente, confirmamos la transacción
            $conectar->commit();

            return true; // O cualquier valor que necesites para indicar éxito
        } catch (Exception $e) {
            // Si ocurre algún error, revertimos la transacción
            $conectar->rollback();
            echo "Error: " . $e->getMessage();
            return false; // O cualquier valor que necesites para indicar fallo
        }
    }

    /* ACTUALIZAR EL ESTADO DE NO FUNCIONAL A STOCK */
    public function Reparado($alista_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            // Primero, actualizamos en la tabla de alistamiento
            $sql_update = 'UPDATE alistamiento SET alista_estado = 4  WHERE alista_id = ?';
            $sql = $conectar->prepare($sql_update);
            $sql->bindValue(1, $alista_id);
            $sql->execute();

            // Luego, actualizamos el estado del vehículo
            $sql_update = 'UPDATE vehiculos
            SET vehi_estado = \'stock\'
            WHERE vehi_id IN (SELECT alista_vehi FROM alistamiento WHERE alista_id = ? )';
            $stmt_update = $conectar->prepare($sql_update);
            $stmt_update->bindValue(1, $alista_id);
            $stmt_update->execute();

            // Si todo se ha ejecutado correctamente, confirmamos la transacción
            $conectar->commit();

            return true; // O cualquier valor que necesites para indicar éxito
        } catch (Exception $e) {
            // Si ocurre algún error, revertimos la transacción
            $conectar->rollback();
            echo "Error: " . $e->getMessage();
            return false; // O cualquier valor que necesites para indicar fallo
        }
    }

    /* REALISTACION */
    public function reasignacion($alista_id, $alista_inspec, $alista_obras)
    {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            $conectar->beginTransaction();

            // Primero, actualizamos en la tabla de alistamiento
            $sql_update = 'UPDATE alistamiento SET alista_inspec = ?, alista_obras = ?, alista_estado=1  WHERE alista_id = ?';
            $sql = $conectar->prepare($sql_update);
            $sql->bindValue(1, $alista_inspec);
            $sql->bindValue(2, $alista_obras);
            $sql->bindValue(3, $alista_id);
            $sql->execute();

            // Luego, actualizamos el estado del vehículo
            $sql_update = 'UPDATE vehiculos
            SET vehi_estado = \'operativo\'
            WHERE vehi_id IN (SELECT alista_vehi FROM alistamiento WHERE alista_id = ? )';
            $stmt_update = $conectar->prepare($sql_update);
            $stmt_update->bindValue(1, $alista_id);
            $stmt_update->execute();

            // Si todo se ha ejecutado correctamente, confirmamos la transacción
            $conectar->commit();

            return true; // O cualquier valor que necesites para indicar éxito
        } catch (Exception $e) {
            // Si ocurre algún error, revertimos la transacción
            $conectar->rollback();
            echo "Error: " . $e->getMessage();
            return false; // O cualquier valor que necesites para indicar fallo
        }
    }
    /* FINALIZAR EL ALISTAMIENTO */
    public function finalizar($alista_codigo,$vehi_estado,$observaciones_inspe,$alista_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            // Primero, actualizamos en la tabla de alistamiento
            $sql_update = 'UPDATE alistamiento SET alista_estado = 4, observaciones_inspe =?, alista_fin =NOW() WHERE alista_codigo = ?';
            $sql = $conectar->prepare($sql_update);
            $sql->bindValue(1, $observaciones_inspe);
            $sql->bindValue(2, $alista_codigo);
            $sql->execute();

            // Luego, actualizamos el estado del vehículo
            $sql_update = 'UPDATE vehiculos
            SET vehi_estado = ?
            WHERE vehi_id IN (SELECT alista_vehi FROM alistamiento WHERE alista_id = ?)';
            $stmt_update = $conectar->prepare($sql_update);
            $stmt_update->bindValue(1, $vehi_estado);
            $stmt_update->bindValue(2, $alista_id);
            $stmt_update->execute();

            // Si todo se ha ejecutado correctamente, confirmamos la transacción
            $conectar->commit();

            return true; // O cualquier valor que necesites para indicar éxito
        } catch (Exception $e) {
            // Si ocurre algún error, revertimos la transacción
            $conectar->rollback();
            echo "Error: " . $e->getMessage();
            return false; // O cualquier valor que necesites para indicar fallo
        }
    }
    /* ASIGNACION DE CONDUCTOR */
    public function conductorAlista($alista_codigo,$alista_conductor)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'UPDATE alistamiento SET alista_conductor = ?  WHERE alista_codigo = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_conductor);
        $sql->bindValue(2, $alista_codigo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR RESPUESTAS */
    public function detalle($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
         CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS residente_nombre_completo,
         u1.user_cedula AS cedula_residente,
         CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS inspector_nombre_completo,
         u2.user_cedula AS cedula_inspec,
         CONCAT(u3.user_nombre, ' ', u3.user_apellidos) AS conductor_nombre_completo,
         a.alista_residente,
         a.alista_inspec,
         a.alista_obras,
         a.alista_vehi,
         v.vehi_placa,
         v.vehi_marca,
         o.obras_nom,
         a.alista_resp_vehi,
         tv.tipo_nombre,
         a.alista_fecha,
         v.vehi_estado,
         a.alista_observaciones
        FROM alistamiento a
        INNER JOIN obras o ON a.alista_obras = o.obras_id
        INNER JOIN vehiculos v ON a.alista_vehi = v.vehi_id
        INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id
        INNER JOIN usuarios u1 ON u1.user_id = a.alista_residente
        INNER JOIN usuarios u2 ON u2.user_id = a.alista_inspec
        LEFT JOIN usuarios u3 ON u3.user_id = a.alista_conductor
        WHERE a.alista_codigo = ? 
        GROUP BY residente_nombre_completo, cedula_residente,inspector_nombre_completo, cedula_inspec, a.alista_residente, a.alista_inspec,v.vehi_estado,
         a.alista_obras, a.alista_vehi, v.vehi_placa, v.vehi_marca, a.alista_resp_vehi, tv.tipo_nombre, a.alista_observaciones,a.alista_fecha,o.obras_nom,conductor_nombre_completo;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_codigo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR RESPUESTAS HERRAMIENTA MENOR */
    public function detalleHM($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT  
            a.alista_id,
            CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS residente_nombre_completo,
            u1.user_cedula AS cedula_residente,
            CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS inspector_nombre_completo,
            u2.user_cedula AS cedula_inspec,
            CONCAT(u3.user_nombre, ' ', u3.user_apellidos) AS conductor_nombre_completo,
            a.alista_residente,
            a.alista_inspec,
            a.alista_obras,
            a.alista_vehi,
            v.vehi_placa,
            v.vehi_marca,
            o.obras_nom,
            a.alista_resp_vehi,
            tv.tipo_nombre,
            a.alista_fecha,
            a.alista_observaciones,
            v.vehi_estado AS estado
        FROM 
            alistamiento a
        INNER JOIN 
            obras o ON a.alista_obras = o.obras_id
        INNER JOIN 
            vehiculos v ON a.alista_vehi = v.vehi_id
        INNER JOIN 
            tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id
        INNER JOIN 
            usuarios u1 ON u1.user_id = a.alista_residente
        INNER JOIN 
            usuarios u2 ON u2.user_id = a.alista_inspec
        LEFT JOIN 
            usuarios u3 ON u3.user_id = a.alista_conductor
        WHERE 
            a.alista_codigo = ? 
            AND v.vehi_estado NOT IN ('no funcional', 'stock')
        GROUP BY 
            residente_nombre_completo, 
            cedula_residente, 
            inspector_nombre_completo, 
            cedula_inspec, 
            a.alista_residente, 
            a.alista_inspec, 
            a.alista_id, 
            estado,
            a.alista_obras, 
            a.alista_vehi, 
            v.vehi_placa, 
            v.vehi_marca, 
            a.alista_resp_vehi, 
            tv.tipo_nombre, 
            a.alista_observaciones, 
            a.alista_fecha, 
            o.obras_nom, 
            conductor_nombre_completo;
        ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_codigo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* MOSTRAR RESPUESTAS MAQUINARIA */
    public function detalleMAQ($alista_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
         CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS residente_nombre_completo,
         u1.user_cedula AS cedula_residente,
         CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS inspector_nombre_completo,
         u2.user_cedula AS cedula_inspec,
         CONCAT(u3.user_nombre, ' ', u3.user_apellidos) AS conductor_nombre_completo,
         a.alista_residente,
         a.alista_inspec,
         a.alista_obras,
         a.alista_vehi,
         v.vehi_placa,
         v.vehi_marca,
         o.obras_nom,
         a.alista_resp_vehi,
         tv.tipo_nombre,
         a.alista_fecha,
         a.alista_observaciones
     FROM alistamiento a
     INNER JOIN obras o ON a.alista_obras = o.obras_id
     INNER JOIN vehiculos v ON a.alista_vehi = v.vehi_id
     INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id
     INNER JOIN usuarios u1 ON u1.user_id = a.alista_residente
     INNER JOIN usuarios u2 ON u2.user_id = a.alista_inspec
     LEFT JOIN usuarios u3 ON u3.user_id = a.alista_conductor
     WHERE a.alista_codigo = ? and alista_resp_vehi='A' AND tipo_id <>18
     GROUP BY residente_nombre_completo, cedula_residente,inspector_nombre_completo, cedula_inspec, a.alista_residente, a.alista_inspec, 
         a.alista_obras, a.alista_vehi, v.vehi_placa, v.vehi_marca, a.alista_resp_vehi, tv.tipo_nombre, a.alista_observaciones,a.alista_fecha,o.obras_nom,conductor_nombre_completo;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $alista_codigo);
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
2023 */
?>