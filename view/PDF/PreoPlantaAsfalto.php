<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");

class Operaciones extends Conectar {
    public function listar_preguntas($pre_formulario) {
        $conectar = new Conectar(); // Asumiendo que la clase Conexion maneja la conexión
        $conexion = $conectar->conexion();
        $sql = "WITH PreoperacionalAnterior AS (
    SELECT
        pre_vehiculo,
        MAX(pre_fecha_crea_form) AS pre_fecha_anterior
    FROM preoperacional
    WHERE pre_fecha_crea_form < CURRENT_DATE
    GROUP BY pre_vehiculo
)
SELECT
    operaciones.oper_nombre,
    preoperacional.pre_fecha_crea_form,
    TO_CHAR(preoperacional.pre_fecha_crea_form, 'FMDay') AS dia_semana,
    suboperaciones.suboper_id,
    suboperaciones.suboper_nombre,
    preoperacional.pre_repuesta,
    vehiculos.vehi_soat_vence,
    vehiculos.vehi_tecnicomecanica,
    vehiculos.vehi_tarjeta_propiedad,
    vehiculos.vehi_poliza_vence,
    conductores.cond_vencimiento_licencia,
    vehiculos.vehi_poliza,
    usuarios.user_cedula,
    conductores.cond_categoria_licencia,
    CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,
    vehiculos.vehi_placa,
    CASE
        WHEN preoperacional.pre_estado = 0 THEN 'No aprobado'
        WHEN preoperacional.pre_estado = 1 THEN 'Aprobado'
        ELSE NULL
    END AS pre_estado,
    tipo_vehiculo.tipo_nombre,
    tipo_vehiculo.tipo_id,
    preoperacional.pre_observaciones,
    preoperacional.pre_kilometraje_inicial,
    preoperacional_anterior.pre_kilometraje_inicial AS pre_kilometraje_inicial_anterior,
    vehiculos.vehi_marca,
    preoperacional.pre_fecha_revision,
    vehiculos.vehi_modelo
FROM preoperacional
INNER JOIN suboperaciones ON preoperacional.pre_suboper = suboperaciones.suboper_id
INNER JOIN operaciones ON suboperaciones.suboper_oper = operaciones.oper_id
INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
INNER JOIN usuarios ON usuarios.user_id = preoperacional.pre_user
LEFT JOIN conductores ON conductores.conductor_usuario = usuarios.user_id
LEFT JOIN PreoperacionalAnterior ON preoperacional.pre_vehiculo = PreoperacionalAnterior.pre_vehiculo
LEFT JOIN preoperacional preoperacional_anterior ON preoperacional_anterior.pre_vehiculo = PreoperacionalAnterior.pre_vehiculo
    AND preoperacional_anterior.pre_fecha_crea_form = PreoperacionalAnterior.pre_fecha_anterior
WHERE preoperacional.pre_formulario = ?
GROUP BY suboperaciones.suboper_nombre, preoperacional.pre_repuesta, operaciones.oper_nombre,suboperaciones.suboper_id,
         usuarios.user_cedula, conductor_nombre_completo, vehiculos.vehi_placa, tipo_vehiculo.tipo_nombre, 
         tipo_vehiculo.tipo_id, preoperacional.pre_observaciones, preoperacional.pre_kilometraje_inicial,
         preoperacional_anterior.pre_kilometraje_inicial, preoperacional.pre_fecha_crea_form, 
         vehiculos.vehi_marca, vehiculos.vehi_modelo, vehiculos.vehi_soat_vence, vehiculos.vehi_tecnicomecanica,
         vehiculos.vehi_tarjeta_propiedad, vehiculos.vehi_poliza, vehiculos.vehi_poliza_vence, 
         conductores.cond_categoria_licencia, conductores.cond_vencimiento_licencia,preoperacional.pre_estado,preoperacional.pre_fecha_revision    
        ORDER BY suboperaciones.suboper_id;";
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

        $this->SetX(150);
        $this->Cell(30, 5, 'Version:', 0, 0, 'R');
        $this->Cell(30, 5, '3', 0, 1, 'L');

        $this->SetX(150);
        $this->Cell(30, 5, 'Implementacion:', 0, 0, 'R');
        $this->Cell(30, 5, '22 Junio 2020', 0, 1, 'L');

        $this->SetX(150);
        $this->Cell(30, 5, 'Codigo:', 0, 0, 'R');
        $this->Cell(30, 5, 'ME-F-4', 0, 1, 'L');

        $this->SetX(150);
        $this->Cell(30, 5, utf8_decode('Página: '), 0, 0, 'R');
        $this->Cell(30, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->SetY(20);
        $this->SetX(70);
        $this->Cell(60, 10, ' INSPECCION PREOPERACIONAL DIARIA PLANTA ELVA 60', 0, 0, 'C');
        $this->Ln(25);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
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
$cedula = isset($operaciones[0]['user_cedula']) ? $operaciones[0]['user_cedula'] : 'N/A';
$Fecha = isset($operaciones[0]['pre_fecha_crea_form']) ? $operaciones[0]['pre_fecha_crea_form'] : 'N/A';
$marca = isset($operaciones[0]['vehi_marca']) ? $operaciones[0]['vehi_marca'] : 'N/A';
$modelo = isset($operaciones[0]['vehi_modelo']) ? $operaciones[0]['vehi_modelo'] : 'N/A';
$observaciones = isset($operaciones[0]['pre_observaciones']) ? $operaciones[0]['pre_observaciones'] : 'N/A';
$operario = isset($operaciones[0]['conductor_nombre_completo']) ? $operaciones[0]['conductor_nombre_completo'] : 'N/A';
$estado = isset($operaciones[0]['pre_estado']) ? $operaciones[0]['pre_estado'] : 'N/A';
$Fecha_revision = isset($operaciones[0]['pre_fecha_revision']) ? $operaciones[0]['pre_fecha_revision'] : 'N/A';
$diaSemana = isset($operaciones[0]['dia_semana']) ? $operaciones[0]['dia_semana'] : 'N/A';
$englishToSpanish = [
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado',
    'Sunday' => 'Domingo'
];

$diaSemanaEnIngles = $diaSemana; 
$diaSemanaEnEspanol = $englishToSpanish[$diaSemanaEnIngles];

// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 30, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetX(10);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(115, 5, 'Inspeccionado por:' . '   ' . $operario.' - '.$cedula, 1, 0, 'L', 0);
$pdf->Cell(75, 5, 'Reportado a/cargo:' . '   ' . 'Mantenimiento', 1, 0, 'L', 0);
$pdf->Ln(5);
$pdf->Cell(95, 5, 'Fecha Inspeccion:' . '   ' . date_format(new DateTime($Fecha), 'd/m/Y'), 1, 0, 'L', 0);
$pdf->Cell(95, 5, 'Placa: ' . $placa, 1, 0, 'L', 0);
$pdf->Ln(5);
$pdf->Cell(95, 5, 'Modelo' . '   ' . $modelo, 1, 0, 'L', 0);
$pdf->Cell(95, 5, 'Marca:' . '   ' . $marca, 1, 0, 'L', 0);
$pdf->Ln(5);
$pdf->Cell(0, 10, 'B= Bueno (SI) M= Malo (NO) N/A= No Aplica', 0, 0, 'C', 0);
$pdf->Ln(10);

$pdf->SetFillColor(233, 229, 235);
$pdf->SetDrawColor(61, 61, 61);

$pdf->Cell(140, 20, 'Concepto', 1, 0, 'C', 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 10,utf8_decode($diaSemanaEnEspanol).'  '. date_format(new DateTime($Fecha), 'd/m/Y'), 1, 1, 'C', 1);


$pdf->SetX(150);
for ($i = 0; $i < 1; $i++) {
    $pdf->Cell(17, 10, 'B', 1, 0, 'C', 1);
    $pdf->Cell(17, 10, 'M', 1, 0, 'C', 1);
    $pdf->Cell(16, 10, 'N/A', 1, 0, 'C', 1);
}
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 9);
$pdf->SetY(90);

$contador = 0; // Contador de suboperaciones
$texto = "";   // Variable para almacenar el texto a mostrar

foreach ($operaciones as $index => $operacion) {

    
    if ($contador == 70) {
        if ($pdf->GetY() > 250) { // Ajusta el valor según el espacio disponible en la página
            $pdf->AddPage(); // Salto de página si no hay suficiente espacio
        }
        $texto = 'SIN FIN DEL FILTRO MANGAS';
    }
    
    // Imprimir el texto basado en el contador
    if ($contador == 0) {
        $texto = 'CABINA';
    }
    elseif ($contador == 8) {
        $texto = 'BASCULA PARA AGREGADOS';
    }
    elseif ($contador == 12) {
        $texto = 'SISTEMA DOSIFICADO DE ASFALTO';
    }
    elseif ($contador == 16) {
        $texto = 'COMPRESOR';
    }
    elseif ($contador == 27) {
        $texto = 'TAMBOR SECADOR';
    }
    elseif ($contador == 32) {
        $texto = 'QUEMADOR';
    }
    elseif ($contador == 39) {
        $texto = 'TURBO';
    }
    elseif ($contador == 43) {
        $texto = 'ELEVADOR';
    }
    elseif ($contador == 48) {
        $texto = 'MEZCLADOR';
    }
    elseif ($contador == 54) {
        $texto = 'UNIDAD HIDRAULICA';
    }
    elseif ($contador == 60) {
        $texto = 'FILTRO DE MANGAS';
    }
    elseif ($contador == 70) {
        $texto = 'SIN FIN DEL FILTRO MANGAS';
    }
    elseif ($contador == 73) {
        $texto = 'SIN FIN SEPARADOR ESTATICO';
    }
    elseif ($contador == 76) {
        $texto = 'SIN FIN INCLINADO DE FINOS';
    }
    elseif ($contador == 80) {
        $texto = 'TOLVAS';
    }
    elseif ($contador == 86) {
        $texto = 'BANDAS TRANSPORTADORAS';
    }
    elseif ($contador == 93) {
        $texto = 'ESTRUCTURA GENERAL, CHIMENEA';
    }
    elseif ($contador == 97) {
        $texto = 'FECHA CALIBRACION EQUIPOS DE PESAJE Y MEDIDA';
    }

    // Si el texto ha cambiado, lo imprimimos
    if ($texto != "") {
        $pdf->Cell(140, 5, $texto, 1, 0, 'C', 1);
        $pdf->Cell(17, 5, 'B', 1, 0, 'C', 1);
        $pdf->Cell(17, 5, 'M', 1, 0, 'C', 1);
        $pdf->Cell(16, 5, 'N/A', 1, 1, 'C', 1);
        $texto = ""; // Reseteamos el texto para no imprimirlo de nuevo
    }

    // Imprimir la suboperación
    $pdf->Cell(140, 5, utf8_decode($operacion['suboper_nombre']), 1, 0, 'L', 0);
    $pdf->SetX(150);

    if ($operacion['pre_repuesta'] == 'B') {
        $pdf->Cell(17, 5, 'X', 1, 0, 'C');
        $pdf->Cell(17, 5, '', 1, 0, 'C');
        $pdf->Cell(16, 5, '', 1, 0, 'C');
    } elseif ($operacion['pre_repuesta'] == 'M') {
        $pdf->Cell(17, 5, '', 1, 0, 'C');
        $pdf->Cell(17, 5, 'X', 1, 0, 'C');
        $pdf->Cell(16, 5, '', 1, 0, 'C');
    } elseif ($operacion['pre_repuesta'] == 'NA') {
        $pdf->Cell(17, 5, '', 1, 0, 'C');
        $pdf->Cell(17, 5, '', 1, 0, 'C');
        $pdf->Cell(16, 5, 'X', 1, 0, 'C');
    } else {
        $pdf->Cell(17, 5, '', 1, 0, 'C');
        $pdf->Cell(17, 5, '', 1, 0, 'C');
        $pdf->Cell(16, 5, '', 1, 0, 'C');
    }
    
    $pdf->Ln();
    
    $contador++; // Aumenta el contador de suboperaciones
}

$pdf->Ln(10);
$pdf->Cell(60, 5, 'Firma Operador:', 0, 0, 'R');
for ($i = 0; $i < 1; $i++) {
    $pdf->Cell(0, 10, '', 1, 0, 'C');
}
$pdf->Ln();
$pdf->Cell(60, 5, 'Firma Suoervisor:', 0, 0, 'R');
for ($i = 0; $i < 1; $i++) {
    $pdf->Cell(0, 10, '', 1, 0, 'C');
}
$pdf->Ln(10);
$pdf->Cell(0, 10,'Observaciones', 0, 0, 'C');
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
