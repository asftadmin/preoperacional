<?php
    /* CLASE TIPO DE VEHICULO */
    class Falla extends Conectar{

        /* INSERTAR */
        public function insert_falla($fallas_nombre, $fallasid_oper){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO fallas (fallas_nombre, fallasid_oper) VALUES (?, ?);";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $fallas_nombre); 
                $sql->bindValue(2, $fallasid_oper);    
                $sql->execute();
                return $resultado=$sql->fetchAll();
        }
        /* VERIFICAR SI LA FALLA EXISTE */
        public function fallaExiste($fallas_nombre){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM fallas WHERE fallas_nombre = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $fallas_nombre);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /* ACTUALIZAR  */
        public function update_fallas($id_fallas, $fallas_nombre, $fallasid_oper){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE fallas SET fallas_nombre = ?, fallasid_oper = ? WHERE id_fallas = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $fallas_nombre);
            $sql->bindValue(2, $fallasid_oper); 
            $sql->bindValue(3, $id_fallas); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR */
        public function delete_falla($id_fallas){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="DELETE FROM fallas WHERE id_fallas = ?";
            $sql=$conectar->prepare($sql);
            $sql -> bindValue(1, $id_fallas);
            $sql->execute();
            return $resultado=$sql->fetchAll(); 
        }

        /* LISTAR FALLAS */
        public function get_falla(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = " SELECT * from fallas inner join operaciones ON fallas.fallasid_oper = operaciones.oper_id";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* MOSTRAR DATOS AL EDITAR */
        public function get_falla_id($id_fallas){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM fallas WHERE id_fallas=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $id_fallas);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

    }

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>