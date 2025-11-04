<?php

    /* CLASE OPERACIONES - ITEMS DEL FORMULARIO PREOPERACIONAL */
    class Operaciones extends Conectar{

        /* LISTAR */
        public function get_oper(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="SELECT *,
            CASE 
                WHEN oper_estado = 1 THEN 'ACTIVO'
                WHEN oper_estado = 0 THEN 'INACTIVO'
                END AS OPER_ESTADO
            FROM operaciones";

            $sql=$conectar -> prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* SELECT DE OPERACIONES */
        public function get_oper_combo(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="SELECT *,
            CASE 
                WHEN oper_estado = 1 THEN 'ACTIVO'
                WHEN oper_estado = 0 THEN 'INACTIVO'
                END AS OPER_ESTADO
            FROM operaciones WHERE oper_estado = 1 ";
            $sql=$conectar -> prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
       
        /* INSERTAR OPERACIONES */
        public function insert_oper($oper_nombre){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO operaciones (oper_nombre, oper_estado) VALUES (?, 1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $oper_nombre); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* VERIFICAR SI EXISTE LA OPERACION */
        public function operExiste($oper_nombre) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM operaciones WHERE oper_nombre = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $oper_nombre);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /* ACTUALIZAR */
        public function update_oper($oper_id, $oper_nombre, $oper_estado){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE operaciones SET oper_nombre=?, oper_estado=? WHERE oper_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $oper_nombre);
            $sql ->bindValue(2, $oper_estado);
            $sql ->bindValue(3, $oper_id);
            $sql->execute();

            if ($oper_estado == 0) {
                $sql = "UPDATE suboperaciones SET suboper_estado=0 WHERE suboper_oper=?";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $oper_id);
                $sql->execute();
            }else if($oper_estado == 1){
                $sql = "UPDATE suboperaciones SET suboper_estado=1 WHERE suboper_oper=?";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $oper_id);
                $sql->execute();
            }
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR OPERACION */
        public function delete_oper($oper_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM operaciones WHERE oper_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $oper_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTA X ID - MOSTRAR EN EDITAR */
        public function get_oper_id($oper_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM operaciones WHERE oper_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $oper_id);
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