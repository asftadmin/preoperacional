<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");

class Operaciones extends Conectar
{
    // Para los datos generales del reporte
    public function obtenerDatosGenerales($repo_numb)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $sql = "SELECT 
                rm.repo_fech, rm.repo_numb, tm.tipo_mantenimiento, v.vehi_placa,
                o.obras_nom, CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS operador,
                rm.repo_inte, rm.repo_hora, rm.repo_estado, dr.deta_diag_inici,
                dr.deta_desc_mtto, dr.deta_esta_fina, dr.deta_total_mtto, r.rol_cargo,
                CASE
                    WHEN dr.deta_esta_fina = 1 THEN 'OPERATIVO'
                    WHEN dr.deta_esta_fina = 2 THEN 'EN SEGUIMIENTO'
                    ELSE NULL 
                END AS estado_final
            FROM reporte_mantenimiento rm
            INNER JOIN tipos_mantenimiento tm ON tm.codigo_tipo_mantenimiento = rm.repo_tipo_mtto
            INNER JOIN obras o ON o.obras_id = rm.repo_obra
            INNER JOIN usuarios u1 ON u1.user_id = rm.repo_usua
            INNER JOIN vehiculos v ON v.vehi_id = rm.repo_vehi
            INNER JOIN detalle_reporte dr ON rm.repo_codi = dr.deta_repo_codi
            INNER JOIN roles r ON r.rol_id = u1.user_rol_usuario
            WHERE rm.repo_numb = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $repo_numb);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // solo una fila
    }

    // Para los insumos
    public function obtenerInsumos($repo_numb)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $sql = "SELECT insu_nomb, insu_refe, insu_marc, insu_seri, insu_cant,
                   insu_cost, insu_occ, insu_fact, insu_mode
            FROM insumos
            INNER JOIN reporte_mantenimiento rm ON insumos.insu_repo = rm.repo_codi
            WHERE rm.repo_numb = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $repo_numb);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Para los proveedores
    public function obtenerProveedores($repo_numb)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $sql = "SELECT p.prov_nomb, p.prov_orde_trab, p.prov_orde_comp,
                   p.prov_carg, p.prov_fact, p.prov_tipo
            FROM proveedores p
            INNER JOIN detalle_reporte dr ON p.prov_deta_repo = dr.deta_codi
            INNER JOIN reporte_mantenimiento rm ON dr.deta_repo_codi = rm.repo_codi
            WHERE rm.repo_numb = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $repo_numb);
        $stmt->execute();
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
}

// Obtener el ID desde la URL
if (isset($_GET['ID'])) {
    $repo_numb = $_GET['ID'];
} else {
    die("Error: ID no especificado.");
}
// Crear instancia de la clase Operaciones y obtener datos
$operacionesClass = new Operaciones();
$datosGenerales = $operacionesClass->obtenerDatosGenerales($repo_numb);
$insumos = $operacionesClass->obtenerInsumos($repo_numb);
$proveedores = $operacionesClass->obtenerProveedores($repo_numb);


$placa = isset($datosGenerales['vehi_placa']) ? $datosGenerales['vehi_placa'] : 'N/A';
$Fecha = isset($datosGenerales['repo_fech']) ? $datosGenerales['repo_fech'] : 'N/A';
$obra = isset($datosGenerales['obras_nom']) ? $datosGenerales['obras_nom'] : 'N/A';
$tipo_mtto = isset($datosGenerales['tipo_mantenimiento']) ? $datosGenerales['tipo_mantenimiento'] : 'N/A';
$operador = isset($datosGenerales['operador']) ? $datosGenerales['operador'] : 'N/A';
$cargo = isset($datosGenerales['rol_cargo']) ? $datosGenerales['rol_cargo'] : 'N/A';
$numero_reporte = isset($datosGenerales['repo_numb']) ? $datosGenerales['repo_numb'] : 'N/A';
// VARIABLES TABLA REPORTE MTTO
$codigo_interno = isset($datosGenerales['repo_inte']) ? $datosGenerales['repo_inte'] : 'N/A';
$hora = isset($datosGenerales['repo_hora']) ? $datosGenerales['repo_hora'] : 'N/A';
$estado_reporte = isset($datosGenerales['repo_estado']) ? $datosGenerales['repo_estado'] : 'N/A';
// VARIABLES TABLA DETALLE REPORTE
$diag_inicial = isset($datosGenerales['deta_diag_inici']) ? $datosGenerales['deta_diag_inici'] : 'N/A';
$desc_mtto = isset($datosGenerales['deta_desc_mtto']) ? $datosGenerales['deta_desc_mtto'] : 'N/A';
$estado_final = isset($datosGenerales['estado_final']) ? $datosGenerales['estado_final'] : 'N/A';
$total_mtto = isset($datosGenerales['deta_total_mtto']) ? $datosGenerales['deta_total_mtto'] : 'N/A';
// VARIABLES TABLA INSUMOS 
$insumo = isset($insumos[0]['insu_nomb']) ? $insumos[0]['insu_nomb'] : 'N/A';
$marca = isset($insumos[0]['insu_marc']) ? $insumos[0]['insu_marc'] : 'N/A';
$modelo = isset($insumos[0]['insu_mode']) ? $insumos[0]['insu_mode'] : 'N/A';
$referencia = isset($insumos[0]['insu_refe']) ? $insumos[0]['insu_refe'] : 'N/A';
$serie = isset($insumos[0]['insu_seri']) ? $insumos[0]['insu_seri'] : 'N/A';
$cantidad = isset($insumos[0]['insu_cant']) ? $insumos[0]['insu_cant'] : 'N/A';
$occ = isset($insumos[0]['insu_occ']) ? $insumos[0]['insu_occ'] : 'N/A';
$costo = isset($insumos[0]['insu_cost']) ? $insumos[0]['insu_cost'] : 'N/A';
$factura = isset($insumos[0]['insu_fact']) ? $insumos[0]['insu_fact'] : 'N/A';
// VARIABLES TABLA PROVEEDORES 
$proveedor = isset($proveedores[0]['prov_nomb']) ? $proveedores[0]['prov_nomb'] : 'N/A';
$orden_compra = isset($proveedores[0]['prov_orde_comp']) ? $proveedores[0]['prov_orde_comp'] : 'N/A';
$orden_trabajo = isset($proveedores[0]['prov_orde_trab']) ? $proveedores[0]['prov_orde_trab'] : 'N/A';
$factura_proveedor = isset($proveedores[0]['prov_fact']) ? $proveedores[0]['prov_fact'] : 'N/A';
$proveedor_cargo = isset($proveedores[0]['prov_carg']) ? $proveedores[0]['prov_carg'] : 'N/A';
$proveedor_tipo = isset($proveedores[0]['prov_tipo']) ? $proveedores[0]['prov_tipo'] : 'N/A';


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

$pdf->Cell(43, 8, 'NOMBRE DEL EQUIPO:', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(51, 8, $placa, 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(20, 8, 'CODIGO:', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(32, 8, $codigo_interno, 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(10, 8, utf8_decode('N.º'), 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(34, 8, $repo_numb, 1, 0, 'L', 0);

$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(45, 8, utf8_decode('UBICACIÓN DEL EQUIPO'), 1, 0, 'L', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(60, 8, $obra, 1, 0, 'L', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(30, 8, 'RESPONSABLE', 1, 0, 'L', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(55, 8, $operador, 1, 0, 'L', 0);

$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(48, 8, 'TIPO DE MANTENIMIENTO', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
if ($tipo_mtto == 'Preventivo') {
    $pdf->Cell(142, 8, 'Preventivo _x_ Correctivo __ Mejora __ Informe __', 1, 0, 'L', 0);
} elseif ($tipo_mtto == 'Correctivo') {
    $pdf->Cell(142, 8, 'Preventivo __ Correctivo _x_ Mejora __ Informe __', 1, 0, 'L', 0);
} elseif ($tipo_mtto == 'Mejora') {
    $pdf->Cell(142, 8, 'Preventivo __ Correctivo __ Mejora _x_ Informe __', 1, 0, 'L', 0);
} elseif ($tipo_mtto == 'Informe') {
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
$pdf->Cell(32, 8, '', 1, 0, 'C', 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(31, 8, 'FINALIZACION', 1, 0, 'C', 1);
$pdf->SetFont('Helvetica', '', 9);
$pdf->Cell(32, 8, '', 1, 0, 'C', 0);

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
$pdf->Cell(0, 6, $estado_final, 1, 0, 'C', 0);

$pdf->Ln(12);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 6, 'REPUESTOS E INSUMOS', 1, 0, 'C', 1);

$pdf->Ln(6);
$pdf->Cell(22, 6, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'REF.', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'MARCA', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'MODELO', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'SERAL', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'CAN.', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'COSTO', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'O.C.', 1, 0, 'C', 1);
$pdf->Cell(21, 6, 'FACT.', 1, 0, 'C', 1);

$pdf->Ln(6);
$pdf->SetFont('Helvetica', '', 8);
foreach ($insumos  as $insumo) {

    $pdf->Cell(22, 6, $insumo['insu_nomb'], 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_refe'], 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_marc'], 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_mode'], 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_seri'], 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_cant'], 1, 0, 'C', 0);
    $valor_formateado = '$ ' . number_format($insumo['insu_cost'], 0, ',', '.');
    $pdf->Cell(21, 6, $valor_formateado, 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_occ'], 1, 0, 'C', 0);
    $pdf->Cell(21, 6, $insumo['insu_fact'], 1, 0, 'C', 0);
    $pdf->Ln();
}
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
$pdf->Cell(47, 6, '', 1, 0, 'C', 0);

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
    if ($proveedor['prov_tipo'] === 'interno') {
        $pdf->Cell(64, 6, $proveedor['prov_nomb'], 1, 0, 'C', 0);
        $pdf->Cell(63, 6, $proveedor['prov_carg'], 1, 0, 'C', 0);
        $pdf->Cell(63, 6, $proveedor['prov_orde_trab'], 1, 0, 'C', 0);
        $pdf->Ln();
    }else {
        $pdf->Cell(64, 6, '', 1, 0, 'C', 0);
        $pdf->Cell(63, 6, '', 1, 0, 'C', 0);
        $pdf->Cell(63, 6, '', 1, 0, 'C', 0);
        $pdf->Ln();
    }
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
$pdf->SetFont('Helvetica', '', 8);
foreach ($proveedores as $proveedor) {
    if ($proveedor['prov_tipo'] === 'externo') {
        $pdf->Cell(48, 6, $proveedor['prov_nomb'], 1, 0, 'C', 0);
        $pdf->Cell(47, 6, $proveedor['prov_orde_trab'], 1, 0, 'C', 0);
        $pdf->Cell(48, 6, $proveedor['prov_orde_comp'], 1, 0, 'C', 0);
        $pdf->Cell(47, 6, $proveedor['prov_fact'], 1, 0, 'C', 0);
        $pdf->Ln();
    }
    else {
        $pdf->Cell(48, 6, '', 1, 0, 'C', 0);
        $pdf->Cell(47, 6, '', 1, 0, 'C', 0);
        $pdf->Cell(48, 6, '', 1, 0, 'C', 0);
        $pdf->Cell(47, 6, '', 1, 0, 'C', 0);
        $pdf->Ln();
    }
}

$pdf->Ln(6);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(120, 6, 'COSTO TOTAL DEL MANTENIMIENTO', 1, 0, 'R', 1);
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(70, 6, $total, 1, 0, 'C', 0);


$pdf->Output();
