<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");

class Operaciones extends Conectar {
    public function listar_preguntas($pre_formulario) {
        $conectar = new Conectar(); // Asumiendo que la clase Conexion maneja la conexión
        $conexion = $conectar->conexion();
        $sql = "SELECT
            operaciones.oper_nombre,
			preoperacional.pre_fecha_crea_form,
            suboperaciones.suboper_nombre,
            preoperacional.pre_repuesta,
            preoperacional.pre_fecha_revision,
            usuarios.user_cedula,
            CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,
            vehiculos.vehi_placa,
            tipo_vehiculo.tipo_nombre,
            tipo_vehiculo.tipo_id,
            preoperacional.pre_observaciones,
            preoperacional.pre_kilometraje_inicial,
            CASE
                    WHEN pre_estado = 0 THEN 'No aprobado'
                    WHEN pre_estado = 1 THEN 'Aprobado'
                    ELSE NULL
                END AS pre_estado,
			vehiculos.vehi_marca,
			vehiculos.vehi_modelo
        FROM preoperacional
        INNER JOIN suboperaciones ON preoperacional.pre_suboper = suboperaciones.suboper_id
        INNER JOIN operaciones ON suboperaciones.suboper_oper = operaciones.oper_id
        INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
        INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        INNER JOIN usuarios ON usuarios.user_id = preoperacional.pre_user
        WHERE preoperacional.pre_formulario = ?
        GROUP BY suboperaciones.suboper_nombre, preoperacional.pre_repuesta, operaciones.oper_nombre, 
        usuarios.user_cedula,  conductor_nombre_completo, vehiculos.vehi_placa, tipo_vehiculo.tipo_nombre,tipo_vehiculo.tipo_id,
        preoperacional.pre_observaciones, preoperacional.pre_kilometraje_inicial,preoperacional.pre_fecha_crea_form,vehiculos.vehi_marca,
		vehiculos.vehi_modelo,preoperacional.pre_estado,preoperacional.pre_fecha_revision;";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $pre_formulario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class PDF extends FPDF {
    // Cabecera de página
    function Header() {

        $this->SetY(15);
        $this->Image('../../public/img/logo.png', 10, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', '', 8);

        $this->SetX(140);
        $this->Cell(30, 5, 'Version:', 0, 0, 'R');
        $this->Cell(30, 5, '2', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, 'Implementacion:', 0, 0, 'R');
        $this->Cell(30, 5, 'Junio 29 2020', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, 'Codigo:', 0, 0, 'R');
        $this->Cell(30, 5, 'ME-F-29', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, utf8_decode('Página: '), 0, 0, 'R');
        $this->Cell(30, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        $this->SetFont('Arial', 'B', 12);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(60, 10, 'Formato Preoperacional Motoniveladora', 0, 0, 'C');
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
    $pre_formulario = $_GET['ID'];
} else {
    die("Error: ID no especificado.");
}
// Crear instancia de la clase Operaciones y obtener datos
$operacionesClass = new Operaciones();
$operaciones = $operacionesClass->listar_preguntas($pre_formulario);

$placa = isset($operaciones[0]['vehi_placa']) ? $operaciones[0]['vehi_placa'] : 'N/A';
$Fecha = isset($operaciones[0]['pre_fecha_crea_form']) ? $operaciones[0]['pre_fecha_crea_form'] : 'N/A';
$horometraje = isset($operaciones[0]['pre_kilometraje_inicial']) ? $operaciones[0]['pre_kilometraje_inicial'] : 'N/A';
$marca = isset($operaciones[0]['vehi_marca']) ? $operaciones[0]['vehi_marca'] : 'N/A';
$modelo = isset($operaciones[0]['vehi_modelo']) ? $operaciones[0]['vehi_modelo'] : 'N/A';
$observaciones = isset($operaciones[0]['pre_observaciones']) ? $operaciones[0]['pre_observaciones'] : 'N/A';
$operario = isset($operaciones[0]['conductor_nombre_completo']) ? $operaciones[0]['conductor_nombre_completo'] : 'N/A';
$estado = isset($operaciones[0]['pre_estado']) ? $operaciones[0]['pre_estado'] : 'N/A';
$Fecha_revision = isset($operaciones[0]['pre_fecha_revision']) ? $operaciones[0]['pre_fecha_revision'] : 'N/A';


// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetX(10);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(105, 10, 'Inspeccionado por:'.'   '.$operario, 1, 0, 'L', 0);
$pdf->Cell(85, 10, 'Reportado a/cargo:'.'   '.'Mantenimiento', 1, 0, 'L', 0);
$pdf->Ln(10);
$pdf->Cell(140, 10, 'Fecha Inspeccion:'.'   '.date_format(new DateTime($Fecha), 'd/m/Y'), 1, 0, 'L', 0);
$pdf->Cell(50, 10, 'Placa: ' . $placa, 1, 0, 'L', 0);

$pdf->Ln(10);
$pdf->Cell(63, 10, 'Modelo'.'   '. $modelo, 1, 0, 'L', 0);
$pdf->Cell(63, 10, 'Horometro:'.'   '. $horometraje, 1, 0, 'L', 0);
$pdf->Cell(64, 10, 'Marca:'.'   '. $marca, 1, 0, 'L', 0);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'B= Bueno (SI) M= Malo (NO) N/A= No Aplica', 0, 0, 'C', 0);
$pdf->Ln(10);

$pdf->SetFillColor(233, 229, 235);
$pdf->SetDrawColor(61, 61, 61);

$pdf->Cell(120, 20, 'Item', 1, 0, 'C', 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(70, 10, date_format(new DateTime($Fecha), 'd/m/Y'), 1, 1, 'C', 1);


$pdf->SetX(130);
for ($i = 0; $i < 1; $i++) {
    $pdf->Cell(24, 10, 'B', 1, 0, 'C', 1);
    $pdf->Cell(23, 10, 'M', 1, 0, 'C', 1);
    $pdf->Cell(23, 10, 'N/A', 1, 0, 'C', 1);
}

$pdf->Ln(10);
$pdf->SetFont('Arial', '', 7);

foreach ($operaciones as $operacion) {
    $pdf->Cell(120, 5, utf8_decode($operacion['suboper_nombre']), 1, 0, 'L', 0);
    $pdf->SetX(130);
    
    if ($operacion['pre_repuesta'] == 'B') {
        $pdf->Cell(24, 5, 'X', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
    } elseif ($operacion['pre_repuesta'] == 'M') {
        $pdf->Cell(24, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, 'X', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
    } elseif ($operacion['pre_repuesta'] == 'NA') {
        $pdf->Cell(24, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, 'X', 1, 0, 'C');
    } else {
        $pdf->Cell(24, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
    }

    $pdf->Ln();
}

$pdf->SetFont('Arial', '', 8);
$pdf->Ln(5);
$pdf->Cell(60, 10, 'Estado Preoperacional:', 0, 0, 'R');
for ($i = 0; $i < 1; $i++) {
    $pdf->Cell(0, 10, $estado, 1, 0, 'C');
}
$pdf->Ln();
$pdf->Cell(60, 10, 'Fecha Revision:', 0, 0, 'R');
for ($i = 0; $i < 1; $i++) {
    $pdf->Cell(0, 10, $Fecha_revision, 1, 0, 'C');
}
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Observaciones', 0, 0, 'C');
$pdf->Ln(10);
$days = [date_format(new DateTime($Fecha), 'd/m/Y')];
foreach ($days as $day) {
    // Definir dimensiones
    $anchoPrimera = 30;
    $anchoSegunda = 160;
    $altura = 4; // Altura de cada línea en ambas celdas

    // Guardar la posición actual
    $xPosInicial = $pdf->GetX();
    $yPosInicial = $pdf->GetY();

    // Calcular la cantidad de líneas necesarias para la segunda MultiCell
    if (!empty(trim($observaciones))) {
        $lineas = $pdf->GetStringWidth($observaciones) / $anchoSegunda;
        $lineas = ceil($lineas);
    } else {
        // Si no hay observaciones, forzar una sola línea
        $lineas = 1;
    }
    $alturaTotal = $lineas * $altura; // Altura total necesaria para la segunda MultiCell

    // Imprimir la primera MultiCell con la altura total calculada
    $pdf->MultiCell($anchoPrimera, $alturaTotal, $day . ':', 1, 'J');

    // Volver a la posición original para que la segunda MultiCell quede al lado
    $pdf->SetXY($xPosInicial + $anchoPrimera, $yPosInicial);

    // Imprimir la segunda MultiCell
    if (!empty(trim($observaciones))) {
        $pdf->MultiCell($anchoSegunda, $altura, utf8_decode($observaciones), 1, 'J');
    } else {
        // Imprimir sin borde o con borde ligero si no hay observaciones
        $pdf->MultiCell($anchoSegunda, $altura, ' ', 1, 'L');
    }
}

$pdf->Output();
?>
