<?php

class OrdenTrabajo extends Conectar {

    public function obternerUltimoConsecutivo() {
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
}
