<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");
require_once("../../models/Tickets.php");

class PDF extends FPDF {
    // Cabecera de página
    function Header() {

        $this->SetY(15);
        $this->Image('../../public/img/logo.png', 10, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', '', 8);

        $this->SetX(140);
        $this->Cell(30, 5, 'Version:', 0, 0, 'R');
        $this->Cell(30, 5, '', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, 'Implementacion:', 0, 0, 'R');
        $this->Cell(30, 5, 'Enero 6 del 2025', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, 'Codigo:', 0, 0, 'R');
        $this->Cell(30, 5, '', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, utf8_decode('Página: '), 0, 0, 'R');
        $this->Cell(30, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        $this->SetFont('Arial', 'B', 13);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(55, 15, 'ORDEN DE TRABAJO MANTENIMIENTO', 0, 0, 'C');
        $this->Ln(25);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
    }
}
// Obtener el ID desde la URL
if (isset($_GET['ID'])) {
    $codigo_orden = $_GET['ID'];
} else {
    die("Error: ID no especificado.");
}

$ticketsClass = new Tickets();
$tikets = $ticketsClass->get_tickets_ordenes($codigo_orden);

$numeroSolicitud = isset($tikets[0]['num_soli']) ? $tikets[0]['num_soli'] : 'N/A';
$fecha_solicitud = isset($tikets[0]['fech_creac_soli']) ? $tikets[0]['fech_creac_soli'] : 'N/A';
$prioridad_solicitud = isset($tikets[0]['prio_soli']) ? $tikets[0]['prio_soli'] : 'N/A';
$user_nomb_solic = isset($tikets[0]['user_nombre']) ? $tikets[0]['user_nombre'] : 'N/A';
$user_apel_solic = isset($tikets[0]['user_apellidos']) ? $tikets[0]['user_apellidos'] : 'N/A';
$notas_solicitud = isset($tikets[0]['desc_soli']) ? $tikets[0]['desc_soli'] : 'N/A';

//separar fecha
$fechaObj = new DateTime($fecha_solicitud);

$fechaFormateada = $fechaObj->format('d/m/Y'); // 06/11/2025
$horaFormateada  = $fechaObj->format('H:i A');   // 11:38 

//orden de trabajo datos

$numeroOrden = isset($tikets[0]['num_otm']) ? $tikets[0]['num_otm'] : 'N/A';
$fechaOrden = isset($tikets[0]['fech_creac_otm']) ? $tikets[0]['fech_creac_otm'] : 'N/A';
$fechaObjOT = new DateTime($fechaOrden);
$fechaOT = $fechaObjOT->format('d/m/Y');
$prioridadOrden = isset($tikets[0]['prio_otm']) ? $tikets[0]['prio_otm'] : 'N/A';
$vehiMarca = isset($tikets[0]['vehi_marca']) ? $tikets[0]['vehi_marca'] : 'N/A';
$vehiPlaca = isset($tikets[0]['vehi_placa']) ? $tikets[0]['vehi_placa'] : 'N/A';
$vehiNumCosto = isset($tikets[0]['vehi_costo']) ? $tikets[0]['vehi_costo'] : 'N/A';
$vehiNumCodi = isset($tikets[0]['vehi_codigo']) ? $tikets[0]['vehi_codigo'] : 'N/A';
$tipoMantenimiento = isset($tikets[0]['tipo_mantenimiento']) ? $tikets[0]['tipo_mantenimiento'] : 'N/A';
$lectura_anterior = isset($tikets[0]['lectura_anterior']) ? $tikets[0]['lectura_anterior'] : 'N/A';
$lectura_actual = isset($tikets[0]['lect_soli']) ? $tikets[0]['lect_soli'] : 'N/A';
$tecnico = isset($tikets[0]['tecn_otm']) ? $tikets[0]['tecn_otm'] : 'N/A';



$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$yInicio = $pdf->GetY();
$pdf->SetMargins(10, 25, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetX(10);
$pdf->SetFont('Helvetica', 'B', 10);


// Encabezado principal
$pdf->Cell(120, 8, utf8_decode('SOLICITUD DE MANTENIMIENTO'), 1, 0, 'C');
$pdf->Cell(65, 8, utf8_decode('CONSECUTIVO: ') . $numeroSolicitud, 'L', 1, 'C');

// =========================
// FECHA - HORA - PRIORIDAD
// =========================
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(25, 8, utf8_decode('FECHA:'), 'LT', 0, 'L');
$pdf->Cell(35, 8, $fechaFormateada, 'T', 0, 'L');
$pdf->Cell(25, 8, utf8_decode('HORA:'), 'T', 0, 'L');
$pdf->Cell(25, 8, $horaFormateada, 'T', 0, 'L');
$pdf->Cell(30, 8, utf8_decode('Prioridad:'), 'T', 0, 'L');
$pdf->Cell(45, 8, utf8_decode($prioridad_solicitud), 'T', 1, 'L');

// Reportado por
$pdf->Cell(30, 8, utf8_decode('REPORTADO POR:'), 'L', 0, 'L');
$pdf->Cell(160, 8, utf8_decode($user_nomb_solic . ' ' . $user_apel_solic), 0, 1, 'L');

// Notas
$pdf->Cell(30, 8, utf8_decode('NOTAS:'), 'LB', 0, 'L');
$pdf->MultiCell(160, 8, utf8_decode($notas_solicitud), 0, 'L');

$yFin = $pdf->GetY();

$xInicio = 10;  // margen izquierdo por defecto
$anchoTotal = $pdf->GetPageWidth() - 25; // margen izq+der
$alto = $yFin - $yInicio;
$pdf->Rect($xInicio, $yInicio, $anchoTotal, $alto);

//ORDEN DE TRABAJO - ENCABEZADO

$pdf->SetY(78);
$pdf->SetMargins(10, 25, 10);
$yInicio = $pdf->GetY();
$pdf->Cell(120, 8, utf8_decode('ORDEN DE TRABAJO'), 1, 0, 'C');
$pdf->Cell(65, 8, utf8_decode('CONSECUTIVO: ') . $numeroOrden, 'L', 1, 'C');

//Cuerpo

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(25, 8, utf8_decode('FECHA INICIO:'), 'LT', 0, 'L');
$pdf->Cell(35, 8, $fechaOT, 'T', 0, 'L');
$pdf->Cell(20, 8, utf8_decode('PRIORIDAD:'), 'T', 0, 'L');
$pdf->Cell(15, 8, $prioridadOrden, 'T', 0, 'L');
$pdf->Cell(35, 8, utf8_decode('CENTRO DE COSTO:'), 'T', 0, 'L');
$pdf->Cell(55, 8, utf8_decode($vehiNumCosto . ' - ' . $vehiMarca . ' ' . $vehiPlaca), 'T', 1, 'L');

// Reportado por
$pdf->Cell(50, 8, utf8_decode('CODIGO MAQUINA / EQUIPO:'), 0, 0, 'L');
$pdf->Cell(140, 8, utf8_decode($vehiNumCodi . ' - ' . $vehiMarca . ' ' . $vehiPlaca), 0, 1, 'L');


// Reportado por
$pdf->Cell(45, 8, utf8_decode('TIPO DE MANTENIMIENTO:'), 0, 0, 'L');
$pdf->Cell(20, 8, utf8_decode($tipoMantenimiento), 0, 0, 'L');
$pdf->Cell(25, 8, utf8_decode('LECTURA ANT:'), 0, 0, 'L');
$pdf->Cell(25, 8, utf8_decode($lectura_anterior), 0, 0, 'L');
$pdf->Cell(30, 8, utf8_decode('NUEVA LECTURA:'), 0, 0, 'L');
$pdf->Cell(20, 8, utf8_decode($lectura_actual), 0, 1, 'L');


$pdf->Cell(45, 8, utf8_decode('FECHA NUEVA LECTURA:'), 0, 0, 'L');
$pdf->Cell(30, 8, utf8_decode($fechaOT), 0, 0, 'L');
$pdf->Cell(40, 8, utf8_decode('TECNICO / PROVEEDOR:'), 0, 0, 'L');
$pdf->Cell(75, 8, utf8_decode($tecnico), 0, 1, 'L');

// Notas
$pdf->Cell(45, 8, utf8_decode('ACTIVIDAD A REALIZAR:'), 'LB', 0, 'L');
$pdf->MultiCell(145, 8, utf8_decode($notas_solicitud), 0, 'L');

$yFin = $pdf->GetY();

$xInicio = 10;  // margen izquierdo por defecto
$anchoTotal = $pdf->GetPageWidth() - 25; // margen izq+der
$alto = $yFin - $yInicio;
$pdf->Rect($xInicio, $yInicio, $anchoTotal, $alto);

$pdf->Ln(5);

// ================================================
// ======== BLOQUE RECEPCION MANTENIMIENTO ========
// ================================================
// PRIMER BLOQUE: RECEPCION DE MANTENIMIENTO
$startX = 10;
$startY = $pdf->GetY();
$blockWidth = 185;
$blockHeight = 60; // Altura solo para RECEPCION

// DIBUJAR MARCO EXTERNO SOLO PARA RECEPCION
$pdf->Rect($startX, $startY, $blockWidth, $blockHeight);

// TITULO CENTRADO CON BORDE
$pdf->SetXY($startX, $startY);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell($blockWidth, 8, 'RECEPCION DE MANTENIMIENTO', 1, 1, 'C');

// FECHA / HORA / ESTADO DE LIMPIEZA
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY($startX + 5, $startY + 10);

$pdf->Cell(15, 6, 'FECHA:', 0, 0);
$pdf->Cell(20, 6, '_________', 0, 0);
$pdf->Cell(3, 6, '', 0, 0);

$pdf->Cell(20, 6, 'HORA:', 0, 0);
$pdf->Cell(20, 6, '_________', 0, 0);
$pdf->Cell(3, 6, '', 0, 0);


$pdf->Cell(45, 6, 'ESTADO DE LIMPIEZA:', 0, 0);

// Casillas de verificación
$pdf->Cell(20, 6, 'Bueno', 0, 0);
$pdf->Rect($pdf->GetX() - 1, $pdf->GetY() + 1, 4, 4);
$pdf->Cell(8, 6, '', 0, 0);

$pdf->Cell(18, 6, 'Regular', 0, 0);
$pdf->Rect($pdf->GetX() - 1, $pdf->GetY() + 1, 4, 4);
// No hay casilla para "Bajo" según la imagen

$pdf->Ln(10);

// NOMBRE QUIEN EJECUTA
$pdf->SetXY($startX + 5, $startY + 23);
$pdf->Cell(55, 6, 'NOMBRE DE QUIEN EJECUTA:', 0, 0);
$pdf->Cell(130, 6, '_________________________________________________', 0, 1);

// TABLA TRABAJOS
$pdf->SetXY($startX + 0, $startY + 32);
$pdf->SetFont('Arial', 'B', 10);

// Definir anchos de columnas
$w1 = 60; // ACTIVIDADES PROGRAMADAS
$w2 = 60; // DETALLE DE TRABAJOS
$w3 = 25; // REPUESTO
$w4 = 20; // UM
$w5 = 20; // CANT

// Encabezado de tabla
$pdf->Cell($w1, 7, 'ACTIVIDADES PROGRAMADAS', 1, 0, 'C');
$pdf->Cell($w2, 7, 'DETALLE DE TRABAJOS', 1, 0, 'C');
$pdf->Cell($w3, 7, 'REPUESTO', 1, 0, 'C');
$pdf->Cell($w4, 7, 'UM', 1, 0, 'C');
$pdf->Cell($w5, 7, 'CANT', 1, 1, 'C');

// Filas de la tabla (3 filas vacías)
$pdf->SetFont('Arial', '', 10);
for ($i = 0; $i < 3; $i++) {
    $pdf->SetX($startX + 0);
    $pdf->Cell($w1, 7, '', 1, 0);
    $pdf->Cell($w2, 7, '', 1, 0);
    $pdf->Cell($w3, 7, '', 1, 0);
    $pdf->Cell($w4, 7, '', 1, 0);
    $pdf->Cell($w5, 7, '', 1, 1);
}

// ================================================
// ============ BLOQUE FIRMA SEPARADO =============
// ================================================
$startX = 10;
$startY = $pdf->GetY()+5;
$blockWidth = 185;
$blockHeight = 80; // Altura solo para RECEPCION

// DIBUJAR MARCO EXTERNO SOLO PARA RECEPCION
$pdf->Rect($startX, $startY, $blockWidth, $blockHeight);

// BLOQUE "FIRMA" CENTRADO
$pdf->SetXY($startX, $startY);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($blockWidth, 6, 'FIRMA', 1, 1, 'C');

// Líneas de firma
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY($startX + 20, $startY + 25); // Más a la izquierda
$pdf->Cell(70, 6, '_______________________', 0, 0, 'L');
$pdf->Cell(20, 6, '', 0, 0); // espacio entre líneas
$pdf->Cell(70, 6, '_______________________', 0, 1, 'L');

// Textos debajo de las líneas
$pdf->SetX($startX + 20);
$pdf->Cell(70, 5, 'FIRMA DE MANTENIMIENTO', 0, 0, 'L');
$pdf->Cell(20, 5, '', 0, 0);
$pdf->Cell(70, 5, 'Vo.Bo Y FIRMA DE QUIEN RECIBE', 0, 1, 'L');

// Campos "NOMBRE:"
$pdf->SetX($startX + 20);
$pdf->Cell(70, 5, 'NOMBRE:', 0, 0, 'L');
$pdf->Cell(20, 5, '', 0, 0);
$pdf->Cell(70, 5, 'NOMBRE:', 0, 1, 'L');

// Línea para firma del jefe
$pdf->SetX($startX + 20);
$pdf->Cell(70, 10, '', 0, 1); // espacio

$pdf->SetXY($startX + 20, $startY + 55);
$pdf->Cell(70, 6, '_______________________', 0, 1, 'L');

$pdf->SetX($startX + 20);
$pdf->Cell(70, 5, 'FIRMA JEFE MANTENIMIENTO', 0, 1, 'L');

$pdf->SetX($startX + 20);
$pdf->Cell(70, 5, 'NOMBRE:', 0, 1, 'L');

// DIBUJAR MARCO ALREDEDOR DE LAS FIRMAS (opcional, si quieres el borde)
// $pdf->Rect($startX, $firmaStartY - 2, $blockWidth, $firmaHeight + 4);

$pdf->Output();