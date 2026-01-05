<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Reporte_salida.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;
    protected $fecha_actual_pagina;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(5, 5, 5); // Márgenes estrictos de 5 mm
    }

    function setFechaActualPagina($fecha)
    {
        $this->fecha_actual_pagina = $fecha;
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'REPORTE DE VENTAS', 0, 1, 'C');
        if ($this->fecha_actual_pagina) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10,utf8_decode($this->fecha_actual_pagina), 0, 1, 'C');
        }
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
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(90, 10, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(19, 10, utf8_decode('COMPR.'), 1, 0, 'C', true);
        $this->Cell(19, 10, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(19, 10, utf8_decode('FECHA'), 1, 0, 'C', true);
        $this->Cell(19, 10, utf8_decode('MONTO'), 1, 0, 'C', true);
        $this->Cell(0, 10, utf8_decode('METODO'), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 8);
    }

    function FillTable($rowsByDate)
    {
        foreach ($rowsByDate as $fecha => $rows) {
            $this->setFechaActualPagina($fecha);
            $this->AddPage();
            $this->HeaderTable();
            $contador = 1;
            
            $totalesPorMetodo = [];
            $sumaTotalDia = 0;
            
            foreach ($rows as $row) {
                $metodo_pago = utf8_decode($row['metodo_pago']);
                if (strlen($metodo_pago) > 8) {
                    $metodo_pago = substr($metodo_pago, 0, 8) . '...';
                }
                
                $this->Cell(10, 6, $contador, 1, 0, 'C');
                $this->Cell(90, 6, utf8_decode($row['nombre_apoderado']), 1, 0, 'C');
                $this->Cell(19, 6, utf8_decode($row['nombre_comprobante']), 1, 0, 'C');
                $this->Cell(19, 6, utf8_decode($row['numeracion']), 1, 0, 'C');
                $this->Cell(19, 6, utf8_decode($row['fecha']), 1, 0, 'C');
                $this->Cell(19, 6, 'S/.' . number_format($row['monto'], 2), 1, 0, 'C');
                $this->Cell(0, 6, $metodo_pago, 1, 1, 'C');
                
                $totalesPorMetodo[$row['metodo_pago']] = ($totalesPorMetodo[$row['metodo_pago']] ?? 0) + $row['monto'];
                $sumaTotalDia += $row['monto'];
                
                $contador++;
            }
            
            $this->Ln(5);
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor(188, 188, 188);
            $this->Cell(50, 8, utf8_decode('MÉTODO DE PAGO'), 1, 0, 'C', true);
            $this->Cell(50, 8, utf8_decode('MONTO TOTAL (S/.)'), 1, 1, 'C', true);
            
            $this->SetFont('Arial', '', 9);
            foreach ($totalesPorMetodo as $metodo => $montoTotal) {
                $this->Cell(50, 8, utf8_decode($metodo), 1, 0, 'C');
                $this->Cell(50, 8, 'S/.' . number_format($montoTotal, 2), 1, 1, 'C');
            }
            
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor(188, 188, 188);
            $this->Cell(50, 8, 'TOTAL DEL DIA', 1, 0, 'C', true);
            $this->Cell(50, 8, 'S/.' . number_format($sumaTotalDia, 2), 1, 1, 'C', true);
        }
    }
}

// Obtener los datos
$modelo = new Reportesalida();
$resultDetalle = $modelo->listar();

if (!$resultDetalle) {
    die("Error al obtener los datos del detalle.");
}

$rowsDetalle = [];
while ($row = $resultDetalle->fetch_assoc()) {
    $rowsDetalle[] = $row;
}

$rowsByDate = [];
foreach ($rowsDetalle as $row) {
    $fecha = $row['fecha'];
    if (!isset($rowsByDate[$fecha])) {
        $rowsByDate[$fecha] = [];
    }
    $rowsByDate[$fecha][] = $row;
}

$pdf = new PDF('P', 'mm', 'A4', date('d/m/Y H:i:s'));
$pdf->AliasNbPages();
$pdf->FillTable($rowsByDate);

$filename = 'Recibo_Matricula.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
$pdf->Output('I', $filename);