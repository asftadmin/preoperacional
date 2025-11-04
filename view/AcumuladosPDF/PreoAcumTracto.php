<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");

class Operaciones extends Conectar
{
    public function listar_preguntas($fecha_inicio, $fecha_fin, $vehi_id)
    {
        $conectar = new Conectar();
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
    preoperacional.pre_formulario,
    operaciones.oper_nombre,
    preoperacional.pre_fecha_crea_form,
    TO_CHAR(preoperacional.pre_fecha_crea_form, 'FMDay') AS dia_semana,
    suboperaciones.suboper_nombre,
    preoperacional.pre_repuesta,
    preoperacional.pre_fecha_revision,
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
WHERE preoperacional.pre_fecha_crea_form BETWEEN ? AND ? AND vehi_id=?
GROUP BY suboperaciones.suboper_nombre, preoperacional.pre_repuesta, operaciones.oper_nombre, 
         usuarios.user_cedula, conductor_nombre_completo, vehiculos.vehi_placa, tipo_vehiculo.tipo_nombre, 
         tipo_vehiculo.tipo_id, preoperacional.pre_observaciones, preoperacional.pre_kilometraje_inicial,
         preoperacional_anterior.pre_kilometraje_inicial, preoperacional.pre_fecha_crea_form, 
         vehiculos.vehi_marca, vehiculos.vehi_modelo, vehiculos.vehi_soat_vence, vehiculos.vehi_tecnicomecanica,
         vehiculos.vehi_tarjeta_propiedad, vehiculos.vehi_poliza, vehiculos.vehi_poliza_vence, preoperacional.pre_formulario,
         conductores.cond_categoria_licencia, conductores.cond_vencimiento_licencia,preoperacional.pre_estado,preoperacional.pre_fecha_revision    
        ORDER BY
            CASE 
                WHEN operaciones.oper_nombre like 'Niveles y%' THEN 1
                WHEN operaciones.oper_nombre like 'Sistema El%' THEN 2
                WHEN operaciones.oper_nombre = 'Sistema de Frenos' THEN 3
                WHEN operaciones.oper_nombre = 'Sistema Mecánico' THEN 4
                WHEN operaciones.oper_nombre = 'Sistema de Rodamiento y Suspensión' THEN 5
                WHEN operaciones.oper_nombre = 'Trailer' THEN 6
                WHEN operaciones.oper_nombre like 'Quinta %' THEN 7
                WHEN operaciones.oper_nombre like 'Seguridad y%' THEN 8

                ELSE 9
            END,
            operaciones.oper_nombre;";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin, $vehi_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {

        $this->SetY(15);
        $this->Image('../../public/img/logo.png', 10, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', '', 8);

        $this->SetX(150);
        $this->Cell(30, 5, 'Version:', 0, 0, 'R');
        $this->Cell(30, 5, '1', 0, 1, 'L');

        $this->SetX(150);
        $this->Cell(30, 5, 'Implementacion:', 0, 0, 'R');
        $this->Cell(30, 5, 'Septiembre 18 2023', 0, 1, 'L');

        $this->SetX(150);
        $this->Cell(30, 5, 'Codigo:', 0, 0, 'R');
        $this->Cell(30, 5, 'ME-F-33', 0, 1, 'L');

        $this->SetX(150);
        $this->Cell(30, 5, utf8_decode('Página: '), 0, 0, 'R');
        $this->Cell(30, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        $this->SetFont('Arial', 'B', 12);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(60, 10, 'Formato Preoperacional Semanal de Tracto-Camiones', 0, 0, 'C');
        $this->Ln(30);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
    }
}

if (isset($_GET['var1']) && isset($_GET['var2'])) {

    $fecha_inicio = $_GET['var1'];
    $fecha_fin = $_GET['var2'];
    $vehi_id = $_GET['var3'];
} else {
    echo "No se han recibido las variables.";
    ob_end_flush();
    exit;
}

$operacionesClass = new Operaciones();
$operaciones = $operacionesClass->listar_preguntas($fecha_inicio, $fecha_fin, $vehi_id);

// Agrupar resultados por pre_formulario
$agrupado_por_formulario = [];
foreach ($operaciones as $operacion) {
    $formulario = $operacion['pre_formulario'];
    if (!isset($agrupado_por_formulario[$formulario])) {
        $agrupado_por_formulario[$formulario] = [];
    }
    $agrupado_por_formulario[$formulario][] = $operacion;
}

// Verificar si se obtuvieron datos
if (!empty($agrupado_por_formulario)) {

    // Crear PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();

    $ultima_fecha = null;
    foreach ($agrupado_por_formulario as $formulario => $operaciones) {
        foreach ($operaciones as $operacion) {
            $placa = isset($operaciones[0]['vehi_placa']) ? $operaciones[0]['vehi_placa'] : 'N/A';
            $soat = isset($operaciones[0]['vehi_soat_vence']) ? $operaciones[0]['vehi_soat_vence'] : 'N/A';
            $tecnicomecanica = isset($operaciones[0]['vehi_tecnicomecanica']) ? $operaciones[0]['vehi_tecnicomecanica'] : 'N/A';
            $tarjeta_propiedad = isset($operaciones[0]['vehi_tarjeta_propiedad']) ? $operaciones[0]['vehi_tarjeta_propiedad'] : 'N/A';
            $poliza = isset($operaciones[0]['vehi_poliza']) ? $operaciones[0]['vehi_poliza'] : 'N/A';
            $fecha_poliza = isset($operaciones[0]['vehi_poliza_vence']) ? $operaciones[0]['vehi_poliza_vence'] : 'N/A';
            $categoria = isset($operaciones[0]['cond_categoria_licencia']) ? $operaciones[0]['cond_categoria_licencia'] : 'N/A';
            $vencimiento_licencia = isset($operaciones[0]['cond_vencimiento_licencia']) ? $operaciones[0]['cond_vencimiento_licencia'] : '01/01/1800';
            $cedula = isset($operaciones[0]['user_cedula']) ? $operaciones[0]['user_cedula'] : 'N/A';
            $Fecha = isset($operaciones[0]['pre_fecha_crea_form']) ? $operaciones[0]['pre_fecha_crea_form'] : 'N/A';
            $Kilometraje_final = isset($operaciones[0]['pre_kilometraje_inicial']) ? $operaciones[0]['pre_kilometraje_inicial'] : 'N/A';
            $Kilometraje_inicial = isset($operaciones[0]['pre_kilometraje_inicial_anterior']) ? $operaciones[0]['pre_kilometraje_inicial_anterior'] : 'N/A';
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

            // Añadir una nueva página solo si la fecha es diferente a la última fecha registrada
            if ($Fecha != $ultima_fecha) {
                $pdf->AddPage();
                $pdf->SetMargins(10, 30, 10);
                $pdf->SetAutoPageBreak(true, 20);
                $pdf->SetX(10);
                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->Cell(105, 5, 'Inspeccionado por:' . '   ' . $operario . ' - ' . $cedula, 1, 0, 'L', 0);
                $pdf->Cell(85, 5, 'Reportado a/cargo:' . '   ' . 'Mantenimiento', 1, 0, 'L', 0);
                $pdf->Ln(5);
                $pdf->Cell(70, 5, 'Fecha Inspeccion:' . '   ' . date_format(new DateTime($Fecha), 'd/m/Y'), 1, 0, 'L', 0);
                $pdf->Cell(60, 5, 'Placa: ' . $placa, 1, 0, 'L', 0);
                $pdf->Cell(60, 5, 'Modelo' . '   ' . $modelo, 1, 0, 'L', 0);
                $pdf->Ln(5);
                $pdf->Cell(60, 5, 'Licencia (Categoria):' . '   ' . $categoria, 1, 0, 'L', 0);
                $pdf->Cell(90, 5, 'Licencia (Vencimiento):' . '   ' . date_format(new DateTime($vencimiento_licencia), 'd/m/Y'), 1, 0, 'L', 0);
                $pdf->Cell(40, 5, 'Marca:' . '   ' . $marca, 1, 0, 'L', 0);
                $pdf->Ln(5);
                $pdf->Cell(60, 5, 'Tarjeta de Propiedad:' . '   ' . $tarjeta_propiedad, 1, 0, 'L', 0);
                $pdf->Cell(50, 5, 'Tecnicomecanica:' . '   ' . date_format(new DateTime($tecnicomecanica), 'd/m/Y'), 1, 0, 'L', 0);
                $pdf->Cell(40, 5, 'SOAT:' . '   ' . date_format(new DateTime($soat), 'd/m/Y'), 1, 0, 'L', 0);
                $pdf->Cell(40, 5, 'Poliza:' . '  ' . $poliza . '  ' . date_format(new DateTime($fecha_poliza), 'd/m/Y'), 1, 0, 'L', 0);
                $pdf->Ln(5);
                $pdf->Cell(0, 10, 'B= Bueno (SI) M= Malo (NO) N/A= No Aplica', 0, 0, 'C', 0);
                $pdf->Ln(10);

                $pdf->SetFillColor(233, 229, 235);
                $pdf->SetDrawColor(61, 61, 61);

                $pdf->Cell(40, 20, 'Item', 1, 0, 'C', 1);
                $pdf->Cell(100, 20, 'Concepto', 1, 0, 'C', 1);

                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(50, 10, utf8_decode($diaSemanaEnEspanol) . '  ' . date_format(new DateTime($Fecha), 'd/m/Y'), 1, 1, 'C', 1);


                $pdf->SetX(150);
                for ($i = 0; $i < 1; $i++) {
                    $pdf->Cell(17, 10, 'B', 1, 0, 'C', 1);
                    $pdf->Cell(17, 10, 'M', 1, 0, 'C', 1);
                    $pdf->Cell(16, 10, 'N/A', 1, 0, 'C', 1);
                }
                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(40, 12, 'Niveles y Fugas', 1, 0, 'C', 1);
                $pdf->Ln(12);
                $pdf->Cell(40, 33, 'Sistema Electrico', 1, 0, 'C', 1);
                $pdf->Ln(33);
                $pdf->Cell(40, 15, 'Sistema De Frenos', 1, 0, 'C', 1);
                $pdf->Ln(15);
                $pdf->Cell(40, 30, 'Sistema Mecanico', 1, 0, 'C', 1);
                $pdf->Ln(30);
                $pdf->Cell(40, 18, 'Sistema de Rodamiento', 1, 0, 'C', 1);
                $pdf->Ln(18);
                $pdf->Cell(40, 15, 'Trailer', 1, 0, 'C', 1);
                $pdf->Ln(15);
                $pdf->Cell(40, 15, 'Quinta Rueda', 1, 0, 'C', 1);
                $pdf->Ln(15);
                $pdf->Cell(40, 36, 'Seguridad y Otros', 1, 0, 'C', 1);

                $pdf->SetFont('Arial', '', 7);
                $pdf->SetY(95);
                foreach ($operaciones as $operacion) {

                    $pdf->SetX(50);
                    $pdf->Cell(100, 3, utf8_decode($operacion['suboper_nombre']), 1, 0, 'L', 0);
                    $pdf->SetX(150);

                    if ($operacion['pre_repuesta'] == 'B') {
                        $pdf->Cell(17, 3, 'X', 1, 0, 'C');
                        $pdf->Cell(17, 3, '', 1, 0, 'C');
                        $pdf->Cell(16, 3, '', 1, 0, 'C');
                    } elseif ($operacion['pre_repuesta'] == 'M') {
                        $pdf->Cell(17, 3, '', 1, 0, 'C');
                        $pdf->Cell(17, 3, 'X', 1, 0, 'C');
                        $pdf->Cell(16, 3, '', 1, 0, 'C');
                    } elseif ($operacion['pre_repuesta'] == 'NA') {
                        $pdf->Cell(17, 3, '', 1, 0, 'C');
                        $pdf->Cell(17, 3, '', 1, 0, 'C');
                        $pdf->Cell(16, 3, 'X', 1, 0, 'C');
                    } else {
                        $pdf->Cell(17, 3, '', 1, 0, 'C');
                        $pdf->Cell(17, 3, '', 1, 0, 'C');
                        $pdf->Cell(16, 3, '', 1, 0, 'C');
                    }
                    $pdf->Ln();
                }
                $pdf->Ln(20);
                $pdf->Cell(60, 5, 'Kilometraje Inicial:', 0, 0, 'R');
                for ($i = 0; $i < 1; $i++) {
                    $pdf->Cell(0, 10, $Kilometraje_inicial . ' km', 1, 0, 'C');
                }
                $pdf->SetFont('Arial', '', 7);
                $pdf->Ln();
                $pdf->Cell(60, 5, 'Kilometraje Final:', 0, 0, 'R');
                for ($i = 0; $i < 1; $i++) {
                    $pdf->Cell(0, 10, $Kilometraje_final . ' km', 1, 0, 'C');
                }
                $pdf->Ln(20);
                $pdf->Cell(60, 5, 'Estado Preoperacional:', 0, 0, 'R');
                for ($i = 0; $i < 1; $i++) {
                    $pdf->Cell(0, 10, $estado, 1, 0, 'C');
                }
                $pdf->Ln();
                $pdf->Cell(60, 5, 'Fecha Revision:', 0, 0, 'R');
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
            }
            // Actualizar la última fecha registrada
            $ultima_fecha = $Fecha;
        }
    }

    $pdf->Output();
} else {
    echo "No se encontraron datos para generar el informe.";
}
