<?php

ob_start();
require_once '../../config/conexion.php';
require_once '../../models/InventarioEquipos.php';
require_once '../../docs/fpdf.php';
ob_end_clean();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('La sesión ha expirado.');
}

$tipo = isset($_GET['tipo']) && !is_array($_GET['tipo']) ? strtoupper(trim((string) $_GET['tipo'])) : '';
$asignacionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!in_array($tipo, array('ENTREGA', 'DEVOLUCION'), true) || !$asignacionId) {
    http_response_code(422);
    exit('Los parámetros del acta no son válidos.');
}

$modelo = new InventarioEquipos();
if (!$modelo->tieneAcceso((int) $_SESSION['user_id'])) {
    http_response_code(403);
    exit('No tiene permiso para consultar esta acta.');
}
$datos = $modelo->obtenerDatosActa((int) $asignacionId, $tipo);
if ($datos === null) {
    http_response_code(404);
    exit('No existen datos suficientes para generar el acta solicitada.');
}

/** Convierte textos UTF-8 a la codificación utilizada por FPDF 1.86. */
function textoPdfInventario($texto) {
    $texto = $texto === null || $texto === '' ? 'N/A' : (string) $texto;
    $convertido = iconv('UTF-8', 'windows-1252//TRANSLIT', $texto);
    return $convertido === false ? $texto : $convertido;
}

/** PDF institucional CO-F-16 con distribución controlada y espacios de firma. */
class ActaInventarioPdf extends FPDF {
    public function Header() {
        $logo = __DIR__ . '/../../public/img/logo.png';
        $this->SetLineWidth(0.2);
        $this->Rect(15, 10, 180, 25);
        $this->Line(52, 10, 52, 35);
        $this->Line(145, 10, 145, 35);
        if (is_file($logo)) {
            $this->Image($logo, 18, 14, 25);
        }
        $this->SetXY(52, 17.5);
        $this->SetFont('Arial', 'B', 9);
        $this->MultiCell(
            93,
            5,
            textoPdfInventario("ACTA DE ENTREGA Y DEVOLUCIÓN DE EQUIPOS DE\nCÓMPUTO / HERRAMIENTA DE TRABAJO"),
            0,
            'C'
        );
        $this->SetXY(145, 10);
        $this->SetFont('Arial', '', 7);
        $this->Cell(30, 6.25, textoPdfInventario('Versión:'), 1);
        $this->Cell(20, 6.25, '4', 1, 1, 'C');
        $this->SetX(145);
        $this->Cell(30, 6.25, textoPdfInventario('Implementación:'), 1);
        $this->Cell(20, 6.25, textoPdfInventario('11 Marzo 2024'), 1, 1, 'C');
        $this->SetX(145);
        $this->Cell(30, 6.25, textoPdfInventario('Código:'), 1);
        $this->Cell(20, 6.25, 'CO-F-16', 1, 1, 'C');
        $this->SetX(145);
        $this->Cell(30, 6.25, textoPdfInventario('Tipo documento:'), 1);
        $this->Cell(20, 6.25, 'Formato', 1, 1, 'C');
        $this->SetY(41);
    }

    public function Footer() {
        $this->SetY(-12);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 5, textoPdfInventario('CO-F-16 · Versión 4 · Página ') . $this->PageNo(), 0, 0, 'C');
    }

    public function tituloBloque($titulo) {
        $this->SetFillColor(220, 230, 241);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(180, 6, textoPdfInventario($titulo), 1, 1, 'C', true);
    }

    public function filaPersona($nombre, $cargo) {
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(90, 5, 'NOMBRE', 1, 0, 'C');
        $this->Cell(90, 5, 'CARGO', 1, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(90, 7, textoPdfInventario($nombre), 1, 0, 'C');
        $this->Cell(90, 7, textoPdfInventario($cargo), 1, 1, 'C');
    }

    public function filaComponente($tipo, $marca, $modelo, $serial) {
        $this->SetFont('Arial', '', 7);
        $this->Cell(60, 6, textoPdfInventario($tipo), 1);
        $this->Cell(40, 6, textoPdfInventario($marca), 1);
        $this->Cell(40, 6, textoPdfInventario($modelo), 1);
        $this->Cell(40, 6, textoPdfInventario($serial), 1, 1);
    }

    /** Calcula las líneas que ocupará un texto con el ancho y fuente actuales. */
    public function cantidadLineas($ancho, $texto) {
        $texto = str_replace("\r", '', (string) $texto);
        $anchoMaximo = ($ancho - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $caracteres = $this->CurrentFont['cw'];
        $longitud = strlen($texto);
        if ($longitud > 0 && $texto[$longitud - 1] === "\n") {
            $longitud--;
        }
        $separador = -1;
        $inicio = 0;
        $indice = 0;
        $anchoLinea = 0;
        $lineas = 1;
        while ($indice < $longitud) {
            $caracter = $texto[$indice];
            if ($caracter === "\n") {
                $indice++;
                $separador = -1;
                $inicio = $indice;
                $anchoLinea = 0;
                $lineas++;
                continue;
            }
            if ($caracter === ' ') {
                $separador = $indice;
            }
            $anchoLinea += isset($caracteres[$caracter]) ? $caracteres[$caracter] : 0;
            if ($anchoLinea > $anchoMaximo) {
                if ($separador === -1) {
                    if ($indice === $inicio) {
                        $indice++;
                    }
                } else {
                    $indice = $separador + 1;
                }
                $separador = -1;
                $inicio = $indice;
                $anchoLinea = 0;
                $lineas++;
            } else {
                $indice++;
            }
        }
        return $lineas;
    }

    /** Dibuja dos textos con una sola altura de fila para evitar celdas desiguales. */
    public function filaTextoDoble($textoIzquierdo, $textoDerecho, $altoLinea = 5) {
        $textoIzquierdo = textoPdfInventario($textoIzquierdo);
        $textoDerecho = textoPdfInventario($textoDerecho);
        $alto = max(
            $this->cantidadLineas(90, $textoIzquierdo),
            $this->cantidadLineas(90, $textoDerecho)
        ) * $altoLinea;
        if ($this->GetY() + $alto > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Rect($x, $y, 90, $alto);
        $this->Rect($x + 90, $y, 90, $alto);
        $this->SetXY($x, $y);
        $this->MultiCell(90, $altoLinea, $textoIzquierdo, 0, 'L');
        $this->SetXY($x + 90, $y);
        $this->MultiCell(90, $altoLinea, $textoDerecho, 0, 'L');
        $this->SetXY($x, $y + $alto);
    }

    /** Dibuja una recomendación numerada conservando bordes y altura uniforme. */
    public function filaRecomendacion($numero, $texto, $altoLinea = 4) {
        $texto = textoPdfInventario($texto);
        $alto = max(6, $this->cantidadLineas(170, $texto) * $altoLinea);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Rect($x, $y, 10, $alto);
        $this->Rect($x + 10, $y, 170, $alto);
        $this->SetXY($x, $y);
        $this->Cell(10, $alto, (string) $numero . '.', 0, 0, 'C');
        $this->SetXY($x + 10, $y);
        $this->MultiCell(170, $altoLinea, $texto, 0, 'L');
        $this->SetXY($x, $y + $alto);
    }

    /** Agrega los tres espacios de firma definidos en el formato institucional. */
    public function bloqueFirmas($fechaTexto) {
        if ($this->GetY() + 68 > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
        $fecha = DateTime::createFromFormat('Y-m-d', $fechaTexto);
        $meses = array(
            1 => 'ENERO',
            'FEBRERO',
            'MARZO',
            'ABRIL',
            'MAYO',
            'JUNIO',
            'JULIO',
            'AGOSTO',
            'SEPTIEMBRE',
            'OCTUBRE',
            'NOVIEMBRE',
            'DICIEMBRE'
        );
        $dia = $fecha ? $fecha->format('d') : '__';
        $mes = $fecha ? $meses[(int) $fecha->format('n')] : '__________';
        $anio = $fecha ? $fecha->format('Y') : '____';

        $this->Ln(8);
        $this->SetFont('Arial', '', 8);
        $this->Cell(
            180,
            5,
            textoPdfInventario("En constancia firman a los {$dia} del mes de {$mes} del año {$anio}."),
            0,
            1,
            'L'
        );
        $this->Ln(13);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(90, 5, 'FIRMA EMPLEADO', 0, 0, 'C');
        $this->Cell(90, 5, 'FIRMA JEFE INMEDIATO O SUPERVISOR', 0, 1, 'C');
        $this->Ln(14);
        $y = $this->GetY();
        $this->Line(25, $y, 90, $y);
        $this->Line(120, $y, 185, $y);
        $this->Ln(14);
        $this->Cell(180, 5, 'FIRMA SISTEMAS / ALMACEN', 0, 1, 'C');
        $this->Ln(14);
        $y = $this->GetY();
        $this->Line(72.5, $y, 137.5, $y);
    }
}

$pdf = new ActaInventarioPdf('P', 'mm', 'Letter');
$pdf->SetMargins(15, 10, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

$pdf->tituloBloque('EMPLEADO');
$pdf->filaPersona($datos['empleado_nombre'], $datos['empleado_cargo']);
$pdf->tituloBloque('JEFE INMEDIATO O SUPERVISOR');
$pdf->filaPersona($datos['jefe_nombre'], $datos['jefe_cargo']);
$pdf->tituloBloque('ÁREA DE SISTEMAS / ALMACÉN');
if ($tipo === 'ENTREGA') {
    $cargoSistemas = stripos($datos['funcionario_entrega'], 'Cristian Arciniegas') !== false
        ? 'Coordinador de Sistemas' : $datos['funcionario_entrega_cargo'];
    $pdf->filaPersona($datos['funcionario_entrega'], $cargoSistemas);
} else {
    $cargoSistemas = stripos($datos['funcionario_recibe'], 'Cristian Arciniegas') !== false
        ? 'Coordinador de Sistemas' : $datos['funcionario_recibe_cargo'];
    $pdf->filaPersona($datos['funcionario_recibe'], $cargoSistemas);
}

$pdf->Ln(4);
$pdf->SetFillColor(220, 230, 241);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(60, 6, 'COMPONENTE / HERRAMIENTA', 1, 0, 'C', true);
$pdf->Cell(40, 6, 'MARCA', 1, 0, 'C', true);
$pdf->Cell(40, 6, 'MODELO', 1, 0, 'C', true);
$pdf->Cell(40, 6, 'SERIAL', 1, 1, 'C', true);
foreach ($datos['componentes'] as $componente) {
    $serial = $tipo === 'DEVOLUCION' && !empty($componente['serial_recibido'])
        ? $componente['serial_recibido'] : $componente['serial_original'];
    $descripcion = $componente['tipo'];
    if ($tipo === 'DEVOLUCION' && !empty($componente['estado_recepcion'])) {
        $descripcion .= ' - ' . $componente['estado_recepcion'];
    }
    $pdf->filaComponente($descripcion, $componente['marca'], $componente['modelo'], $serial);
}

$software = array();
foreach ($datos['software'] as $programa) {
    $software[] = $programa['nombre'] . (!empty($programa['version']) ? ' ' . $programa['version'] : '');
}
$softwareTexto = $datos['software_no_aplica'] ? 'NO APLICA' : implode(', ', $software);
$diagnostico = $tipo === 'ENTREGA' ? $datos['diagnostico_entrega'] : $datos['diagnostico_devolucion'];
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(90, 6, 'SOFTWARE INSTALADO', 1, 0, 'C', true);
$marcaEntrega = $tipo === 'ENTREGA' ? 'X' : ' ';
$marcaDevolucion = $tipo === 'DEVOLUCION' ? 'X' : ' ';
$pdf->Cell(90, 6, textoPdfInventario("DIAGNÓSTICO: ENTREGA [{$marcaEntrega}] DEVOLUCIÓN [{$marcaDevolucion}]"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 7);
$pdf->filaTextoDoble($softwareTexto, $diagnostico);

if ($tipo === 'DEVOLUCION' && !empty($datos['novedades'])) {
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(180, 6, 'NOVEDADES', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(180, 5, textoPdfInventario($datos['novedades']), 1);
}

$manifestaciones = '1. Haber recibido en perfecto estado toda herramienta y la capacitación suficiente para su utilización. '
    . '2. Darle el debido uso y entregarla en condiciones favorables, salvo el deterioro natural. '
    . '3. Asumir el valor de la herramienta y/o equipo en caso que No lo devuelva a la compañía. '
    . '4. Que los bienes entregados tienen naturaleza devolutiva, ya que son de propiedad de ASFALTART S.A.S EN REORGANIZACIÓN '
    . 'y deberán ser entregados a la terminación de la relación laboral o ante la solicitud expresa del empleador, por tanto, '
    . 'la tenencia de los bienes se entenderá efectuada a título de comodato, originándose de ella todas las obligaciones '
    . 'inherentes a los contratos de este tipo, consagradas en el art. 2200 y S.S del Código Civil Colombiano y normas concordantes. '
    . '5. De manera libre, voluntaria, espontánea y sin ningún tipo de vicio del consentimiento (error, fuerza o dolo), el trabajador '
    . 'autoriza expresamente a la Sociedad ASFALTART S.A.S EN REORGANIZACIÓN y, para que descuente de las sumas de dinero devengadas '
    . 'por concepto de salarios, bonificaciones salariales, bonificaciones no salariales, auxilios no constitutivos de salario, '
    . 'prestaciones sociales, vacaciones, indemnizaciones y demás emolumentos de naturaleza laboral, que reciba como trabajador '
    . 'al servicio de la compañía los valores que correspondan en el evento de daño, pérdida, hurto, no devolución y/o deterioro '
    . 'no operacional (deterioro natural) o faltante de la herramienta o equipos de trabajo entregados. '
    . '6. Adicionalmente, autoriza expresamente a ASFALTART S.A.S EN REORGANIZACIÓN y para que, una vez terminado el contrato de trabajo, '
    . 'compense o descuente cualquier saldo insoluto por concepto de daño, pérdida, hurto, no devolución y/o deterioro no operacional '
    . '(deterioro natural) de la herramienta o equipos de trabajo, con cualquier suma de dinero que corresponda en la liquidación final '
    . 'por concepto de salarios, bonificaciones salariales, bonificaciones no salariales, auxilios no constitutivos de salario, '
    . 'prestaciones sociales, vacaciones, indemnizaciones, de conformidad con lo dispuesto para tal evento en los artículos 59, '
    . 'numeral 1º y 149 Inciso 1º, del Código Sustantivo del Trabajo, modificados por el art. 18 de la Ley 1429 de 2010. '
    . '7. Para los casos que el daño sea ocasionado por la empresa de vigilancia, el guarda que tenga el celular asumirá este costo.';

$recomendaciones = array(
    'Se entiende que el equipo recibido es para uso exclusivo de tareas estrictamente laborales y usted será responsable por utilizarlo de otra manera que cause daños o perjuicios a los intereses de la empresa.',
    'No está permitida la instalación de software que no haya sido revisado y aprobado por el área de sistemas y queda bajo responsabilidad del usuario cualquier proceso ilegal sobre software instalado sin autorización.',
    'En caso de presentarse alguna avería en el equipo se debe informar inmediatamente a la oficina de sistemas con el fin de realizar la debida reparación.',
    'En caso de pérdida o robo, se debe realizar el reporte inmediato al área de sistemas y realizar el denuncio ante las autoridades pertinentes.'
);

$pdf->Ln(3);
$pdf->tituloBloque('EL TRABAJADOR MANIFIESTA');
$pdf->SetFont('Arial', '', 6.6);
$pdf->MultiCell(180, 4, textoPdfInventario($manifestaciones), 1, 'J');
$pdf->AddPage();
$pdf->tituloBloque('RECOMENDACIÓN');
$pdf->SetFont('Arial', '', 6.6);
foreach ($recomendaciones as $indice => $recomendacion) {
    $pdf->filaRecomendacion($indice + 1, $recomendacion);
}

$fechaTexto = $tipo === 'ENTREGA' ? $datos['fecha_entrega'] : $datos['fecha_devolucion'];
$pdf->bloqueFirmas($fechaTexto);

$contenido = $pdf->Output('S');
$anio = date('Y');
$directorioRelativo = 'storage/inventario_actas/' . $anio;
$directorioAbsoluto = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
    . str_replace('/', DIRECTORY_SEPARATOR, $directorioRelativo);
if (!is_dir($directorioAbsoluto) && !mkdir($directorioAbsoluto, 0770, true) && !is_dir($directorioAbsoluto)) {
    http_response_code(500);
    exit('No fue posible preparar el almacenamiento del acta.');
}
$numero = 'CO-F-16-' . $tipo . '-' . str_pad((string) $asignacionId, 8, '0', STR_PAD_LEFT);
$nombreArchivo = $numero . '.pdf';
$rutaAbsoluta = $directorioAbsoluto . DIRECTORY_SEPARATOR . $nombreArchivo;
if (file_put_contents($rutaAbsoluta, $contenido, LOCK_EX) === false) {
    http_response_code(500);
    exit('No fue posible almacenar el acta.');
}
$modelo->registrarActa(
    (int) $asignacionId,
    $tipo === 'DEVOLUCION' ? (int) $datos['devolucion_id'] : null,
    $tipo,
    $numero,
    $nombreArchivo,
    $directorioRelativo . '/' . $nombreArchivo,
    hash('sha256', $contenido),
    (int) $_SESSION['user_id']
);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
header('Content-Length: ' . strlen($contenido));
echo $contenido;
