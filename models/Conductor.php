<?php 
    /*CLASE CONDUCTOR */
    class Conductor extends Conectar{

        /* INSERTAR */
        public function insert_driver($conductor_usuario, $cond_expedicion_licencia,$cond_categoria_licencia,$cond_vencimiento_licencia,$rolcond){
            $conectar = parent::conexion();
            parent::set_names();
            $sql =  "INSERT INTO conductores (conductor_usuario, cond_expedicion_licencia,cond_categoria_licencia,cond_vencimiento_licencia,rolcond) VALUES (?,?,?,?,?)";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $conductor_usuario);
            $sql -> bindValue(2, $cond_expedicion_licencia);
            $sql -> bindValue(3, $cond_categoria_licencia);
            $sql -> bindValue(4, $cond_vencimiento_licencia);
            $sql -> bindValue(5, $rolcond);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* VERIFICAR SI EL CONDUCTOR EXISTE */
        public function condExiste($conductor_usuario) {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "SELECT COUNT(*) AS count FROM conductores WHERE conductor_usuario = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $conductor_usuario);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /* ACTUALIZAR */
        public function update_driver($cond_id, $conductor_usuario,$cond_expedicion_licencia,$cond_vencimiento_licencia,$cond_categoria_licencia,$rolcond) {
            $conectar = parent::conexion();
            parent::set_names();
    
        // La placa y el usuario están disponibles, proceder con la actualización
                $sql_update = "UPDATE conductores SET conductor_usuario = ?, cond_expedicion_licencia = ?, cond_vencimiento_licencia = ?, cond_categoria_licencia = ?, rolcond=? WHERE cond_id = ?";
                $sql_update = $conectar->prepare($sql_update);
                $sql_update->bindValue(1, $conductor_usuario);
                $sql_update->bindValue(2, $cond_expedicion_licencia);
                $sql_update->bindValue(3, $cond_vencimiento_licencia);
                $sql_update->bindValue(4, $cond_categoria_licencia);
                $sql_update->bindValue(5, $rolcond);
                $sql_update->bindValue(6, $cond_id);
                $sql_update->execute();
                return array("status" => "success", "message" => "Datos actualizados correctamente");
            
        }
        

        /* ELIMINAR */
        public function delete_driver($cond_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM conductores WHERE cond_id = ?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $cond_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* MOSTRAR DATOS */
        public function get_driver(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM conductores";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* MOSTRAR TODOS DATOS */
        public function get_driver_all(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT c.cond_id, c.cond_expedicion_licencia, c.cond_vencimiento_licencia,c.cond_categoria_licencia, u.user_cedula,
            u.user_nombre || ' ' || u.user_apellidos AS nombre_usuario, u.user_cedula  AS cedula ,  u.user_nombre || ' ' || u.user_apellidos AS user_nombre_com,
            r.rol_cargo, rolcond_nombre FROM conductores c
            JOIN usuarios u ON c.conductor_usuario = u.user_id
            JOIN roles r ON u.user_rol_usuario = r.rol_id
			LEFT JOIN roles_conductor ON roles_conductor.rolcond_id = c.rolcond
            WHERE r.rol_id = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }


        /* MOSTRAR DATOS X ID AL EDITAR*/
        public function get_driver_id($cond_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM conductores WHERE cond_id=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $cond_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ACTUALIZAR LICENCIA */
        public function update_driver_licencia($cond_id,$cond_expedicion_licencia, $cond_vencimiento_licencia, $cond_categoria_licencia){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE conductores  SET cond_expedicion_licencia = ?, cond_vencimiento_licencia = ?, cond_categoria_licencia = ? WHERE cond_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $cond_expedicion_licencia);
            $sql->bindValue(2, $cond_vencimiento_licencia);
            $sql->bindValue(3, $cond_categoria_licencia);
            $sql->bindValue(4, $cond_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
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