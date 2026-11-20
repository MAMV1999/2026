<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/institucion.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(4, 5, 4);
        $this->SetAutoPageBreak(true, 12);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, utf8_decode('REPORTE DE INSTITUCIONES'), 0, 1, 'C');
        $this->Ln(1);
        $this->HeaderTable();
    }

    function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 5, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'L');
        $this->Cell(0, 5, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable()
    {
        $this->SetFont('Arial', 'B', 6.5);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(45, 8, utf8_decode('INSTITUCIÓN'), 1, 0, 'C', true);
        $this->Cell(45, 8, utf8_decode('DIRECTOR'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('TELÉFONO'), 1, 0, 'C', true);
        $this->Cell(42, 8, utf8_decode('CORREO'), 1, 0, 'C', true);
        $this->Cell(23, 8, utf8_decode('RUC'), 1, 0, 'C', true);
        $this->Cell(45, 8, utf8_decode('RAZÓN SOCIAL'), 1, 0, 'C', true);
        $this->Cell(40, 8, utf8_decode('DIRECCIÓN'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('ESTADO'), 1, 1, 'C', true);
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
        $this->SetFont('Arial', '', 6);
        $contador = 1;
        foreach ($rows as $row) {
            $estado = ((int)$row['estado'] === 1) ? 'ACTIVO' : 'INACTIVO';
            $this->Cell(8, 6, $contador, 1, 0, 'C');
            $this->Cell(45, 6, utf8_decode($this->fitText($row['nombre'], 45)), 1, 0, 'L');
            $this->Cell(45, 6, utf8_decode($this->fitText($row['director'], 45)), 1, 0, 'L');
            $this->Cell(20, 6, utf8_decode($this->fitText($row['telefono'], 20)), 1, 0, 'C');
            $this->Cell(42, 6, utf8_decode($this->fitText($row['correo'], 42)), 1, 0, 'L');
            $this->Cell(23, 6, utf8_decode($this->fitText($row['ruc'], 23)), 1, 0, 'C');
            $this->Cell(45, 6, utf8_decode($this->fitText($row['razon_social'], 45)), 1, 0, 'L');
            $this->Cell(40, 6, utf8_decode($this->fitText($row['direccion'], 40)), 1, 0, 'L');
            $this->Cell(15, 6, utf8_decode($estado), 1, 1, 'C');
            $contador++;
        }
    }
}

$modelo = new ReporteInstitucion();
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

$filename = 'Reporte_Instituciones.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
$pdf->Output('I', $filename);
?>
