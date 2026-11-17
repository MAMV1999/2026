<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/biblioteca_retiro_recibo.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('Código de retiro no válido.');
}

$modelo = new ReporteBibliotecaRecibo();
$cabecera = $modelo->mostrar($id);
if (!$cabecera) {
    http_response_code(404);
    exit('No se encontró el retiro solicitado.');
}

$rsDetalle = $modelo->listar_detalle($id);
$detalle = [];
while ($reg = $rsDetalle->fetch_assoc()) {
    $detalle[] = $reg;
}

class PDFReciboBiblioteca extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 9, utf8_decode('RECIBO DE RETIRO DE LIBROS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode('BIBLIOTECA'), 0, 1, 'C');
        $this->Ln(4);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 8, utf8_decode('Documento generado por el sistema'), 0, 0, 'C');
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

    function Row($data, $widths)
    {
        $nb = 1;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($widths[$i], utf8_decode((string)$data[$i])));
        }
        $height = 6 * $nb;

        if ($this->GetY() + $height > 270) {
            $this->AddPage();
            $this->TableHeader();
        }

        for ($i = 0; $i < count($data); $i++) {
            $w = $widths[$i];
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $height);
            $this->MultiCell($w, 6, utf8_decode((string)$data[$i]), 0, $i == 2 ? 'C' : 'L');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($height);
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(32, 8, utf8_decode('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(118, 8, utf8_decode('DESCRIPCIÓN DEL LIBRO'), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('CANTIDAD'), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 9);
    }
}

$pdf = new PDFReciboBiblioteca('P', 'mm', 'A4');
$pdf->SetMargins(15, 12, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode('N° DE RETIRO: ' . $cabecera['codigo_retiro']), 0, 1, 'R');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('DOCENTE:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, utf8_decode($cabecera['docente']), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('TELÉFONO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$telefono = !empty($cabecera['telefono']) ? $cabecera['telefono'] : 'NO REGISTRADO';
$pdf->Cell(65, 7, utf8_decode($telefono), 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 7, utf8_decode('FECHA:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, utf8_decode($cabecera['fecha']), 0, 1);

$estado = ((int)$cabecera['estado'] == 0) ? 'ANULADO' : (((int)$cabecera['devuelto'] == 1) ? 'DEVUELTO' : 'PENDIENTE DE DEVOLUCIÓN');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('ESTADO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(65, 7, utf8_decode($estado), 0, 0);
if (!empty($cabecera['fecha_devolucion'])) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(35, 7, utf8_decode('DEVOLUCIÓN:'), 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 7, utf8_decode($cabecera['fecha_devolucion']), 0, 1);
} else {
    $pdf->Ln(7);
}
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont('Arial', '', 9);
foreach ($detalle as $row) {
    $pdf->Row([$row['codigo'], $row['libro'], $row['cantidad']], [32, 118, 25]);
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 7, utf8_decode('OBSERVACIONES:'), 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 6, utf8_decode($cabecera['observaciones'] ? $cabecera['observaciones'] : 'Sin observaciones.'), 1, 'L');

$filename = 'Recibo_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $cabecera['codigo_retiro']) . '.pdf';
if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
$pdf->Output('I', $filename);
?>