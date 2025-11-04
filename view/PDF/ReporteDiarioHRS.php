<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");

class Operaciones extends Conectar
{
    public function listar_preguntas($repdia_recib)
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
    rd.repdia_kilo,
    rd.repdia_kilo_final,
    rd.repdia_obras,
    rd.repdia_firma,
    o.obras_nom,
    string_agg(rd.repdia_observa, ', ') AS observaciones_actividades,
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
WHERE rd.repdia_recib = ?
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
    rd.repdia_kilo,
    rd.repdia_obras,
    rd.repdia_firma,
    o.obras_nom,
    rd.repdia_kilo_final
ORDER BY rd.repdia_hr_inic;";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $repdia_recib);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class PDF extends FPDF
{
    private $fecha;
    private $Recibo;

    function __construct($fecha,$Recibo)
    {
        parent::__construct();
        $this->fecha = $fecha;
        $this->Recibo = $Recibo;
    }

    // Cabecera de página
    function Header()
    {
        $this->SetY(15);
        $this->Image('../../public/img/logo.png', 15, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', 'B', 12);

        $this->SetX(230);
        $this->Cell(25, 5, '', 'B', 0, 'R');
        $this->SetTextColor(255, 0, 0);
        $this->Cell(24, 5, $this->Recibo, 'B', 1, 'L');
        $this->SetTextColor(50, 50, 50);

        $this->SetFont('Arial', '', 8);
        $this->SetX(230);
        $this->Cell(24, 5, 'FECHA:', 1, 0, 'L');
        $this->Cell(25, 5, date_format(new DateTime($this->fecha),'d - m - Y'), 1, 0, 'R');  // Aquí se usa la variable $fecha

        $this->SetFont('Arial', 'B', 15);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(160, 10, 'REPORTE DIARIO', 0, 0, 'C');
        $this->Ln(25);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
    }
}
// Obtener el ID desde la URL
if (isset($_GET['ID'])) {
    $repdia_recib = $_GET['ID'];
} else {
    die("Error: ID no especificado.");
}
$operacionesClass = new Operaciones();
$operaciones = $operacionesClass->listar_preguntas($repdia_recib);

// Extraer la fecha de los resultados de la consulta
$Fecha = isset($operaciones[0]['repdia_fech']) ? $operaciones[0]['repdia_fech'] : 'N/A';
$Recibo = isset($operaciones[0]['repdia_recib']) ? $operaciones[0]['repdia_recib'] : 'N/A';
$operario = isset($operaciones[0]['conductor_nombre_completo']) ? $operaciones[0]['conductor_nombre_completo'] : 'N/A';
$cedula = isset($operaciones[0]['user_cedula']) ? $operaciones[0]['user_cedula'] : 'N/A';
$placa = isset($operaciones[0]['vehi_placa']) ? $operaciones[0]['vehi_placa'] : 'N/A';
$tipo_vehiculo = isset($operaciones[0]['tipo_nombre']) ? $operaciones[0]['tipo_nombre'] : 'N/A';
$gasolina = isset($operaciones[0]['total_gaso']) ? $operaciones[0]['total_gaso'] : 'N/A';
$acpm = isset($operaciones[0]['total_acpm']) ? $operaciones[0]['total_acpm'] : 'N/A';
$aceite_motor = isset($operaciones[0]['total_acet_moto']) ? $operaciones[0]['total_acet_moto'] : 'N/A';
$aceite_trasmision = isset($operaciones[0]['total_acet_tram']) ? $operaciones[0]['total_acet_tram'] : 'N/A';
$aceite_hidraulico = isset($operaciones[0]['total_acet_hidr']) ? $operaciones[0]['total_acet_hidr'] : 'N/A';
$grasa = isset($operaciones[0]['total_acet_gras']) ? $operaciones[0]['total_acet_gras'] : 'N/A';
$observaciones = isset($operaciones[0]['observaciones_actividades']) ? $operaciones[0]['observaciones_actividades'] : 'N/A';
$firma = isset($operaciones[0]['repdia_firma']) ? $operaciones[0]['repdia_firma'] : 'N/A';


// Crear PDF y pasar la fecha
$pdf = new PDF($Fecha,$Recibo);
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetX(15);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(152, 10, 'Obra:' . '   ', 1, 0, 'L', 0);
$pdf->Cell(115, 10, 'Operador:' . ' ' . $operario . ' - ' . $cedula, 1, 0, 'L', 0);
$pdf->Ln(10);
$pdf->Cell(152, 10, 'Maquinaria:' . ' ' . $placa . ' - ' .utf8_decode($tipo_vehiculo), 1, 0, 'L', 0);
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
$pdf->Cell(0, 10, 'HOROMETRO (HRS/KMS)', 0, 0, 'C', 0);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(20, 10, 'Hora Inicio', 1, 0, 'C', 1);
$pdf->Cell(25, 10, 'Horometro Inic', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Hora Final', 1, 0, 'C', 1);
$pdf->Cell(25, 10, 'Horometro Fnal', 1, 0, 'C', 1);
$pdf->Cell(70, 10, 'Obra', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Volumen', 1, 0, 'C', 1);
$pdf->Cell(55, 10, 'Actividad', 1, 0, 'C', 1);
$pdf->Cell(32, 10, 'Total Horometraje', 1, 1, 'C', 1);

$pdf->SetFont('Arial', '', 7);
foreach ($operaciones as $operacion) {
    $pdf->Cell(20, 5, date_format(new DateTime($operacion['repdia_hr_inic']),'H:i:s'), 1, 0, 'C', 0);
    $pdf->Cell(25, 5, utf8_decode($operacion['repdia_kilo']), 1, 0, 'C', 0);
    $pdf->Cell(20, 5, date_format(new DateTime($operacion['repdia_hr_term']),'H:i:s'), 1, 0, 'C', 0);
    $pdf->Cell(25, 5, utf8_decode($operacion['repdia_kilo_final']), 1, 0, 'C', 0);
    $pdf->Cell(70, 5, utf8_decode($operacion['obras_nom']), 1, 0, 'C', 0);
    $pdf->Cell(20, 5, $operacion['repdia_volu'], 1, 0, 'C', 0);
    $pdf->Cell(55, 5, utf8_decode($operacion['act_nombre']), 1, 0, 'L', 0);
    $pdf->Cell(32, 5, number_format($operacion['total_kilometraje_gastado'], 0, ',', '.').'  Hr', 1, 0, 'C', 0);
    $pdf->Ln();
}
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Observaciones', 1, 1, 'C', 1);
$pdf->MultiCell(0, 5,  utf8_decode($observaciones),1);

// Verifica si la firma no es null o vacía
if ($firma !== 'N/A' && !empty($firma)) {
    // Decodifica la imagen Base64
    $data = explode(',', $firma); // Divide la cadena en el encabezado y los datos
    $imageData = base64_decode($data[1]); // Decodifica el contenido

    // Guarda la imagen en un archivo temporal
    $imagePath = 'temp_firma.png'; // Nombre del archivo temporal
    file_put_contents($imagePath, $imageData);

    // Agregar la imagen al PDF
    $pdf->Image($imagePath, 230, 165, 50, 30); // Ajusta x, y, width, height según sea necesario
    
    
    // Limpiar la imagen temporal
    unlink($imagePath);
}
$pdf->SetY(176);
$pdf->SetX(230);
$pdf->Cell(50, 8, '', 'B', 1, 'C', 0);
$pdf->SetX(230);
$pdf->Cell(50, 6, 'Ing. Residente y/o Inspector', 0, 1, 'C', 0);

$pdf->Output();
