<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Mensualidad_reporte_general.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
    }

    function Footer()
    {
        $this->SetY(-23);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function Reporte($data)
    {
        $this->AddPage();
    
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('REPORTE GENERAL DE MENSUALIDADES'), 0, 1, 'C');
        $this->Ln(5);
    
        // Anchos de columnas
        $columnWidths = [10, 80, 30, 30, 40];
    
        // Encabezados centrados
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($columnWidths[0], 10, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell($columnWidths[1], 10, utf8_decode('MES'), 1, 0, 'C');
        $this->Cell($columnWidths[2], 10, utf8_decode('DEUDORES'), 1, 0, 'C');
        $this->Cell($columnWidths[3], 10, utf8_decode('PAGOS'), 1, 0, 'C');
        $this->Cell($columnWidths[4], 10, utf8_decode('MONTO PAGADO'), 1, 1, 'C');
    
        $this->SetFont('Arial', '', 10);
    
        $i = 1;
        foreach ($data as $row) {
            $this->Cell($columnWidths[0], 8, $i++, 1, 0, 'C');
            $this->Cell($columnWidths[1], 8, utf8_decode($row['nombre_mes']), 1, 0, 'C');
            $this->Cell($columnWidths[2], 8, $row['deudor'], 1, 0, 'C');
            $this->Cell($columnWidths[3], 8, $row['cancelado'], 1, 0, 'C');
            $this->Cell($columnWidths[4], 8, 'S/. ' . number_format($row['suma_cancelado'], 2), 1, 1, 'C');
        }
    }
}

// Obtener los datos
$modelo = new Mensualidad_reporte_general();
$result = $modelo->listar();

// Convertir resultado a array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if (empty($data)) {
    die("No se encontraron datos para generar el reporte.");
}

// Crear el PDF
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->Reporte($data);

// Nombre del archivo
$filename = 'Reporte_Mensualidades.pdf';

// Salida del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
