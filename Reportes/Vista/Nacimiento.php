<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Nacimiento.php");

// Clase personalizada para el PDF
class PDFNacimiento extends FPDF
{
    // Encabezado de la página
    function Header()
    {
        $this->SetFont('Arial', 'BU', 15);
        $this->Cell(0, 10, utf8_decode('LISTA DE CUMPLEAÑOS'), 0, 1, 'C');
        $this->Ln(2);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'PAGINA ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Instancia de la clase Nacimiento
$nacimiento = new Nacimiento();
$data = $nacimiento->listarNacimiento();

// Crear una instancia de FPDF
$pdf = new PDFNacimiento();
$pdf->SetMargins(5, 10, 5);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);


// Variable para controlar el mes actual en el PDF
$currentMonth = null;

// Recorrer los resultados
while ($row = $data->fetch_assoc()) {
    // Configurar localización en español
    setlocale(LC_TIME, 'es_ES.UTF-8');

    // Obtener el mes en español y convertirlo a mayúsculas
    $birthMonth = strtoupper(strftime('%B', strtotime($row['nacimiento'])));



    // Si es un nuevo mes, agregar una nueva página con el encabezado del mes
    if ($birthMonth !== $currentMonth) {
        if ($currentMonth !== null) {
            $pdf->AddPage();
        }
        $currentMonth = $birthMonth;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10,$currentMonth, 0, 1, 'C', false);
        $pdf->Ln(5);
        // Encabezados de la tabla
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(188, 188, 188);
        $pdf->Cell(25, 10, 'NIVEL', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'GRADO', 1, 0, 'C', true);
        $pdf->Cell(100, 10, 'ALUMNO(A)', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'FECHA', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'EDAD', 1, 1, 'C', true);
    }

    // Filas de datos
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(25, 10, utf8_decode($row['nivel']), 0, 0, 'C');
    $pdf->Cell(25, 10, utf8_decode($row['grado']), 0, 0, 'C');
    $pdf->Cell(100, 10, utf8_decode($row['nombre_alumno']), 0, 0, 'C');
    $pdf->Cell(25, 10, date('d / m', strtotime($row['nacimiento'])), 0, 0, 'C');
    $pdf->Cell(25, 10, utf8_decode($row['edad'] . ' AÑOS'), 0, 1, 'C');
}

// Salida del PDF
$pdf->Output('I', 'Nacimientos_Por_Mes.pdf');
