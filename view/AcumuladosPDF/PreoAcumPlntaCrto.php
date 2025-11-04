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
        WHERE preoperacional.pre_fecha_crea_form BETWEEN ? AND ? AND vehi_id=?
        GROUP BY suboperaciones.suboper_nombre, preoperacional.pre_repuesta, operaciones.oper_nombre, 
            usuarios.user_cedula, conductor_nombre_completo, vehiculos.vehi_placa, tipo_vehiculo.tipo_nombre, 
            tipo_vehiculo.tipo_id, preoperacional.pre_observaciones, preoperacional.pre_kilometraje_inicial,
            preoperacional_anterior.pre_kilometraje_inicial, preoperacional.pre_fecha_crea_form, 
            vehiculos.vehi_marca, vehiculos.vehi_modelo, vehiculos.vehi_soat_vence, vehiculos.vehi_tecnicomecanica,
            vehiculos.vehi_tarjeta_propiedad, vehiculos.vehi_poliza, vehiculos.vehi_poliza_vence, preoperacional.pre_formulario,
            conductores.cond_categoria_licencia, conductores.cond_vencimiento_licencia,preoperacional.pre_estado,preoperacional.pre_fecha_revision    
            ORDER BY operaciones.oper_nombre;";
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
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(60, 5, 'INSPECCION PRE-OPERACIONAL DE SISTEMA DOSIFICADOR', 0, 0, 'C');
        $this->Ln(2);
        $this->Cell(185, 10, 'DE AGUA PLANTA DE CONCRETOS', 0, 0, 'C');
        $this->Ln(25);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-28);
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
    $pdf = new PDF();
    $pdf->AliasNbPages();

    $ultima_fecha = null;
    foreach ($agrupado_por_formulario as $formulario => $operaciones) {
        foreach ($operaciones as $operacion) {
            $Fecha = isset($operaciones[0]['pre_fecha_crea_form']) ? $operaciones[0]['pre_fecha_crea_form'] : 'N/A';
            $observaciones = isset($operaciones[0]['pre_observaciones']) ? $operaciones[0]['pre_observaciones'] : 'N/A';
            $operario = isset($operaciones[0]['conductor_nombre_completo']) ? $operaciones[0]['conductor_nombre_completo'] : 'N/A';
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
                $pdf->SetFont('Helvetica', 'B', 10);

                $pdf->Cell(70, 5, utf8_decode('Fecha Ejecución:' . '   ' . date_format(new DateTime($Fecha), 'd/m/Y')), 0, 0, 'L', 0);
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->Cell(120, 5, 'Estado del Tiempo:' . ' ' . ' Luuvia__  Seco__', 0, 0, 'R', 0);

                $pdf->Ln(10);
                $pdf->MultiCell(0, 4, utf8_decode('Por favor marque con una X en la casilla donde corresponda, siendo B: BUENO un estado Conforme y M: MALO un estado No Conforme de la máquina, NA: No aplica. Las casillas que se marquen como MALO debe gestionarse la solución inmediatamente, y por ningún motivo se debe dejar operar la maquina así. '), 0, 'C');
                $pdf->Ln(3);

                $pdf->SetFillColor(233, 229, 235);
                $pdf->SetDrawColor(61, 61, 61);

                $pdf->Cell(60, 20, 'Item', 1, 0, 'C', 1);
                $pdf->Cell(80, 20, 'Revision', 1, 0, 'C', 1);

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
                $pdf->Cell(60, 15, 'MOTOBOMBA 1:', 'L,T', 1, 'C', 1);
                $pdf->Cell(60, 15, 'DOSIFICACION DE AGUA', 'L', 0, 'C', 1);
                $pdf->Ln(15);
                $pdf->Cell(60, 15, 'MOTOBOMBA 2:', 'L,T', 1, 'C', 1);
                $pdf->Cell(60, 15, 'LAVADO DE MIXER', 'L', 0, 'C', 1);
                $pdf->Ln(15);
                $pdf->Cell(60, 15, 'MOTOBOMBA 3:', 'L,T', 1, 'C', 1);
                $pdf->Cell(60, 15, 'SUMINISTRO AGUA AL SISTEMA', 'L', 0, 'C', 1);
                $pdf->Ln(15);
                $pdf->Cell(60, 5, 'SENSOR DE FLUJO', 1, 0, 'C', 1);



                $pdf->SetFont('Arial', '', 9);
                $pdf->SetY(87);
                foreach ($operaciones as $operacion) {

                    $pdf->SetX(70);
                    $pdf->Cell(80, 5, utf8_decode($operacion['suboper_nombre']), 1, 0, 'L', 0);
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
                }
                $pdf->Ln(10);
                $pdf->Cell(60, 5, 'Firma Supervisior:', 0, 0, 'R');
                for ($i = 0; $i < 1; $i++) {
                    $pdf->Cell(0, 10, '', 1, 0, 'C');
                }
                $pdf->Ln();
                $pdf->Cell(60, 5, 'Firma Operador:', 0, 0, 'R');
                for ($i = 0; $i < 1; $i++) {
                    $pdf->Cell(0, 10, '', 1, 0, 'C');
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
