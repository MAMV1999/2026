<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/institucion_estructura.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;
    private $anchos = array(70, 30, 55, 55, 45);

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(10, 8, 10);
        $this->SetAutoPageBreak(true, 15);
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 8, utf8_decode('REPORTE DE ESTRUCTURA ACADÉMICA'), 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(188, 188, 188);
        $this->SetX(($this->GetPageWidth() - array_sum($this->anchos)) / 2);
        $this->Cell($this->anchos[0], 8, utf8_decode('INSTITUCIÓN'), 1, 0, 'C', true);
        $this->Cell($this->anchos[1], 8, utf8_decode('LECTIVO'), 1, 0, 'C', true);
        $this->Cell($this->anchos[2], 8, utf8_decode('NIVEL'), 1, 0, 'C', true);
        $this->Cell($this->anchos[3], 8, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell($this->anchos[4], 8, utf8_decode('SECCIÓN'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 5, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 0, 'L');
        $this->Cell(0, 5, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    private function fitText($text, $width)
    {
        $text = trim((string)$text);
        $maxWidth = $width - 2;
        if ($this->GetStringWidth(utf8_decode($text)) <= $maxWidth) {
            return $text;
        }
        while ($text !== '' && $this->GetStringWidth(utf8_decode($text . '...')) > $maxWidth) {
            $text = mb_substr($text, 0, mb_strlen($text, 'UTF-8') - 1, 'UTF-8');
        }
        return $text . '...';
    }

    // Dibuja una celda alta equivalente a rowspan y centra el texto en ambos ejes.
    private function celdaAgrupada($x, $y, $ancho, $alto, $texto)
    {
        $this->Rect($x, $y, $ancho, $alto);
        $this->SetXY($x, $y + ($alto - 6) / 2);
        $this->Cell($ancho, 6, utf8_decode($this->fitText($texto, $ancho)), 0, 0, 'C');
    }

    private function clave($fila, $columnas)
    {
        $partes = array();
        foreach ($columnas as $columna) {
            $partes[] = (string)$fila[$columna];
        }
        return implode(':', $partes);
    }

    public function FillTable($rows)
    {
        $this->SetFont('Arial', '', 7);
        $campos = array('institucion', 'lectivo', 'nivel', 'grado');
        $claves = array(
            array('institucion_id'),
            array('institucion_id', 'lectivo_id'),
            array('institucion_id', 'lectivo_id', 'nivel_id'),
            array('institucion_id', 'lectivo_id', 'nivel_id', 'grado_id')
        );
        $altoFila = 7;
        $total = count($rows);
        $inicio = 0;

        while ($inicio < $total) {
            // Cada página tiene su propio encabezado; no se extiende un rowspan entre páginas.
            $yInicial = $this->GetY();
            $filasPagina = max(1, (int)floor((190 - $yInicial) / $altoFila));
            $fin = min($total, $inicio + $filasPagina);
            $xInicial = ($this->GetPageWidth() - array_sum($this->anchos)) / 2;

            for ($i = $inicio; $i < $fin; $i++) {
                $yFila = $yInicial + ($i - $inicio) * $altoFila;
                $fila = $rows[$i];
                $x = $xInicial;

                foreach ($campos as $indice => $campo) {
                    $claveActual = $this->clave($fila, $claves[$indice]);
                    $claveAnterior = $i > $inicio ? $this->clave($rows[$i - 1], $claves[$indice]) : null;

                    if ($i == $inicio || $claveActual !== $claveAnterior) {
                        $cantidad = 1;
                        while ($i + $cantidad < $fin &&
                               $this->clave($rows[$i + $cantidad], $claves[$indice]) === $claveActual) {
                            $cantidad++;
                        }
                        $this->celdaAgrupada($x, $yFila, $this->anchos[$indice], $altoFila * $cantidad, $fila[$campo]);
                    }
                    $x += $this->anchos[$indice];
                }

                $this->SetXY($x, $yFila);
                $textoSeccion = $this->fitText($fila['seccion'], $this->anchos[4]);
                $this->Cell($this->anchos[4], $altoFila, utf8_decode($textoSeccion), 1, 0, 'C');
            }

            $filasImpresas = $fin - $inicio;
            $inicio = $fin;
            // Posicionar al final del bloque impreso antes de pasar de página.
            $this->SetXY($xInicial, $yInicial + $filasImpresas * $altoFila);
            if ($inicio < $total) {
                $this->AddPage();
            }
        }
    }
}

$modelo = new ReporteInstitucionEstructura();
$resultado = $modelo->listar();
$rows = [];
while ($row = $resultado->fetch_assoc()) {
    $rows[] = $row;
}

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');
$pdf = new PDF('L', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->FillTable($rows);

$filename = 'Reporte_Estructura_Academica.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
$pdf->Output('I', $filename);
?>
