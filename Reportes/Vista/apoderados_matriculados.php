<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/apoderados_matriculados.php");

class PDF extends FPDF
{
    // Cabecera del PDF
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('REPORTE DE NUMEROS TELEFONICOS'), 0, 1, 'C');
        $this->Ln(5);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Instanciar PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$apoderado = new Apoderadosmatriculados();
$rspta = $apoderado->listar();

$pdf->Cell(10, 8, utf8_decode('N°'), 1, 0, 'C');
$pdf->Cell(30, 8, utf8_decode('TIPO'), 1, 0, 'C');
$pdf->Cell(120, 8, utf8_decode('NOMBRE Y APELLIDO'), 1, 0, 'C');
$pdf->Cell(0, 8, utf8_decode('TELÉFONO'), 1, 1, 'C');

$contador = 1;
while ($reg = $rspta->fetch_object()) {
    $pdf->Cell(10, 8, utf8_decode($contador), 1, 0, 'C');
    $pdf->Cell(30, 8, utf8_decode($reg->tipo), 1, 0, 'C');
    $pdf->Cell(120, 8, utf8_decode($reg->nombreyapellido), 1, 0, 'C');
    $pdf->Cell(0, 8, utf8_decode($reg->telefono), 1, 1, 'C');
    $contador++;
}

$pdf->Output("I", "reporte_num_telefono.pdf");
?>
