<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");
require_once("../../models/ReporteMtto.php");



class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {

        $this->SetY(15);
        $this->Image('../../public/img/logo.png', 10, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', '', 8);

        $this->SetX(147);
        $this->Cell(30, 5, 'Version:', 0, 0, 'R');
        $this->Cell(30, 5, '3', 0, 1, 'L');

        $this->SetX(147);
        $this->Cell(30, 5, 'Implementacion:', 0, 0, 'R');
        $this->Cell(30, 5, '3 Diciembre 2016', 0, 1, 'L');

        $this->SetX(147);
        $this->Cell(30, 5, 'Codigo:', 0, 0, 'R');
        $this->Cell(30, 5, 'ME-F-1', 0, 1, 'L');

        $this->SetX(147);
        $this->Cell(30, 5, 'Tipo de documento:', 0, 0, 'R');
        $this->Cell(30, 5, 'Formato', 0, 1, 'L');

        $this->SetX(147);
        $this->Cell(30, 5, utf8_decode('Página: '), 0, 0, 'R');
        $this->Cell(30, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        $this->SetFont('Arial', 'B', 15);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(60, 10, 'REPORTE MANTENIMIENTO', 0, 0, 'C');
        $this->Ln(25);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
    }

    function NbLines($w, $txt)
    {
        $cw = $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

function getMultiCellHeight($pdf, $w, $text)
{
    $lineHeight = 6;
    $nb = $pdf->NbLines($w, utf8_decode($text));
    return $nb * $lineHeight;
}

// Obtener el ID desde la URL
if (isset($_GET['id'])) {
    $repo_numb = $_GET['id'];
} else {
    die("Error: ID no especificado.");
}
// Crear instancia de la clase Operaciones y obtener datos
$reporteClass = new ReporteMtto();
$datosGenerales = $reporteClass->get_reporte_detalle($repo_numb);
$proveedores = $reporteClass->get_proveedores_reporte($repo_numb);
$items = $reporteClass->get_repuestos_por_reporte($repo_numb);

/* echo "<pre>";
var_dump($items);
echo "</pre>";
exit; */

$informe  = $datosGenerales["reporte"];
$equipo   = $datosGenerales["equipo"];
$ot       = $datosGenerales["ot"];
$solicitud    = $datosGenerales["solicitud"];
$conductor    = $datosGenerales["conductor"];


$placa = isset($equipo['vehi_placa']) ? $equipo['vehi_placa'] : 'N/A';
$Fecha = isset($informe['repo_mtto_fecha_creacion']) ? $informe['repo_mtto_fecha_creacion'] : 'N/A';
$fecha_inicio = isset($solicitud['fech_creac_soli']) ? $solicitud['fech_creac_soli'] : 'N/A';
$fecha_fin = isset($informe['repo_mtto_fecha_cierre']) ? $informe['repo_mtto_fecha_cierre'] : 'N/A';
//$obra = isset($datosGenerales['obras_nom']) ? $datosGenerales['obras_nom'] : 'N/A';
//$tipo_mtto = isset($datosGenerales['tipo_mantenimiento']) ? $datosGenerales['tipo_mantenimiento'] : 'N/A';
$operador_nombre = isset($conductor['user_nombre']) ? $conductor['user_nombre'] : 'N/A';
$operador_apellido = isset($conductor['user_apellidos']) ? $conductor['user_apellidos'] : 'N/A';
$operador = $operador_nombre . ' ' . $operador_apellido;
//$cargo = isset($datosGenerales['rol_cargo']) ? $datosGenerales['rol_cargo'] : 'N/A';
$numero_reporte = isset($informe['repo_mtto_num_reporte']) ? $informe['repo_mtto_num_reporte'] : 'N/A';
// VARIABLES TABLA REPORTE MTTO
$codigo_interno = isset($equipo['vehi_codigo']) ? $equipo['vehi_codigo'] : 'N/A';
$hora = isset($informe['repo_mtto_horas_programadas']) ? $informe['repo_mtto_horas_programadas'] : 'N/A';
$hora_ejec = isset($informe['repo_mtto_horas_ejec']) ? $informe['repo_mtto_horas_ejec'] : 'N/A';
//$estado_reporte = isset($informe['repo_estado']) ? $informe['repo_estado'] : 'N/A';
// VARIABLES TABLA DETALLE REPORTE
$diag_inicial = isset($solicitud['desc_soli']) ? $solicitud['desc_soli'] : 'N/A';
$desc_mtto = isset($ot['desc_atcv_otm']) ? $ot['desc_atcv_otm'] : 'N/A';
$estado_final = isset($informe['repo_mtto_estado_final']) ? $informe['repo_mtto_estado_final'] : 'N/A';
$total_mtto = isset($informe['repo_mtto_vlr_total']) ? $informe['repo_mtto_vlr_total'] : 'N/A';

$tipo_mtto = isset($ot['mtto_otm']) ? $ot['mtto_otm'] : 'N/A';

// VARIABLES TABLA PROVEEDORES 
$proveedor = isset($proveedores[0]['rpts_prov']) ? $proveedores[0]['rpts_prov'] : 'N/A';
$orden_compra = isset($proveedores[0]['rpts_docu']) ? $proveedores[0]['rpts_docu'] : 'N/A';
$orden_trabajo = isset($proveedores[0]['num_otm']) ? $proveedores[0]['num_otm'] : 'N/A';
//$factura_proveedor = isset($items[0]['factura']) ? $proveedores[0]['factura'] : 'N/A';
//$proveedor_cargo = isset($proveedores[0]['prov_carg']) ? $proveedores[0]['prov_carg'] : 'N/A';
//$proveedor_tipo = isset($proveedores[0]['prov_tipo']) ? $proveedores[0]['prov_tipo'] : 'N/A';


// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetX(10);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor(236, 242, 249); // Verde clarito
$pdf->SetDrawColor(61, 61, 61); // Gris oscuro para el borde

$pdf->Cell(45, 8, 'NOMBRE DEL EQUIPO:', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(51, 8, $placa, 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(30, 8, 'CODIGO:', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(20, 8, $codigo_interno, 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(10, 8, utf8_decode('N.º'), 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(34, 8, $numero_reporte, 1, 0, 'L', 0);

$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(45, 8, utf8_decode('UBICACIÓN DEL EQUIPO'), 1, 0, 'L', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(51, 8, '', 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(30, 8, 'RESPONSABLE', 1, 0, 'L', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(64, 8, $operador, 1, 0, 'L', 0);

$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(48, 8, 'TIPO DE MANTENIMIENTO', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
if ($tipo_mtto == '1') {
    $pdf->Cell(142, 8, 'Preventivo _x_ Correctivo __ Mejora __ Informe __', 1, 0, 'L', 0);
} elseif ($tipo_mtto == '2') {
    $pdf->Cell(142, 8, 'Preventivo __ Correctivo _x_ Mejora __ Informe __', 1, 0, 'L', 0);
} elseif ($tipo_mtto == '3') {
    $pdf->Cell(142, 8, 'Preventivo __ Correctivo __ Mejora _x_ Informe __', 1, 0, 'L', 0);
} elseif ($tipo_mtto == '4') {
    $pdf->Cell(142, 8, 'Preventivo __ Correctivo __ Mejora __ Informe _x_', 1, 0, 'L', 0);
} else {
    $pdf->Cell(142, 8, 'Preventivo __ Correctivo __ Mejora __ Informe __', 1, 0, 'L', 0);
}

$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 8, 'FECHAS', 1, 0, 'C', 1);

$pdf->Ln(8);
$pdf->Cell(31, 8, 'ASIGNACION', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(33, 8, date_format(new DateTime($Fecha), 'd/m/Y'), 1, 0, 'C', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(31, 8, 'INICIO', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(33, 8, date_format(new DateTime($fecha_inicio), 'd/m/Y'), 1, 0, 'C', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(31, 8, 'FINALIZACION', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(31, 8, date_format(new DateTime($fecha_fin), 'd/m/Y'), 1, 0, 'C', 0);

$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 8, 'ESTADO Y/O DIAGNOSTICO INICIAL', 1, 0, 'C', 1);

$pdf->Ln(8);
$pdf->SetFont('Helvetica', '', 9);
$pdf->MultiCell(0, 6, utf8_decode($diag_inicial), 1);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, utf8_decode('DESCRIPCIÓN DEL MANTENIMIENTO (fotos del proceso del mantenimiento)'), 1, 0, 'C', 1);
$pdf->Ln(6);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(0, 6, utf8_decode('NATURALEZA DEL MANTENIMIENTO: Mecánico ___ Eléctrico ___ Lubricación ___ Pintura ___ Otro ___'), 1, 0, 'C', 0);
$pdf->Ln(6);
$pdf->MultiCell(0, 6, utf8_decode($desc_mtto), 1);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'ESTADO FINAL (Pruebas de funcionamiento y resultado) (Fotos del trabajo terminado) ', 1, 0, 'C', 1);
$pdf->Ln(6);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(0, 6, utf8_decode($estado_final), 1, 0, 'L', 0);

$pdf->Ln(12);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'REPUESTOS E INSUMOS', 1, 0, 'C', 1);

$pdf->Ln(6);
$pdf->Cell(47, 6, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(19, 6, 'REF.', 1, 0, 'C', 1);
$pdf->Cell(15, 6, 'MARCA', 1, 0, 'C', 1);
$pdf->Cell(17, 6, 'MODELO', 1, 0, 'C', 1);
$pdf->Cell(17, 6, 'SERIAL', 1, 0, 'C', 1);
$pdf->Cell(12, 6, 'CAN.', 1, 0, 'C', 1);
$pdf->Cell(23, 6, 'COSTO', 1, 0, 'C', 1);
$pdf->Cell(20, 6, 'O.C.', 1, 0, 'C', 1);
$pdf->Cell(20, 6, 'FACT.', 1, 0, 'C', 1);

$pdf->Ln(6);
$pdf->SetFont('Helvetica', '', 8);

$w = [
    47, // nombre (con multicell)
    19, // referencia
    15, // marca
    17, // modelo
    17, // serial
    12, // cantidad
    23, // costo
    20, // O.C.
    20  // factura
];

foreach ($items  as $insumo) {

    $lineHeight = 6;

    // Cálculo de altura según MULTICELL
    $h = getMultiCellHeight($pdf, $w[0], $insumo['descripcion']);

    // Guardar posición inicial
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Imprimir NOMBRE con MultiCell
    $pdf->MultiCell($w[0], $lineHeight, utf8_decode($insumo['descripcion']), 1, 'L');

    // Volver a la posición inicial (a la derecha)
    $pdf->SetXY($x + $w[0], $y);

    // Imprimir el resto de celdas respetando la altura calculada
    $pdf->Cell($w[1], $h, utf8_decode($insumo['referencia']), 1, 0, 'L');
    $pdf->Cell($w[2], $h, 'N/A', 1, 0, 'C');
    $pdf->Cell($w[3], $h, 'N/A', 1, 0, 'C');
    $pdf->Cell($w[4], $h, 'N/A', 1, 0, 'C');
    $pdf->Cell($w[5], $h, $insumo['cantidad'], 1, 0, 'C');

    $valor_formateado = '$ ' . number_format($insumo['valor'], 0, ',', '.');
    $pdf->Cell($w[6], $h, $valor_formateado, 1, 0, 'R');

    $pdf->Cell($w[7], $h, utf8_decode($insumo['documento']), 1, 0, 'C');
    $pdf->Cell($w[8], $h, utf8_decode($insumo['factura'] ? $insumo['factura'] : 'N/A'), 1, 0, 'C');

    // Salto de línea después de la fila
    $pdf->Ln();
}
$pdf->Ln(6);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(120, 6, 'COSTO TOTAL DE REPUESTOS E INSUMOS', 1, 0, 'R', 1);
$pdf->SetFont('Helvetica', '', 10);
$total = '$ ' . number_format($total_mtto, 0, ',', '.');
$pdf->Cell(70, 6, $total, 1, 0, 'C', 0);

$pdf->Ln(12);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'ENTREGA DEL TRABAJO', 1, 0, 'C', 1);
$pdf->Ln(6);
$pdf->Cell(48, 6, 'HORAS PROGRAMADAS', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(47, 6, $hora, 1, 0, 'C', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(48, 6, 'HORAS EJECUTADAS', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(47, 6, $hora_ejec, 1, 0, 'C', 0);

$pdf->Ln(12);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'PERSONAL INVOLUCRADO EN EL MANTENIMIENTO (mano de obra)', 1, 0, 'C', 1);

$pdf->Ln(6);
// INTERNO
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'INTERNO', 1, 0, 'C', 1);
$pdf->Ln(6);
$pdf->Cell(64, 6, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(63, 6, 'CARGO', 1, 0, 'C', 1);
$pdf->Cell(63, 6, utf8_decode('N° ORDEN DE TRABAJO'), 1, 0, 'C', 1);

$pdf->Ln(6);
$pdf->SetFont('Helvetica', '', 8);
foreach ($proveedores as $proveedor) {

    $pdf->Cell(64, 6, '', 1, 0, 'C', 0);
    $pdf->Cell(63, 6, '', 1, 0, 'C', 0);
    $pdf->Cell(63, 6, '', 1, 0, 'C', 0);
    $pdf->Ln();
}

// EXTERNO
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'EXTERNO', 1, 0, 'C', 1);
$pdf->Ln(6);
$pdf->Cell(48, 6, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(47, 6, utf8_decode('N° ORDEN DE TRABAJO'), 1, 0, 'C', 1);
$pdf->Cell(48, 6, utf8_decode('N° ORDEN DE COMPRA'), 1, 0, 'C', 1);
$pdf->Cell(47, 6, utf8_decode('N° FACTURA'), 1, 0, 'C', 1);

$pdf->Ln(6);
$pdf->SetFont('Helvetica', '', 7.5);
foreach ($proveedores as $proveedor) {
    $pdf->Cell(48, 6, $proveedor['rpts_prov'], 1, 0, 'C', 0);
    $pdf->Cell(47, 6, $proveedor['num_otm'], 1, 0, 'C', 0);
    $pdf->Cell(48, 6, $proveedor['rpts_docu'], 1, 0, 'C', 0);
    $pdf->Cell(47, 6, '', 1, 0, 'C', 0);
    $pdf->Ln();
}

$pdf->Ln(6);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(120, 6, 'COSTO TOTAL DEL MANTENIMIENTO', 1, 0, 'R', 1);
$pdf->SetTextColor(255, 0, 0);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(70, 6, $total, 1, 0, 'C', 0);

// --- SECCIÓN DE FIRMAS ---
$pdf->Ln(25);

$colWidth = 90;   // ancho de cada columna

// Títulos
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell($colWidth, 6, "RECIBIO Y REVISO", 0, 0, 'C');
$pdf->Cell($colWidth, 6, utf8_decode('APROBÓ'), 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell($colWidth, 4, "(VoBo Mantenimiento)", 0, 0, 'C');
$pdf->Cell($colWidth, 4, "", 0, 1, 'C');

$pdf->Ln(5);

// -------- FIRMAS ESTÁTICAS --------
// (puedes reemplazar luego las rutas reales)
$firmaRecibe = "../../assets/firmas/recibe.png";
$firmaAprueba = "../../assets/firmas/aprueba.png";

if (file_exists($firmaRecibe)) {
    $pdf->Image($firmaRecibe, $pdf->GetX() + 15, $pdf->GetY(), 60, 20);
}
if (file_exists($firmaAprueba)) {
    $pdf->Image($firmaAprueba, $pdf->GetX() + 105, $pdf->GetY(), 60, 20);
}

$pdf->Ln(22);

// Líneas
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell($colWidth, 2, str_repeat("_", 40), 0, 0, 'C');
$pdf->Cell($colWidth, 2, str_repeat("_", 40), 0, 1, 'C');

$pdf->Ln(3);

// -------- NOMBRES ESTÁTICOS --------
// (los reemplazamos cuando ya tengas la consulta final)
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell($colWidth, 6, "Nombre: ", 0, 0, 'L');
$pdf->Cell($colWidth, 6, "Nombre: ", 0, 1, 'L');



$pdf->Output();
