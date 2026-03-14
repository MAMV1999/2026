<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Nacimiento.php");

class PDFNacimiento extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'BU', 15);
        $this->Cell(0, 10, utf8_decode('LISTA DE CUMPLEAÑOS'), 0, 1, 'C');
        $this->Ln(2);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo(), 0, 0, 'C');
    }

    function mesEspanol($numeroMes)
    {
        $meses = [
            1 => 'ENERO',
            2 => 'FEBRERO',
            3 => 'MARZO',
            4 => 'ABRIL',
            5 => 'MAYO',
            6 => 'JUNIO',
            7 => 'JULIO',
            8 => 'AGOSTO',
            9 => 'SETIEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVIEMBRE',
            12 => 'DICIEMBRE'
        ];

        return isset($meses[(int)$numeroMes]) ? $meses[(int)$numeroMes] : 'SIN MES';
    }

    function encabezadoGrupo($row)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 5, 'NIVEL:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode($row['nivel']), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 5, 'GRADO:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode($row['grado']), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 5, utf8_decode('SECCIÓN:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode($row['seccion']), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 5, 'TUTOR:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, utf8_decode($row['tutor']), 0, 'L');

        $this->Ln(3);
    }

    function encabezadoTabla()
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(188, 188, 188);

        $this->Cell(90, 8, utf8_decode('ALUMNO(A)'), 1, 0, 'C', true);
        $this->Cell(28, 8, utf8_decode('CUMPLEAÑOS'), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('EDAD'), 1, 0, 'C', true);
        $this->Cell(0, 8, utf8_decode('EDAD DETALLADA'), 1, 1, 'C', true);
    }

    function verificarSalto($alto = 8)
    {
        if ($this->GetY() + $alto > 270) {
            $this->AddPage();
        }
    }
}

$nacimiento = new Nacimiento();
$data = $nacimiento->listarNacimiento();

$pdf = new PDFNacimiento('P', 'mm', 'A4');
$pdf->SetMargins(8, 10, 8);
$pdf->SetAutoPageBreak(true, 15);

$grupoActual = '';
$mesActual = null;

while ($row = $data->fetch_assoc()) {

    $grupoNuevo = $row['institucion'] . '|' .
                  $row['nivel'] . '|' .
                  $row['grado'] . '|' .
                  $row['seccion'] . '|' .
                  $row['tutor'];

    // Si cambia el grupo, crear nueva página
    if ($grupoNuevo !== $grupoActual) {
        $pdf->AddPage();
        $pdf->encabezadoGrupo($row);

        $grupoActual = $grupoNuevo;
        $mesActual = null;
    }

    // Obtener nombre del mes en español desde el número
    $nombreMes = $pdf->mesEspanol($row['mes_nacimiento']);

    // Si cambia el mes dentro del mismo grupo
    if ($nombreMes !== $mesActual) {
        $pdf->verificarSalto(15);

        $mesActual = $nombreMes;

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(220, 230, 241);
        $pdf->Cell(0, 8, utf8_decode($mesActual), 1, 1, 'C', true);
        $pdf->encabezadoTabla();
    }

    $pdf->SetFont('Arial', '', 9);

    $pdf->verificarSalto(8);

    $pdf->Cell(90, 8, utf8_decode($row['alumno']), 1, 0, 'C');
    $pdf->Cell(28, 8, utf8_decode($row['cumpleanios']), 1, 0, 'C');
    $pdf->Cell(25, 8, utf8_decode($row['edad']), 1, 0, 'C');
    $pdf->Cell(0, 8, utf8_decode($row['edad_detallada']), 1, 1, 'C');
}

$pdf->Output('I', 'Lista_Cumpleanios.pdf');
?>