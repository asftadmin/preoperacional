<?php
    /* CLASE TIPO DE VEHICULO */
    class TipoVehiculo extends Conectar{

        /* INSERTAR */
        public function insert_typecar($tipo_nombre){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO tipo_vehiculo (tipo_nombre) VALUES (?);";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $tipo_nombre);    
                $sql->execute();
                return $resultado=$sql->fetchAll();

        }
        /* VERIFICAR SI EL TIPO DE VEHICULO EXISTE */
        public function typecarExiste($tipo_nombre){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM tipo_vehiculo WHERE tipo_nombre = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_nombre);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /* ACTUALIZAR  */
        public function update_typecar($tipo_id, $tipo_nombre){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE tipo_vehiculo SET tipo_nombre = ? WHERE tipo_id = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_nombre); 
            $sql->bindValue(2, $tipo_id); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR */
        public function delete_typecar($tipo_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="DELETE FROM tipo_vehiculo WHERE tipo_id = ?";
            $sql=$conectar->prepare($sql);
            $sql -> bindValue(1, $tipo_id);
            $sql->execute();
            return $resultado=$sql->fetchAll(); 
        }

        /* LISTAR TIPOS DE VEHICULOS */
        public function get_typecar(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM tipo_vehiculo";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* MOSTRAR DATOS AL EDITAR */
        public function get_typecar_id($tipo_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM tipo_vehiculo WHERE tipo_id=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $tipo_id);
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