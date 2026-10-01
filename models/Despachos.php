<?php

/* CLASE DESPACHOS ACPM */
class  Despachos extends Conectar {
    /* GUARDAR LOS DESPACHOS */
    public function guardar_despacho($desp_vehi, $desp_galones, $desp_user, $desp_obra) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = 'INSERT INTO despachos_acpm (desp_fech_crea,desp_vehi,desp_galones_autorizados,desp_estado,desp_user,desp_obra) VALUES (NOW(),?,?,0,?,?)';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_vehi, PDO::PARAM_INT);
        $sql->bindValue(2, $desp_galones, PDO::PARAM_INT);
        $sql->bindValue(3, $desp_user, PDO::PARAM_INT);
        $sql->bindValue(4, $desp_obra, PDO::PARAM_INT);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR */
    public function update_despacho($desp_id, $desp_galones, $desp_recibo, $desp_km_hr, $desp_cond, $desp_observaciones, $desp_despachador) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE despachos_acpm SET desp_galones = ?,
        desp_recibo = ?,
        desp_fech = NOW(),
        desp_km_hr = ?,
        desp_cond = ?,
        desp_hora = now(),
        desp_observaciones = ?,
        desp_despachador = ?
        WHERE desp_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_galones);
        $sql->bindValue(2, $desp_recibo);
        $sql->bindValue(3, $desp_km_hr);
        $sql->bindValue(4, $desp_cond);
        $sql->bindValue(5, $desp_observaciones);
        $sql->bindValue(6, $desp_despachador);
        $sql->bindValue(7, $desp_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR DESPACHOS */
    public function get_despachos($user_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CASE
        WHEN desp_estado = 0 THEN 'Activo'
        WHEN desp_estado = 1 THEN 'Anulado'
        ELSE NULL 
        END AS desp_estado FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id
        WHERE desp_user=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR DESPACHOS ACTIVOS */
    public function get_despachos_activos() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *
        FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id 
        WHERE desp_estado=0 and desp_galones is null";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VALIDACION DE EXISTENCIA */
    public function despExiste($desp_vehi) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'SELECT COUNT(*) AS count FROM despachos_acpm WHERE desp_vehi = ? and desp_estado =3 ';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_vehi);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /* ACTUALIZAR EL ESTADO A ANULADO*/
    public function CambioEstado($desp_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'UPDATE despachos_acpm SET desp_estado = 1  WHERE desp_id = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_despacho_id($desp_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM despachos_acpm INNER JOIN vehiculos ON despachos_acpm.desp_vehi = vehiculos.vehi_id  WHERE desp_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* REPORTE DESPACHOS */
    public function RpteDespachos($desp_vehi, $desp_cond, $fecha_inicio, $fecha_final) {

        // Convertir cadena vacía en NULL si es necesario
        if ($desp_vehi === '') {
            $desp_vehi = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($desp_cond === '') {
            $desp_cond = null;
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
        $sql = "SELECT * FROM filtro_despachos(?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $desp_vehi);
        $sql->bindValue(2, $desp_cond);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_km_gal_individual($vehi_id, $obra = '', $fechaIni = '', $fechaFin = '') {
        return $this->get_rendimiento_diario($vehi_id, $obra, $fechaIni, $fechaFin);
    }

    // Sumar las fuentes por separado evita duplicar galones por actividad.
    private function get_rendimiento_diario($vehi_id, $obra, $fechaIni, $fechaFin) {
        $conectar = parent::conexion();
        $sql = "WITH parametros AS (
            SELECT CAST(:vehi_id AS integer) AS vehiculo,
                   CAST(:obra AS integer) AS obra,
                   CAST(:fechaIni AS date) AS inicio,
                   CAST(:fechaFin AS date) AS fin
        ), recorridos AS (
            SELECT r.repdia_fech::date AS fecha,
                   MIN(r.repdia_kilo) AS inicial, MAX(r.repdia_kilo_final) AS final,
                   SUM(r.repdia_kilo_final - r.repdia_kilo) AS recorrido
            FROM reportes_diarios r CROSS JOIN parametros p
            WHERE r.repdia_vehi = p.vehiculo
              AND r.repdia_kilo >= 0 AND r.repdia_kilo_final >= r.repdia_kilo
              AND (p.obra IS NULL OR r.repdia_obras = p.obra)
              AND (p.inicio IS NULL OR r.repdia_fech::date >= p.inicio)
              AND (p.fin IS NULL OR r.repdia_fech::date <= p.fin)
            GROUP BY r.repdia_fech::date
        ), autorizaciones AS (
            SELECT d.desp_fech_crea::date AS fecha,
                   SUM(d.desp_galones_autorizados) AS galones
            FROM despachos_acpm d CROSS JOIN parametros p
            WHERE d.desp_vehi = p.vehiculo AND d.desp_estado = 0
              AND d.desp_galones_autorizados > 0
              AND (p.obra IS NULL OR d.desp_obra = p.obra)
              AND (p.inicio IS NULL OR d.desp_fech_crea::date >= p.inicio)
              AND (p.fin IS NULL OR d.desp_fech_crea::date <= p.fin)
            GROUP BY d.desp_fech_crea::date
        )
        SELECT COALESCE(r.fecha, a.fecha) AS desp_fech, a.galones AS desp_galones,
               r.inicial AS km_hr_anterior, r.final AS km_hr_actual,
               r.inicial AS hr_anterior, r.final AS hr_actual,
               r.recorrido AS diferencia,
               ROUND(r.recorrido::numeric / NULLIF(a.galones, 0), 2) AS km_por_galon,
               ROUND(a.galones::numeric / NULLIF(r.recorrido, 0), 2) AS gl_por_hora
        FROM recorridos r FULL JOIN autorizaciones a ON r.fecha = a.fecha
        ORDER BY COALESCE(r.fecha, a.fecha)";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':vehi_id', $vehi_id, PDO::PARAM_INT);
        $stmt->bindValue(':obra', $obra === '' ? null : $obra);
        $stmt->bindValue(':fechaIni', $fechaIni === '' ? null : $fechaIni);
        $stmt->bindValue(':fechaFin', $fechaFin === '' ? null : $fechaFin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_obras_por_vehiculo($vehi_id) {
        $conectar = parent::conexion();

        $sql = "SELECT DISTINCT 
                o.obras_id,
                o.obras_nom,
                o.obras_codigo
            FROM despachos_acpm d
            INNER JOIN obras o ON o.obras_id = d.desp_obra
            WHERE d.desp_vehi = :vehi_id
            AND d.desp_obra IS NOT NULL
            ORDER BY o.obras_nom ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':vehi_id', $vehi_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_gl_hora_individual($vehi_id, $obra = '', $fechaIni = '', $fechaFin = '') {
        return $this->get_rendimiento_diario($vehi_id, $obra, $fechaIni, $fechaFin);
    }

    public function get_placas_combustible() {
        $conectar = parent::conexion();

        $sql = "SELECT DISTINCT 
                v.vehi_id,
                v.vehi_placa,
                t.tipo_id,
                t.tipo_nombre
            FROM despachos_acpm d
            INNER JOIN vehiculos v ON v.vehi_id = d.desp_vehi
            INNER JOIN tipo_vehiculo t ON t.tipo_id = v.vehi_tipo
            WHERE t.tipo_id NOT IN (12,20,21,22,23)
            ORDER BY t.tipo_id ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* RESUMEN DE DESPACHOS REALIZADOS HOY */
    public function get_resumen_despachos_hoy() {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                COUNT(desp_id) AS total_despachos,
                COALESCE(SUM(desp_galones), 0) AS total_galones
            FROM despachos_acpm
            WHERE desp_fech = CURRENT_DATE
            AND desp_galones IS NOT NULL
            AND desp_galones > 0
            AND desp_estado = 0";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* DESPACHOS REALIZADOS HOY AGRUPADOS POR OBRA */
    public function get_despachos_obra_hoy() {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                o.obras_id,
                o.obras_nom,
                COUNT(d.desp_id) AS total_despachos,
                COALESCE(
                    SUM(d.desp_galones),
                    0
                ) AS total_galones

            FROM despachos_acpm d

            INNER JOIN obras o
                ON o.obras_id = d.desp_obra

            WHERE d.desp_fech = CURRENT_DATE

            AND d.desp_galones IS NOT NULL

            AND d.desp_galones > 0

            AND d.desp_estado = 0

            GROUP BY
                o.obras_id,
                o.obras_nom

            ORDER BY total_galones DESC";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* RESUMEN CONTROL ACPM DEL DIA */
    public function get_resumen_control_acpm_hoy() {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                COUNT(desp_id) AS total_despachos,

                COALESCE(
                    SUM(desp_galones),
                    0
                ) AS total_galones,

                COUNT(desp_id) FILTER (
                    WHERE desp_documento_siesa IS NULL
                    OR TRIM(desp_documento_siesa) = ''
                ) AS total_pendientes_siesa,

                COALESCE(
                    SUM(desp_galones) FILTER (
                        WHERE desp_documento_siesa IS NULL
                        OR TRIM(desp_documento_siesa) = ''
                    ),
                    0
                ) AS galones_pendientes_siesa,

                COUNT(desp_id) FILTER (
                    WHERE desp_documento_siesa IS NOT NULL
                    AND TRIM(desp_documento_siesa) <> ''
                ) AS total_registrados_siesa,

                COALESCE(
                    SUM(desp_galones) FILTER (
                        WHERE desp_documento_siesa IS NOT NULL
                        AND TRIM(desp_documento_siesa) <> ''
                    ),
                    0
                ) AS galones_registrados_siesa

            FROM despachos_acpm

            WHERE desp_fech = CURRENT_DATE
            AND desp_galones IS NOT NULL
            AND desp_galones > 0
            AND desp_estado = 0";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* LISTAR DESPACHOS PENDIENTES DE DOCUMENTO SIESA */
    public function get_despachos_pendientes_siesa_hoy() {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                d.desp_id,
                d.desp_fech,
                d.desp_hora,
                d.desp_galones,
                d.desp_recibo,
                d.desp_documento_siesa,
                o.obras_nom,
                v.vehi_placa

            FROM despachos_acpm d

            INNER JOIN obras o
                ON o.obras_id = d.desp_obra

            INNER JOIN vehiculos v
                ON v.vehi_id = d.desp_vehi

            WHERE d.desp_fech = CURRENT_DATE

            AND d.desp_galones IS NOT NULL

            AND d.desp_galones > 0

            AND d.desp_estado = 0

            AND (
                d.desp_documento_siesa IS NULL
                OR TRIM(d.desp_documento_siesa) = ''
            )

            ORDER BY
                d.desp_fech ASC,
                d.desp_hora ASC";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /* REGISTRAR DOCUMENTO INTERNO SIESA */
    public function update_documento_siesa($desp_id, $desp_documento_siesa) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE despachos_acpm
            SET desp_documento_siesa = ?
            WHERE desp_id = ?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(
            1,
            $desp_documento_siesa
        );

        $sql->bindValue(
            2,
            $desp_id,
            PDO::PARAM_INT
        );

        $sql->execute();

        return true;
    }

    /* HISTORIAL CONTROL ACPM */
    public function get_historial_control_acpm(
        $fecha_inicio,
        $fecha_final,
        $desp_obra = '',
        $desp_vehi = '',
        $estado_siesa = ''
    ) {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                d.desp_id,
                d.desp_fech,
                d.desp_hora,
                d.desp_galones,
                d.desp_recibo,
                d.desp_documento_siesa,

                o.obras_id,
                o.obras_nom,

                v.vehi_id,
                v.vehi_placa,

                TRIM(
                    CONCAT(
                        COALESCE(cond.user_nombre, ''),
                        ' ',
                        COALESCE(cond.user_apellidos, '')
                    )
                ) AS conductor


            FROM despachos_acpm d

            INNER JOIN obras o
                ON o.obras_id = d.desp_obra

            INNER JOIN vehiculos v
                ON v.vehi_id = d.desp_vehi

            LEFT JOIN usuarios cond
                ON cond.user_id = d.desp_cond


            WHERE d.desp_fech BETWEEN :fecha_inicio AND :fecha_final

            AND d.desp_galones IS NOT NULL
            AND d.desp_galones > 0
            AND d.desp_estado = 0";

        /*
     * FILTRO OBRA
     */
        if ($desp_obra !== '') {

            $sql .= " AND d.desp_obra = :desp_obra";
        }


        /*
     * FILTRO EQUIPO
     */
        if ($desp_vehi !== '') {

            $sql .= " AND d.desp_vehi = :desp_vehi";
        }


        /*
     * ESTADO SIESA
     */
        if ($estado_siesa === 'cargado') {

            $sql .= " AND d.desp_documento_siesa IS NOT NULL
                  AND TRIM(d.desp_documento_siesa) <> ''";
        } elseif ($estado_siesa === 'pendiente') {

            $sql .= " AND (
                    d.desp_documento_siesa IS NULL
                    OR TRIM(d.desp_documento_siesa) = ''
                  )";
        }


        $sql .= " ORDER BY
                d.desp_fech DESC,
                d.desp_hora DESC,
                d.desp_id DESC";


        $stmt = $conectar->prepare($sql);


        $stmt->bindValue(
            ':fecha_inicio',
            $fecha_inicio
        );

        $stmt->bindValue(
            ':fecha_final',
            $fecha_final
        );


        if ($desp_obra !== '') {

            $stmt->bindValue(
                ':desp_obra',
                $desp_obra,
                PDO::PARAM_INT
            );
        }


        if ($desp_vehi !== '') {

            $stmt->bindValue(
                ':desp_vehi',
                $desp_vehi,
                PDO::PARAM_INT
            );
        }


        $stmt->execute();

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
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