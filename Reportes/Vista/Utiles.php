<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Utiles.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    exit('Alumno no válido.');
}

$modelo = new ReporteUtiles();
$info = $modelo->mostrar($id);

if (!$info) {
    http_response_code(404);
    exit('No se encontró la información del alumno.');
}

$rsDetalle = $modelo->listar_detalle($id);
$detalle = [];
while ($reg = $rsDetalle->fetch_assoc()) {
    $detalle[] = $reg;
}

class PDFUtiles extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 9, utf8_decode('REPORTE DE ÚTILES ESCOLARES'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode('LISTADO DE ÚTILES ESCOLARES DEL ALUMNO'), 0, 1, 'C');
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

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(15, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(90, 8, utf8_decode('ÚTIL ESCOLAR'), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('CANT.'), 1, 0, 'C', true);
        $this->Cell(45, 8, utf8_decode('OBSERVACIONES'), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 9);
    }

    function Row($numero, $util, $stock, $observaciones)
    {
        $widths = [15, 90, 25, 45];
        $data = [
            (string)$numero,
            utf8_decode((string)$util),
            (string)$stock,
            utf8_decode((string)$observaciones)
        ];

        $nb = 1;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
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

            if ((int)$stock === 0 && ($i == 2 || $i == 3)) {
                $this->SetFillColor(235, 235, 235);
                $this->Rect($x, $y, $w, $height, 'DF');
            } else {
                $this->Rect($x, $y, $w, $height);
            }

            $alineacion = ($i == 0 || $i == 2) ? 'C' : 'L';
            $this->MultiCell($w, 6, $data[$i], 0, $alineacion);
            $this->SetXY($x + $w, $y);
        }

        $this->Ln($height);
    }
}

$pdf = new PDFUtiles('P', 'mm', 'A4');
$pdf->SetMargins(17, 12, 18);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('AULA:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$aula = $info['lectivo'] . ' / ' . $info['nivel'] . ' / ' . $info['grado'] . ' / ' . $info['seccion'];
$pdf->Cell(0, 7, utf8_decode($aula), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('ALUMNO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, utf8_decode($info['alumno_nombre']), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('APODERADO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, utf8_decode($info['apoderado_nombre']), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('TELÉFONO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$telefono = !empty($info['apoderado_telefono']) ? $info['apoderado_telefono'] : 'NO REGISTRADO';
$pdf->Cell(0, 7, utf8_decode($telefono), 0, 1);

$pdf->Ln(5);
$pdf->TableHeader();

$pdf->SetFont('Arial', '', 9);

if (count($detalle) > 0) {
    $numero = 1;
    foreach ($detalle as $row) {
        $observaciones = !empty($row['observaciones']) ? $row['observaciones'] : '';
        $pdf->Row($numero, $row['util'], $row['stock'], $observaciones);
        $numero++;
    }
} else {
    $pdf->Cell(175, 9, utf8_decode('NO HAY ÚTILES REGISTRADOS PARA ESTA MATRÍCULA'), 1, 1, 'C');
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('TOTAL DE ÍTEMS:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, (string)count($detalle), 0, 1);

$filename = 'Utiles_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $info['alumno_nombre']) . '.pdf';

if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
?>
