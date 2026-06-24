<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/listado_alumnado.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(5, 5, 5);
        $this->SetAutoPageBreak(true, 15);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, utf8_decode('LISTADO GENERAL DE ALUMNOS'), 0, 1, 'C');
        $this->Ln(2);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 6);

        $this->Cell(0, 10, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'C');

        $this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable()
    {
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(188, 188, 188);

        $this->Cell(7, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('NIVEL'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('SEC.'), 1, 0, 'C', true);
        $this->Cell(58, 8, utf8_decode('NOMBRE ALUMNO'), 1, 0, 'C', true);
        $this->Cell(58, 8, utf8_decode('NOMBRE APODERADO'), 1, 0, 'C', true);
        $this->Cell(22, 8, utf8_decode('TELÉFONO'), 1, 1, 'C', true);
    }

    function Texto($texto)
    {
        if ($texto == null) {
            return '';
        }

        return utf8_decode($texto);
    }

    function CellFitScale($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $fit = false;

        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $txt_width = $this->GetStringWidth($txt);

        if ($txt_width > 0) {
            $ratio = ($w - 1) / $txt_width;

            if ($ratio < 1) {
                $fit = true;
                $horiz_scale = $ratio * 100.0;
                $this->_out(sprintf('BT %.2F Tz ET', $horiz_scale));
            }
        }

        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);

        if ($fit) {
            $this->_out('BT 100 Tz ET');
        }
    }

    function FillTable($results)
    {
        $this->Ln(2);
        $this->HeaderTable();
        $this->SetFont('Arial', '', 6);
        $contador = 1;

        foreach ($results as $row) {
            $this->CellFitScale(7, 5, $contador, 1, 0, 'C');
            $this->CellFitScale(20, 5, $this->Texto($row['nivel']), 1, 0, 'C');
            $this->CellFitScale(20, 5, $this->Texto($row['grado']), 1, 0, 'C');
            $this->CellFitScale(15, 5, $this->Texto($row['seccion']), 1, 0, 'C');
            $this->CellFitScale(58, 5, $this->Texto($row['alumno_nombre']), 1, 0, 'C');
            $this->CellFitScale(58, 5, $this->Texto($row['apoderado_nombre']), 1, 0, 'C');
            $this->CellFitScale(22, 5, $this->Texto($row['apoderado_telefono']), 1, 1, 'C');

            $contador++;
        }
    }
}

// Obtener los datos
$modelo = new Listado_general_alumnos();
$resultDetalle = $modelo->listar();

if (!$resultDetalle) {
    die("Error al obtener los datos del listado general de alumnos.");
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

$filename = 'Listado_General_Alumnos.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);