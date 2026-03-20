<?php
ob_start();

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/ReporteAlumnosTutores.php");

class PDF extends FPDF
{
    protected $fecha_actual;
    protected $currentSubTitle;

    public function __construct($fecha_actual)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->fecha_actual = $fecha_actual;
        $this->SetMargins(5, 5, 5);
        $this->SetAutoPageBreak(true, 15);
        $this->currentSubTitle = '';
    }

    public function setCurrentSubTitle($text)
    {
        $this->currentSubTitle = $text;
    }

    private function fechaEnTexto($fecha)
    {
        $dias = [
            1 => 'UNO', 2 => 'DOS', 3 => 'TRES', 4 => 'CUATRO', 5 => 'CINCO',
            6 => 'SEIS', 7 => 'SIETE', 8 => 'OCHO', 9 => 'NUEVE', 10 => 'DIEZ',
            11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE', 15 => 'QUINCE',
            16 => 'DIECISEIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE', 20 => 'VEINTE',
            21 => 'VEINTIUNO', 22 => 'VEINTIDOS', 23 => 'VEINTITRES', 24 => 'VEINTICUATRO', 25 => 'VEINTICINCO',
            26 => 'VEINTISEIS', 27 => 'VEINTISIETE', 28 => 'VEINTIOCHO', 29 => 'VEINTINUEVE', 30 => 'TREINTA',
            31 => 'TREINTA Y UNO'
        ];

        $meses = [
            '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL',
            '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'
        ];

        $partes = explode('/', $fecha);

        $dia = isset($partes[0]) ? intval($partes[0]) : 1;
        $mes = isset($partes[1]) ? $partes[1] : '01';

        return $dias[$dia] . ' DE ' . $meses[$mes];
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode('REUNIÓN DE APODERADOS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $fechaTexto = $this->fechaEnTexto(date('d/m/Y'));
        $this->Cell(0, 5, utf8_decode($fechaTexto), 0, 1, 'C');
        $this->Ln(1);

        if (!empty($this->currentSubTitle)) {
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(0, 5, utf8_decode($this->currentSubTitle), 0, 'L');
            $this->Ln(2);
        }

        $this->HeaderTable();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('FECHA Y HORA: ' . $this->fecha_actual), 0, 0, 'L');
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);

        $this->Cell(10, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(75, 8, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(75, 8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(0, 8, utf8_decode('FIRMA'), 1, 1, 'C', true);
    }

    function PrintRow($row, $contador)
    {
        $this->SetFont('Arial', '', 8);

        $this->Cell(10, 13, $contador, 1, 0, 'C');
        $this->Cell(75, 13, utf8_decode($row['apoderado']), 1, 0, 'L');
        $this->Cell(75, 13, utf8_decode($row['alumno']), 1, 0, 'L');
        $this->Cell(0, 13, '', 1, 1, 'C');
    }
}

date_default_timezone_set('America/Lima');
$fecha_actual = date('d/m/Y H:i:s');

$modelo = new Reportealumnostutores();
$result = $modelo->listar();

if (!$result) {
    ob_end_clean();
    die("Error al obtener los datos.");
}

$rows = [];
while ($fila = $result->fetch_assoc()) {
    $rows[] = $fila;
}

usort($rows, function ($a, $b) {
    $grupoA = $a['lectivo'] . '|' . $a['nivel'] . '|' . $a['grado'] . '|' . $a['seccion'] . '|' . $a['docente'];
    $grupoB = $b['lectivo'] . '|' . $b['nivel'] . '|' . $b['grado'] . '|' . $b['seccion'] . '|' . $b['docente'];
    return strcmp($grupoA, $grupoB);
});

$pdf = new PDF($fecha_actual);
$pdf->AliasNbPages();

$lastGroup = '';
$contador = 1;

foreach ($rows as $row) {
    $currentGroup = $row['lectivo'] . '|' . $row['nivel'] . '|' . $row['grado'] . '|' . $row['seccion'] . '|' . $row['docente'];

    if ($lastGroup != $currentGroup) {
        $subtitulo =
            "LECTIVO: " . $row['lectivo'] . "\n" .
            "NIVEL: " . $row['nivel'] . "\n" .
            "GRADO: " . $row['grado'] . "\n" .
            "SECCIÓN: " . $row['seccion'] . "\n" .
            "DOCENTE: " . $row['docente'];

        $pdf->setCurrentSubTitle($subtitulo);
        $pdf->AddPage();

        $contador = 1;
        $lastGroup = $currentGroup;
    }

    $pdf->PrintRow($row, $contador);
    $contador++;
}

ob_end_clean();
$pdf->Output('I', 'Reporte_Alumnos_Tutores.pdf');
exit;
?>