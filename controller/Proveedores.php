<?php
require_once('../config/conexion.php');
require_once("curl.php");

switch ($_GET['op']) {

    case "consultaProveedorSiesa":
        header('Content-Type: application/json');

        // Validar parámetros
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página solicitada
        $tamPag = isset($_GET['tamPag']) ? (int)$_GET['tamPag'] : 100; // Número de registros por página
        $proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : '';  // Obtener el parámetro 'proveedor'

        try {
            // Construir la URL de la solicitud a la API
            $url = 'idCompania=6026&descripcion=asfaltart_f202_proveedores_nombre';
            $url .= '&paginacion=' . urlencode("numPag=$pagina|tamPag=$tamPag");

            // Agregar parámetro de búsqueda del proveedor
            if (!empty($proveedor)) {
                $url .= '&parametros=' . urlencode("nombre=$proveedor");
            }

            // Realizar la solicitud a la API
            $response = CurlController::requestEstandar($url, "GET");

            // Verificar si la respuesta es válida
            if (!$response || !isset($response->detalle) || !isset($response->detalle->Datos)) {
                throw new Exception("Respuesta de API inválida o sin datos.");
            }

            // Procesar los datos de la API
            $data = [];
            foreach ($response->detalle->Datos as $row) {
                $data[] = [
                    $row->f200_nit ?? '',
                    $row->f202_descripcion_sucursal ?? ''
                ];
            }

            // Obtener el total de registros y el total de páginas
            $totalRegistros = isset($response->detalle->total_registros) ? $response->detalle->total_registros : count($data);
            $totalPaginas = isset($response->detalle->total_páginas) ? $response->detalle->total_páginas : 1;

            // Devolver la respuesta en formato JSON
            echo json_encode([
                "success" => true,
                "iTotalRecords" => $totalRegistros,
                "iTotalDisplayRecords" => $totalRegistros,
                "totalPaginas" => $totalPaginas,
                "aaData" => $data
            ]);
        } catch (Exception $e) {
            // Manejo de errores: si ocurre una excepción, enviar la respuesta de error
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage(),
                "aaData" => []
            ]);
        }
        break;
}
