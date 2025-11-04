<?php
    /* CLASE TIPO DE VEHICULO */
    class Actividades extends Conectar{

        /* INSERTAR */
        public function insert_actividad($act_nombre, $act_tarifa, $act_tipo, $act_unidades){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO actividades(act_nombre, act_tarifa, act_tipo, act_unidades) VALUES (?,?,?,?);";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $act_nombre);
                $sql->bindValue(2, $act_tarifa);
                $sql->bindValue(3, $act_tipo);
                $sql->bindValue(4, $act_unidades); 
                $sql->execute();
                return $resultado=$sql->fetchAll();

        }
        /* VERIFICAR SI EL TIPO DE VEHICULO EXISTE */
        public function actividadExiste($act_nombre){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM actividades WHERE act_nombre = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $act_nombre);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /* ACTUALIZAR  */
        public function update_actividad($act_id, $act_nombre, $act_tarifa, $act_tipo, $act_unidades){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE actividades SET act_nombre = ?, act_tarifa = ?, act_tipo = ?, act_unidades = ?  WHERE act_id = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $act_nombre); 
            $sql->bindValue(2, $act_tarifa); 
            $sql->bindValue(3, $act_tipo);
            $sql->bindValue(4, $act_unidades); 
            $sql->bindValue(5, $act_id); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR */
        public function delete_actividad($act_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="DELETE FROM actividades WHERE act_id = ?";
            $sql=$conectar->prepare($sql);
            $sql -> bindValue(1, $act_id);
            $sql->execute();
            return $resultado=$sql->fetchAll(); 
        }

        /* LISTAR TIPOS DE VEHICULOS */
        public function get_actividad(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM actividades INNER JOIN tipo_vehiculo on tipo_vehiculo.tipo_id = actividades.act_tipo ";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        

        /* MOSTRAR DATOS AL EDITAR */
        public function get_actividad_id($tipo_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM actividades WHERE act_id=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $tipo_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

    }

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE:JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>