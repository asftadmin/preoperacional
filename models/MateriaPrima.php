<?php

    /* CLASE MATERIA PRIMA - ITEMS DEL FORMULARIO PREOPERACIONAL */
    class MateriaPrima extends Conectar{

        /* LISTAR */
        public function get_mtprm(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="SELECT *,
            CASE 
                WHEN mtprm_linea = 1 THEN 'CONCRETO'
                WHEN mtprm_linea = 0 THEN 'ASFALTO'
                WHEN mtprm_linea = 2 THEN 'OBRA'
                END AS mtprm_linea
            FROM materia_prima";

            $sql=$conectar -> prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* SELECT DE MATERIA PRIMA */
        public function get_mtprm_combo(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="SELECT *,
            CASE 
                WHEN mtprm_linea = 1 THEN 'CONCRETO'
                WHEN mtprm_linea = 0 THEN 'ASFALTO'
                WHEN mtprm_linea = 2 THEN 'OBRAS'
                END AS mtprm_linea
            FROM materia_prima ";
            $sql=$conectar -> prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
       
        /* INSERTAR MATERIA PRIMA */
        public function insert_mtprm($mtprm_nombre,$mtprm_linea){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO materia_prima (mtprm_nombre, mtprm_linea) VALUES (?, ?)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $mtprm_nombre); 
            $sql->bindValue(2, $mtprm_linea); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* VERIFICAR SI EXISTE LA MATERIA PRIMA */
        public function mtprmExiste($mtprm_nombre) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM materia_prima WHERE mtprm_nombre = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $mtprm_nombre);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /* ACTUALIZAR */
        public function update_mtprm($mtprm_id, $mtprm_nombre, $mtprm_linea){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE materia_prima SET mtprm_nombre=?, mtprm_linea=? WHERE mtprm_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $mtprm_nombre);
            $sql ->bindValue(2, $mtprm_linea);
            $sql ->bindValue(3, $mtprm_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR  */
        public function delete_mtprm($mtprm_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM materia_prima WHERE mtprm_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $mtprm_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTA X ID - MOSTRAR EN EDITAR */
        public function get_mtprm_id($mtprm_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM materia_prima WHERE mtprm_id=?";
            $sql = $conectar->prepare($sql);
            $sql ->bindValue(1, $mtprm_id);
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
2023 */
?>