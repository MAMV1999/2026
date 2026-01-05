<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Matriculados_cantidad.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
    }

    function Footer()
    {
        $this->SetY(-23);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function Reporte($data)
    {
        $this->AddPage();

        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, utf8_decode('CANTIDAD DE ALUMNOS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, utf8_decode($this->fecha_hora_actual), 0, 1, 'C');
        $this->Ln(5);

        // Ancho de cada columna calculado para ocupar todo el margen
        $columnWidths = [38, 38, 38, 38, 38]; // 190mm / 5 columnas = 38mm por columna

        // Encabezados
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($columnWidths[0], 10, utf8_decode('LECTIVO'), 1, 0, 'C');
        $this->Cell($columnWidths[1], 10, utf8_decode('NIVEL'), 1, 0, 'C');
        $this->Cell($columnWidths[2], 10, utf8_decode('GRADO'), 1, 0, 'C');
        $this->Cell($columnWidths[3], 10, utf8_decode('SECCIÓN'), 1, 0, 'C');
        $this->Cell($columnWidths[4], 10, utf8_decode('CANTIDAD'), 1, 1, 'C');

        $this->SetFont('Arial', '', 10);

        // Variables para seguimiento de valores anteriores
        $prevLectivo = '';
        $prevNivel = '';
        $prevGrado = '';
        $totalCantidad = 0; // Para acumular el total de la columna "CANTIDAD"

        // Contar las ocurrencias de cada lectivo, nivel y grado
        $lectivoCounts = array_count_values(array_column($data, 'lectivo'));
        $nivelCounts = [];
        $gradoCounts = [];
        foreach ($data as $row) {
            $nivelCounts[$row['lectivo']][$row['nivel']] = isset($nivelCounts[$row['lectivo']][$row['nivel']]) ? $nivelCounts[$row['lectivo']][$row['nivel']] + 1 : 1;
            $gradoCounts[$row['lectivo']][$row['nivel']][$row['grado']] = isset($gradoCounts[$row['lectivo']][$row['nivel']][$row['grado']]) ? $gradoCounts[$row['lectivo']][$row['nivel']][$row['grado']] + 1 : 1;
        }

        foreach ($data as $row) {
            // Unificar celda de lectivo
            if ($row['lectivo'] !== $prevLectivo) {
                $prevLectivo = $row['lectivo'];
                $rowspan = $lectivoCounts[$row['lectivo']] * 8;
                $this->Cell($columnWidths[0], $rowspan, utf8_decode($row['lectivo']), 1, 0, 'C');
            } else {
                $this->SetX($this->GetX() + $columnWidths[0]);
            }

            // Unificar celda de nivel
            if ($row['nivel'] !== $prevNivel || $row['lectivo'] !== $prevLectivo) {
                $prevNivel = $row['nivel'];
                $rowspan = $nivelCounts[$row['lectivo']][$row['nivel']] * 8;
                $this->Cell($columnWidths[1], $rowspan, utf8_decode($row['nivel']), 1, 0, 'C');
            } else {
                $this->SetX($this->GetX() + $columnWidths[1]);
            }

            // Unificar celda de grado
            if ($row['grado'] !== $prevGrado || $row['nivel'] !== $prevNivel || $row['lectivo'] !== $prevLectivo) {
                $prevGrado = $row['grado'];
                $rowspan = $gradoCounts[$row['lectivo']][$row['nivel']][$row['grado']] * 8;
                $this->Cell($columnWidths[2], $rowspan, utf8_decode($row['grado']), 1, 0, 'C');
            } else {
                $this->SetX($this->GetX() + $columnWidths[2]);
            }

            // Dibujar seccion y cantidad
            $this->Cell($columnWidths[3], 8, utf8_decode($row['seccion']), 1, 0, 'C');
            $this->Cell($columnWidths[4], 8, utf8_decode($row['cantidad_alumnos']), 1, 1, 'C');

            // Sumar al total de la columna "CANTIDAD"
            $totalCantidad += $row['cantidad_alumnos'];
        }

        // Mostrar total en una fila final
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($columnWidths[0] + $columnWidths[1] + $columnWidths[2] + $columnWidths[3], 10, utf8_decode('TOTAL'), 1, 0, 'C');
        $this->Cell($columnWidths[4], 10, utf8_decode($totalCantidad), 1, 1, 'C');
    }
}

// Crear instancia del modelo y obtener datos
$modelo = new InstitucionLectivo();
$result = $modelo->listarLectivoNivelGradoSeccion();

// Convertir los resultados a un array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if (empty($data)) {
    die("No se encontraron datos para generar el reporte.");
}

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

// Crear el PDF
$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->Reporte($data);

// Definir el nombre del archivo
$filename = 'Cantidad_de_Matriculados.pdf';

// Salida del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
