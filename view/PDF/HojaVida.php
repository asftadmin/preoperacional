<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");
require_once("../../models/HojaVida.php");

class PDF extends FPDF {

    function Header() {
        $pageW = $this->GetPageWidth();  // 279mm landscape

        /* logo */
        $this->SetXY(10, 8);
        $this->Image('../../public/img/logo.png', 10, 8, 35);

        /* datos lado derecho — ancla desde el borde derecho */
        $this->SetFont('Arial', '', 8);

        $this->SetXY($pageW - 65, 8);
        $this->Cell(30, 5, 'Version:',            0, 0, 'R');
        $this->Cell(35, 5, '1',                   0, 1, 'L');

        $this->SetX($pageW - 65);
        $this->Cell(30, 5, utf8_decode('Código:'), 0, 0, 'R');
        $this->Cell(35, 5, 'ME-HV-1',             0, 1, 'L');

        $this->SetX($pageW - 65);
        $this->Cell(30, 5, utf8_decode('Página:'), 0, 0, 'R');
        $this->Cell(35, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        /* título centrado entre logo y datos */
        $this->SetFont('Arial', 'B', 14);
        $this->SetXY(50, 10);
        $this->Cell($pageW - 120, 10, 'HOJA DE VIDA - MANTENIMIENTO', 0, 0, 'C');

        $this->Ln(30);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetTextColor(0, 0, 0);   // <-- negro
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras', 'T', 0, 'C');
    }
}

/* ── parámetros ── */
$id_vehiculo = $_GET['id_vehiculo'] ?? null;
$fechaIni    = $_GET['fechaIni']    ?? '';
$fechaFin    = $_GET['fechaFin']    ?? '';

if (!$id_vehiculo || $id_vehiculo === 'undefined') {
    die("Error: vehículo no especificado.");
}

/* ── datos ── */
$hojaVida = new HojaVida();
$data     = $hojaVida->get_hoja_vida($id_vehiculo, $fechaIni, $fechaFin);

if (!$data) die("Error: sin datos.");

$equipo   = $data['equipo'];
$reportes = $data['reportes'];

$placa  = $equipo['vehi_placa']   ?? 'N/A';
$tipo   = $equipo['tipo_nombre']  ?? 'N/A';
$rango  = ($fechaIni && $fechaFin) ? $fechaIni . ' / ' . $fechaFin : 'Todos los registros';

/* ── PDF ── */
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetDrawColor(61, 61, 61);
$pdf->SetFillColor(236, 242, 249);

/* ── encabezado equipo ── */
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(40, 8, 'TIPO DE EQUIPO:',  1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(70, 8, utf8_decode($tipo), 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(25, 8, 'PLACA:',           1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(40, 8, $placa,             1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(40, 8, 'RANGO FECHAS:',    1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(45, 8, $rango,             1, 0, 'L', 0);

$pdf->Ln(12);

/* ── historial ── */
$total_general = 0;

foreach ($reportes as $rep) {

    /* cabecera mantenimiento */
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetFillColor(23, 162, 184);
    $pdf->SetTextColor(255, 255, 255);

    $fecha     = isset($rep['fech_creac_soli']) ? date('d/m/Y', strtotime($rep['fech_creac_soli'])) : 'N/A';
    $kmAnt     = number_format($rep['lectura_anterior'] ?? 0, 0, ',', '.');
    $kmAct     = number_format($rep['lect_soli']        ?? 0, 0, ',', '.');
    $tipo_mtto = $rep['tipo_mantenimiento'] ?? 'N/A';
    $num_otm   = $rep['num_otm']            ?? 'N/A';
    $obra      = utf8_decode($rep['obras_nom'] ?? 'N/A');

    $pdf->Cell(
        0,
        7,
        'OTM: ' . $num_otm .
            '   Fecha: ' . $fecha .
            '   Tipo: ' . utf8_decode($tipo_mtto) .
            '   Km Anterior: ' . $kmAnt .
            '   Km Actual: '   . $kmAct .
            '   Obra: ' . $obra,
        1,
        0,
        'L',
        1
    );

    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(7);

    /* tabla repuestos */
    $repuestos = $rep['actividades'] ?? [];

    if (!empty($repuestos)) {
        /* cabecera tabla — total = 259mm */
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetFillColor(236, 242, 249);
        $pdf->Cell(10,  6, utf8_decode('N°'), 1, 0, 'C', 1);  // 10
        $pdf->Cell(30,  6, 'Documento',        1, 0, 'C', 1);  // 30
        $pdf->Cell(99,  6, 'Referencia',       1, 0, 'C', 1);  // 99
        $pdf->Cell(20,  6, 'Cantidad',         1, 0, 'C', 1);  // 20
        $pdf->Cell(35,  6, 'Vlr. Neto',        1, 0, 'C', 1);  // 35
        $pdf->Cell(65,  6, 'Proveedor',        1, 0, 'C', 1);  // 65
        $pdf->Ln(6);

        /* filas */
        /* filas — mismos anchos */
        $pdf->SetFont('Helvetica', '', 8);
        foreach ($repuestos as $i => $rpt) {
            $pdf->Cell(10,  6, ($i + 1),                                                        1, 0, 'C', 0);
            $pdf->Cell(30,  6, utf8_decode($rpt['rpts_docu'] ?? ''),                            1, 0, 'C', 0);
            $pdf->Cell(99,  6, utf8_decode($rpt['rpts_refr'] ?? ''),                            1, 0, 'L', 0);
            $pdf->Cell(20,  6, $rpt['rpts_cant'] ?? '',                                         1, 0, 'C', 0);
            $pdf->Cell(35,  6, '$ ' . number_format($rpt['rpts_vlr_neto'] ?? 0, 0, ',', '.'),   1, 0, 'R', 0);
            $pdf->Cell(65,  6, utf8_decode($rpt['rpts_prov'] ?? ''),                            1, 0, 'L', 0);
            $pdf->Ln(6);
        }

        /* total OTM */
        $total_otm = floatval($rep['total_valor_repuestos'] ?? 0);
        $total_general += $total_otm;

        /* total OTM */
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetFillColor(236, 242, 249);
        $pdf->Cell(159, 6, 'TOTAL REPUESTOS OTM',                                  1, 0, 'R', 1);  // 10+30+99+20 = 159
        $pdf->Cell(100, 6, '$ ' . number_format($total_otm, 0, ',', '.'),          1, 0, 'R', 0);  // 35+65 = 100
        $pdf->Ln(6);
    } else {
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->Cell(0, 6, 'Sin repuestos registrados.', 1, 0, 'C', 0);
        $pdf->Ln(6);
    }

    $pdf->Ln(4);
}

/* ── total general ── */
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor(236, 242, 249);
$pdf->Cell(193, 8, 'COSTO TOTAL MANTENIMIENTOS',                        1, 0, 'R', 1);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(66,  8, '$ ' . number_format($total_general, 0, ',', '.'),  1, 0, 'C', 0);

$pdf->Output();
