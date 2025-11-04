<?php
/* CLASE VER REPORTE DIARIO - ROL VERIFICADOR */
class  VerReporteDiario extends Conectar
{

    /* REPORTES DIARIOS*/
    public function  listarReportesDiarios()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_recib, repdia_fech, conductor_nombre_completo,repdia_estado, vehi_placa,tipo_id
            FROM (
                SELECT repdia_recib, CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,repdia_fech,repdia_estado,vehi_placa,tipo_id, 
                        ROW_NUMBER() OVER (PARTITION BY repdia_recib ORDER BY repdia_fech DESC) AS rn
                FROM reportes_diarios
                INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
                INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
                LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
                GROUP BY repdia_recib, conductor_nombre_completo, repdia_fech, repdia_estado, vehi_placa, tipo_id
            ) AS ranked
            WHERE rn = 1 
            ORDER BY repdia_fech DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarReportesDiariosCond($repdia_cond)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_recib, repdia_fech, conductor_nombre_completo,repdia_estado, vehi_placa,tipo_id,repdia_cond, repdia_firma
            FROM (
                SELECT repdia_recib, CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,repdia_fech,repdia_estado,
				vehi_placa,tipo_id,repdia_cond,repdia_firma,
                        ROW_NUMBER() OVER (PARTITION BY repdia_recib ORDER BY repdia_fech DESC) AS rn
                FROM reportes_diarios
                INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
                INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
                LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
                GROUP BY repdia_recib, conductor_nombre_completo, repdia_fech, repdia_estado,vehi_placa,tipo_id,repdia_cond,repdia_firma
            ) AS ranked
            WHERE rn = 1 and repdia_cond=?
            ORDER BY repdia_fech DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_cond);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarReportesDiariosAdmin()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_id, repdia_fech, conductor_nombre_completo,obras_nom, vehi_placa,tipo_id,repdia_kilo,repdia_kilo_final, act_nombre
            FROM (
                SELECT repdia_id, CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,repdia_fech,obras_nom,vehi_placa,tipo_id,
                repdia_kilo, repdia_kilo_final, act_nombre    
                FROM reportes_diarios
                INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
                INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
                LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
				LEFT JOIN obras ON obras.obras_id = reportes_diarios.repdia_obras  
                LEFT JOIN actividades ON actividades.act_id = reportes_diarios.repdia_actv    
            ) AS ranked
            ORDER BY repdia_fech DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarRepdiaAdminxConductor($repdia_cond)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_id, repdia_fech, conductor_nombre_completo,obras_nom, vehi_placa,tipo_id,repdia_kilo,repdia_kilo_final, act_nombre,repdia_cond
            FROM (
                SELECT repdia_id, CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,repdia_fech,obras_nom,vehi_placa,tipo_id,
                repdia_kilo, repdia_kilo_final, act_nombre,repdia_cond    
                FROM reportes_diarios
                INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
                INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
                LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
				LEFT JOIN obras ON obras.obras_id = reportes_diarios.repdia_obras  
                LEFT JOIN actividades ON actividades.act_id = reportes_diarios.repdia_actv    
            ) AS ranked
            WHERE repdia_cond =?
            ORDER BY repdia_fech DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_cond);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    // FILTRO DE BUSQUEDA DE REPORTES DIARIOS
    public function  filtrorepdia($repdia_user, $repdia_vehi, $fecha_inicio, $fecha_final)
    {
        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_vehi === '') {
            $repdia_vehi = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_user === '') {
            $repdia_user = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_inicio === '') {
            $fecha_inicio = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_final === '') {
            $fecha_final = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM ReporteDiario(?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_user);
        $sql->bindValue(2, $repdia_vehi);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarReportesDiariosPlaca($repdia_vehi)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_placa, repdia_fech, CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo, vehi_placa, repdia_recib
            FROM reportes_diarios
            INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
            INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
            WHERE repdia_vehi =? 
            GROUP BY repdia_placa,repdia_fech,conductor_nombre_completo,vehi_placa,repdia_recib
            ORDER BY repdia_fech DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarActividadesCerar($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_id, repdia_kilo, vehi_placa, repdia_recib, repdia_obras, act_nombre,obras_nom
            FROM reportes_diarios
            INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
            INNER JOIN obras ON reportes_diarios.repdia_obras = obras.obras_id
			INNER JOIN actividades ON reportes_diarios.repdia_actv = actividades.act_id
            INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
            where repdia_recib=? and repdia_kilo_final < '1'
            ORDER BY repdia_fech DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR EL KILOMETRAJE FINAL */
    public function update_user_pass($repdia_id, $repdia_kilo_final)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE reportes_diarios SET repdia_kilo_final = ?, repdia_hr_term = NOW() WHERE repdia_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_kilo_final);
        $sql->bindValue(2, $repdia_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* INSERTAR LA FIRMA DEL AUTORIZADO */
    public function update_firma($repdia_firma, $repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE reportes_diarios SET repdia_firma = ? WHERE repdia_recib=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_firma);
        $sql->bindValue(2, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* EDITAR EL REPORTE DIARIO */
    public function update_repdia($repdia_id, $repdia_kilo, $repdia_kilo_final, $repdia_obras)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE reportes_diarios SET repdia_kilo = ?, repdia_kilo_final = ?, repdia_obras = ? WHERE repdia_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_kilo);
        $sql->bindValue(2, $repdia_kilo_final);
        $sql->bindValue(3, $repdia_obras);
        $sql->bindValue(4, $repdia_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_repdia_id($repdia_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM reportes_diarios INNER JOIN obras ON obras.obras_id = reportes_diarios.repdia_obras WHERE repdia_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR POR REPORTE DEL DIA */
    public function get_repdia_recib_id($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM reportes_diarios  WHERE repdia_recib=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR RESPUESTAS */
    public function detalle($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            reportes_diarios.repdia_fech,
			reportes_diarios.repdia_hr_inic,
			reportes_diarios.repdia_hr_term,
			reportes_diarios.repdia_volu,
			reportes_diarios.repdia_recib,
			reportes_diarios.repdia_gaso,
			reportes_diarios.repdia_acpm,
			reportes_diarios.repdia_acet_moto,
			reportes_diarios.repdia_acet_hidr,
			reportes_diarios.repdia_acet_tram,
			reportes_diarios.repdia_acet_gras,
            reportes_diarios.repdia_puntas,
			actividades.act_nombre,
            u1.user_cedula,
            CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS conductor_nombre_completo,
			CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS residente_nombre_completo,
			CONCAT(u3.user_nombre, ' ', u3.user_apellidos) AS inspec_nombre_completo,
            vehiculos.vehi_placa,
            tipo_vehiculo.tipo_nombre,
            tipo_vehiculo.tipo_id,
			reportes_diarios.repdia_kilo,
            reportes_diarios.repdia_kilo_final,
            reportes_diarios.repdia_obras,
            reportes_diarios.repdia_ca,
             reportes_diarios.repdia_km_hr,
            obras.obras_nom,
            reportes_diarios.repdia_observa
        FROM reportes_diarios
        INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
		INNER JOIN actividades ON reportes_diarios.repdia_actv = actividades.act_id
        INNER JOIN usuarios u1 ON reportes_diarios.repdia_cond = u1.user_id
		LEFT JOIN usuarios u2 ON reportes_diarios.repdia_residente = u2.user_id
		LEFT JOIN usuarios u3 ON reportes_diarios.repdia_inspec = u3.user_id
		INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        INNER JOIN obras ON obras.obras_id = reportes_diarios.repdia_obras
        WHERE reportes_diarios.repdia_recib = ?
		GROUP BY reportes_diarios.repdia_fech,reportes_diarios.repdia_hr_inic,reportes_diarios.repdia_hr_term,reportes_diarios.repdia_volu,reportes_diarios.repdia_recib,reportes_diarios.repdia_gaso,
		reportes_diarios.repdia_acpm,reportes_diarios.repdia_acet_moto,reportes_diarios.repdia_acet_hidr,reportes_diarios.repdia_acet_tram,reportes_diarios.repdia_acet_gras,actividades.act_nombre,
        u1.user_cedula,conductor_nombre_completo,residente_nombre_completo,inspec_nombre_completo,vehiculos.vehi_placa,tipo_vehiculo.tipo_nombre,reportes_diarios.repdia_kilo,reportes_diarios.repdia_obras,
        reportes_diarios.repdia_observa, obras.obras_nom,reportes_diarios.repdia_kilo_final,reportes_diarios.repdia_puntas,tipo_vehiculo.tipo_id,reportes_diarios.repdia_ca,
             reportes_diarios.repdia_km_hr";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function RDMensuales($repdia_vehi, $repdia_user, $fecha_inicio, $fecha_final)
    {

        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_vehi === '') {
            $repdia_vehi = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_user === '') {
            $repdia_user = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_inicio === '') {
            $fecha_inicio = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_final === '') {
            $fecha_final = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM reportes_mensuales(?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_vehi);
        $sql->bindValue(2, $repdia_user);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function detalleCombustible($repdia_placa)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT SUM (repdia_kilo_final - repdia_kilo) AS kilometraje,
			reportes_diarios.repdia_fech,
			reportes_diarios.repdia_gaso,
			reportes_diarios.repdia_acpm,
            vehiculos.vehi_placa,
			reportes_diarios.repdia_kilo,
            reportes_diarios.repdia_kilo_final,
			reportes_diarios.repdia_placa,
			reportes_diarios.repdia_recib,
			tipo_vehiculo.tipo_nombre,
			actividades.act_nombre
            FROM reportes_diarios
            INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
		    INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
			INNER JOIN actividades ON reportes_diarios.repdia_actv = actividades.act_id
			WHERE reportes_diarios.repdia_placa= ?
		    GROUP BY reportes_diarios.repdia_fech,reportes_diarios.repdia_gaso,reportes_diarios.repdia_acpm,
            vehiculos.vehi_placa,reportes_diarios.repdia_kilo, reportes_diarios.repdia_kilo_final,reportes_diarios.repdia_placa,
			reportes_diarios.repdia_recib, tipo_vehiculo.tipo_nombre,actividades.act_nombre";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_placa);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR AL EDITAR */
    public function  mostrarReportesDiarios($repdia_recib)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_recib 
            FROM reportes_diarios
            INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
            INNER JOIN actividades ON reportes_diarios.repdia_actv = actividades.act_id
            INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
            WHERE repdia_recib = ?
            GROUP BY repdia_recib;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_recib);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarReportesDiarios_Calendario($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repdia_recib as id,
                conductor_nombre_completo as title,
                repdia_fech as start
                FROM (
                SELECT repdia_recib, CONCAT(usuarios.user_nombre, ' - ',vehiculos.vehi_placa) AS conductor_nombre_completo,repdia_fech,user_id, 
                        ROW_NUMBER() OVER (PARTITION BY repdia_recib ORDER BY repdia_fech DESC) AS rn
                FROM reportes_diarios
                INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
                INNER JOIN usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
                LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
                GROUP BY repdia_recib, conductor_nombre_completo, repdia_fech, user_id
            ) AS ranked
            WHERE rn = 1 and user_id=?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function lsitarConsumibles($repdia_obras, $repdia_vehi, $fecha_inicio, $fecha_final)
    {

        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_obras === '') {
            $repdia_obras = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($repdia_vehi === '') {
            $repdia_vehi = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_inicio === '') {
            $fecha_inicio = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_final === '') {
            $fecha_final = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM Rte_Consumibles(?, ?, ?, ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repdia_obras);
        $sql->bindValue(2, $repdia_vehi);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    public function detalleConsumibles($vehi_id, $obra, $fecha_inicio, $fecha_final)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
        repdia_fech,
        vehi_placa,
        tipo_nombre,
        act_nombre,
        SUM(act_tarifa) AS tarifa,
        obras_nom,
        repdia_kilo,
        repdia_kilo_final,
        SUM(repdia_kilo_final - repdia_kilo) AS KH_gastado,
        SUM(repdia_gaso) AS gasolina,
        SUM(repdia_puntas) AS puntas, 
        SUM(repdia_acpm) AS acpm,
        SUM(repdia_acet_moto) AS aceite_motor,
        SUM(repdia_acet_hidr) AS aceite_hidraulico,
        SUM(repdia_acet_tram) AS aceite_trasmicion,
        SUM(repdia_acet_gras) AS grasa,
        SUM(repdia_volu) AS volumen,
        CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS operador,
        CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS inspector,
        repdia_observa,
        SUM(act_tarifa * repdia_volu * (repdia_kilo_final - repdia_kilo)) AS facturacion
        FROM reportes_diarios
        INNER JOIN vehiculos ON reportes_diarios.repdia_vehi = vehiculos.vehi_id
        INNER JOIN obras ON reportes_diarios.repdia_obras = obras.obras_id
        INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        INNER JOIN actividades ON reportes_diarios.repdia_actv = actividades.act_id
        LEFT JOIN usuarios u1 ON reportes_diarios.repdia_cond = u1.user_id
        LEFT JOIN usuarios u2 ON reportes_diarios.repdia_inspec = u2.user_id
        WHERE vehi_id = ?";

        $params = [$vehi_id];

        // Agregar obra si se proporciona
        if (!is_null($obra) && $obra !== '') {
            $sql .= " AND repdia_obras = ?";
            $params[] = $obra;
        }

        // Agregar fechas si ambas están presentes
        if (!is_null($fecha_inicio) && !is_null($fecha_final) && $fecha_inicio !== '' && $fecha_final !== '') {
            $sql .= " AND repdia_fech BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_final;
        }

        $sql .= "
        GROUP BY
        repdia_fech,
        vehi_placa, 
        tipo_nombre,
        obras_nom,
        act_nombre,
        repdia_observa,
        operador,
        inspector,
        repdia_kilo,
        repdia_kilo_final";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function listarCumplimientoInspec()
    {
        // Obtener los parámetros de mes y año desde el formulario
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];

        // Validar que mes y año sean válidos
        if (empty($mes) || empty($anio)) {
            throw new Exception("Mes y año son requeridos");
        }

        $conectar = parent::conexion();
        parent::set_names();

        // Consulta SQL compatible con PostgreSQL
        $sql = "
			WITH reporte_agrupado AS (
            SELECT 
                ro.ro_id_operador AS operador,
                ro.ro_id_inspector AS inspector,
                TO_CHAR(ro.ro_fecha::DATE, 'YYYY-MM') AS mes_agrupado,
                COUNT(ro.ro_id_operador) AS total_operador
            FROM reporte_obra ro
            WHERE ro.ro_fecha::DATE BETWEEN TO_DATE(:anio || '-' || :mes || '-01', 'YYYY-MM-DD') 
                                        AND (TO_DATE(:anio || '-' || :mes || '-01', 'YYYY-MM-DD') + INTERVAL '1 MONTH' - INTERVAL '1 DAY')
            GROUP BY ro.ro_id_operador, ro.ro_id_inspector, TO_CHAR(ro.ro_fecha::DATE, 'YYYY-MM')
        ),
        datos_agregados AS (
            SELECT 
                ra.operador,
                ra.inspector,
                ra.mes_agrupado AS mes,
                ra.total_operador,
                (
                    SELECT COUNT(DISTINCT rd.repdia_fech)
                    FROM reportes_diarios rd
                    WHERE rd.repdia_cond = ra.operador
                    AND rd.repdia_inspec = ra.inspector
                    AND TO_CHAR(rd.repdia_fech::DATE, 'YYYY-MM') = ra.mes_agrupado
                    AND rd.repdia_fech::DATE BETWEEN TO_DATE(:anio || '-' || :mes || '-01', 'YYYY-MM-DD') 
                                                AND (TO_DATE(:anio || '-' || :mes || '-01', 'YYYY-MM-DD') + INTERVAL '1 MONTH' - INTERVAL '1 DAY')
                ) AS total_dias
            FROM reporte_agrupado ra
        ),
        porcentaje_cumplimiento_por_operador AS (
            SELECT 
                da.operador,
                da.inspector,
                da.mes,
                (CAST(da.total_dias AS FLOAT) / NULLIF(da.total_operador, 0)) * 100 AS porcentaje_cumplimiento
            FROM datos_agregados da
        ),
        cumplimiento_por_inspector AS (
            SELECT 
                pco.inspector,
                AVG(pco.porcentaje_cumplimiento) AS porcentaje_cumplimiento_inspector,
                COUNT(DISTINCT pco.operador) AS total_operadores
            FROM porcentaje_cumplimiento_por_operador pco
            GROUP BY pco.inspector
        )
        SELECT
            cpi.inspector AS ro_id_inspector,
            CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS inpector,
            cpi.total_operadores AS operador,
            ROUND(CAST(cpi.porcentaje_cumplimiento_inspector AS NUMERIC), 2) AS cumplimiento
        FROM cumplimiento_por_inspector cpi
        LEFT JOIN usuarios u2 ON cpi.inspector = u2.user_id
        ORDER BY cpi.inspector;






        ";

        $sql = $conectar->prepare($sql);

        // Asignar parámetros de forma segura
        $sql->bindParam(':mes', $mes, PDO::PARAM_INT);
        $sql->bindParam(':anio', $anio, PDO::PARAM_INT);

        // Ejecutar la consulta
        $sql->execute();

        // Retornar los resultados
        return $resultado = $sql->fetchAll();
    }

    public function DetalleCumplimiento($ro_id_inspector)
    {
        // Obtener los parámetros de mes y año desde el formulario
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];

        // Validar que mes y año sean válidos
        if (empty($mes) || empty($anio)) {
            throw new Exception("Mes y año son requeridos");
        }

        $conectar = parent::conexion();
        parent::set_names();

        // Consulta SQL
        $sql = "
            SELECT nombre_operador AS operador,ROUND((porcentaje_cumplimiento::DECIMAL), 2) AS porcentaje_cumplimiento FROM calcular_cumplimiento_por_mes(:anio, :mes) WHERE inspector = :ro_id_inspector;
        ";

        // Preparar la consulta
        $sql = $conectar->prepare($sql);

        // Asignar parámetros de forma segura
        $sql->bindParam(':ro_id_inspector', $ro_id_inspector, PDO::PARAM_INT);
        $sql->bindParam(':mes', $mes, PDO::PARAM_STR);
        $sql->bindParam(':anio', $anio, PDO::PARAM_STR);

        // Ejecutar la consulta
        $sql->execute();

        // Retornar los resultados
        return $sql->fetchAll();
    }

    public function CumplimientoCond()
    {

        $fecha_inicio = $_POST['fecha_inicio']; // Obtener la fecha de inicio desde un formulario HTML
        $fecha_final = $_POST['fecha_final'];

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
        CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor,
        ROUND((COUNT(DISTINCT preoperacional.pre_formulario) * 100.0 / 25), 2) AS porcentaje_preoperacionales,
        ROUND((COUNT(DISTINCT reportes_diarios.repdia_recib) * 100.0 / 25), 2) AS porcentaje_repdia
        FROM 
            preoperacional
        INNER JOIN
            usuarios ON preoperacional.pre_user = usuarios.user_id
        INNER JOIN 
            reportes_diarios ON reportes_diarios.repdia_cond = usuarios.user_id
        WHERE 
            preoperacional.pre_fecha_crea_form BETWEEN '$fecha_inicio' AND '$fecha_final' 
            AND 
            reportes_diarios.repdia_fech BETWEEN '$fecha_inicio' AND '$fecha_final'
        GROUP BY 
           conductor;";
        $sql = $conectar->prepare($sql);
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
2023 */
?>