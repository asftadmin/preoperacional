<?php

class OrdenTrabajo extends Conectar
{

    public function obternerUltimoConsecutivo()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT num_otm FROM ordenes_trabajo 
            WHERE num_otm LIKE 'OTM-%'
            ORDER BY CAST(SUBSTRING(num_otm FROM 11) AS INTEGER) DESC 
            LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function insert_orden(
        $numeroOrden,
        $fechaAsig,
        $mantenimiento,
        $tecnico,
        $actividad,
        $prioridad,
        $ticket
    ) {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            // Iniciar la transacción
            $conectar->beginTransaction();

            // Insertar el reporte en la tabla 'reporte_mantenimiento'
            $sql_reporte = "INSERT INTO ordenes_trabajo 
            (num_otm, fech_creac_otm, prio_otm, esta_otm, desc_atcv_otm, tecn_otm, mtto_otm, codi_solc_otm)
            VALUES (:num_otm, :fech_creac_otm, :prio_otm, '1', :desc_atcv_otm, :tecn_otm, :mtto_otm, :codi_solc_otm)";

            $stmt_reporte = $conectar->prepare($sql_reporte);
            $stmt_reporte->bindParam(':num_otm', $numeroOrden);
            $stmt_reporte->bindParam(':fech_creac_otm', $fechaAsig);
            $stmt_reporte->bindParam(':prio_otm', $prioridad);
            $stmt_reporte->bindParam(':desc_atcv_otm', $actividad);
            $stmt_reporte->bindParam(':tecn_otm', $tecnico);
            $stmt_reporte->bindParam(':mtto_otm', $mantenimiento);
            $stmt_reporte->bindParam(':codi_solc_otm', $ticket);

            // Ejecutar la inserción
            $stmt_reporte->execute();

            // Obtener el ID del reporte recién insertado falta arregla esto
            $repo_codi = $conectar->lastInsertId();
            error_log("Nuevo repo_codi obtenido: " . $repo_codi);

            // Insertar los detalles en la tabla 'detalle_reporte'
            /*$sql_detalle = "INSERT INTO detalle_reporte 
            (deta_diag_inici, deta_repo_codi)
            VALUES (:deta_diag_inici, :deta_repo_codi)";
            $stmt_detalle = $conectar->prepare($sql_detalle);
            $stmt_detalle->bindParam(':deta_diag_inici', $diagnostico_inic);
            $stmt_detalle->bindParam(':deta_repo_codi',  $repo_codi);
            $stmt_detalle->execute();*/

            // Actualizar el estado en la tabla 'solicitudes_mtto'
            $sql_solicitud = "UPDATE solicitudes_mtto SET esta_soli = :estado WHERE codi_soli = :codigo";
            $smt_solicitud = $conectar->prepare($sql_solicitud);
            $smt_solicitud->bindValue(':estado', '2', PDO::PARAM_STR);
            $smt_solicitud->bindValue(':codigo', $ticket, PDO::PARAM_INT);
            $smt_solicitud->execute();

            // Confirmar la transacción si todo salió bien
            $conectar->commit();

            return ['success' => true];
        } catch (PDOException $e) {
            // Si ocurre un error en las consultas, revertir la transacción
            $conectar->rollBack();
            error_log("Error de base de datos: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error al guardar en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Si ocurre cualquier otro tipo de error, revertir la transacción
            $conectar->rollBack();
            error_log("Error general: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    public function get_ordenes_id($ticket_id)
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM ordenes_trabajo WHERE codi_otm=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ticket_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function cerrarOrdenTrabajo(
        $id_orden,
        $solicitud_siesa,
        $equipo_operativo,
        $pendientes,
        $obras,
        $horas,
        $usuario
    ) {
        $conectar = parent::Conexion();

        try {

            $conectar->beginTransaction();

            // =========================================================
            // 1. ACTUALIZAR ORDEN DE TRABAJO
            // =========================================================
            $sql = "UPDATE ordenes_trabajo SET
                    solicitud_otm = :solicitud_siesa,
                    equipo_operativo_otm = :equipo_operativo,
                    pendientes_otm = :pendientes,
                    esta_otm = 2
                WHERE codi_otm = :id_orden";

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(":solicitud_siesa", $solicitud_siesa, PDO::PARAM_STR);
            $stmt->bindValue(":equipo_operativo", $equipo_operativo, PDO::PARAM_INT);
            $stmt->bindValue(":pendientes", $pendientes, PDO::PARAM_STR);
            $stmt->bindValue(":id_orden", $id_orden, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                print_r($stmt->errorInfo());
                die("ERROR EN UPDATE");
            }

            // =========================================================
            // 2. OBTENER SOLICITUD ASOCIADA A ESTA OT
            // =========================================================
            $sql = "SELECT codi_solc_otm
                    FROM ordenes_trabajo
                    WHERE codi_otm = :id_orden";

            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(":id_orden", $id_orden, PDO::PARAM_INT);
            $stmt->execute();

            $solicitud = $stmt->fetchColumn();

            if (!$solicitud) {
                throw new Exception("No se encontró solicitud asociada a la OT.");
            }

            // =========================================================
            // 3. ACTUALIZAR ESTADO DE LA SOLICITUD A 3
            // =========================================================
            $sql = "UPDATE solicitudes_mtto 
                    SET esta_soli = 3
                    WHERE codi_soli = :id_solicitud";

            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(":id_solicitud", $solicitud, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                print_r($stmt->errorInfo());
                die("ERROR EN UPDATE SOLICITUD");
            }

            // =========================================================
            // 4. GENERAR CONSECUTIVO MTTO-YYYY-XXXX (POSTGRESQL)
            // =========================================================
            $sql = "    SELECT 
                        'MTTO-' || EXTRACT(YEAR FROM CURRENT_DATE)::text || '-' ||
                        LPAD(
                            (
                                COALESCE(
                                    MAX(
                                        (regexp_replace(repo_mtto_num_reporte, '^MTTO-[0-9]{4}-', '', 'g'))::integer
                                    ),
                                0) + 1
                            )::text,
                        4,
                        '0'
                        ) AS numero_reporte
                    FROM reporte_mtto
                    WHERE repo_mtto_num_reporte LIKE 
                        'MTTO-' || EXTRACT(YEAR FROM CURRENT_DATE)::text || '-%'

                    UNION ALL

                    -- Caso donde NO existen reportes en el año actual
                    SELECT 
                        'MTTO-' || EXTRACT(YEAR FROM CURRENT_DATE)::text || '-0001'
                    WHERE NOT EXISTS (
                        SELECT 1 FROM reporte_mtto 
                        WHERE repo_mtto_num_reporte LIKE 
                            'MTTO-' || EXTRACT(YEAR FROM CURRENT_DATE)::text || '-%'
                    )
                    LIMIT 1;";

            $stmt = $conectar->prepare($sql);
            if (!$stmt->execute()) {
                print_r($stmt->errorInfo());
                die("ERROR SQL");
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);



            if (!$row) {
                throw new Exception("No se pudo generar el número de reporte");
            }

            $num_reporte = $row["numero_reporte"];

            // =========================================================
            // 3. INSERTAR EN reporte_mtto
            // =========================================================
            $sql = "INSERT INTO reporte_mtto 
                    (repo_mtto_num_reporte, repo_mtto_obra_id, repo_mtto_horas_programadas, repo_mtto_estado, repo_mtto_usuario_creacion_id)
                VALUES (:num_reporte, :obra, :horas, 1, :usuario)";

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(":num_reporte", $num_reporte, PDO::PARAM_STR);
            $stmt->bindValue(":obra", $obras, PDO::PARAM_INT);
            $stmt->bindValue(":horas", number_format($horas, 2, '.', ''), PDO::PARAM_STR);
            $stmt->bindValue(":usuario", $usuario, PDO::PARAM_INT);

            $stmt->execute();

            $conectar->commit();

            return [
                "status" => "success",
                "message" => "Orden cerrada exitosamente"
            ];
        } catch (Exception $e) {

            $conectar->rollBack();

            return [
                "status" => "error",
                "message" => "Error al cerrar la OT: " . $e->getMessage()
            ];
        }
    }
}
