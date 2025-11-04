<?php
/*CONTOLADOR TIPO DE VEHICULO*/
require_once("../config/conexion.php");
require_once("../models/Liquidacion.php");

$liquidacion = new Liquidaciones();

switch ($_GET["op"]) {

    case 'combotipovehi':
        $datos = $liquidacion->consultarTipoVehiculo();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Tipo Vehiculo--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['tipo_id'] . "'>" . $row['tipo_nombre'] . "</option>";
            }
            echo $html;
        }
        break;
		
    case 'combotipovehiMultiple':
        // Devolverá HTML (lista de <option>)
        header('Content-Type: text/html; charset=utf-8');

        $datos = $liquidacion->consultarTipoVehiculo(); // sin filtro
        $html = ''; // <<< IMPORTANTE

        // $html .= "<option disabled selected>--Tipo Vehiculo--</option>";
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $id   = htmlspecialchars($row['tipo_id'], ENT_QUOTES, 'UTF-8');
                $text = htmlspecialchars($row['tipo_nombre'], ENT_QUOTES, 'UTF-8');
                $html .= "<option value='{$id}'>{$text}</option>";
            }
        }
        echo $html;
        exit;

    /* SELECT OBRAS  */
    // controller/Liquidacion.php

    case 'comboObras':
        // 1) Recoger y normalizar los parámetros
        $fecha_inicio = isset($_POST['fecha_inicio']) && $_POST['fecha_inicio'] !== ''
            ? $_POST['fecha_inicio']
            : null;
        $fecha_fin = isset($_POST['fecha_fin']) && $_POST['fecha_fin'] !== ''
            ? $_POST['fecha_fin']
            : null;

        $tipo_vehic = isset($_POST['tipo_vehiculo']) && $_POST['ftipo_vehiculo'] !== ''
            ? $_POST['tipo_vehiculo']
            : null;

        // 2) Llamar al modelo con filtro de fechas
        $datos = $liquidacion->get_obras_liquidacion($fecha_inicio, $fecha_fin, $tipo_vehic);

        // 3) Construir el <option>
        if (is_array($datos) && count($datos) > 0) {
            $html = "<option value='' disabled selected>--Obra--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='"
                    . $row['repdia_obras']  // o 'obras_id' si prefieres
                    . "'>"
                    . htmlspecialchars($row['repdia_obras'] . ' - ' . $row['obras_nom'])
                    . "</option>";
            }
        } else {
            $html = "<option value='' disabled>— No hay obras en ese rango —</option>";
        }

        echo $html;
        break;


    /* SELECT ACTIVIDADES */
    case 'comboActividades':
        $tipo_id = isset($_POST['tipo_id']) ? intval($_POST['tipo_id']) : 0;

        if ($tipo_id > 0) {
            $datos = $liquidacion->get_actividades($tipo_id);
            if (is_array($datos) && count($datos) > 0) {
                $html = "<option value='' disabled selected>--Actividad--</option>";
                foreach ($datos as $row) {
                    $html .= "<option value='" . $row['act_id'] . "'>" . htmlspecialchars($row['act_nombre']) . "</option>";
                }
                echo $html;
            } else {
                echo "<option value=''>No hay actividades</option>";
            }
        } else {
            echo "<option value=''>Seleccione un tipo de vehículo válido</option>";
        }
        break;


    /** LISTAR LIQUIDACIONES */

    case 'listarLiquidaciones':

        $datos = $liquidacion->get_liquidacion();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["liq_codigo"];
            $sub_array[] = $row["liq_descripcion"];
            $sub_array[] = $row["liq_fecha_inicio"] . " / " . $row["liq_fecha_fin"];
            $sub_array[] = $row["liq_total"];

            // 🔥 Convertir estado en etiqueta <span>
            $estado = '';
            switch ($row["liq_estado"]) {
                case 1:
                    $estado = '<span class="badge badge-primary">Creado</span>';
                    break;
                case 2:
                    $estado = '<span class="badge badge-warning">En liquidación</span>';
                    break;
                case 3:
                    $estado = '<span class="badge badge-success">Liquidado</span>';
                    break;
                case 4:
                    $estado = '<span class="badge badge-danger">Anulado</span>';
                    break;
                default:
                    $estado = '<span class="badge badge-secondary">Desconocido</span>';
                    break;
            }


            if ($row["liq_estado"] == 4) {
                // Si ya está anulada, ningún botón activo
                $botones = '<span class="text-muted"> </span>';
            } else if ($row["liq_estado"] == 3) {
                $botones = '<div class="button-container text-center">
        <button id="editLiq" type="button" onClick="ver(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-warning btn-icon" disabled>
            <div><i class="fa fa-edit"></i></div>
        </button>
        <button id="closeLiq" type="button" onClick="liquidar(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-secondary btn-icon" disabled>
            <div><i class="fas fa-lock"></i></div>
        </button>
        <button type="button" onClick="anular(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-danger btn-icon" disabled>
            <div><i class="fas fa-ban"></i></div>
        </button>
		<button type="button" onClick="detalle(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-info btn-icon">
            <div><i class="fas fa-eye"></i></div>
        </button>
		<button type="button" onClick="listarComision(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-dark btn-icon">
            <div><i class="fas fa-file-invoice-dollar"></i></div>
        </button>

    </div>';
            } else {
                $botones = '<div class="button-container text-center">
        <button id="editLiq" type="button" onClick="ver(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-warning btn-icon">
            <div><i class="fa fa-edit"></i></div>
        </button>
        <button id="closeLiq" type="button" onClick="liquidar(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-secondary btn-icon">
            <div><i class="fas fa-lock"></i></div>
        </button>
        <button type="button" onClick="anular(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-danger btn-icon">
            <div><i class="fas fa-ban"></i></div>
        </button>
		<button type="button" onClick="detalle(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-info btn-icon">
            <div><i class="fas fa-eye"></i></div>
        </button>
        <button type="button" onClick="listarComision(' . $row["liq_codigo"] . ');" id="' . $row["liq_codigo"] . '" class="btn btn-dark btn-icon" disabled>
            <div><i class="fas fa-file-invoice-dollar"></i></div>
        </button>

    </div>';
            }
            $sub_array[] = $estado;
            $sub_array[] = $botones;

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);




        break;
		
    /**LISTAR LIQUIDACIONES CERRADAS */


    case 'listarLiquidacionesCerradas':


        $id = $_POST['id'] ?? null;

        // Si no hay id o no es entero → no consultes
        if (!$id || !ctype_digit((string)$id)) {
            echo json_encode(["aaData" => []]); // o mensaje simple
            break;
        }

        $datos = $liquidacion->get_liquidacion_cerrada((int)$id);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["fecha_reporte"];
            $sub_array[] = $row["descrip_liquidacion"];
            $sub_array[] = $row["fecha_inicio"];
            $sub_array[] = $row["fecha_fin"];
            $sub_array[] = $row["actividad"];
			$sub_array[] = $row["tipo"];
			$sub_array[] = $row["ccosto"];
            $sub_array[] = $row["marca"];
            $sub_array[] = $row["placa"];
			$sub_array[] = $row["conductor"];
            $sub_array[] = $row["obra"];
            $sub_array[] = $row["km_hm_inicial"];
            $sub_array[] = $row["km_hm_final"];
            $sub_array[] = $row["km_hm_total"];
            $sub_array[] = $row["volumen"];
            $sub_array[] = $row["tarifa"];
            $sub_array[] = $row["sub_total"];
            $sub_array[] = $row["total"];
            $sub_array[] = $row["observaciones"];
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results, JSON_UNESCAPED_UNICODE);


        break;


    /* GUARDAR Y EDITAR */
    case 'saveLiquidacion':
        $nombre = $_POST["name_liquidacion"] ?? '';
        $fecha_inicio = $_POST["fecha_inicio"] ?? '';
        $fecha_fin = $_POST["fecha_fin"] ?? '';
        $user_codigo = $_POST["user_idx"] ?? '';

        // Validación básica
        if (!empty($nombre) && !empty($fecha_inicio) && !empty($fecha_fin) && !empty($user_codigo)) {

            // Opcional: validar formato de fecha (YYYY-MM-DD)
            if (DateTime::createFromFormat('Y-m-d', $fecha_inicio) && DateTime::createFromFormat('Y-m-d', $fecha_fin)) {

                // Llamada al modelo para insertar
                $resultado = $liquidacion->insertLiquidacion($nombre, $fecha_inicio, $fecha_fin, $user_codigo);

                if ($resultado) {
                    echo json_encode(array(
                        "status" => "success",
                        "message" => "Datos guardados correctamente"
                    ));
                } else {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "No se pudo guardar la liquidación en la base de datos"
                    ));
                }
            } else {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Formato de fecha no válido"
                ));
            }
        } else {
            echo json_encode(array(
                "status" => "error",
                "message" => "Todos los campos son obligatorios"
            ));
        }
        break;

    case 'detalleLiquidacion':
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        $liquidacionID = $_POST['id'];

        // Obtiene el detalle
        $detalle = $liquidacion->get_detalle_liquid($liquidacionID);

        if ($detalle === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al consultar la base de datos'
            ]);
            exit;
        }

        if (empty($detalle)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron detalles para el ticket ID: ' . $liquidacionID
            ]);
            exit;
        }

        // Respuesta con detalle y fechas
        echo json_encode([
            'status' => 'success',
            'data' => $detalle,
        ]);
        exit;

        break;
    case 'readLiquidacion':

        $datos = $liquidacion->get_read_liquidacion(
            $_POST['tipo_vehiculo'],
            $_POST['actividad'],
            $_POST['obra'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin']
        );

        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["repdia_fech"];
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = $row["repdia_volu"];
            $sub_array[] = $row["repdia_num_viajes"];
            $sub_array[] = $row["repdia_kilo"];
            $sub_array[] = $row["repdia_kilo_final"];
            $sub_array[] = $row["act_tarifa"]; //
            $sub_array[] = $row["repdia_observa"];
            $sub_array[] = $row["repdia_id"];
            $sub_array[] = $row["repdia_actv"];
            $sub_array[] = $row["repdia_obras"];
            $data[] = $sub_array;
        }

        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case 'saveDetalle':
        // 1) Indicamos que vamos a devolver JSON
        header('Content-Type: application/json; charset=utf-8');

        // 2) Leer y validar parámetros
        $liqId    = isset($_GET['id'])              ? intval($_GET['id'])          : null;
        $tipoVeh  = isset($_POST['tipo_vehiculo'])  ? intval($_POST['tipo_vehiculo']) : null;
        $detalles = isset($_POST['detalles'])       ? json_decode($_POST['detalles'], true) : [];

        if (!$liqId || !$tipoVeh || !is_array($detalles)) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Parámetros inválidos.'
            ]);
            exit;
        }

        // 3) Llamar al modelo para insertar y actualizar
        $result = $liquidacion->insertDetalle($liqId, $tipoVeh, $detalles);

        // 4) Devolver la respuesta según el resultado
        if (isset($result['success']) && $result['success'] === true) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Detalle guardado correctamente.'
            ]);
        } else {
            // Si el modelo devuelve error o exception
            http_response_code(500);
            $msg = isset($result['error']) ? $result['error'] : 'Error desconocido.';
            echo json_encode([
                'status'  => 'error',
                'message' => 'Error al guardar: ' . $msg
            ]);
        }
        break;

    case 'anularLiquidacion':

        header('Content-Type: application/json; charset=utf-8');

        $codi_liq = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($codi_liq <= 0) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'ID de liquidación inválido.'
            ]);
            exit;
        }

        // 3) Llamar al método del modelo
        $result = $liquidacion->anularDetalle($codi_liq);

        // 4) Procesar resultado
        if (!empty($result['success']) && $result['success'] === true) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Liquidación anulada correctamente.'
            ]);
        } else {
            http_response_code(500);
            $err = isset($result['error']) ? $result['error'] : 'Error desconocido.';
            echo json_encode([
                'status'  => 'error',
                'message' => 'No se pudo anular: ' . $err
            ]);
        }

        break;
    case 'liquidar':
        header('Content-Type: application/json; charset=utf-8');
        $codi_liq = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($codi_liq <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
            exit;
        }
        $result   = $liquidacion->liquidarLiqu($codi_liq);

        if (!empty($result['success']) && $result['success'] === true) {
            echo json_encode([
                'status'  => 'success',
                'message' => "Liquidación procesada. Total: {$result['total']}"
            ]);
        } else {
            http_response_code(500);
            $msg = $result['error'] ?? 'Error desconocido.';
            echo json_encode(['status' => 'error', 'message' => "No se pudo liquidar: $msg"]);
        }
        break;
		
    case 'comisiones':
        header('Content-Type: application/json; charset=utf-8');


        $codi_liq = isset($_POST['liquidacion_id']) ? intval($_POST['liquidacion_id']) : 0;
        if ($codi_liq <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
            exit;
        }
        // Tipos (puede venir vacío, 1 solo valor o arreglo)
        $tipos = isset($_POST['tipos']) ? $_POST['tipos'] : [];
        if (!is_array($tipos)) {
            $tipos = [$tipos];
        }

        // (Opcional) limpiar espacios y quitar vacíos
        $tipos = array_filter(array_map(function ($v) {
            return trim((string)$v);
        }, $tipos), fn($v) => $v !== '');



        // Llamar al modelo -> DEVUELVE FILAS
        $result = $liquidacion->getComisiones($codi_liq, $tipos);

        // Responder con las filas directamente (lo que espera tu DataTable/JS)
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;


        break;

    case 'reporteComisiones':

        // Valida que venga el id
        if (!isset($_POST['liquidacion_id']) || (int)$_POST['liquidacion_id'] <= 0) {
            http_response_code(400);
            echo 'ID de liquidación inválido.';
            exit;
        }

        $id_liq = (int)$_POST['liquidacion_id'];

        // OJO: el array llega en 'tipos' (sin [])
        $tipos = $_POST['tipos'] ?? [];
        if (!is_array($tipos)) {
            $tipos = [$tipos];
        }

        // Llama al modelo (ajusta el método a lo que devuelva)
        $reporte = $liquidacion->getComisiones($id_liq, $tipos);

        // Genera el PDF (esta vista debería hacer FPDF->Output y no imprimir nada más)
        require '../view/PDF/comisiones.php';
        exit; // Muy importante para no seguir ejecutando ni mandar más salida

        break;
}