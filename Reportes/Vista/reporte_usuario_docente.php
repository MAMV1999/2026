<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/reporte_usuario_docente.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;

        // Márgenes más ajustados
        $this->SetMargins(3, 5, 3);
        $this->SetAutoPageBreak(true, 10);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, utf8_decode('LISTADO DE DOCENTES'), 0, 1, 'C');

        $this->Ln(1);
        $this->HeaderTable();
    }

    function Footer()
    {
        $this->SetY(-8);
        $this->SetFont('Arial', 'I', 7);

        $this->Cell(0, 4, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'L');
        $this->Cell(0, 4, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable()
    {
        $this->SetFont('Arial', 'B', 5.5);
        $this->SetFillColor(188, 188, 188);

        $this->Cell(7, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(7, 8, utf8_decode('DOC'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('NÚMERO'), 1, 0, 'C', true);
        $this->Cell(73, 8, utf8_decode('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('NAC.'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('E. CIVIL'), 1, 0, 'C', true);
        $this->Cell(18, 8, utf8_decode('SEXO'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('TEL.'), 1, 0, 'C', true);
        $this->Cell(18, 8, utf8_decode('CARGO'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('T. CONTRATO'), 1, 0, 'C', true);
        $this->Cell(16, 8, utf8_decode('F. INICIO'), 1, 0, 'C', true);
        $this->Cell(16, 8, utf8_decode('F. FIN'), 1, 0, 'C', true);
        $this->Cell(34, 8, utf8_decode('T. LABORADO'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('SUELDO'), 1, 0, 'C', true);
        $this->Cell(7, 8, utf8_decode('EST.'), 1, 1, 'C', true);
    }

    private function safeText($text)
    {
        if ($text === null) {
            return '';
        }
        return trim((string)$text);
    }

    private function fitText($text, $width)
    {
        $text = $this->safeText($text);

        if ($text === '') {
            return '';
        }

        $maxWidth = $width - 1;

        if ($this->GetStringWidth(utf8_decode($text)) <= $maxWidth) {
            return $text;
        }

        $suffix = '...';

        while ($text !== '' && $this->GetStringWidth(utf8_decode($text . $suffix)) > $maxWidth) {
            $text = mb_substr($text, 0, mb_strlen($text, 'UTF-8') - 1, 'UTF-8');
        }

        return $text . $suffix;
    }

    public function FillTable($rows)
    {
        $this->SetFont('Arial', '', 5);
        $contador = 1;

        foreach ($rows as $row) {

            $estado_texto = ((int)$row['estado'] === 1) ? 'ACT' : 'INA';

            $this->Cell(7, 5, $contador, 1, 0, 'C');
            $this->Cell(7, 5, utf8_decode($this->fitText($row['documento_nombre'], 7)), 1, 0, 'C');
            $this->Cell(15, 5, utf8_decode($this->fitText($row['numerodocumento'], 15)), 1, 0, 'C');
            $this->Cell(73, 5, utf8_decode($this->fitText($row['nombreyapellido'], 73)), 1, 0, 'L');
            $this->Cell(15, 5, utf8_decode($this->fitText($row['nacimiento'], 15)), 1, 0, 'C');
            $this->Cell(15, 5, utf8_decode($this->fitText($row['estado_civil_nombre'], 15)), 1, 0, 'C');
            $this->Cell(18, 5, utf8_decode($this->fitText($row['sexo_nombre'], 18)), 1, 0, 'C');
            $this->Cell(15, 5, utf8_decode($this->fitText($row['telefono'], 15)), 1, 0, 'C');
            $this->Cell(18, 5, utf8_decode($this->fitText($row['cargo_nombre'], 18)), 1, 0, 'C');
            $this->Cell(20, 5, utf8_decode($this->fitText($row['tipo_contrato_nombre'], 20)), 1, 0, 'C');
            $this->Cell(16, 5, utf8_decode($this->fitText($row['fechainicio'], 16)), 1, 0, 'C');
            $this->Cell(16, 5, utf8_decode($this->fitText($row['fechafin'], 16)), 1, 0, 'C');
            $this->Cell(34, 5, utf8_decode($this->fitText($row['tiempo_laborado'], 34)), 1, 0, 'L');
            $this->Cell(15, 5, utf8_decode($this->fitText($row['sueldo'], 15)), 1, 0, 'R');
            $this->Cell(7, 5, utf8_decode($estado_texto), 1, 1, 'C');

            $contador++;
        }
    }
}

// ==========================
// 1) OBTENER DATOS DEL MODELO
// ==========================
$modelo = new Reportedocente();
$resultado = $modelo->listar();

if (!$resultado) {
    die("Error al obtener los datos.");
}

$rows = [];
while ($row = $resultado->fetch_assoc()) {
    $rows[] = $row;
}

// ==========================
// 2) GENERAR PDF
// ==========================
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->FillTable($rows);

$filename = 'Reporte_Docentes.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);