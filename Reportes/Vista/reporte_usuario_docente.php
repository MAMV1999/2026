<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/reporte_usuario_docente.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;
    protected $tipo_contrato_actual;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->tipo_contrato_actual = '';
        $this->SetMargins(5, 5, 5);
        $this->SetAutoPageBreak(true, 15);
    }

    public function setTipoContratoActual($tipo)
    {
        $this->tipo_contrato_actual = $tipo;
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode('LISTADO DOCENTES'), 0, 1, 'C');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, utf8_decode('TIPO DE CONTRATO: ' . $this->tipo_contrato_actual), 0, 1, 'C');

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
        $this->Cell(20, 8, utf8_decode('DNI'), 1, 0, 'C', true);
        $this->Cell(70, 8, utf8_decode('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('TELÉFONO'), 1, 0, 'C', true);
        $this->Cell(70, 8, utf8_decode('DOMICILIO'), 1, 0, 'C', true);
        $this->Cell(22, 8, utf8_decode('NAC.'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('INICIO'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('FIN'), 1, 0, 'C', true);
        $this->Cell(12, 8, utf8_decode('MESES'), 1, 0, 'C', true);
        $this->Cell(0, 8, utf8_decode('CUENTA BANCARIA'), 1, 1, 'C', true);
    }

    private function safeText($text)
    {
        if ($text === null) return '';
        return trim((string)$text);
    }

    private function clip($text, $maxChars)
    {
        $text = $this->safeText($text);
        // Recorta para evitar desbordes en celdas
        if (mb_strlen($text, 'UTF-8') > $maxChars) {
            return mb_substr($text, 0, $maxChars - 1, 'UTF-8') . '…';
        }
        return $text;
    }

    private function mesesEntre($inicio, $fin)
    {
        $inicio = $this->safeText($inicio);
        $fin    = $this->safeText($fin);

        if ($inicio === '') return 0;

        // Si fin está vacío, se calcula hasta hoy
        if ($fin === '') {
            $fin = date('Y-m-d');
        }

        // Los datos vienen formateados como dd/mm/YYYY desde el SELECT (fecha_inicio/fecha_fin).
        // Convertimos a DateTime.
        $d1 = DateTime::createFromFormat('d/m/Y', $inicio);
        $d2 = DateTime::createFromFormat('d/m/Y', $fin);

        if (!$d1 || !$d2) return 0;

        // Asegurar orden
        if ($d2 < $d1) {
            $tmp = $d1; $d1 = $d2; $d2 = $tmp;
        }

        $diff = $d1->diff($d2);

        // Meses totales (años*12 + meses), y si hay días, redondea hacia arriba (contratos parciales cuentan como mes)
        $meses = ($diff->y * 12) + $diff->m;
        if ($diff->d > 0) $meses += 1;

        return $meses;
    }

    public function FillTable($rows)
    {
        $this->SetFont('Arial', '', 7);
        $contador = 1;

        foreach ($rows as $row) {

            $dni       = $this->safeText($row['numerodocumento']); // "DNI"
            $nombre    = $this->safeText($row['docente_nombre']);
            $telefono  = $this->safeText($row['telefono']);
            $domicilio = $this->safeText($row['direccion']);
            $nac       = $this->safeText($row['fecha_nacimiento']);
            $inicio    = $this->safeText($row['fecha_inicio']);
            $fin       = $this->safeText($row['fecha_fin']);
            $cuenta    = $this->safeText($row['cuentabancaria']);

            $meses = $this->mesesEntre($inicio, $fin);

            // Recortes para mantener tabla limpia
            $nombre_corto    = $this->clip($nombre, 45);
            $domicilio_corto = $this->clip($domicilio, 60);
            $cuenta_corta    = $this->clip($cuenta, 35);

            $this->Cell(8, 6, $contador, 1, 0, 'C');
            $this->Cell(20, 6, utf8_decode($dni), 1, 0, 'C');
            $this->Cell(70, 6, utf8_decode($nombre_corto), 1, 0, 'C');
            $this->Cell(20, 6, utf8_decode($telefono), 1, 0, 'C');
            $this->Cell(70, 6, utf8_decode($domicilio_corto), 1, 0, 'C');
            $this->Cell(22, 6, utf8_decode($nac), 1, 0, 'C');
            $this->Cell(20, 6, utf8_decode($inicio), 1, 0, 'C');
            $this->Cell(20, 6, utf8_decode($fin), 1, 0, 'C');
            $this->Cell(12, 6, utf8_decode((string)$meses), 1, 0, 'C');
            $this->Cell(0, 6, utf8_decode($cuenta_corta), 1, 1, 'C');

            $contador++;
        }
    }
}

// ==========================
// 1) OBTENER DATOS DEL MODELO
// ==========================
$modelo = new Reportedocente();
$resultDetalle = $modelo->listar();

if (!$resultDetalle) {
    die("Error al obtener los datos del detalle.");
}

$rows = [];
while ($row = $resultDetalle->fetch_assoc()) {
    $rows[] = $row;
}

// ======================================
// 2) AGRUPAR POR TIPO DE CONTRATO (HOJAS)
//    Y ORDENAR POR CARGO EN CADA GRUPO
// ======================================
$grupos = [];

foreach ($rows as $r) {
    $tipo = isset($r['tipo_contrato']) ? trim((string)$r['tipo_contrato']) : '';
    if ($tipo === '') $tipo = 'SIN TIPO DE CONTRATO';
    $grupos[$tipo][] = $r;
}

// Ordenar por nombre del tipo de contrato (para que las hojas salgan ordenadas)
ksort($grupos, SORT_NATURAL | SORT_FLAG_CASE);

// Ordenar cada grupo por cargo (y luego por nombre)
foreach ($grupos as $tipo => &$lista) {
    usort($lista, function ($a, $b) {
        $cargoA = isset($a['cargo']) ? trim((string)$a['cargo']) : '';
        $cargoB = isset($b['cargo']) ? trim((string)$b['cargo']) : '';

        $cmpCargo = strcasecmp($cargoA, $cargoB);
        if ($cmpCargo !== 0) return $cmpCargo;

        $nomA = isset($a['docente_nombre']) ? trim((string)$a['docente_nombre']) : '';
        $nomB = isset($b['docente_nombre']) ? trim((string)$b['docente_nombre']) : '';
        return strcasecmp($nomA, $nomB);
    });
}
unset($lista);

// ==========================
// 3) GENERAR PDF
// ==========================
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();

foreach ($grupos as $tipo => $lista) {
    $pdf->setTipoContratoActual($tipo);
    $pdf->AddPage();
    $pdf->FillTable($lista);
}

$filename = 'Reporte_Docentes.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
