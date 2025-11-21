<?php
/*CONTOLADOR TIPO DE VEHICULO*/
require_once("../config/conexion.php");
require_once("../models/OrdenTrabajo.php");

$ordenes = new OrdenTrabajo();

switch ($_REQUEST["op"]) {

    case 'numeroOrden':
        $ultimo = $ordenes->obternerUltimoConsecutivo();

        // Verifica si obtuvo un resultado correcto
        if ($ultimo && isset($ultimo['num_otm'])) {
            // Extraer los últimos 6 dígitos del repo_numb
            preg_match('/(\d{6})$/', $ultimo['num_otm'], $matches);

            if (isset($matches[1])) {
                $ultimoNumero = intval($matches[1]); // Convertir a número
                $nuevoNumero = str_pad($ultimoNumero + 1, 6, "0", STR_PAD_LEFT);
            } else {
                $nuevoNumero = "000001"; // Si no encuentra el número, inicia en 000001
            }
        } else {
            $nuevoNumero = "000001";
        }

        // Generar el nuevo código con el año actual
        $nuevoReporte = "OTM-" . date("Y") . "-" . $nuevoNumero;

        echo $nuevoReporte;


        break;

    case 'guardarOrdenTrabajo':

        if (isset(
            $_POST['ticket_id'],
            $_POST['num_orden'],
            $_POST['fecha_asignacion'],
            $_POST['mantenimiento'],
            $_POST['tecnico'],
            $_POST['prioridad'],
            $_POST['actividad']
        )) {

            $numeroOrden =  $_POST['num_orden'];
            $fechaAsig =  $_POST['fecha_asignacion'];
            $mantenimiento =  $_POST['mantenimiento'];
            $tecnico =  $_POST['tecnico'];
            $actividad =  $_POST['actividad'];
            $ticket =  $_POST['ticket_id'];
            $prioridad = $_POST['prioridad'];

            $resultado = $ordenes->insert_orden(
                $numeroOrden,
                $fechaAsig,
                $mantenimiento,
                $tecnico,
                $actividad,
                $prioridad,
                $ticket
            );
            if ($resultado) {
                echo json_encode(['status' => 'success', 'message' => 'Reporte creado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Hubo un error al crear el reporte.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos.']);
        }


        break;
}
