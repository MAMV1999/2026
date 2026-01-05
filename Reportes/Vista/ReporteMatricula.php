<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/ReporteMatricula.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'LISTADO DE ALUMNOS', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function ChapterTitle($lectivo, $nivel, $grado, $seccion)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(188, 188, 188);
        $totalWidth = $this->GetPageWidth() - 20;
        $cellWidth = $totalWidth / 4;

        $this->Cell($cellWidth, 9, utf8_decode('LECTIVO'), 1, 0, 'C', true);
        $this->Cell($cellWidth, 9, utf8_decode('NIVEL'), 1, 0, 'C', true);
        $this->Cell($cellWidth, 9, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell($cellWidth, 9, utf8_decode('SECCION'), 1, 1, 'C', true);

        $this->Cell($cellWidth, 9, utf8_decode($lectivo), 1, 0, 'C', false);
        $this->Cell($cellWidth, 9, utf8_decode($nivel), 1, 0, 'C', false);
        $this->Cell($cellWidth, 9, utf8_decode($grado), 1, 0, 'C', false);
        $this->Cell($cellWidth, 9, utf8_decode($seccion), 1, 1, 'C', false);
        $this->Ln(10);
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(10, 10, '#', 0, 0, 'C');
        $this->Cell(120, 10, 'APELLIDO Y NOMBRE', 0, 0, 'L');
        $this->Cell(30, 10, 'GENERO', 0, 0, 'L');
        $this->Cell(30, 10, 'MATRICULA', 0, 1, 'L');
    }

    function TableRow($row, $num)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 8, $num, 0, 0, 'C');

        if ($row['alumno'] === 'SIN ALUMNOS') {
            $this->SetFont('Arial', 'I', 12); // Cursiva para resaltar
        }

        $this->Cell(120, 8, utf8_decode($row['alumno']), 0, 0, 'L');
        $this->Cell(30, 8, utf8_decode($row['sexo']), 0, 0, 'L');
        $this->Cell(30, 8, utf8_decode($row['categoria']), 0, 0, 'L');
        $this->Ln();
    }

    function TableSummary($countFemenino, $countMasculino)
    {
        $this->Ln(10); // Espacio entre la tabla de alumnos y el resumen
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'FEMENINO: ' . $countFemenino, 0, 1, 'L');
        $this->Cell(0, 5, 'MASCULINO: ' . $countMasculino, 0, 1, 'L');
    }
}

$reporte = new ReporteMatricula();
$datos = $reporte->listar();

$pdf = new PDF();
$pdf->AliasNbPages();

$currentGrado = '';
$currentNivel = '';
$currentLectivo = '';
$currentSeccion = '';
$counter = 1;
$countFemenino = 0;
$countMasculino = 0;

while ($row = $datos->fetch_assoc()) {
    // Si hay un cambio de sección, imprimimos la tabla de resumen
    if ($row['grado'] !== $currentGrado || $row['nivel'] !== $currentNivel || $row['lectivo'] !== $currentLectivo || $row['seccion'] !== $currentSeccion) {
        if ($counter > 1) {
            // Solo imprimimos la tabla de resumen si ya hemos contado estudiantes
            $pdf->TableSummary($countFemenino, $countMasculino);
        }

        // Agregar nueva página para la nueva sección
        $pdf->AddPage();
        $currentGrado = $row['grado'];
        $currentNivel = $row['nivel'];
        $currentLectivo = $row['lectivo'];
        $currentSeccion = $row['seccion'];

        // Reiniciar contadores para el nuevo grupo
        $countFemenino = 0;
        $countMasculino = 0;

        // Imprimir título y encabezado
        $pdf->ChapterTitle($row['lectivo'], $row['nivel'], $row['grado'], $row['seccion']);
        $pdf->TableHeader();
        $counter = 1;
    }

    // Contar alumnos por género
    if (strtoupper($row['sexo']) === 'FEMENINO') {
        $countFemenino++;
    } elseif (strtoupper($row['sexo']) === 'MASCULINO') {
        $countMasculino++;
    }

    // Imprimir fila de datos
    $pdf->TableRow($row, $counter);
    $counter++;
}

// Imprimir la última tabla de resumen después de salir del bucle
if ($counter > 1) {
    $pdf->TableSummary($countFemenino, $countMasculino);
}

$pdf->Output();
