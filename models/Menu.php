<?php
    /* CLASE MENU */
    class Menu extends Conectar {


         /* INSERTAR NUEVO MENU */
         public function insert_menu($menu_nom,$menu_ruta,$menu_estado,$menu_icono,$menu_identi,$menu_grupo){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO menu (menu_nom, menu_ruta, menu_estado, menu_icono, menu_identi, menu_grupo) VALUES (?,?,?,?,?,?)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $menu_nom); 
            $sql->bindValue(2, $menu_ruta);
            $sql->bindValue(3, $menu_estado); 
            $sql->bindValue(4, $menu_icono); 
            $sql->bindValue(5, $menu_identi); 
            $sql->bindValue(6, $menu_grupo); 
 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
        /* VERIFICAR SI EXISTE LA MATERIA PRIMA */
        public function menuExiste($menu_nom) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM menu WHERE menu_nom = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $menu_nom);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

         /* ACTUALIZAR */
         public function update_menu($menu_id, $menu_nom, $menu_ruta,$menu_estado,$menu_icono,$menu_identi,$menu_grupo){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE menu SET menu_nom=?, menu_ruta=?, menu_estado=?,  menu_icono =?, menu_identi=?, menu_grupo=? WHERE menu_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $menu_nom);
            $sql ->bindValue(2, $menu_ruta);
            $sql ->bindValue(3, $menu_estado);
            $sql ->bindValue(4, $menu_icono);
            $sql ->bindValue(5, $menu_identi);
            $sql ->bindValue(6, $menu_grupo);
            $sql ->bindValue(7, $menu_id);

            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR  */
        public function delete_menu($menu_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM menu WHERE menu_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $menu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTA X ID - MOSTRAR EN EDITAR */
        public function get_menu_id($menu_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM menu WHERE menu_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $menu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR LOS MENU */
        public function listar_menu(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM menu";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR LOS MENU SEGUN CADA ROL */
        public function listar_menu_xrol($tipo_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM menu INNER JOIN permiso ON menu.menu_id = permiso.permiso_menu WHERE permiso_rol=?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_id); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* HABILITAR EL MENU */
        public function update_habilitar($permiso_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE permiso SET permiso = 'Si' WHERE permiso_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $permiso_id); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* DESHABILITAR EL MENU */
        public function update_deshabilitar($permiso_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE permiso SET permiso = 'No' WHERE permiso_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $permiso_id); 
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