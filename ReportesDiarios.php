<?php
require_once('docs/fpdf.php');
require_once("config/conexion.php");

class Operaciones extends Conectar
{
    public function listar_preguntas($vehi_id, $fecha_inicio, $fecha_fin)
    {
        $conectar = new Conectar(); // Asumiendo que la clase Conexion maneja la conexión
        $conexion = $conectar->conexion();
        $sql = "SELECT
        rd.repdia_fech,
        rd.repdia_hr_inic,
        rd.repdia_hr_term,
        rd.repdia_volu,
        rd.repdia_recib,
        a.act_nombre,
        u.user_cedula,
        CONCAT(u.user_nombre, ' ', u.user_apellidos) AS conductor_nombre_completo,
        v.vehi_placa,
        tv.tipo_nombre,
        tv.tipo_id,
        rd.repdia_kilo,
        rd.repdia_kilo_final,
        rd.repdia_obras,
        rd.repdia_firma,
        o.obras_nom,
        rd.repdia_observa,
        SUM(rd.repdia_gaso) AS total_gaso,
        SUM(rd.repdia_acpm) AS total_acpm,
        SUM(rd.repdia_acet_moto) AS total_acet_moto,
        SUM(rd.repdia_acet_hidr) AS total_acet_hidr,
        SUM(rd.repdia_acet_tram) AS total_acet_tram,
        SUM(rd.repdia_acet_gras) AS total_acet_gras,
        SUM(rd.repdia_kilo_final - rd.repdia_kilo) AS total_kilometraje_gastado
    FROM reportes_diarios rd
    INNER JOIN vehiculos v ON rd.repdia_vehi = v.vehi_id
    INNER JOIN actividades a ON rd.repdia_actv = a.act_id
    INNER JOIN usuarios u ON rd.repdia_cond = u.user_id
    INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id
    INNER JOIN obras o ON o.obras_id = rd.repdia_obras
    WHERE rd.repdia_vehi = ? AND rd.repdia_fech BETWEEN ? AND ?
    GROUP BY
        rd.repdia_fech,
        rd.repdia_hr_inic,
        rd.repdia_hr_term,
        rd.repdia_volu,
        rd.repdia_recib,
        a.act_nombre,
        u.user_cedula,
        conductor_nombre_completo,
        v.vehi_placa,
        tv.tipo_nombre,
        tv.tipo_id,
        rd.repdia_kilo,
        rd.repdia_obras,
        rd.repdia_firma,
        rd.repdia_observa,
        o.obras_nom,
        rd.repdia_kilo_final;";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$vehi_id, $fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class PDF extends FPDF
{

    // Cabecera de página
    function Header()
    {
        $this->SetY(15);
        $this->Image('public/img/logo.png', 15, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', 'B', 12);


        $this->SetFont('Arial', 'B', 15);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(160, 10, 'REPORTE DIARIO', 0, 0, 'C');
        $this->Ln(25);
    }

    // Pie de pagina
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
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
$operaciones = $operacionesClass->listar_preguntas($vehi_id, $fecha_inicio, $fecha_fin);

// Agrupar resultados por pre_formulario
$agrupado_por_recibo = [];
foreach ($operaciones as $operacion) {
    $formulario = $operacion['repdia_recib'];
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
            // Extraer la fecha de los resultados de la consulta
            $Fecha = isset($operacion['repdia_fech']) ? $operacion['repdia_fech'] : 'N/A';
            $Recibo = isset($operacion['repdia_recib']) ? $operacion['repdia_recib'] : 'N/A';
            $operario = isset($operacion['conductor_nombre_completo']) ? $operacion['conductor_nombre_completo'] : 'N/A';
            $cedula = isset($operacion['user_cedula']) ? $operacion['user_cedula'] : 'N/A';
            $placa = isset($operacion['vehi_placa']) ? $operacion['vehi_placa'] : 'N/A';
            $tipo_vehiculo = isset($operacion['tipo_nombre']) ? $operacion['tipo_nombre'] : 'N/A';
            $gasolina = isset($operacion['total_gaso']) ? $operacion['total_gaso'] : 'N/A';
            $acpm = isset($operacion['total_acpm']) ? $operacion['total_acpm'] : 'N/A';
            $aceite_motor = isset($operacion['total_acet_moto']) ? $operacion['total_acet_moto'] : 'N/A';
            $aceite_trasmision = isset($operacion['total_acet_tram']) ? $operacion['total_acet_tram'] : 'N/A';
            $aceite_hidraulico = isset($operacion['total_acet_hidr']) ? $operacion['total_acet_hidr'] : 'N/A';
            $grasa = isset($operacion['total_acet_gras']) ? $operacion['total_acet_gras'] : 'N/A';
            $tipo_id = isset($operacion['tipo_id']) ? $operacion['tipo_id'] : 'N/A';
            $observaciones = isset($operacion['repdia_observa']) ? $operacion['repdia_observa'] : 'N/A';
            $firma = isset($operaciones[0]['repdia_firma']) ? $operaciones[0]['repdia_firma'] : 'N/A';
            // Añadir una nueva página solo si la fecha es diferente a la última fecha registrada
            if ($Fecha != $ultima_fecha) {
                $pdf->AddPage('L');
                $pdf->SetMargins(15, 15, 15);
                $pdf->SetAutoPageBreak(true, 20);
                $pdf->SetXY(225, 15);
                $pdf->SetFont('Arial', 'B', 13);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell(54, 5,  $Recibo, 0, 1, 'R', 0);
                $pdf->SetXY(225, 20);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(54, 5, 'FECHA:' . '  ' . $Fecha, 0, 0, 'R', 0);
                $pdf->SetY(40);
                $pdf->SetX(15);
                $pdf->SetFont('Helvetica', 'B', 10);
                $pdf->Cell(152, 10, 'Obra:' . '   ', 1, 0, 'L', 0);
                $pdf->Cell(115, 10, 'Operador:' . ' ' . $operario . ' - ' . $cedula, 1, 0, 'L', 0);
                $pdf->Ln(10);
                $pdf->Cell(152, 10, 'Maquinaria:' . ' ' . $placa . ' - ' . utf8_decode($tipo_vehiculo), 1, 0, 'L', 0);
                $pdf->Cell(115, 10, 'Ing Y/O Autorizado: ', 1, 1, 'L', 0);
                $pdf->Cell(0, 10, 'COMBUSTIBLES Y LUBRICANTES' . '   ', 0, 1, 'C', 0);
                $pdf->SetFillColor(233, 229, 235);
                $pdf->SetDrawColor(61, 61, 61);

                $pdf->Cell(55, 10, 'Concepto', 1, 0, 'C', 1);
                $pdf->Cell(20, 10, 'Und', 1, 0, 'C', 1);
                $pdf->Cell(54, 10, 'Cantidad', 1, 0, 'C', 1);

                $pdf->Cell(9, 10, '   ', 0, 0, 'L', 0);

                $pdf->Cell(55, 10, 'Concepto', 1, 0, 'C', 1);
                $pdf->Cell(20, 10, 'Und', 1, 0, 'C', 1);
                $pdf->Cell(54, 10, 'Cantidad', 1, 1, 'C', 1);

                $pdf->Cell(55, 10, 'Gasolina', 1, 0, 'C', 0);
                $pdf->Cell(20, 10, 'GAL', 1, 0, 'C', 0);
                $pdf->Cell(54, 10, $gasolina, 1, 0, 'C', 0);

                $pdf->Cell(9, 10, '   ', 0, 0, 'L', 0);

                $pdf->Cell(55, 10, 'Aceite Hidraulico', 1, 0, 'C', 0);
                $pdf->Cell(20, 10, 'GAL', 1, 0, 'C', 0);
                $pdf->Cell(54, 10, $aceite_hidraulico, 1, 1, 'C', 0);

                $pdf->Cell(55, 10, 'Acpm', 1, 0, 'C', 0);
                $pdf->Cell(20, 10, 'GAL', 1, 0, 'C', 0);
                $pdf->Cell(54, 10, $acpm, 1, 0, 'C', 0);

                $pdf->Cell(9, 10, '   ', 0, 0, 'L', 0);

                $pdf->Cell(55, 10, 'Aceite Trasmision', 1, 0, 'C', 0);
                $pdf->Cell(20, 10, 'GAL', 1, 0, 'C', 0);
                $pdf->Cell(54, 10, $aceite_trasmision, 1, 1, 'C', 0);

                $pdf->Cell(55, 10, 'Aceite Motor', 1, 0, 'C', 0);
                $pdf->Cell(20, 10, 'GAL', 1, 0, 'C', 0);
                $pdf->Cell(54, 10, $aceite_motor, 1, 0, 'C', 0);

                $pdf->Cell(9, 10, '   ', 0, 0, 'L', 0);

                $pdf->Cell(55, 10, 'Grasa', 1, 0, 'C', 0);
                $pdf->Cell(20, 10, 'KG', 1, 0, 'C', 0);
                $pdf->Cell(54, 10, $grasa, 1, 0, 'C', 0);

                $pdf->Ln(10);
                $pdf->Cell(0, 10, 'KILOMETRAJE (HRS/KMS)', 0, 0, 'C', 0);
                $pdf->Ln(10);

                $pdf->SetFont('Arial', 'B', 9);

                $pdf->Cell(25, 10, 'Hora Inicio', 1, 0, 'C', 1);

                if ($tipo_id == 1 || $tipo_id == 2 || $tipo_id == 3 || $tipo_id == 4) {
                    $pdf->Cell(35, 10, 'Kilometraje Inicial', 1, 0, 'C', 1);
                } else {
                    $pdf->Cell(35, 10, 'Horometraje Inicial', 1, 0, 'C', 1);
                }
                $pdf->Cell(25, 10, 'Hora Final', 1, 0, 'C', 1);
                if ($tipo_id == 1 || $tipo_id == 2 || $tipo_id == 3 || $tipo_id == 4) {
                    $pdf->Cell(35, 10, 'Kilometraje Final', 1, 0, 'C', 1);
                } else {
                    $pdf->Cell(35, 10, 'Horometraje Final', 1, 0, 'C', 1);
                }
                $pdf->Cell(40, 10, 'Obra', 1, 0, 'C', 1);
                $pdf->Cell(20, 10, 'Volumen', 1, 0, 'C', 1);
                $pdf->Cell(55, 10, 'Actividad', 1, 0, 'C', 1);
                if ($tipo_id == 1 || $tipo_id == 2 || $tipo_id == 3 || $tipo_id == 4) {
                    $pdf->Cell(32, 10, 'Total Kms', 1, 1, 'C', 1);
                } else {
                    $pdf->Cell(32, 10, 'Total Horometraje', 1, 1, 'C', 1);
                }

                $pdf->SetFont('Arial', '', 7);

                foreach ($operaciones as $operacion) {

                    $pdf->Cell(25, 5, date_format(new DateTime($operacion['repdia_hr_inic']), 'H:s'), 1, 0, 'C', 0);
                    $pdf->Cell(35, 5, utf8_decode($operacion['repdia_kilo']), 1, 0, 'C', 0);
                    $pdf->Cell(25, 5, date_format(new DateTime($operacion['repdia_hr_term']), 'H:s'), 1, 0, 'C', 0);
                    $pdf->Cell(35, 5, utf8_decode($operacion['repdia_kilo_final']), 1, 0, 'C', 0);
                    $pdf->Cell(40, 5, utf8_decode($operacion['obras_nom']), 1, 0, 'C', 0);
                    $pdf->Cell(20, 5, $operacion['repdia_volu'], 1, 0, 'C', 0);
                    $pdf->Cell(55, 5, utf8_decode($operacion['act_nombre']), 1, 0, 'L', 0);
                    $pdf->Cell(32, 5, $operacion['total_kilometraje_gastado'] . '  ', 1, 0, 'C', 0);
                    $pdf->Ln();
                }
                $pdf->Ln(8);
                $pdf->Cell(0, 10, 'Observaciones', 1, 1, 'C', 1);
                $pdf->MultiCell(0, 5,  utf8_decode($observaciones), 1);

                // Verifica si la firma no es null o vacía
                if ($firma !== 'N/A' && !empty($firma)) {
                    // Decodifica la imagen Base64
                    $data = explode(',', $firma); // Divide la cadena en el encabezado y los datos
                    $imageData = base64_decode($data[1]); // Decodifica el contenido

                    // Guarda la imagen en un archivo temporal
                    $imagePath = 'temp_firma.png'; // Nombre del archivo temporal
                    file_put_contents($imagePath, $imageData);

                    // Agregar la imagen al PDF
                    $pdf->Image($imagePath, 230, 163, 50, 30); // Ajusta x, y, width, height según sea necesario


                    // Limpiar la imagen temporal
                    unlink($imagePath);
                }
                $pdf->SetY(177);
                $pdf->SetX(230);
                $pdf->Cell(50, 7, '', 'B', 1, 'C', 0);
                $pdf->SetX(230);
                $pdf->Cell(50, 5, 'Ing. Residente y/o Inspector', 0, 1, 'C', 0);
            }
            // Actualizar la última fecha registrada
            $ultima_fecha = $Fecha;
        }
    }
    $pdf->Output();
} else {
    echo "No se encontraron datos para generar el informe.";
}
