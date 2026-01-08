<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/reporte_usuario_alumno.php");

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
        $this->Cell(0, 7, utf8_decode('LISTADO ALUMNOS'), 0, 1, 'C');

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
        $this->Cell(55, 8, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('DOC.'), 1, 0, 'C', true);
        $this->Cell(22, 8, utf8_decode('NRO DOC'), 1, 0, 'C', true);
        $this->Cell(70, 8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(22, 8, utf8_decode('NAC.'), 1, 0, 'C', true);
        $this->Cell(35, 8, utf8_decode('EDAD (A/M/D)'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('SEXO'), 1, 0, 'C', true);
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

    private function edadAMD($nacimiento)
    {
        $nacimiento = $this->safeText($nacimiento);
        if ($nacimiento === '') return '0/0/0';

        // viene dd/mm/YYYY desde el SELECT
        $d1 = DateTime::createFromFormat('d/m/Y', $nacimiento);
        if (!$d1) return '0/0/0';

        $hoy = new DateTime(date('Y-m-d'));

        // Si por data viniera una fecha futura, se corrige para no romper
        if ($d1 > $hoy) {
            $tmp = $d1; $d1 = $hoy; $hoy = $tmp;
        }

        $diff = $d1->diff($hoy);
        return $diff->y . ' AÑOS ' . $diff->m . ' MESES ' . $diff->d . ' DÍAS';
    }

    public function FillTable($rows)
    {
        $this->SetFont('Arial', '', 7);
        $contador = 1;

        foreach ($rows as $row) {

            $apoderado = $this->safeText($row['apoderado_nombre']);
            $docTipo   = $this->safeText($row['documento_tipo']);
            $nroDoc    = $this->safeText($row['numerodocumento']);
            $alumno    = $this->safeText($row['alumno_nombre']);
            $nac       = $this->safeText($row['nacimiento']);
            $sexo      = $this->safeText($row['sexo']);
            $obs       = $this->safeText($row['observaciones']);

            if ($apoderado === '') $apoderado = 'SIN APODERADO';

            $edad = $this->edadAMD($nac);

            $apoderado_c = $this->clip($apoderado, 35);
            $docTipo_c   = $this->clip($docTipo, 16);
            $alumno_c    = $this->clip($alumno, 45);
            $sexo_c      = $this->clip($sexo, 12);
            $obs_c       = $this->clip($obs, 70);

            $this->Cell(8, 6, $contador, 1, 0, 'C');
            $this->Cell(55, 6, utf8_decode($apoderado_c), 1, 0, 'C');
            $this->Cell(15, 6, utf8_decode($docTipo_c), 1, 0, 'C');
            $this->Cell(22, 6, utf8_decode($nroDoc), 1, 0, 'C');
            $this->Cell(70, 6, utf8_decode($alumno_c), 1, 0, 'C');
            $this->Cell(22, 6, utf8_decode($nac), 1, 0, 'C');
            $this->Cell(35, 6, utf8_decode($edad), 1, 0, 'C');
            $this->Cell(20, 6, utf8_decode($sexo_c), 1, 0, 'C');
            $this->Cell(0, 6, utf8_decode($obs_c), 1, 1, 'C');

            $contador++;
        }
    }
}

// ==========================
// 1) OBTENER DATOS DEL MODELO
// ==========================
$modelo = new Reportealumno();
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

$filename = 'Reporte_Alumnos.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
