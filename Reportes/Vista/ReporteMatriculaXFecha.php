<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/ReporteMatriculaXFecha.php");

class PDF extends FPDF
{
    public $totalesMetodosPorDia = [];
    public $currentFecha = '';
    protected $fecha_hora_actual;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 15);

        // Concatenar el título con la fecha actual
        $titulo = 'PAGOS AGRUPADOS POR FECHA';
        if (!empty($this->currentFecha)) {
            $titulo .= ' - ' . $this->currentFecha;
        }

        $this->Cell(277, 7, strtoupper($titulo), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('PÁGINA ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(74.79, 10, strtoupper('APODERADO'), 1, 0, 'C', true);
        $this->Cell(97, 10, strtoupper('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(25.2, 10, utf8_decode(strtoupper('TELEF.')), 1, 0, 'C', true);
        $this->Cell(25.2, 10, strtoupper('FECHA'), 1, 0, 'C', true);
        $this->Cell(16.94, 10, strtoupper('MONTO'), 1, 0, 'C', true);
        $this->Cell(0, 10, strtoupper('METODO'), 1, 0, 'C', true);
        $this->Ln();
    }

    function TableRow($row)
    {
        $this->SetFont('Arial', '', 8);

        $maxHeight = max(
            $this->NbLines(74.79, utf8_decode($row['apoderado'])),
            $this->NbLines(97, utf8_decode($row['alumno'])),
            $this->NbLines(25.2, utf8_decode($row['apoderado_telefono'])),
            $this->NbLines(25.2, $row['pago_fecha']),
            $this->NbLines(16.94, $row['pago_monto']),
            $this->NbLines(37.5, utf8_decode($row['metodo_pago']))
        ) * 5;

        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell(74.79, 5, utf8_decode($row['apoderado']), 1, 'C', false);
        $this->SetXY($x + 74.79, $y);

        $this->MultiCell(97, 5, utf8_decode($row['alumno']), 1, 'C', false);
        $this->SetXY($x + 171.79, $y);

        $this->MultiCell(25.2, 5, utf8_decode($row['apoderado_telefono']), 1, 'C', false);
        $this->SetXY($x + 196.99, $y);

        $this->MultiCell(25.2, 5, $row['pago_fecha'], 1, 'C', false);
        $this->SetXY($x + 222.19, $y);

        $this->MultiCell(16.94, 5, 'S/. ' . $row['pago_monto'], 1, 'C', false);
        $this->SetXY($x + 239.13, $y);

        $this->MultiCell(0, 5, utf8_decode($row['metodo_pago']), 1, 'C', false);
        $this->SetXY($x + 276.63, $y);

        $this->Ln(max($y + $maxHeight - $this->GetY(), 5));

        if (!isset($this->totalesMetodosPorDia[$this->currentFecha])) {
            $this->totalesMetodosPorDia[$this->currentFecha] = [];
        }
        if (!isset($this->totalesMetodosPorDia[$this->currentFecha][$row['metodo_pago']])) {
            $this->totalesMetodosPorDia[$this->currentFecha][$row['metodo_pago']] = 0;
        }
        $this->totalesMetodosPorDia[$this->currentFecha][$row['metodo_pago']] += $row['pago_monto'];
    }

    function TotalesMetodosPago()
    {
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(138.5, 10, strtoupper('Totales por Metodo de Pago'), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 9);

        $totalGeneral = 0;
        if (isset($this->totalesMetodosPorDia[$this->currentFecha])) {
            foreach ($this->totalesMetodosPorDia[$this->currentFecha] as $metodo => $total) {
                $this->Cell(101.5, 7, utf8_decode($metodo), 1, 0, 'L');
                $this->Cell(37, 7, 'S/. ' . number_format($total, 2), 1, 1, 'R');
                $totalGeneral += $total;
            }
        }

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(101.5, 10, strtoupper('Total'), 1, 0, 'L', true);
        $this->Cell(37, 10, 'S/. ' . number_format($totalGeneral, 2), 1, 1, 'R', true);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
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
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$reporte = new ReporteMatricula();
$datos = $reporte->matriculadospagos();

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

// Crear el PDF
$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();

$fechaActual = '';

while ($row = $datos->fetch_assoc()) {
    if ($pdf->PageNo() == 0) {
        $pdf->currentFecha = $row['pago_fecha']; // Inicializa la fecha para la primera página
        $pdf->AddPage();
        $pdf->TableHeader();
    }

    if ($row['pago_fecha'] !== $fechaActual && $fechaActual !== '') {
        $pdf->TotalesMetodosPago();

        // Actualizar la fecha antes de agregar una nueva página
        $pdf->currentFecha = $row['pago_fecha'];
        $pdf->totalesMetodosPorDia = [];
        $pdf->AddPage();
        $pdf->TableHeader();
    }

    $pdf->TableRow($row);
    $fechaActual = $row['pago_fecha'];
}

if (!empty($fechaActual)) {
    $pdf->TotalesMetodosPago();
}

$pdf->Output();
