<?php

require_once('../config/conexion.php');
require_once('../models/Despachos.php');
require_once("curl.php");

$despachos = new Despachos();

switch ($_GET['op']) {


    case "consultarInventarioAcpmSiesa":

        $url = 'idCompania=6026';
        $url .= '&descripcion=API_v2_Inventarios_InvFecha';
        $url .= '&paginacion=' . urlencode("numPag=1|tamPag=100");

        $parametros = "f120_referencia =''5005''"
            . " and f120_id_cia = ''1''"
            . " and f150_id = ''001''";

        $url .= '&parametros=' . urlencode($parametros);

        $method = "GET";

        $response = CurlController::requestEstandarSiesaReal(
            $url,
            $method
        );

        if (
            isset($response->codigo) &&
            $response->codigo == 0 &&
            isset($response->detalle->Table) &&
            is_array($response->detalle->Table) &&
            count($response->detalle->Table) > 0
        ) {

            $row = $response->detalle->Table[0];

            $resultado = array(
                "status" => "success",
                "f120_id_cia" => $row->f120_id_cia ?? '',
                "f150_id" => trim($row->f150_id ?? ''),
                "f120_id" => $row->f120_id ?? '',
                "f120_referencia" => trim($row->f120_referencia ?? ''),
                "f120_id_unidad_inventario" => trim(
                    $row->f120_id_unidad_inventario ?? ''
                ),
                "f400_cant_existencia_1" => $row->f400_cant_existencia_1 ?? 0
            );

            echo json_encode($resultado);
        } else {

            echo json_encode(array(
                "status" => "error",
                "message" => "No se pudo consultar el inventario de ACPM en SIESA."
            ));
        }

        break;

    /*
    |--------------------------------------------------------------------------
    | RESUMEN DE DESPACHOS DEL DIA
    |--------------------------------------------------------------------------
    |
    | Retorna:
    | - Total despachos
    | - Total galones despachados
    | - Despachos pendientes de documento SIESA
    | - Galones pendientes SIESA
    | - Despachos registrados en SIESA
    | - Galones registrados en SIESA
    |
    */
    case "resumenControlAcpmHoy":

        $datos = $despachos->get_resumen_control_acpm_hoy();

        if (is_array($datos)) {

            echo json_encode(array(
                "status" => "success",

                "total_despachos" =>
                (int) ($datos["total_despachos"] ?? 0),

                "total_galones" =>
                (float) ($datos["total_galones"] ?? 0),

                "total_pendientes_siesa" =>
                (int) ($datos["total_pendientes_siesa"] ?? 0),

                "galones_pendientes_siesa" =>
                (float) ($datos["galones_pendientes_siesa"] ?? 0),

                "total_registrados_siesa" =>
                (int) ($datos["total_registrados_siesa"] ?? 0),

                "galones_registrados_siesa" =>
                (float) ($datos["galones_registrados_siesa"] ?? 0)
            ));
        } else {

            echo json_encode(array(
                "status" => "error",
                "message" => "No fue posible consultar el resumen de despachos."
            ));
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | DESPACHOS DEL DIA AGRUPADOS POR OBRA
    |--------------------------------------------------------------------------
    */
    case "despachosObraHoy":

        $datos = $despachos->get_despachos_obra_hoy();

        $data = array();

        if (is_array($datos)) {

            foreach ($datos as $row) {

                $data[] = array(
                    "obras_id" =>
                    $row["obras_id"],

                    "obras_nom" =>
                    $row["obras_nom"],

                    "total_despachos" =>
                    (int) $row["total_despachos"],

                    "total_galones" =>
                    (float) $row["total_galones"]
                );
            }
        }

        echo json_encode(array(
            "status" => "success",
            "data" => $data
        ));

        break;


    /*
    |--------------------------------------------------------------------------
    | LISTAR DESPACHOS PENDIENTES DE DOCUMENTO SIESA
    |--------------------------------------------------------------------------
    */
    case "listarPendientesSiesa":

        $datos = $despachos->get_despachos_pendientes_siesa_hoy();

        $data = array();

        if (is_array($datos)) {

            foreach ($datos as $row) {

                $sub_array = array();

                /*
                 * FECHA / HORA
                 */
                $fecha = '';

                if (!empty($row["desp_fech"])) {

                    $fecha = date_format(
                        new DateTime($row["desp_fech"]),
                        'd/m/Y'
                    );
                }

                $hora = '';

                if (!empty($row["desp_hora"])) {

                    $horaObj = new DateTime(
                        $row["desp_hora"]
                    );

                    $hora = $horaObj->format('H:i:s');
                }

                $sub_array[] =
                    $fecha . ' ' . $hora;


                /*
                 * OBRA
                 */
                $sub_array[] =
                    $row["obras_nom"] ?? '';


                /*
                 * EQUIPO
                 */
                $sub_array[] =
                    $row["vehi_placa"] ?? '';


                /*
                 * GALONES
                 */
                $sub_array[] =
                    $row["desp_galones"] ?? 0;


                /*
                 * TIEMPO PENDIENTE
                 *
                 * Inicialmente lo dejamos vacío.
                 * Lo calcularemos después cuando definamos
                 * exactamente cómo queremos mostrarlo.
                 */
                $sub_array[] = '';


                /*
                 * DOCUMENTO SIESA
                 */
                $sub_array[] =
                    '<span class="badge badge-warning">
                        Pendiente
                    </span>';


                /*
                 * ACCIONES
                 */
                $sub_array[] =
                    '<div class="button-container text-center">

                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="registrarDocumentoSiesa('
                    . $row["desp_id"] .
                    ')">

                            <i class="fas fa-file-alt"></i>

                        </button>

                    </div>';


                $data[] = $sub_array;
            }
        }

        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($resultado);

        break;


    /*
    |--------------------------------------------------------------------------
    | GUARDAR DOCUMENTO INTERNO SIESA
    |--------------------------------------------------------------------------
    */
    case "guardarDocumentoSiesa":

        $desp_id =
            $_POST["desp_id"] ?? '';

        $desp_documento_siesa =
            trim(
                $_POST["desp_documento_siesa"]
                    ?? ''
            );


        /*
         * VALIDAR DESPACHO
         */
        if (empty($desp_id)) {

            echo json_encode(array(
                "status" => "error",
                "message" => "No se recibió el despacho."
            ));

            break;
        }


        /*
         * VALIDAR DOCUMENTO
         */
        if (empty($desp_documento_siesa)) {

            echo json_encode(array(
                "status" => "error",
                "message" => "Debe ingresar el documento interno SIESA."
            ));

            break;
        }


        /*
         * ACTUALIZAR
         */
        $resultado =
            $despachos->update_documento_siesa(
                $desp_id,
                $desp_documento_siesa
            );


        if ($resultado) {

            echo json_encode(array(
                "status" => "success",
                "message" =>
                "Documento SIESA registrado correctamente."
            ));
        } else {

            echo json_encode(array(
                "status" => "error",
                "message" =>
                "No fue posible registrar el documento SIESA."
            ));
        }

        break;

    case "listarHistorialAcpm":

        /*
     * Si no llegan fechas desde el frontend,
     * cargar únicamente el mes actual.
     */
        $fecha_inicio = !empty($_POST["fecha_inicio"])
            ? $_POST["fecha_inicio"]
            : date("Y-m-01");

        $fecha_final = !empty($_POST["fecha_final"])
            ? $_POST["fecha_final"]
            : date("Y-m-d");

        $desp_obra = !empty($_POST["desp_obra"])
            ? $_POST["desp_obra"]
            : '';

        $desp_vehi = !empty($_POST["desp_vehi"])
            ? $_POST["desp_vehi"]
            : '';

        $estado_siesa = !empty($_POST["estado_siesa"])
            ? $_POST["estado_siesa"]
            : '';


        $datos =
            $despachos->get_historial_control_acpm(
                $fecha_inicio,
                $fecha_final,
                $desp_obra,
                $desp_vehi,
                $estado_siesa
            );


        $data = array();

        $total_galones = 0;


        foreach ($datos as $row) {

            $sub_array = array();


            /*
         * FECHA
         */
            $sub_array[] =
                !empty($row["desp_fech"])
                ? date_format(
                    new DateTime(
                        $row["desp_fech"]
                    ),
                    "d/m/Y"
                )
                : '';


            /*
         * HORA
         */
            $sub_array[] =
                !empty($row["desp_hora"])
                ? date_format(
                    new DateTime(
                        $row["desp_hora"]
                    ),
                    "H:i"
                )
                : '';


            /*
         * OBRA
         */
            $sub_array[] =
                $row["obras_nom"] ?? '';


            /*
         * EQUIPO
         */
            $sub_array[] =
                $row["vehi_placa"] ?? '';


            /*
         * GALONES
         */
            $galones =
                (float) (
                    $row["desp_galones"]
                    ?? 0
                );

            $sub_array[] =
                $galones;

            $total_galones +=
                $galones;


            /*
         * RECIBO INTERNO
         */
            $sub_array[] =
                $row["desp_recibo"] ?? '';


            /*
         * DOCUMENTO SIESA
         */
            $documentoSiesa =
                trim(
                    $row["desp_documento_siesa"]
                        ?? ''
                );

            $sub_array[] =
                $documentoSiesa;


            /*
         * CONDUCTOR
         */
            $sub_array[] =
                $row["conductor"] ?? '';


            /*
         * ESTADO
         */
            if ($documentoSiesa !== '') {

                $sub_array[] =
                    '<div class="text-center">
                    <span class="badge badge-success">
                        CARGADO SIESA
                    </span>
                </div>';
            } else {

                $sub_array[] =
                    '<div class="text-center">
                    <span class="badge badge-warning">
                        PENDIENTE SIESA
                    </span>
                </div>';
            }


            /*
         * ACCION
         */
            $sub_array[] =
                '<div class="text-center">

                <button
                    type="button"
                    class="btn btn-info btn-sm"
                    onClick="verDetalleHistorial('
                . $row["desp_id"] .
                ');">

                    <i class="fas fa-eye"></i>

                </button>

            </div>';


            $data[] =
                $sub_array;
        }


        echo json_encode(array(

            "sEcho" => 1,

            "iTotalRecords" =>
            count($data),

            "iTotalDisplayRecords" =>
            count($data),

            "aaData" =>
            $data,

            /*
         * Este dato nos servirá para
         * la card resumen.
         */
            "total_galones" =>
            $total_galones,

            "fecha_inicio" =>
            $fecha_inicio,

            "fecha_final" =>
            $fecha_final

        ));

        break;


    /*
    |--------------------------------------------------------------------------
    | OPERACION NO VALIDA
    |--------------------------------------------------------------------------
    */
    default:

        echo json_encode(array(
            "status" => "error",
            "message" => "Operación no válida."
        ));

        break;
}
