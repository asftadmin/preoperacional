<?php

    /* CLASE PREOPERACIONAL */
    class  Preoperacional extends Conectar {

        /* LISTAR PREGUNTAS X ID */
        public function listar_preguntas($tipo_id) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT *  FROM tipo_vehiculo INNER JOIN suboperaciones ON suboperaciones.suboper_vehi = tipo_vehiculo.tipo_id
            INNER JOIN operaciones ON operaciones.oper_id = suboperaciones.suboper_oper WHERE tipo_id = ? AND operaciones.oper_estado=1 AND Suboperaciones.suboper_estado=1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$tipo_id);
            $sql->execute();
            return $resultado = $sql->fetchAll();
        }

        /* FUNCION PARA VERIFICAR SI YA SE REGISTRO EL FORMULARIO */
        public function preExiste($pre_formulario) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = 'SELECT COUNT(*) AS count FROM preoperacional WHERE pre_formulario = ?';
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$pre_formulario);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        }

        /* GUARDAR LAS PREGUNTAS */
        public function guardar_preguntas( $pre_id, $pre_observaciones, $pre_suboper, $pre_formulario, $pre_respuesta, $pre_kilometraje_inicial, $pre_usuario ) {
            $conectar = parent::conexion();
            parent::set_names();
            $pre_formulario = trim($pre_formulario);
            $sql = 'INSERT INTO preoperacional (pre_vehiculo, pre_observaciones, pre_fecha_crea_form, pre_suboper, pre_formulario, pre_repuesta, pre_estado,  pre_kilometraje_inicial, pre_hora, pre_user) VALUES (?, ?, NOW(), ?, ?, ?, NULL , ?,  NOW(), ?)';
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $pre_id, PDO::PARAM_INT);
            $sql->bindValue(2, $pre_observaciones, PDO::PARAM_STR);
            $sql->bindValue(3, $pre_suboper, PDO::PARAM_INT);
            $sql->bindValue(4, $pre_formulario, PDO::PARAM_STR);
            $sql->bindValue(5, $pre_respuesta, PDO::PARAM_STR);
            $sql->bindValue(6, $pre_kilometraje_inicial, PDO::PARAM_STR);
            $sql->bindValue(7, $pre_usuario, PDO::PARAM_INT);
            $sql->execute();
            return $resultado = $sql->fetchAll();
        } 
       /* FUNCION PARA LA GRAFICA DE KILOMETRAJE */
        public function get_kilometraje_grafico($vehi_placa) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "WITH ranked_preoperacional AS (
                SELECT 
                    pre_fecha_crea_form,
                    COALESCE(pre_kilometraje_inicial::numeric, 0) AS value,
                    LAG(COALESCE(pre_kilometraje_inicial::numeric, 0)) OVER (PARTITION BY pre_vehiculo ORDER BY pre_fecha_crea_form) AS prev_value,
                    vehiculos.vehi_placa
                FROM preoperacional
                INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
            )
            SELECT 
                CASE
                    WHEN 
                        (SELECT COUNT(*) FROM ranked_preoperacional WHERE pre_fecha_crea_form = current_date) = 0 
                        AND 
                        (SELECT COUNT(*) FROM ranked_preoperacional WHERE pre_fecha_crea_form = current_date - interval '1 day') = 0
                    THEN (
                        SELECT ABS(value - prev_value) FROM ranked_preoperacional
                        WHERE prev_value IS NOT NULL
                        ORDER BY pre_fecha_crea_form DESC
                        LIMIT 1
                    )
                    ELSE ABS(value - prev_value)
                END AS kilometraje
            FROM ranked_preoperacional
            WHERE ranked_preoperacional.vehi_placa = ?
            ORDER BY pre_fecha_crea_form DESC
            LIMIT 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $vehi_placa, PDO::PARAM_STR);
            $sql->execute();
            return $resultado = $sql->fetchAll();
        }

    }

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>