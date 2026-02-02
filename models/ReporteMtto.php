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

    public function get_reporte_detalle($num_reporte) {
        $conectar = parent::conexion();
        parent::set_names();
        // -----------------------------------------------------
        // 1. REPORTE DE MTTO
        // -----------------------------------------------------
        $sql = "SELECT *
            FROM reporte_mtto
            WHERE repo_mtto_id = :num_reporte";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":num_reporte", $num_reporte, PDO::PARAM_STR);

        $stmt->execute();
        $reporte = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reporte) {
            return false;
        }

        // El campo correcto que SI existe = repo_mtto_orden
        $id_ot = $reporte["repo_mtto_orden"];
        $id_obra = $reporte["repo_mtto_obra_id"];


        // -----------------------------------------------------
        // 2. ORDEN DE TRABAJO
        // -----------------------------------------------------
        $sql = "SELECT *
            FROM ordenes_trabajo
            WHERE codi_otm = :id_ot";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id_ot", $id_ot, PDO::PARAM_INT);

        $stmt->execute();
        $ot = $stmt->fetch(PDO::FETCH_ASSOC);


        $id_solic = $ot["codi_solc_otm"];

        // -----------------------------------------------------
        // 2. ORDEN DE TRABAJO
        // -----------------------------------------------------
        $sql = "SELECT *
            FROM obras
            WHERE obras_id = :id_obra";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id_obra", $id_obra, PDO::PARAM_INT);

        $stmt->execute();
        $obra = $stmt->fetch(PDO::FETCH_ASSOC);


        $id_solic = $ot["codi_solc_otm"];


        // -----------------------------------------------------
        // 3. SOLICITUD DE MANTENIMIENTO
        // -----------------------------------------------------
        $sql = "SELECT *
            FROM solicitudes_mtto
            WHERE codi_soli = :id_solic";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id_solic", $id_solic, PDO::PARAM_INT);

        $stmt->execute();
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);



        $id_vehiculo = $solicitud["codi_vehi_soli"];

        // -----------------------------------------------------
        // 4. OBTENER VEHÍCULO (pendiente de confirmar)
        // -----------------------------------------------------
        $sql = "SELECT *
            FROM vehiculos
            WHERE vehi_id = :id_vehiculo";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id_vehiculo", $id_vehiculo, PDO::PARAM_INT);

        $stmt->execute();
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        $id_conductor = $solicitud["codi_cond_soli"];

        // -----------------------------------------------------
        // 5. OBTENER CONDUCTOR (pendiente de confirmar)
        // -----------------------------------------------------
        $sql = "SELECT *
            FROM usuarios
            WHERE user_id = :id_user";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id_user", $id_conductor, PDO::PARAM_INT);

        $stmt->execute();
        $conductor = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            "reporte"   => $reporte,
            "equipo"    => $equipo,
            "ot"        => $ot,
            "solicitud" => $solicitud,
            "conductor" => $conductor,
            "obra" => $obra,
            "repuestos" => [] // Items vienen de la API SIESA
        ];
    }

    public function insertar_insumos($idReporte, $items) {
        $conectar = parent::conexion();
        parent::set_names();

        foreach ($items as $it) {

            $sql = "INSERT INTO reporte_repuestos
                (repo_mtto_id, rpts_docu, rpts_refr, rpts_cant, rpts_vlr_neto, rpts_notas, rpts_prov, repo_item)
                VALUES (:id, :docto, :ref, :cant, :costo, :notas, :prove, :item)";

            $stmt = $conectar->prepare($sql);

            $stmt->bindValue(":id", $idReporte, PDO::PARAM_STR);
            $stmt->bindValue(":docto", $it["documento"], PDO::PARAM_STR);
            $stmt->bindValue(":ref", $it["descripcion"], PDO::PARAM_STR);
            $stmt->bindValue(":cant", $it["cantidad"], PDO::PARAM_STR);
            $stmt->bindValue(":costo", $it["valor"], PDO::PARAM_STR);
            $stmt->bindValue(":notas", $it["notas"], PDO::PARAM_STR);
            $stmt->bindValue(":prove", $it["proveedor"], PDO::PARAM_STR);
            $stmt->bindValue(":item", $it["referencia"], PDO::PARAM_STR);

            $stmt->execute();
        }

        return true;
    }

    public function get_repuestos_por_reporte($idReporte) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                repo_rpts_id AS id,
                rpts_refr AS descripcion,
                rpts_docu AS documento,
                rpts_cant AS cantidad,
                rpts_vlr_neto AS valor,
                rpts_notas AS notas,
                rpts_prov AS proveedor,
                repo_item AS referencia,
                rpts_fact AS factura
            FROM reporte_repuestos
            WHERE repo_mtto_id = :id
            ORDER BY rpts_docu ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id", $idReporte, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete_item($idItem) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "DELETE FROM reporte_repuestos WHERE repo_rpts_id = :id";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id", $idItem, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function actualizar_horas_ejecutadas($id, $horas) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE reporte_mtto 
            SET repo_mtto_horas_ejec = :horas
            WHERE repo_mtto_id = :id";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":horas", $horas, PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function get_proveedores_reporte($idReporte) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT  rpts_prov, rpts_docu, num_otm
                FROM reporte_repuestos rr
                INNER JOIN reporte_mtto rm ON rr.repo_mtto_id = rm.repo_mtto_id
                INNER JOIN ordenes_trabajo ot ON ot.codi_otm = rm.repo_mtto_orden
                WHERE rr.repo_mtto_id = :id
				GROUP BY num_otm, rpts_prov, rpts_docu
                ORDER BY rpts_docu ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":id", $idReporte, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /****************************************************
     * INSERTAR FACTURAS / REEMBOLSOS EN LOTE
     ****************************************************/
    public function insertar_facturas_lote($idReporte, $items) {
        $conectar = parent::conexion();
        parent::set_names();

        try {

            $conectar->beginTransaction();

            $sql = "INSERT INTO reporte_repuestos 
                (repo_mtto_id, 
                 rpts_refr, 
                 rpts_cant, 
                 rpts_vlr_neto, 
                 rpts_docu, 
                 rpts_fact, 
                 repo_item,
                 rpts_prov)
                VALUES 
                (:id, :ref, :cant, :costo, :oc, :fact, :item, :prov)";

            $stmt = $conectar->prepare($sql);

            foreach ($items as $it) {

                // Normalización (evitar errores)
                $nombre     = strtoupper(trim($it["nombre"] ?? ""));
                $ref        = strtoupper(trim($it["referencia"] ?? ""));
                $cant       = floatval($it["cantidad"] ?? 0);
                $costo      = floatval($it["costo"] ?? 0);
                $oc         = strtoupper(trim($it["oc"] ?? ""));
                $factura    = strtoupper(trim($it["factura"] ?? ""));
                $proveedor    = strtoupper(trim($it["proveedor"] ?? ""));

                $stmt->bindValue(":id", $idReporte, PDO::PARAM_INT);
                $stmt->bindValue(":ref", $nombre, PDO::PARAM_STR);
                $stmt->bindValue(":cant", $cant, PDO::PARAM_STR);
                $stmt->bindValue(":costo", $costo, PDO::PARAM_STR);
                $stmt->bindValue(":oc", $oc, PDO::PARAM_STR);
                $stmt->bindValue(":fact", $factura, PDO::PARAM_STR);
                $stmt->bindValue(":item", $ref, PDO::PARAM_STR);
                $stmt->bindValue(":prov", $proveedor, PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    $conectar->rollBack();
                    return print_r($stmt->errorInfo(), true);
                }
            }

            $conectar->commit();
            return true;
        } catch (Exception $e) {

            $conectar->rollBack();
            return "Error SQL: " . $e->getMessage();
        }
    }

    public function get_reporte_by_id($reporte_id) {
        $conectar = parent::Conexion();
        $sql = "SELECT  *
                FROM reporte_repuestos rr
                INNER JOIN reporte_mtto rm ON rr.repo_mtto_id = rm.repo_mtto_id
                INNER JOIN ordenes_trabajo ot ON ot.codi_otm = rm.repo_mtto_orden
                INNER JOIN solicitudes_mtto ON solicitudes_mtto.codi_soli = ot.codi_solc_otm
                INNER JOIN vehiculos ON vehiculos.vehi_id = solicitudes_mtto.codi_vehi_soli
                WHERE rr.repo_mtto_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $reporte_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar_soporte_factura($reporte_id, $nombre_archivo, $ruta_remota) {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "INSERT INTO facturas_soportes 
            (fact_sopo_reporte, fact_sopo_nombre, fact_sopo_ruta, fact_sopo_fecha)
            VALUES (?, ?, ?, NOW())";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $reporte_id);
        $stmt->bindValue(2, $nombre_archivo);
        $stmt->bindValue(3, $ruta_remota);

        return $stmt->execute();
    }

    public function get_soportes_factura($reporte_id) {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "SELECT * FROM facturas_soportes 
            WHERE fact_sopo_reporte = ?
            ORDER BY fact_sopo_fecha ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $reporte_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cerrar_reporte($idReporte, $estadoFinal, $total) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE reporte_mtto 
            SET 
                repo_mtto_estado_final   = ?,
                repo_mtto_vlr_total = ?,
                repo_mtto_estado = 2,
                repo_mtto_fecha_cierre = NOW()
            WHERE repo_mtto_id = ?";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $estadoFinal);
        $stmt->bindValue(2, $total);
        $stmt->bindValue(3, $idReporte);

        return $stmt->execute();
    }








    /* 
    ===========================================
    ===========================================
    
    */

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
