<?php

/* CLASE REPORTES DIARIOS */
class  ReportesDiarios extends Conectar
{

    public function repExiste($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'SELECT COUNT(*) AS count FROM reportes_diarios WHERE repdia_recib = ? and repdia_estado =1 ';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function repEstado($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'UPDATE reportes_diarios SET repdia_estado = 1, repdia_hr_term = NOW() WHERE repdia_recib  = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR TIPOS DE ACTIVIDADES */
    public function combo_actividades($tipo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM actividades INNER JOIN tipo_vehiculo on tipo_vehiculo.tipo_id = actividades.act_tipo WHERE tipo_id =?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tipo_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR OBRAS ASFALTO */
    public function combo_obras_asfl()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM obras where obra_estado=1 and tipo_obra=1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* SELECT DE MATERIA PRIMA */
    public function get_mtprm_combo(){
        $conectar = parent::conexion();
        parent::set_names();
        $sql="SELECT *,
        CASE 
            WHEN mtprm_linea = 1 THEN 'CONCRETO'
            WHEN mtprm_linea = 0 THEN 'ASFALTO'
            END AS mtprm_linea
        FROM materia_prima ";
        $sql=$conectar -> prepare($sql);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    /* LISTAR OBRAS CONCRETO */
    public function combo_obras_cnct()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM obras where obra_estado=1 and tipo_obra=2";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_repdia_id($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM reportes_diarios WHERE repdia_recib = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS  */
    public function listar_repdia($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo, * FROM reportes_diarios 
                INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id 
                INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id 
                WHERE repdia_recib = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* GUARDAR LAS PREGUNTAS */
    public function guardar_preguntas($repdia_cond, $repdia_vehi, $repdia_actv, $repdia_volu, $repdia_recib, $repdia_gaso, $repdia_acpm, $repdia_acet_moto, $repdia_acet_hidr, $repdia_acet_tram, $repdia_acet_gras, $repdia_kilo, $repdia_estado, $repdia_placa, $repdia_observa, $repdia_obras, $repdia_kilo_final, $repdia_puntas, $repdia_mtprima,$repdia_residente,$repdia_inspec,$repdia_ca,$repdia_km_hr,$repdia_num_viajes)
    {
        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_mtprima === '') {
            $repdia_mtprima = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $repdia_recib = trim($repdia_recib);
        $sql = 'INSERT INTO reportes_diarios (repdia_cond,repdia_vehi,repdia_actv,repdia_fech,repdia_hr_inic,repdia_volu,repdia_recib,repdia_gaso,repdia_acpm,repdia_acet_moto,repdia_acet_hidr,repdia_acet_tram,repdia_acet_gras,repdia_kilo,repdia_estado,repdia_placa,repdia_observa,repdia_obras,repdia_kilo_final,repdia_puntas,repdia_mtprima,repdia_residente,repdia_inspec,repdia_ca,repdia_km_hr,repdia_num_viajes) VALUES (?, ?, ?, NOW(),now(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $repdia_cond, PDO::PARAM_INT);
        $sql->bindValue(2, $repdia_vehi, PDO::PARAM_INT);
        $sql->bindValue(3, $repdia_actv, PDO::PARAM_INT);
        $sql->bindValue(4, $repdia_volu, PDO::PARAM_INT);
        $sql->bindValue(5, $repdia_recib, PDO::PARAM_STR);
        $sql->bindValue(6, $repdia_gaso, PDO::PARAM_INT);
        $sql->bindValue(7, $repdia_acpm, PDO::PARAM_INT);
        $sql->bindValue(8, $repdia_acet_moto, PDO::PARAM_INT);
        $sql->bindValue(9, $repdia_acet_hidr, PDO::PARAM_INT);
        $sql->bindValue(10, $repdia_acet_tram, PDO::PARAM_INT);
        $sql->bindValue(11, $repdia_acet_gras, PDO::PARAM_INT);
        $sql->bindValue(12, $repdia_kilo, PDO::PARAM_INT);
        $sql->bindValue(13, $repdia_estado, PDO::PARAM_INT);
        $sql->bindValue(14, $repdia_placa, PDO::PARAM_STR);
        $sql->bindValue(15, $repdia_observa, PDO::PARAM_STR);
        $sql->bindValue(16, $repdia_obras, PDO::PARAM_STR);
        $sql->bindValue(17, $repdia_kilo_final, PDO::PARAM_STR);
        $sql->bindValue(18, $repdia_puntas, PDO::PARAM_INT);
        $sql->bindValue(19, $repdia_mtprima === null ? null : $repdia_mtprima, $repdia_mtprima === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $sql->bindValue(20, $repdia_residente, PDO::PARAM_INT);
        $sql->bindValue(21, $repdia_inspec, PDO::PARAM_INT);
        $sql->bindValue(22, $repdia_ca, PDO::PARAM_STR);
        $sql->bindValue(23, $repdia_km_hr  === null ? null : $repdia_km_hr, $repdia_km_hr === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $sql->bindValue(24, $repdia_num_viajes  === null ? null : $repdia_num_viajes, $repdia_num_viajes === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* CONSULTA RENDIMIENTO */
    public function get_repdia_grafico($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            DISTINCT(repdia_fech),
            SUM(repdia_kilo_final - repdia_kilo) AS kilometraje,
            SUM(repdia_gaso + repdia_acpm) AS consumo,
            CASE 
                WHEN SUM(repdia_gaso + repdia_acpm) <> 0 THEN 
                    CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC) / CAST(SUM(repdia_gaso + repdia_acpm) AS NUMERIC)
                ELSE NULL
            END AS rendimiento, 
            vehi_placa
        FROM 
            reportes_diarios 
        INNER JOIN 
            vehiculos 
        ON 
            reportes_diarios.repdia_vehi = vehiculos.vehi_id
        WHERE 
            (repdia_gaso + repdia_acpm) <> 0 
            AND repdia_vehi = ?
        GROUP BY 
            repdia_fech, vehi_placa
        ORDER BY 
            repdia_fech DESC
        LIMIT 5;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* CONSULTA RENDIMIENTO HOROMETRAJE */
    public function get_repdia_grafico_hrs($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            SUM(repdia_kilo_final - repdia_kilo) AS horometraje,
            SUM(repdia_gaso + repdia_acpm) AS consumo,
            CASE 
                WHEN SUM(repdia_kilo_final - repdia_kilo) <> 0 THEN 
                    CAST(SUM(repdia_gaso + repdia_acpm) AS NUMERIC) / CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC)
                ELSE NULL
            END AS rendimiento, 
            vehi_placa, 
            repdia_fech
        FROM reportes_diarios 
            INNER JOIN vehiculos 
            ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        WHERE (repdia_gaso + repdia_acpm) <> 0 
        AND repdia_vehi = ?
        GROUP BY repdia_fech, vehi_placa
        LIMIT 5;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_repdia_tabla_grafico($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            SUM (repdia_kilo_final - repdia_kilo) AS kilometraje,
            SUM (repdia_gaso + repdia_acpm) AS consumo,
            CASE 
                WHEN SUM (repdia_gaso + repdia_acpm) <> 0 THEN CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC) / CAST(SUM(repdia_gaso + repdia_acpm) AS NUMERIC)
                ELSE NULL
            END AS rendimiento, vehi_placa, repdia_fech, repdia_placa
        FROM reportes_diarios INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        WHERE (repdia_gaso + repdia_acpm) <> 0 and repdia_vehi =?
        GROUP BY repdia_fech, vehi_placa,repdia_placa";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_repdia_tabla_grafico_hrs($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            SUM (repdia_kilo_final - repdia_kilo) AS kilometraje,
            SUM (repdia_gaso + repdia_acpm) AS consumo,
            CASE 
                WHEN SUM (repdia_kilo_final - repdia_kilo) <> 0 THEN CAST(SUM(repdia_gaso + repdia_acpm) AS NUMERIC) / CAST(SUM (repdia_kilo_final - repdia_kilo) AS NUMERIC)
                ELSE NULL
            END AS rendimiento, vehi_placa, repdia_fech, repdia_placa
        FROM reportes_diarios INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        WHERE (repdia_kilo_final - repdia_kilo) <> 0 and repdia_vehi =?
        GROUP BY repdia_fech, vehi_placa,repdia_placa";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function detalle_tabla_grafico($repdia_placa)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            SUM (repdia_kilo_final - repdia_kilo) AS kilometraje,
            SUM (repdia_gaso + repdia_acpm) AS consumo,
            CASE 
                WHEN SUM (repdia_gaso + repdia_acpm) <> 0 THEN CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC) / CAST(SUM(repdia_gaso + repdia_acpm) AS NUMERIC)
                ELSE NULL
            END AS rendimiento, vehi_placa, repdia_fech, repdia_kilo, repdia_kilo_final, repdia_gaso, repdia_acpm, vehi_id
        FROM reportes_diarios INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        WHERE (repdia_gaso + repdia_acpm) <> 0 and repdia_placa = ?
		GROUP BY  vehi_placa, repdia_fech, repdia_kilo, repdia_kilo_final, repdia_gaso, repdia_acpm, vehi_id";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_placa);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function detalle_tabla_grafico_hrs($repdia_placa)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            SUM (repdia_kilo_final - repdia_kilo) AS kilometraje,
            SUM (repdia_gaso + repdia_acpm) AS consumo,
            CASE 
                WHEN SUM (repdia_gaso + repdia_acpm) <> 0 THEN CAST(SUM(repdia_gaso + repdia_acpm) AS NUMERIC) / CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC)
                ELSE NULL
            END AS rendimiento, vehi_placa, repdia_fech, repdia_kilo, repdia_kilo_final, repdia_gaso, repdia_acpm, vehi_id
        FROM reportes_diarios INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        WHERE (repdia_kilo_final - repdia_kilo) <> 0 and repdia_placa = ?
		GROUP BY  vehi_placa, repdia_fech, repdia_kilo, repdia_kilo_final, repdia_gaso, repdia_acpm, vehi_id";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_placa);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* CONSULTA RENDIMIENTO FRESADORA */
    public function get_repdia_grafico_fresadora($repdia_vehi)
    {
        $fecha_inicio = $_POST['fecha_inicio']; // Obtener la fecha de inicio desde un formulario HTML
        $fecha_final = $_POST['fecha_final'];

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
        repdia_fech,
        vehi_placa,
        tipo_nombre,
        SUM(repdia_kilo_final - repdia_kilo) AS total_kilometraje,
        SUM(repdia_volu) AS total_volumen,
        CAST(SUM(repdia_volu) AS NUMERIC) / CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC) AS rendimiento
        FROM 
        reportes_diarios 
        LEFT JOIN 
        vehiculos 
        ON 
        reportes_diarios.repdia_vehi = vehiculos.vehi_id
        LEFT JOIN
        tipo_vehiculo
        ON
        vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        WHERE 
        repdia_vehi = ?  AND repdia_fech BETWEEN '$fecha_inicio' AND '$fecha_final' 
        GROUP BY 
        repdia_fech, vehi_placa,tipo_nombre;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_repdia_tabla_grafico_frsd($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
        repdia_fech,
        vehi_placa,
        tipo_nombre,
		repdia_placa,
        SUM(repdia_kilo) AS total_kilometraje_inicial,
        SUM(repdia_kilo_final) AS total_kilometraje_final,
        SUM(repdia_kilo_final - repdia_kilo) AS total_kilometraje,
        SUM(repdia_volu) AS total_volumen,
        CAST(SUM(repdia_volu) AS NUMERIC) / CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC) AS rendimiento
        FROM 
            reportes_diarios 
        LEFT JOIN 
            vehiculos 
            ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        LEFT JOIN
            tipo_vehiculo
            ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        WHERE 
            repdia_vehi = ?
            AND (repdia_kilo_final - repdia_kilo) <> 0
        GROUP BY 
            repdia_fech, vehi_placa, tipo_nombre,repdia_placa;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function detalle_tabla_grafica_frsd($repdia_placa)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
        repdia_fech,
        vehi_placa,
        tipo_nombre,
		repdia_kilo,
		repdia_kilo_final,
        SUM(repdia_kilo_final - repdia_kilo) AS total_kilometraje,
		repdia_volu,
        SUM(repdia_volu) AS total_volumen,
        CAST(SUM(repdia_volu) AS NUMERIC) / CAST(SUM(repdia_kilo_final - repdia_kilo) AS NUMERIC) AS rendimiento,
		repdia_placa,
		tipo_nombre
        FROM 
        reportes_diarios 
        LEFT JOIN 
        vehiculos 
        ON 
        reportes_diarios.repdia_vehi = vehiculos.vehi_id
        LEFT JOIN
        tipo_vehiculo
        ON
        vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        WHERE 
        repdia_placa = ? AND
		(repdia_kilo_final - repdia_kilo) <> 0
        GROUP BY 
        repdia_fech, vehi_placa,tipo_nombre,repdia_volu,repdia_kilo_final,repdia_kilo,repdia_placa,tipo_nombre;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_placa);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* CONSULTA RENDIMIENTO FRESADORA  PUNTAS/M3 */
    public function get_repdia_grafico_frsd_pnts($repdia_vehi)
    {
        $fecha_inicio = $_POST['fecha_inicio']; // Obtener la fecha de inicio desde un formulario HTML
        $fecha_final = $_POST['fecha_final'];

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
        repdia_fech,
        vehi_placa,
        tipo_nombre,
        SUM(repdia_puntas) AS total_puntas,
        SUM(repdia_volu) AS total_volumen,
        CAST(SUM(repdia_puntas) AS NUMERIC) / CAST(SUM(repdia_volu) AS NUMERIC) AS rendimiento
        FROM 
        reportes_diarios 
        LEFT JOIN 
        vehiculos 
        ON 
        reportes_diarios.repdia_vehi = vehiculos.vehi_id
        LEFT JOIN
        tipo_vehiculo
        ON
        vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        WHERE 
        repdia_vehi = ?  AND repdia_fech BETWEEN '$fecha_inicio' AND '$fecha_final' AND repdia_volu <> 0
        GROUP BY 
        repdia_fech, vehi_placa,tipo_nombre;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    public function get_repdia_tabla_grafico_frsd_pnts($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
        repdia_fech,
        vehi_placa,
        tipo_nombre,
        SUM(repdia_puntas) AS total_puntas,
        SUM(repdia_volu) AS total_volumen,
        CAST(SUM(repdia_puntas) AS NUMERIC) / CAST(SUM(repdia_volu) AS NUMERIC) AS rendimiento,
        repdia_placa
        FROM 
        reportes_diarios 
        LEFT JOIN 
        vehiculos 
        ON 
        reportes_diarios.repdia_vehi = vehiculos.vehi_id
        LEFT JOIN
        tipo_vehiculo
        ON
        vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        WHERE 
        repdia_vehi = ?  AND repdia_volu <> 0
        GROUP BY 
        repdia_fech, vehi_placa,tipo_nombre,repdia_placa;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
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