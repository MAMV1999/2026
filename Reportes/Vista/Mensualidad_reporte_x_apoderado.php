<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Mensualidad_reporte_x_apoderado.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode('Reporte de Mensualidades por Apoderado'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5); // Reducir los márgenes para ajustar mejor el contenido
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$reporte = new Reportemensualidadxapoderado();
$resultados = $reporte->listar();

$apoderado_actual = null;
$contador = 1;

foreach ($resultados as $fila) {
    if ($apoderado_actual !== $fila['nombre_apoderado']) {
        if ($apoderado_actual !== null) {
            $pdf->AddPage();
        }
        $apoderado_actual = $fila['nombre_apoderado'];
        $contador = 1;

        // Tabla de Datos del Apoderado
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 10, utf8_decode('Datos del Apoderado'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 7, utf8_decode('Nombre del Apoderado:'), 1);
        $pdf->Cell(130, 7, utf8_decode($fila['nombre_apoderado']), 1, 1);
        $pdf->Cell(60, 7, utf8_decode('Tipo de Apoderado:'), 1);
        $pdf->Cell(130, 7, utf8_decode($fila['tipo_apoderado']), 1, 1);
        $pdf->Cell(60, 7, utf8_decode('Documento:'), 1);
        $pdf->Cell(130, 7, utf8_decode($fila['tipo_documento_apoderado'] . ' - ' . $fila['numero_documento_apoderado']), 1, 1);
        $pdf->Cell(60, 7, utf8_decode('Teléfono:'), 1);
        $pdf->Cell(130, 7, utf8_decode($fila['telefono_apoderado']), 1, 1);

        $pdf->Ln(5);

        // Tabla de Alumnos
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, utf8_decode('Datos de los Alumnos'), 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 9); // Reducimos el tamaño de letra para ajustarlo al margen
        $pdf->Cell(7, 6, utf8_decode('N°'), 1, 0, 'C');
        $pdf->Cell(35, 6, utf8_decode('NIVEL - GRADO'), 1, 0, 'C');
        $pdf->Cell(70, 6, utf8_decode('ALUMNO'), 1, 0, 'C');
        $pdf->Cell(19, 6, utf8_decode('CODIGO'), 1, 0, 'C');

        // Agregar los meses como títulos de columna con abreviación si tienen más de 5 letras
        $meses = explode(', ', utf8_decode($fila['meses']));
        foreach ($meses as $mes) {
            $mes_corto = (mb_strlen($mes) > 5) ? mb_substr($mes, 0, 5) . '.' : $mes;
            $pdf->Cell(15, 6, $mes_corto, 1, 0, 'C');
        }
        $pdf->Ln();
    }

    // Separar los datos de alumnos
    $nivel_grado = utf8_decode($fila['nombre_nivel'] . ' - ' . $fila['nombre_grado']);
    $documento_alumno = utf8_decode($fila['numero_documento_alumno']);
    // Limitar el nombre del alumno a 40 caracteres
    $nombre_alumno = utf8_decode(mb_substr($fila['nombre_alumno'], 0, 35));
    $montos = explode(', ', utf8_decode($fila['montos']));
    $estados = explode(', ', utf8_decode($fila['estados_pago']));

    $pdf->SetFont('Arial', '', 8); // Reducimos tamaño de letra para mantener todo dentro del margen
    $pdf->Cell(7, 6, $contador, 1, 0, 'C');
    $pdf->Cell(35, 6, $nivel_grado, 1, 0, 'C');
    $pdf->Cell(70, 6, $nombre_alumno, 1, 0, 'C');
    $pdf->Cell(19, 6, $documento_alumno, 1, 0, 'C');

    // Agregar los montos en las columnas correspondientes a los meses

    foreach ($montos as $idx => $monto) {
        $estado = isset($estados[$idx]) ? $estados[$idx] : 0;
    
        if ($estado == 0) {
            // Deuda → celda gris y sin monto
            $pdf->SetFillColor(200,200,200);
            $pdf->Cell(15, 6, $monto, 1, 0, 'C', true);
        } else {
            // Pagado → celda en blanco (sin monto, sin color)
            $pdf->Cell(15, 6, $monto, 1, 0, 'C', false);
        }
    }


    $pdf->Ln();
    $contador++;
}

$pdf->Output();
