<?php 
    class Suboperacion extends Conectar{

        /* MOSTRAR X ID - EDITAR */
        public function get_suboper_id($suboper_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM suboperaciones WHERE suboper_id=?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $suboper_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();

        }

        /* LISTAR DATOS */
        public function get_suboper(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT 
            suboperaciones.suboper_id, suboperaciones.suboper_nombre,  
            operaciones.oper_nombre,tipo_vehiculo.tipo_nombre AS vehiculo_tipo_nombre,
            CASE 
                WHEN suboperaciones.suboper_estado = 1 THEN 'ACTIVO'
                WHEN suboperaciones.suboper_estado = 0 THEN 'INACTIVO'
            END AS SUBOPER_ESTADO
            FROM suboperaciones 
            JOIN operaciones ON suboperaciones.suboper_oper = operaciones.oper_id
            JOIN tipo_vehiculo ON suboperaciones.suboper_vehi = tipo_vehiculo.tipo_id
          
            WHERE suboperaciones.suboper_estado = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* INSERTAR SUBOPERACIONES */       
        public function insert_suboper($suboper_nombre,$suboper_oper,$suboper_vehi){
            $conectar = parent::conexion();
            parent::set_names();
             // Verificar si el array de empleados no está vacío
        if (empty($suboper_nombre)) {
            throw new Exception('El array de suboperacion está vacío.');
        }
        // Iterar sobre el array de empleados y ejecutar la consulta para cada uno
        foreach ($suboper_nombre as $oper_id) {
            // Preparar la consulta dentro del bucle para cada empleado
            $sql = "INSERT INTO suboperaciones (suboper_nombre,suboper_estado, suboper_oper, suboper_vehi)  VALUES (:suboper_nombre, 1, :suboper_oper, :suboper_vehi)";
            $stmt = $conectar->prepare($sql);
           // Ejecutar la consulta con los valores actuales
           $stmt->execute([
            ':suboper_nombre' => $oper_id,
            ':suboper_oper' => $suboper_oper,
            ':suboper_vehi' => $suboper_vehi,
        ]);
    }
            //return $resultado=$sql->fetchAll();
        }

        /*ACTUALIZAR*/
        public function update_suboper($suboper_id,$suboper_nombre, $suboper_estado, $suboper_oper,$suboper_vehi){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE suboperaciones SET suboper_nombre=?, suboper_estado=?, suboper_oper=?, suboper_vehi=? WHERE suboper_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $suboper_nombre);
            $sql ->bindValue(2, $suboper_estado);
            $sql ->bindValue(3, $suboper_oper);
            $sql ->bindValue(4, $suboper_vehi);
            $sql ->bindValue(5, $suboper_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR */
        public function delete_suboper($suboper_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM suboperaciones WHERE suboper_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $suboper_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* VERIFICAR SI YA SE ENCUENTRA REGISTRADO */
        public function suboperExiste($suboper_nombre) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM suboperaciones WHERE suboper_nombre= ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $suboper_nombre);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }
    }

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>