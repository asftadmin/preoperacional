<?php
    /* CLASE MENU */
    class Permiso extends Conectar {


         /* INSERTAR NUEVO PERMISO */
         public function insert_permiso($permiso_menu,$permiso_rol,$permiso){
            $conectar = parent::conexion();
            parent::set_names();
        
            // Verificar si el array de vehículos no está vacío
            if (empty($permiso_rol)) {
                throw new Exception('El array de Roles está vacío.');
            }
        
            // Iterar sobre el array de vehículos y ejecutar la consulta para cada uno
            foreach ($permiso_rol as $rol) {
                // Preparar la consulta para insertar en la tabla alistamiento
                $sql_insert = "INSERT INTO permiso (permiso_menu, permiso_rol, permiso, permiso_estado)
                    VALUES (:permiso_menu, :permiso_rol, :permiso, 1)";
                $stmt_insert = $conectar->prepare($sql_insert);
        
                // Ejecutar la consulta de inserción
                $stmt_insert->execute([
                    ':permiso_menu' => $permiso_menu,
                    ':permiso_rol' => $rol,
                    ':permiso' => $permiso
                ]);
            }
        }
        /* VERIFICAR SI EXISTE EL PERMISO */
        public function permisoExiste($permiso_menu, $permiso_rol) {
            $conectar = parent::conexion();
            parent::set_names();
        
            // Si $permiso_rol es un array, generamos los placeholders dinámicamente
            if (is_array($permiso_rol)) {
                $placeholders = implode(',', array_fill(0, count($permiso_rol), '?'));
                $sql = "SELECT COUNT(*) AS count FROM permiso WHERE permiso_menu = ? AND permiso_rol IN ($placeholders)";
                $stmt = $conectar->prepare($sql);
        
                // Unimos el $permiso_menu con los valores de $permiso_rol
                $params = array_merge([$permiso_menu], $permiso_rol);
            } else {
                // Si no es un array, ejecutamos la consulta normal
                $sql = "SELECT COUNT(*) AS count FROM permiso WHERE permiso_menu = ? AND permiso_rol = ?";
                $stmt = $conectar->prepare($sql);
                $params = [$permiso_menu, $permiso_rol];
            }
        
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result["count"] > 0;
        }
        

         /* ACTUALIZAR */
         public function update_permiso($permiso_id, $permiso_menu, $permiso_rol,$permiso){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE permiso SET permiso_menu=?, permiso_rol=?, permiso=? WHERE permiso_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $permiso_menu);
            $sql ->bindValue(2, $permiso_rol);
            $sql ->bindValue(3, $permiso);
            $sql ->bindValue(4, $permiso_id);

            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR  */
        public function delete_permiso($permiso_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM permiso WHERE permiso_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $permiso_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTA X ID - MOSTRAR EN EDITAR */
        public function get_permiso_id($permiso_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM permiso WHERE permiso_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $permiso_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR LOS MENU */
        public function listar_permisos(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM permiso INNER JOIN menu ON permiso.permiso_menu = menu.menu_id INNER JOIN roles ON permiso.permiso_rol = roles.rol_id";
            $sql = $conectar->prepare($sql);
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