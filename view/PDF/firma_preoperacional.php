<?php

function obtenerFirmaPreoperacional($pre_formulario)
{
    $conectar = new Conectar();
    $conexion = $conectar->getConexion();
    $conectar->set_names();

    $sql = "SELECT
            pf.pre_firma,
            pf.firma_fecha,
            MIN(p.pre_fecha_crea_form) AS pre_fecha_crea_form,
            MIN(p.pre_hora) AS pre_hora
        FROM preoperacional_firmas pf
        INNER JOIN preoperacional p ON p.pre_formulario = pf.pre_formulario
        WHERE pf.pre_formulario = ?
        GROUP BY pf.pre_firma, pf.firma_fecha
        LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(1, $pre_formulario);
    $stmt->execute();

    $firma = $stmt->fetch(PDO::FETCH_ASSOC);
    return $firma ?: null;
}

function prepararImagenFirmaPreoperacional($firmaBase64)
{
    if (!is_string($firmaBase64) || $firmaBase64 === '') {
        return null;
    }

    $base64 = preg_replace('/^data:image\/png;base64,/', '', $firmaBase64);
    $imagen = base64_decode($base64, true);

    if ($imagen === false || strncmp($imagen, "\x89PNG\r\n\x1a\n", 8) !== 0) {
        return null;
    }

    $rutaTemporal = tempnam(sys_get_temp_dir(), 'pre_firma_');
    if ($rutaTemporal === false) {
        return null;
    }

    file_put_contents($rutaTemporal, $imagen);
    return $rutaTemporal;
}

function fechaHoraPreoperacionalFirma($firma)
{
    if (empty($firma['pre_fecha_crea_form'])) {
        return '';
    }

    $fecha = date_format(new DateTime($firma['pre_fecha_crea_form']), 'd/m/Y');
    $hora = '';

    if (!empty($firma['pre_hora'])) {
        $hora = date_format(new DateTime($firma['pre_hora']), 'H:i');
    }

    return trim($fecha . ($hora !== '' ? ' ' . $hora : ''));
}

function imprimirFirmaInspeccionadoPreoperacional($pdf, $pre_formulario, $altoEtiqueta = 20, $altoCelda = 20)
{
    $firma = obtenerFirmaPreoperacional($pre_formulario);

    $pdf->Cell(60, $altoEtiqueta, utf8_decode('V°B° Inspeccionado por:'), 0, 0, 'R');

    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $w = $pdf->GetPageWidth() - $x - 10;

    $pdf->Cell($w, $altoCelda, '', 1, 0, 'C');

    if ($firma) {
        $rutaFirma = prepararImagenFirmaPreoperacional($firma['pre_firma'] ?? '');

        if ($rutaFirma) {
            $pdf->Image(
                $rutaFirma,
                $x + 8,
                $y + 3,
                75,
                12,
                'PNG'
            );

            unlink($rutaFirma);
        }

        $fechaHora = fechaHoraPreoperacionalFirma($firma);

        if ($fechaHora !== '') {
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->SetXY($x + 85, $y + 12);
            $pdf->Cell($w - 85, 4, 'Fecha/Hora: ' . $fechaHora, 0, 0, 'L');
            $pdf->SetFont('Arial', '', 7);
        }
    }

    $pdf->SetXY(10, $y + $altoCelda);
}

?>
