<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/mensualidad_detalle_general.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(5, 5, 5);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'DETALLE DE MENSUALIDADES X MES', 0, 1, 'C');
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'C');
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable($mes)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, utf8_decode(strtoupper($mes)), 0, 1, 'C');
        $this->Ln(3);

        $orientacion = 'C';

        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(8, 8, utf8_decode('N°'), 1, 0, $orientacion, true); // Numeración
        $this->Cell(23, 8, utf8_decode('NIVEL GRADO'), 1, 0, $orientacion, true);
        $this->Cell(60, 8, utf8_decode('ALUMNO'), 1, 0, $orientacion, true);
        $this->Cell(60, 8, utf8_decode('APODERADO'), 1, 0, $orientacion, true);
        $this->Cell(18, 8, utf8_decode('TELEFONO'), 1, 0, $orientacion, true);
        $this->Cell(15, 8, utf8_decode('MONTO'), 1, 0, $orientacion, true);
        $this->Cell(0, 8, utf8_decode('ESTADO'), 1, 1, $orientacion, true);
    }

    function FillTable($results)
    {
        $this->SetFont('Arial', '', 6.5);
        $orientacion = 'C';
        $num = 1;

        $total_general = 0;
        $total_pagado = 0;
        $total_pendiente = 0;

        foreach ($results as $row) {
            $monto = floatval($row['detalle_monto']);
            $estado = strtoupper($row['detalle_estado_pago']);

            $total_general += $monto;
            if ($estado === 'PAGADO') {
                $total_pagado += $monto;
            } elseif ($estado === 'PENDIENTE') {
                $total_pendiente += $monto;
            }

            $this->Cell(8, 5, $num++, 1, 0, $orientacion);
            $this->Cell(23, 5, utf8_decode(substr($row['nivel_nombre'], 0, 4) . ' ' . $row['grado_nombre']), 1, 0, $orientacion);
            $this->Cell(60, 5, utf8_decode(substr($row['alumno_nombre'], 0, 35)), 1, 0, $orientacion);
            $this->Cell(60, 5, utf8_decode(substr($row['apoderado_nombre'], 0, 35)), 1, 0, $orientacion);
            $this->Cell(18, 5, utf8_decode($row['apoderado_telefono']), 1, 0, $orientacion);
            $this->Cell(15, 5, 'S/.' . number_format($monto, 2), 1, 0, $orientacion);

            if ($estado === 'PENDIENTE') {
                $this->SetFillColor(192, 192, 192);
                $this->Cell(0, 5, utf8_decode($row['detalle_estado_pago']), 1, 1, $orientacion, true);
            } else {
                $this->Cell(0, 5, utf8_decode($row['detalle_estado_pago']), 1, 1, $orientacion);
            }
        }

        // Agregar fila de totales
        $this->SetFont('Arial', 'B', 6.5);
        $this->Cell(169, 6, 'TOTAL GENERAL', 1, 0, 'R');
        $this->Cell(15, 6, 'S/.' . number_format($total_general, 2), 1, 0, 'C');
        $this->Cell(0, 6, '', 1, 1);

        $this->Cell(169, 6, 'TOTAL PAGADO', 1, 0, 'R');
        $this->Cell(15, 6, 'S/.' . number_format($total_pagado, 2), 1, 0, 'C');
        $this->Cell(0, 6, '', 1, 1);

        $this->Cell(169, 6, 'TOTAL PENDIENTE', 1, 0, 'R');
        $this->Cell(15, 6, 'S/.' . number_format($total_pendiente, 2), 1, 0, 'C');
        $this->Cell(0, 6, '', 1, 1);
    }
}

// Obtener los datos
$modelo = new Mensualidad_detalle();
$resultDetalle = $modelo->listar_mensualidad_detalle_general();

if (!$resultDetalle) {
    die("Error al obtener los datos del detalle.");
}

$rowsDetalle = [];
while ($row = $resultDetalle->fetch_assoc()) {
    $rowsDetalle[] = $row;
}

// Agrupar por mes
$meses = [];
foreach ($rowsDetalle as $row) {
    $mes = $row['mensualidad_mes_nombre'];
    if (!isset($meses[$mes])) {
        $meses[$mes] = [];
    }
    $meses[$mes][] = $row;
}

// Generar el PDF
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();

foreach ($meses as $mes => $registros) {
    $pdf->AddPage();
    $pdf->HeaderTable($mes);
    $pdf->FillTable($registros);
}

// Salida del archivo
$filename = 'MENSUALIDADES_' . $fecha_hora_actual . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
