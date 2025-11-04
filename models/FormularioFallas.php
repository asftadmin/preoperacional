<?php

    /* CLASE PREOPERACIONAL */
    class  FormularioFallas extends Conectar {

        /* LISTAR PREGUNTAS X ID */
        public function listar_preguntas() {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = " SELECT * from fallas inner join operaciones ON fallas.fallasid_oper = operaciones.oper_id where operaciones.oper_estado=1 and fallas.estado_fallas=1 ";
           $sql = $conectar->prepare($sql);
           $sql->execute();
            return $resultado = $sql->fetchAll();
        }

         /* FUNCION PARA VERIFICAR SI YA SE REGISTRO EL FORMULARIO */
         public function formExiste($pre_fallas) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = 'SELECT COUNT(*) AS count FROM formulario_fallas WHERE pre_fallas = ?';
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$pre_fallas);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        }

         /* FUNCION PARA VERIFICAR SI YA SE REGISTRO EL FORMULARIO */
         public function formExisteP($form_id) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = 'SELECT COUNT(*) AS count FROM formulario_fallas WHERE form_id = ?';
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$form_id);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        }

        /* GUARDAR LAS PREGUNTAS */
        public function guardar_preguntas($form_vehi, $form_fallas, $form_respuesta,$pre_fallas,$pre_user) {
            $conectar = parent::conexion();
            parent::set_names();
            $pre_fallas = trim($pre_fallas);
            $sql = 'INSERT INTO formulario_fallas (form_vehiculos,form_fallas,form_respuesta,fecha_form,pre_fallas,pre_user) VALUES (?, ?, ?, NOW(),?,?)';
            $sql = $conectar->prepare($sql);
           
            $sql->bindValue(1, $form_vehi, PDO::PARAM_INT);
            $sql->bindValue(2, $form_fallas, PDO::PARAM_INT);
            $sql->bindValue(3, $form_respuesta, PDO::PARAM_STR);
            $sql->bindValue(4, $pre_fallas, PDO::PARAM_STR);
            $sql->bindValue(5, $pre_user, PDO::PARAM_INT);

            
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
2024 */
?>