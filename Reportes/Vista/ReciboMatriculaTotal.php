<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/ReciboMatriculaTotal.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(5, 5, 5); // Márgenes estrictos de 5 mm
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'LISTADO DETALLE MATRICULADOS', 0, 1, 'C');
        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'C');
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable()
    {
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(8, 10, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(15, 10, utf8_decode('NIVEL'), 1, 0, 'C', true);
        $this->Cell(15, 10, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell(22, 10, utf8_decode('MATRICULA'), 1, 0, 'C', true);
        $this->Cell(65, 10, utf8_decode('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(65, 10, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(18, 10, utf8_decode('TELEFONO'), 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('FECHA'), 1, 0, 'C', true);
        $this->Cell(17, 10, utf8_decode('N° RECIBO'), 1, 0, 'C', true);
        $this->Cell(15, 10, utf8_decode('MONTO'), 1, 0, 'C', true);
        $this->Cell(0, 10, utf8_decode('MÉTODO'), 1, 1, 'C', true);
    }

    function FillTable($results)
    {
        $this->SetFont('Arial', '', 7);
        $contador = 1;
        foreach ($results as $row) {
            $this->Cell(8, 6, $contador, 1, 0, 'C');
            $this->Cell(15, 6, utf8_decode($row['nivel']), 1, 0, 'C');
            $this->Cell(15, 6, utf8_decode($row['grado']), 1, 0, 'C');
            $this->Cell(22, 6, utf8_decode($row['categoria_matricula']), 1, 0, 'C');
            $this->Cell(65, 6, utf8_decode($row['nombre_alumno']), 1, 0, 'C');
            $this->Cell(65, 6, utf8_decode($row['nombre_apoderado']), 1, 0, 'C');
            $this->Cell(18, 6, utf8_decode($row['telefono_apoderado']), 1, 0, 'C');
            $this->Cell(20, 6, utf8_decode($row['fecha']), 1, 0, 'C');
            $this->Cell(17, 6, utf8_decode('N° ' . $row['numeracion']), 1, 0, 'C');
            $this->Cell(15, 6, 'S/.' . number_format($row['monto'], 2), 1, 0, 'C');
            $this->Cell(0, 6, utf8_decode($row['metodo_pago']), 1, 1, 'C');

            $contador++;
        }
    }

    function FillGroupedTableFromArray($totalesPorMetodo, $sumaGeneral)
    {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(90, 10, utf8_decode('MÉTODO DE PAGO'), 1, 0, 'C', true);
        $this->Cell(50, 10, utf8_decode('MONTO TOTAL (S/.)'), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);

        foreach ($totalesPorMetodo as $metodo => $montoTotal) {
            $this->Cell(90, 7, utf8_decode($metodo), 1, 0, 'C');
            $this->Cell(50, 7, 'S/.' . number_format($montoTotal, 2), 1, 1, 'C');
        }

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(90, 10, 'TOTAL GENERAL', 1, 0, 'C', true);
        $this->Cell(50, 10, 'S/.' . number_format($sumaGeneral, 2), 1, 1, 'C', true);
    }
}

// Obtener los datos
$modelo = new ReciboMatriculaTotal();
$resultDetalle = $modelo->listarReciboMatriculaTotal();

if (!$resultDetalle) {
    die("Error al obtener los datos del detalle.");
}

$rowsDetalle = [];
while ($row = $resultDetalle->fetch_assoc()) {
    $rowsDetalle[] = $row;
}

// Calcular totales por método de pago y suma general
$totalesPorMetodo = [];
$sumaGeneral = 0;

foreach ($rowsDetalle as $row) {
    $metodo = $row['metodo_pago'];
    $monto = floatval($row['monto']);
    $sumaGeneral += $monto;

    if (!isset($totalesPorMetodo[$metodo])) {
        $totalesPorMetodo[$metodo] = 0;
    }
    $totalesPorMetodo[$metodo] += $monto;
}

// Generar el PDF
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->HeaderTable();
// Primera tabla: Detalle
$pdf->FillTable($rowsDetalle);

// Segunda tabla: Resumen por método de pago
$pdf->FillGroupedTableFromArray($totalesPorMetodo, $sumaGeneral);

// Salida del archivo
$filename = 'Recibo_Matricula.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
