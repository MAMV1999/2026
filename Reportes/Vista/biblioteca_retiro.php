<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/biblioteca_retiro.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;
    protected $anchos = [8, 35, 20, 22, 55, 127, 20];

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
        $this->Cell(0, 9, utf8_decode('REPORTE DE RETIRO DE LIBROS POR DOCENTE'), 0, 1, 'C');
        $this->Ln(2);
        $this->HeaderTable();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'L');
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function HeaderTable()
    {
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(188, 188, 188);
        $titulos = ['N°', 'CÓDIGO RETIRO', 'FECHA', 'DNI', 'DOCENTE', 'LIBROS DEL RETIRO', 'TOTAL'];
        foreach ($titulos as $i => $titulo) {
            $this->Cell($this->anchos[$i], 8, utf8_decode($titulo), 1, 0, 'C', true);
        }
        $this->Ln();
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += isset($cw[$c]) ? $cw[$c] : 0;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    function RowGrouped($data)
    {
        $this->SetFont('Arial', '', 7);
        $lineHeight = 5;
        $maxLines = 1;
        for ($i = 0; $i < count($data); $i++) {
            $maxLines = max($maxLines, $this->NbLines($this->anchos[$i], utf8_decode((string)$data[$i])));
        }
        $height = $lineHeight * $maxLines + 2;

        if ($this->GetY() + $height > 195) {
            $this->AddPage();
        }

        $alineaciones = ['C', 'C', 'C', 'C', 'L', 'L', 'C'];
        for ($i = 0; $i < count($data); $i++) {
            $x = $this->GetX();
            $y = $this->GetY();
            $w = $this->anchos[$i];
            $this->Rect($x, $y, $w, $height);
            $this->MultiCell($w, $lineHeight, utf8_decode((string)$data[$i]), 0, $alineaciones[$i]);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($height);
    }

    function FillTable($groups)
    {
        $contador = 1;
        foreach ($groups as $grupo) {
            $this->RowGrouped([
                $contador,
                $grupo['codigo_retiro'],
                $grupo['fecha'],
                $grupo['numerodocumento'],
                $grupo['docente'],
                implode("\n", $grupo['libros']),
                $grupo['cantidad_total']
            ]);
            $contador++;
        }
    }
}

$modelo = new ReporteBibliotecaRetiro();
$resultado = $modelo->listar();

// Consolidar todos los libros de cada préstamo bajo un único código de retiro.
$grupos = [];
while ($row = $resultado->fetch_assoc()) {
    $id = $row['retiro_id'];
    if (!isset($grupos[$id])) {
        $grupos[$id] = [
            'codigo_retiro' => $row['codigo_retiro'],
            'fecha' => $row['fecha'],
            'numerodocumento' => $row['numerodocumento'],
            'docente' => $row['docente'],
            'libros' => [],
            'cantidad_total' => 0
        ];
    }

    $grupos[$id]['libros'][] = $row['codigo'] . ' - ' . $row['libro'] . ' (' . $row['cantidad'] . ')';
    $grupos[$id]['cantidad_total'] += (int)$row['cantidad'];
}

$filas = array_values($grupos);

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->FillTable($filas);

$filename = 'Reporte_Biblioteca_Retiros.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
$pdf->Output('I', $filename);
?>