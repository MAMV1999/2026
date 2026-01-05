<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/almacen_producto.php");

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
        $this->Cell(0, 10, 'LISTADO DE PRODUCTOS', 0, 1, 'C');
        $this->Ln(3);
        $this->HeaderTable();
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
        $this->Cell(80, 10, utf8_decode('NOMBRE PRODUCTO'), 1, 0, 'C', true);
        $this->Cell(40, 10, utf8_decode('CATEGORIA'), 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('STOCK'), 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('P. DE VENTA'), 1, 0, 'C', true);
        $this->Cell(0, 10, utf8_decode('ESTADO'), 1, 1, 'C', true);
    }

    function FillTable($results)
    {
        $this->SetFont('Arial', '', 7);
        $contador = 1;
        foreach ($results as $row) {
            $this->Cell(8, 5, $contador, 1, 0, 'C');
            $this->Cell(80, 5, utf8_decode($row['nombre_producto']), 1, 0, 'C');
            $this->Cell(40, 5, utf8_decode($row['categoria']), 1, 0, 'C');
            $this->Cell(20, 5, utf8_decode($row['stock']), 1, 0, 'C');
            $this->Cell(20, 5, utf8_decode('S/. '.$row['precio_venta']), 1, 0, 'C');
            $this->Cell(0, 5, utf8_decode($row['estado_texto']), 1, 1, 'C');

            $contador++;
        }
    }
}

// Obtener los datos
$modelo = new Almacenproducto();
$resultDetalle = $modelo->listar();

if (!$resultDetalle) {
    die("Error al obtener los datos del detalle.");
}

$rowsDetalle = [];
while ($row = $resultDetalle->fetch_assoc()) {
    $rowsDetalle[] = $row;
}

// Generar el PDF
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();


$pdf->FillTable($rowsDetalle);

$filename = 'Recibo_Matricula.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
