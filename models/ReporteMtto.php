<?php

class ReporteMtto extends Conectar {


    /**LISTAR TIPO DE MANTENIMIENTO */

    public function get_tipo_mtto() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tipos_mantenimiento";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function obternerUltimoConsecutivo() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repo_numb FROM reporte_mantenimiento 
            WHERE repo_numb LIKE 'MTTO-%'
            ORDER BY CAST(SUBSTRING(repo_numb FROM 11) AS INTEGER) DESC 
            LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function listaReporte() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT repo_codi, deta_codi, vehi_id, repo_numb, repo_fech, vehi_placa, deta_total_mtto,
            CASE
                WHEN repo_estado = 1 THEN 'APROBADO'
                WHEN repo_estado = 2 THEN 'ANULADO'
                ELSE NULL 
            END AS estado
        FROM reporte_mantenimiento 
            INNER JOIN detalle_reporte ON deta_repo_codi = repo_codi
            INNER JOIN vehiculos ON repo_vehi = vehi_id 
        ORDER BY repo_codi DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function guardarReporte($reporte, $detalle, $proveedores, $insumos) {

        $conectar = parent::conexion();
        parent::set_names();
        try {

            $conectar->beginTransaction();


            /** INSERTAR REPORTE MANTENIMIENTO **/
            $sql_reporte = "INSERT INTO reporte_mantenimiento 
                (repo_numb, repo_fech, repo_vehi, repo_obra, repo_usua, repo_inte, repo_hora, repo_tipo_mtto, repo_estado)
                VALUES (:repo_numb, :repo_fech, :repo_vehi, :repo_obra, :repo_usua, :repo_inte, :repo_hora, :repo_tipo_mtto, '1')";

            $stmt_reporte = $conectar->prepare($sql_reporte);
            $stmt_reporte->bindParam(':repo_numb', $reporte['numb_reporte']);
            $stmt_reporte->bindParam(':repo_fech', $reporte['fecha_reporte']);
            $stmt_reporte->bindParam(':repo_vehi', $reporte['nomb_vehi']);
            $stmt_reporte->bindParam(':repo_obra', $reporte['nomb_obra']);
            $stmt_reporte->bindParam(':repo_usua', $reporte['nomb_cond']);
            $stmt_reporte->bindParam(':repo_inte', $reporte['codig_equipo']);
            $stmt_reporte->bindParam(':repo_hora', $reporte['hora_reporte']);
            $stmt_reporte->bindParam(':repo_tipo_mtto', $reporte['tipo_mtto']);
            error_log("Ejecutando INSERT en reporte_mantenimiento...");
            $stmt_reporte->execute();
            $repo_codi = $conectar->lastInsertId();
            error_log("Nuevo repo_codi obtenido: " . $repo_codi);

            /** INSERTAR DETALLE REPORTE **/
            $sql_detalle = "INSERT INTO detalle_reporte 
                (deta_diag_inici, deta_desc_mtto, deta_esta_fina, deta_total_mtto, deta_repo_codi)
                VALUES (:deta_diag_inici, :deta_desc_mtto, :deta_esta_fina, :deta_total_mtto, :deta_repo_codi)";

            $stmt_detalle = $conectar->prepare($sql_detalle);
            $stmt_detalle->bindParam(':deta_diag_inici', $detalle['deta_diag_inici']);
            $stmt_detalle->bindParam(':deta_desc_mtto', $detalle['deta_desc_mtto']);
            $stmt_detalle->bindParam(':deta_esta_fina', $detalle['deta_esta_fina']);
            $deta_total_mtto = (float) str_replace(',', '.', $detalle['deta_total_mtto']);
            $stmt_detalle->bindParam(':deta_total_mtto', $deta_total_mtto, PDO::PARAM_STR);
            $stmt_detalle->bindParam(':deta_repo_codi', $repo_codi);
            $stmt_detalle->execute();
            $deta_codi = $conectar->lastInsertId();

            /** INSERTAR PROVEEDORES (Bulk Insert) **/
            if (!empty($proveedores)) {
                $sql_proveedor = "INSERT INTO proveedores 
                    (prov_nomb, prov_carg, prov_orde_trab, prov_orde_comp, prov_fact, prov_tipo, prov_deta_repo)
                    VALUES (:prov_nomb, :prov_carg, :prov_orde_trab, :prov_orde_comp, :prov_fact, :prov_tipo, :prov_deta_repo)";

                $stmt_proveedor = $conectar->prepare($sql_proveedor);
                foreach ($proveedores as $proveedor) {
                    $stmt_proveedor->bindParam(':prov_nomb', $proveedor['prov_nomb']);
                    $stmt_proveedor->bindParam(':prov_deta_repo', $deta_codi);

                    if ($proveedor['tipo'] === "interno") {
                        $stmt_proveedor->bindParam(':prov_carg', $proveedor['prov_carg']);
                        $stmt_proveedor->bindParam(':prov_orde_trab', $proveedor['prov_orden']);
                        $stmt_proveedor->bindParam(':prov_tipo', $proveedor['tipo']);
                        $stmt_proveedor->bindValue(':prov_orde_comp', null, PDO::PARAM_NULL);
                        $stmt_proveedor->bindValue(':prov_fact', null, PDO::PARAM_NULL);
                    } else {
                        $stmt_proveedor->bindParam(':prov_orde_trab', $proveedor['prov_orde_trab']);
                        $stmt_proveedor->bindParam(':prov_orde_comp', $proveedor['prov_ord_comp']);
                        $stmt_proveedor->bindParam(':prov_fact', $proveedor['prov_fact']);
                        $stmt_proveedor->bindParam(':prov_tipo', $proveedor['tipo']);
                        $stmt_proveedor->bindValue(':prov_carg', null, PDO::PARAM_NULL);
                    }

                    $stmt_proveedor->execute();
                }
            }

            /** INSERTAR INSUMOS (Bulk Insert) **/
            if (!empty($insumos)) {
                $sql_insumo = "INSERT INTO insumos 
                (insu_nomb, insu_refe, insu_marc, insu_seri, insu_cant, insu_cost, insu_occ, insu_fact, insu_repo, insu_mode) 
                VALUES (:insu_nomb, :insu_refe, :insu_marc, :insu_seri, :insu_cant, :insu_cost, :insu_occ, :insu_fact, :insu_repo, :insu_mode)";

                $stmt_insumo = $conectar->prepare($sql_insumo);
                foreach ($insumos as $insumo) {
                    $stmt_insumo->bindParam(':insu_nomb', $insumo['insumo_nombre']);
                    $stmt_insumo->bindParam(':insu_refe', $insumo['insumo_referencia']);
                    $stmt_insumo->bindParam(':insu_marc', $insumo['insumo_marca']);
                    $stmt_insumo->bindParam(':insu_seri', $insumo['insumo_serial']);
                    $insumo_cantidad = intval($insumo['insumo_cantidad']);
                    $stmt_insumo->bindParam(':insu_cant', $insumo_cantidad, PDO::PARAM_INT);
                    $insumo_costo = intval($insumo['insumo_costo']);
                    $stmt_insumo->bindParam(':insu_cost', $insumo_costo, PDO::PARAM_INT);
                    $stmt_insumo->bindParam(':insu_occ', $insumo['insumo_orden_compra']);
                    $stmt_insumo->bindParam(':insu_fact', $insumo['insumo_factura']);
                    $stmt_insumo->bindParam(':insu_repo', $repo_codi);
                    $stmt_insumo->bindParam(':insu_mode', $insumo['insumo_modelo']);
                    $stmt_insumo->execute();
                }
            }

            // Confirmar transacción
            $conectar->commit();
            return ['success' => true, 'repo_codi' => $repo_codi];
        } catch (Exception $e) {
            //Revertir transacción en caso de error
            $conectar->rollBack();
            error_log("Error al guardar el reporte: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    /* ACTUALIZAR A UN ESTADO ANULADO */
    public function anulado($repo_codi) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE reporte_mantenimiento SET repo_estado = 2 WHERE repo_codi = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $repo_codi);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insert_reporte(
        $numeroRpte,
        $hora,
        $fechaAsig,
        $obra,
        $mantenimiento,
        $vehiculo,
        $conductor,
        $diagnostico_inic,
        $ticket
    ) {
        $conectar = parent::conexion();
        parent::set_names();
        try {
            // Iniciar la transacción
            $conectar->beginTransaction();

            // Insertar el reporte en la tabla 'reporte_mantenimiento'
            $sql_reporte = "INSERT INTO reporte_mantenimiento 
            (repo_numb, repo_fech, repo_vehi, repo_obra, repo_usua, repo_hora, repo_tipo_mtto, repo_estado)
            VALUES (:repo_numb, :repo_fech, :repo_vehi, :repo_obra, :repo_usua, :repo_hora, :repo_tipo_mtto, '1')";

            $stmt_reporte = $conectar->prepare($sql_reporte);
            $stmt_reporte->bindParam(':repo_numb', $numeroRpte);
            $stmt_reporte->bindParam(':repo_fech', $fechaAsig);
            $stmt_reporte->bindParam(':repo_vehi', $vehiculo);
            $stmt_reporte->bindParam(':repo_obra', $obra);
            $stmt_reporte->bindParam(':repo_usua', $conductor);
            $stmt_reporte->bindParam(':repo_hora', $hora);
            $stmt_reporte->bindParam(':repo_tipo_mtto', $mantenimiento);

            // Ejecutar la inserción
            $stmt_reporte->execute();

            // Obtener el ID del reporte recién insertado
            $repo_codi = $conectar->lastInsertId();
            error_log("Nuevo repo_codi obtenido: " . $repo_codi);

            // Insertar los detalles en la tabla 'detalle_reporte'
            $sql_detalle = "INSERT INTO detalle_reporte 
            (deta_diag_inici, deta_repo_codi)
            VALUES (:deta_diag_inici, :deta_repo_codi)";
            $stmt_detalle = $conectar->prepare($sql_detalle);
            $stmt_detalle->bindParam(':deta_diag_inici', $diagnostico_inic);
            $stmt_detalle->bindParam(':deta_repo_codi',  $repo_codi);
            $stmt_detalle->execute();

            // Actualizar el estado en la tabla 'solicitudes_mtto'
            $sql_solicitud = "UPDATE solicitudes_mtto SET esta_soli = :estado WHERE codi_soli = :codigo";
            $smt_solicitud = $conectar->prepare($sql_solicitud);
            $smt_solicitud->bindValue(':estado', '2', PDO::PARAM_STR);
            $smt_solicitud->bindValue(':codigo', $ticket, PDO::PARAM_INT);
            $smt_solicitud->execute();

            // Confirmar la transacción si todo salió bien
            $conectar->commit();

            return ['success' => true, 'repo_codi' => $repo_codi];
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
