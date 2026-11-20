<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/institucion_lectivo.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(8, 8, 8);
        $this->SetAutoPageBreak(true, 15);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 8, utf8_decode('REPORTE DE AÑOS LECTIVOS'), 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(55, 8, utf8_decode('INSTITUCIÓN'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('AÑO'), 1, 0, 'C', true);
        $this->Cell(125, 8, utf8_decode('NOMBRE DEL AÑO LECTIVO'), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('FECHA'), 1, 0, 'C', true);
        $this->Cell(28, 8, utf8_decode('ESTADO'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 5, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'L');
        $this->Cell(0, 5, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    private function fitText($text, $width)
    {
        $text = trim((string)$text);
        $maxWidth = $width - 1;
        if ($this->GetStringWidth(utf8_decode($text)) <= $maxWidth) {
            return $text;
        }
        while ($text !== '' && $this->GetStringWidth(utf8_decode($text . '...')) > $maxWidth) {
            $text = mb_substr($text, 0, mb_strlen($text, 'UTF-8') - 1, 'UTF-8');
        }
        return $text . '...';
    }

    public function FillTable($rows)
    {
        $this->SetFont('Arial', '', 7);
        $contador = 1;
        foreach ($rows as $row) {
            $estado = ((int)$row['estado'] === 1) ? 'ACTIVO' : 'INACTIVO';
            $this->Cell(8, 6, $contador, 1, 0, 'C');
            $this->Cell(55, 6, utf8_decode($this->fitText($row['institucion'], 55)), 1, 0, 'L');
            $this->Cell(20, 6, utf8_decode($this->fitText($row['lectivo'], 20)), 1, 0, 'C');
            $this->Cell(125, 6, utf8_decode($this->fitText($row['nombre_lectivo'], 125)), 1, 0, 'L');
            $this->Cell(25, 6, utf8_decode($row['fecha']), 1, 0, 'C');
            $this->Cell(28, 6, utf8_decode($estado), 1, 1, 'C');
            $contador++;
        }
    }
}

$modelo = new ReporteInstitucionLectivo();
$resultado = $modelo->listar();
$rows = [];
while ($row = $resultado->fetch_assoc()) {
    $rows[] = $row;
}

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');
$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->FillTable($rows);

$filename = 'Reporte_Anios_Lectivos.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
$pdf->Output('I', $filename);
?>
