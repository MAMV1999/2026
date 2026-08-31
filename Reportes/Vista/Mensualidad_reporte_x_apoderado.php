<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Mensualidad_reporte_x_apoderado.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode('Reporte de Mensualidades por Apoderado'), 0, 1, 'C');
        $this->Ln(6);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function texto($txt)
    {
        return utf8_decode((string)$txt);
    }

    function cabeceraAlumnos($meses)
    {
        $this->SetFont('Arial', 'B', 9);

        $this->Cell(7, 6, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell(35, 6, utf8_decode('NIVEL - GRADO'), 1, 0, 'C');
        $this->Cell(70, 6, utf8_decode('ALUMNO'), 1, 0, 'C');
        $this->Cell(19, 6, utf8_decode('CODIGO'), 1, 0, 'C');

        foreach ($meses as $mes) {
            $mes = trim($mes);
            $mes_corto = (mb_strlen($mes, 'UTF-8') > 5)
                ? mb_substr($mes, 0, 5, 'UTF-8') . '.'
                : $mes;

            $this->Cell(15, 6, utf8_decode($mes_corto), 1, 0, 'C');
        }

        $this->Ln();
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(true, 12);
$pdf->AddPage();

$reporte = new Reportemensualidadxapoderado();
$resultados = $reporte->listar();

$apoderado_actual = null;
$contador = 1;
$meses_actuales = array();

foreach ($resultados as $fila) {

    if ($apoderado_actual !== $fila['nombre_apoderado']) {

        if ($apoderado_actual !== null) {
            $pdf->AddPage();
        }

        $apoderado_actual = $fila['nombre_apoderado'];
        $contador = 1;

        $meses_actuales = explode(', ', $fila['meses']);

        // DATOS DEL APODERADO
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, utf8_decode('Datos del Apoderado'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(60, 7, utf8_decode('Nombre del Apoderado:'), 1, 0);
        $pdf->Cell(130, 7, $pdf->texto($fila['nombre_apoderado']), 1, 1);

        $pdf->Cell(60, 7, utf8_decode('Tipo de Apoderado:'), 1, 0);
        $pdf->Cell(130, 7, $pdf->texto($fila['tipo_apoderado']), 1, 1);

        $pdf->Cell(60, 7, utf8_decode('Documento:'), 1, 0);
        $pdf->Cell(
            130,
            7,
            $pdf->texto($fila['tipo_documento_apoderado'] . ' - ' . $fila['numero_documento_apoderado']),
            1,
            1
        );

        $pdf->Cell(60, 7, utf8_decode('Teléfono:'), 1, 0);
        $pdf->Cell(130, 7, $pdf->texto($fila['telefono_apoderado']), 1, 1);

        $pdf->Ln(8);

        // DATOS DE LOS ALUMNOS
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, utf8_decode('Datos de los Alumnos'), 0, 1, 'C');

        $pdf->cabeceraAlumnos($meses_actuales);
    }

    // Salto de página si ya no entra otra fila
    if ($pdf->GetY() > 180) {
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, utf8_decode('Datos de los Alumnos'), 0, 1, 'C');

        $pdf->cabeceraAlumnos($meses_actuales);
    }

    $nivel_grado = $fila['nombre_nivel'] . ' - ' . $fila['nombre_grado'];
    $documento_alumno = $fila['numero_documento_alumno'];
    $nombre_alumno = mb_substr($fila['nombre_alumno'], 0, 35, 'UTF-8');

    $montos = explode(', ', $fila['montos']);
    $estados = explode(', ', $fila['estados_pago']);
    $recibos = explode(', ', $fila['recibos']);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetFillColor(255, 255, 255);

    $pdf->Cell(7, 6, $contador, 1, 0, 'C');
    $pdf->Cell(35, 6, $pdf->texto($nivel_grado), 1, 0, 'C');
    $pdf->Cell(70, 6, $pdf->texto($nombre_alumno), 1, 0, 'C');
    $pdf->Cell(19, 6, $pdf->texto($documento_alumno), 1, 0, 'C');

    foreach ($montos as $idx => $monto) {

        $estado = isset($estados[$idx]) ? trim($estados[$idx]) : '0';
        $recibo = isset($recibos[$idx]) ? trim($recibos[$idx]) : '0';

        $monto = number_format((float)$monto, 2, '.', '');

        if ($estado == '0') {
            // Pendiente: celda gris
            $pdf->SetFillColor(200, 200, 200);
            $relleno = true;
        } else {
            // Cancelado: celda blanca
            $pdf->SetFillColor(255, 255, 255);
            $relleno = false;
        }

        if ($recibo == '1') {
            // Si tiene recibo, monto subrayado
            $pdf->SetFont('Arial', 'U', 8);
        } else {
            $pdf->SetFont('Arial', '', 8);
        }

        $pdf->Cell(15, 6, $monto, 1, 0, 'C', $relleno);

        // Reset de fuente
        $pdf->SetFont('Arial', '', 8);
    }

    $pdf->Ln();
    $contador++;
}

$pdf->Output();