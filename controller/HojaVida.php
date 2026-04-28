<?php
/*CONTOLADOR TIPO DE VEHICULO*/
require_once("../config/conexion.php");
require_once("../models/HojaVida.php");

$sheet = new HojaVida();

switch ($_GET["op"]) {
    case 'listarHV':
        $id_vehiculo = $_POST["id_vehiculo"] ?? null;
        $fechaIni    = $_POST["fechaIni"]    ?? '';
        $fechaFin    = $_POST["fechaFin"]    ?? '';
        $tipo_mtto   = $_POST["tipo_mtto"]   ?? '';

        if (empty($id_vehiculo)) {
            echo json_encode(['success' => false, 'mensaje' => 'Vehículo requerido.']);
            exit;
        }

        $data = $sheet->get_hoja_vida($id_vehiculo, $fechaIni, $fechaFin, $tipo_mtto);

        if (!$data) {
            echo json_encode(['success' => false, 'mensaje' => 'Equipo no encontrado.']);
            exit;
        }

        echo json_encode(['success' => true, 'data' => $data]);
        break;
}
