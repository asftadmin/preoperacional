<?php

require_once(__DIR__ . '/../../docs/fpdf.php');
require_once(__DIR__ . '/../../config/conexion.php');
require_once(__DIR__ . '/../../models/Liquidacion.php');

// 2) Helpers de formato
function moneyCO($n) {
    return '$ ' . number_format((float)$n, 0, ',', '.');
}
function pct($n) {
    return number_format($n * 100, 2, ',', '.') . '%';
}

// Comisión por placa (mismo criterio que usas en JS)
const COMISION_DEF = 0.07;
const MAPA_COMISION = [
    'SXS660' => 0.07,
    'SXS661' => 0.07,
    'SXT576' => 0.07,
    'XMD683' => 0.07,
    'WFB907' => 0.05,
    'WFC435' => 0.05,
    'WFC575' => 0.05,
    'WFC436' => 0.05,
    'TTT-402' => 0.05,
];
function tasaPorPlaca($placa) {
    $p = strtoupper(trim((string)$placa));
    return MAPA_COMISION[$p] ?? COMISION_DEF;
}

class PDF extends FPDF {
    public $fecha = '';
    public $periodo = '';
    public $equipos = '';

    function Header() {
        // Reset de estilos/colores en cada página
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFillColor(255);
        $this->SetLineWidth(0.2);

        // Logo
        $logo = __DIR__ . '/../../public/img/logo.png';
        if (file_exists($logo)) {
            $this->Image($logo, 10, 8, 35);
        }

        // Título
        $this->SetFont('Arial', 'B', 15);
        $this->SetY(12);
        $this->Cell(0, 10, utf8_decode('LIQUIDACIONES DE EQUIPOS'), 0, 1, 'C');
        $this->Ln(1);

        // Deja el estilo por defecto para el cuerpo
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(0);
        $this->SetDrawColor(220, 220, 220);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(0);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
    }
}


$id_liquidacion = isset($_POST['liquidacion_id']) ? (int)$_POST['liquidacion_id'] : 0;;

$tiposIds = $_POST['tipos'] ?? [];

if (!is_array($tiposIds)) $tiposIds = [$tiposIds];

$liquidacion = new Liquidaciones();

$rows = $liquidacion->getComisiones($id_liquidacion, $tiposIds);

$iniRaw = $rows[0]['liq_fecha_inicio'] ?? null;
$finRaw = $rows[0]['liq_fecha_fin']    ?? null;

$periodoTexto = ($iniRaw || $finRaw) ? ($iniRaw . ' a ' . $finRaw) : '';

$tiposTexto = 'TODOS';
$unique = [];
foreach ($rows as $r) {
    if (!empty($r['tipos'])) {
        // tu SQL ya hace STRING_AGG con ' / '
        foreach (explode(' / ', $r['tipos']) as $nom) {
            $nom = trim($nom);
            if ($nom !== '') $unique[$nom] = true;
        }
    }
}
if (!empty($unique)) {
    $tiposTexto = implode(', ', array_keys($unique));
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage('L', 'Letter', '0');
$pdf->SetFont('Times', 'B', 14);

// Obtener la fecha y hora actual
// Establecer zona horaria GMT-5
date_default_timezone_set('America/Bogota');
$date = date('d-m-Y h:i A');

$pdf->SetY(40);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(55, 8, utf8_decode('Impresión: ') . $date, 1, 0, 'L');
$pdf->Cell(170, 8, utf8_decode('Periodo: ') .  $periodoTexto,  1, 0, 'L');
$pdf->Cell(35, 8, utf8_decode('Página: ') . $pdf->PageNo() . ' de {nb}', 1, 1, 'R');
$pdf->Ln(5);


$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('EQUIPOS LIQUIDADOS: ') . utf8_decode($tiposTexto), 1, 1, 'L');
$pdf->SetFont('Times', 'B', 12);
$pdf->Ln(5);

$w = [5, 78, 30, 35, 25, 27, 20, 40];

$headers = ['#', 'CONDUCTOR', 'PLACA', 'PRODUCCION', '% COMISION', 'SUBTOTAL', 'BONO', 'VALOR A PAGAR'];

$drawHeader = function () use ($pdf, $headers, $w) {
    $pdf->SetFillColor(0, 123, 167);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(255, 255, 255);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('Arial', 'B', 9);
    foreach ($headers as $i => $h) {
        $pdf->Cell($w[$i], 8, utf8_decode($h), 1, 0, 'C', true);
    }
    $pdf->Ln();
    // Restablecer estilos para el cuerpo
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(220, 220, 220);
};

$drawHeader();

// Acumuladores
$tProd = 0;
$tSub = 0;
$tBono = 0;
$tPagar = 0;
$fill = false; // zebra
$idx = 1;

foreach ($rows as $r) {
    $prod = (float)($r['subtotal_liquidado'] ?? 0);
    $tasa = tasaPorPlaca($r['placa'] ?? '');
    $sub  = round($prod * $tasa);
    $bono = 0;                     // ajusta si manejas bono
    $pago = $sub + $bono;

    $tProd += $prod;
    $tSub += $sub;
    $tBono += $bono;
    $tPagar += $pago;

    // Salto + reencabezado si se acerca al final de la página
    if ($pdf->GetY() > ($pdf->GetPageHeight() - 20)) {
        $pdf->AddPage('L', 'Letter');
        $drawHeader();
    }

    // Fila (zebra suave)
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 248 : 255, $fill ? 252 : 255);
    $pdf->Cell($w[0], 7, $idx++, 1, 0, 'C', true);
    $pdf->Cell($w[1], 7, utf8_decode($r['conductor']), 1, 0, 'L', true);
    $pdf->Cell($w[2], 7, utf8_decode($r['placa']), 1, 0, 'C', true);
    $pdf->Cell($w[3], 7, moneyCO($prod), 1, 0, 'R', true);
    $pdf->Cell($w[4], 7, pct($tasa), 1, 0, 'R', true);
    $pdf->Cell($w[5], 7, moneyCO($sub), 1, 0, 'R', true);
    $pdf->Cell($w[6], 7, moneyCO($bono), 1, 0, 'R', true);
    $pdf->Cell($w[7], 7, moneyCO($pago), 1, 0, 'R', true);
    $pdf->Ln();

    $fill = !$fill;
}

// Fila TOTAL (estilo destacado)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(0, 68, 119);
$pdf->SetTextColor(255);
$pdf->Cell($w[0] + $w[1] + $w[2], 8, 'TOTAL', 1, 0, 'R', true); // colspan 3
$pdf->Cell($w[3], 8, moneyCO($tProd), 1, 0, 'R', true);
$pdf->Cell($w[4], 8, '', 1, 0, 'R', true);
$pdf->Cell($w[5], 8, moneyCO($tSub), 1, 0, 'R', true);
$pdf->Cell($w[6], 8, moneyCO($tBono), 1, 0, 'R', true);
$pdf->Cell($w[7], 8, moneyCO($tPagar), 1, 0, 'R', true);



// Construir agregación: suma de VALOR A PAGAR por conductor
$resumen = []; // key = conductor (UPPER), value = total a pagar
foreach ($rows as $r) {
    $prod = (float)($r['subtotal_liquidado'] ?? 0);
    $tasa = tasaPorPlaca($r['placa'] ?? '');
    $pago = round($prod * $tasa); // mismo cálculo que en la tabla principal
    $con  = strtoupper(trim((string)($r['conductor'] ?? '')));
    if ($con === '') {
        $con = 'SIN NOMBRE';
    }
    if (!isset($resumen[$con])) $resumen[$con] = 0;
    $resumen[$con] += $pago;
}

$pdf->SetTextColor(0);
$pdf->SetDrawColor(220, 220, 220);
$pdf->AddPage('L', 'Letter', '0'); // nueva página; si lo quieres debajo, comenta esta línea
$pdf->SetY(40);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(55, 8, utf8_decode('Impresión: ') . $date, 1, 0, 'L');
$pdf->Cell(170, 8, utf8_decode('Periodo: ') .  $periodoTexto,  1, 0, 'L');
$pdf->Cell(35, 8, utf8_decode('Página: ') . $pdf->PageNo() . ' de {nb}', 1, 1, 'R');
$pdf->Ln(5);
$pdf->SetY(55);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('EQUIPOS LIQUIDADOS: ') . utf8_decode($tiposTexto), 1, 1, 'L');
$pdf->SetFont('Times', 'B', 12);
$pdf->Ln(5);

// Anchuras (Letter horizontal: ancho útil ~259mm)
$wR = [12, 190, 58]; // #, CONDUCTOR, VALOR A PAGAR
$hdrR = ['#', 'CONDUCTOR', 'VALOR A PAGAR'];

// Encabezado
$pdf->SetFillColor(0, 123, 167);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('Arial', 'B', 10);
foreach ($hdrR as $i => $h) {
    $pdf->Cell($wR[$i], 8, utf8_decode($h), 1, 0, 'C', true);
}
$pdf->Ln();

// Cuerpo
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(220, 220, 220);
$fill = false;
$idx  = 1;
$totalResumen = 0;

foreach ($resumen as $conductor => $totalPagar) {
    // salto de página si hace falta
    if ($pdf->GetY() > ($pdf->GetPageHeight() - 20)) {
        $pdf->AddPage('L', 'Letter');
        // reimprimir header
        $pdf->SetFillColor(0, 123, 167);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('Arial', 'B', 10);
        foreach ($hdrR as $i => $h) {
            $pdf->Cell($wR[$i], 8, utf8_decode($h), 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(220, 220, 220);
    }

    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 248 : 255, $fill ? 252 : 255);
    $pdf->Cell($wR[0], 7, $idx++,            1, 0, 'C', true);
    $pdf->Cell($wR[1], 7, utf8_decode($conductor), 1, 0, 'L', true);
    $pdf->Cell($wR[2], 7, moneyCO($totalPagar),    1, 0, 'R', true);
    $pdf->Ln();

    $totalResumen += $totalPagar;
    $fill = !$fill;
}

// Fila TOTAL del resumen
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(0, 68, 119);
$pdf->SetTextColor(255);
$pdf->Cell($wR[0] + $wR[1], 8, 'TOTAL',          1, 0, 'R', true);
$pdf->Cell($wR[2],        8, moneyCO($totalResumen), 1, 0, 'R', true);

/**
 * Firma / responsable
 * @param FPDF   $pdf
 * @param string $nombre   Ej: 'CARLOS ANDRES DURAN'
 * @param string $cargo    Ej: 'Producción'
 * @param string $label    Ej: 'ELABORO'
 * @param float  $x        Posición X inicial
 * @param float  $ancho    Ancho del bloque (para la línea)
 * @param float|null $y    Si se pasa, posiciona en Y; si no, usa la actual
 */
function renderFirma(
    FPDF $pdf,
    string $nombre,
    string $cargo,
    string $label = 'ELABORO',
    float $x = 12,
    float $ancho = 180,
    ?float $y = null
): void {
    if ($y !== null) $pdf->SetY($y);
    $pdf->SetX($x);

    // Nombre (negrita)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ancho, 6, utf8_decode($nombre), 0, 1, 'L');

    // Línea bajo el nombre
    $y1 = $pdf->GetY();
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(0.3);
    $pdf->Line($x, $y1, $x + $ancho, $y1);
    $pdf->Ln(3);

    // Cargo (itálica, prefijo “Cargo:”)
    $pdf->SetX($x);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell($ancho, 5, utf8_decode('Cargo: ' . $cargo), 0, 1, 'L');
    $pdf->Ln(3);

    // Etiqueta “ELABORO” (negrita)
    $pdf->SetX($x);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ancho, 6, utf8_decode($label), 0, 1, 'L');
}

// Coloca el bloque cerca del pie:
$pdf->SetY(-45); // ~45 mm desde el borde inferior
$pdf->SetTextColor(0);
renderFirma($pdf, 'CARLOS ANDRES DURAN', 'Producción', 'ELABORO', 12, 180);


// 7) Salida
if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/pdf');

$pdf->Output('I', 'reporte_comisiones.pdf');

exit;
