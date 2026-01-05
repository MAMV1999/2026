<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Mensualidad_reporte_bcp_id.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, utf8_decode('REPORTE DE MENSUALIDAD FORMATO BCP'), 0, 1, 'C');
        $this->Ln(2);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 8, utf8_decode('CÓDIGO'), 1, 0, 'C');
        $this->Cell(70, 8, utf8_decode('DEPOSITANTE'), 1, 0, 'C');
        $this->Cell(60, 8, utf8_decode('RETORNO'), 1, 0, 'C');
        $this->Cell(20, 8, utf8_decode('F. EMISIÓN'), 1, 0, 'C');
        $this->Cell(20, 8, utf8_decode('F. VENCIMI.'), 1, 0, 'C');
        $this->Cell(18, 8, utf8_decode('MONTO'), 1, 0, 'C');
        $this->Cell(18, 8, utf8_decode('MORA'), 1, 0, 'C');
        $this->Cell(18, 8, utf8_decode('M. MÍNIMO'), 1, 0, 'C');
        $this->Cell(15, 8, utf8_decode('REG.'), 1, 0, 'C');
        $this->Cell(0, 8, utf8_decode('DOCUMENTO'), 1, 1, 'C');
    }
}

// Crear el PDF en horizontal con márgenes reducidos
$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5);           // Márgenes izquierdo, superior y derecho reducidos
$pdf->SetAutoPageBreak(true, 5);     // Pie de página a 5 mm del borde inferior
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7.5);

$id = $_GET['id'];

$modelo = new Mensualidadbcp();
$rspta = $modelo->listar($id);

while ($reg = $rspta->fetch_object()) {
    $pdf->Cell(20, 7, utf8_decode($reg->CODIGO), 1, 0, 'C');
    $pdf->Cell(70, 7, utf8_decode($reg->DEPOSITANTE), 1, 0, 'C');
    $pdf->Cell(60, 7, utf8_decode($reg->RETORNO), 1, 0, 'C');
    $pdf->Cell(20, 7, utf8_decode($reg->FECHA_EMISION), 1, 0, 'C');
    $pdf->Cell(20, 7, utf8_decode($reg->FECHA_VENCIMIENTO), 1, 0, 'C');
    $pdf->Cell(18, 7, number_format($reg->MONTO, 2), 1, 0, 'C');
    $pdf->Cell(18, 7, number_format($reg->MORA, 2), 1, 0, 'C');
    $pdf->Cell(18, 7, number_format($reg->MONTO_MINIMO, 2), 1, 0, 'C');
    $pdf->Cell(15, 7, utf8_decode($reg->REGISTRO), 1, 0, 'C');
    $pdf->Cell(0, 7, utf8_decode($reg->DOCUMENTO), 1, 1, 'C');
}

$pdf->Output("I", "reporte_mensualidad_bcp.pdf");
