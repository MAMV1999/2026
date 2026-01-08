<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/reporte_usuario_apoderado.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(5, 5, 5);
        $this->SetAutoPageBreak(true, 15);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode('LISTADO APODERADOS'), 0, 1, 'C');

        $this->Ln(2);
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

        $this->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(28, 8, utf8_decode('TIPO'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('DOC.'), 1, 0, 'C', true);
        $this->Cell(22, 8, utf8_decode('NRO DOC'), 1, 0, 'C', true);
        $this->Cell(90, 8, utf8_decode('NOMBRE Y APELLIDO'), 1, 0, 'C', true);
        $this->Cell(22, 8, utf8_decode('TELÉFONO'), 1, 0, 'C', true);
        $this->Cell(18, 8, utf8_decode('ALUMNOS'), 1, 0, 'C', true);
        $this->Cell(0, 8, utf8_decode('OBSERVACIONES'), 1, 1, 'C', true);
    }

    private function safeText($text)
    {
        if ($text === null) return '';
        return trim((string)$text);
    }

    private function clip($text, $maxChars)
    {
        $text = $this->safeText($text);
        if (mb_strlen($text, 'UTF-8') > $maxChars) {
            return mb_substr($text, 0, $maxChars - 1, 'UTF-8') . '…';
        }
        return $text;
    }

    public function FillTable($rows)
    {
        $this->SetFont('Arial', '', 7);
        $contador = 1;

        foreach ($rows as $row) {

            $tipo     = $this->safeText($row['apoderado_tipo']);
            $docTipo  = $this->safeText($row['documento_tipo']);
            $nroDoc   = $this->safeText($row['numerodocumento']);
            $nombre   = $this->safeText($row['apoderado_nombre']);
            $telefono = $this->safeText($row['telefono']);
            $alumnos  = $this->safeText($row['total_alumnos']);
            $obs      = $this->safeText($row['observaciones']);

            $tipo_c    = $this->clip($tipo, 18);
            $docTipo_c = $this->clip($docTipo, 18);
            $nombre_c  = $this->clip($nombre, 55);
            $obs_c     = $this->clip($obs, 80);

            $this->Cell(8, 6, $contador, 1, 0, 'C');
            $this->Cell(28, 6, utf8_decode($tipo_c), 1, 0, 'C');
            $this->Cell(15, 6, utf8_decode($docTipo_c), 1, 0, 'C');
            $this->Cell(22, 6, utf8_decode($nroDoc), 1, 0, 'C');
            $this->Cell(90, 6, utf8_decode($nombre_c), 1, 0, 'C');
            $this->Cell(22, 6, utf8_decode($telefono), 1, 0, 'C');
            $this->Cell(18, 6, utf8_decode((string)$alumnos), 1, 0, 'C');
            $this->Cell(0, 6, utf8_decode($obs_c), 1, 1, 'C');

            $contador++;
        }
    }
}

// ==========================
// 1) OBTENER DATOS DEL MODELO
// ==========================
$modelo = new Reporteapoderado();
$resultDetalle = $modelo->listar();

if (!$resultDetalle) {
    die("Error al obtener los datos del detalle.");
}

$rows = [];
while ($row = $resultDetalle->fetch_assoc()) {
    $rows[] = $row;
}

// ==========================
// 2) GENERAR PDF (SIN GRUPOS)
// ==========================
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->FillTable($rows);

$filename = 'Reporte_Apoderados.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
