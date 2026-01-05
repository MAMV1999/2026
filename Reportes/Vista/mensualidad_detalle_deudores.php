<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/mensualidad_detalle_deudores.php");

$pdf = new FPDF(); // PORTRAIT (vertical)
$pdf->SetMargins(5, 5, 5); // Márgenes ajustados: izquierda, arriba, derecha
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('REPORTE DE ALUMNOS DEUDORES'), 0, 1, 'C');
$pdf->Ln(5);

// Obtener datos
$modelo = new Mensualidad_detalle_deudores();
$data = $modelo->listar_mensualidad_detalle_deudores();

$pdf->SetFont('Arial', '', 7);
$numero = 1;
$mes_actual = '';

// Función para imprimir encabezado de tabla
function encabezado_tabla($pdf) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C', true);
    $pdf->Cell(18, 8, utf8_decode('CÓDIGO'), 1, 0, 'C', true);
    $pdf->Cell(67, 8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
    $pdf->Cell(67, 8, utf8_decode('APODERADO'), 1, 0, 'C', true);
    $pdf->Cell(18, 8, utf8_decode('TELÉFONO'), 1, 0, 'C', true);
    $pdf->Cell(0, 8, utf8_decode('ESTADO'), 1, 1, 'C', true);
}

// Función para cortar texto a 35 caracteres
function limitar_texto($texto, $limite = 35) {
    return mb_strimwidth($texto, 0, $limite, "...", "UTF-8");
}

while ($fila = $data->fetch_assoc()) {
    // Si cambia el mes, reiniciar numeración y mostrar subtítulo
    if ($mes_actual != $fila['mensualidad_mes_nombre']) {
        $mes_actual = $fila['mensualidad_mes_nombre'];
        $numero = 1;
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 8, utf8_decode("MES: " . $mes_actual), 0, 1, 'L');
        encabezado_tabla($pdf);
        $pdf->SetFont('Arial', '', 8);
    }

    $alumno_nombre = limitar_texto($fila['alumno_nombre'], 35);
    $apoderado_nombre = limitar_texto($fila['apoderado_nombre'], 35);

    $pdf->Cell(8, 7, $numero++, 1, 0, 'C');
    $pdf->Cell(18, 7, utf8_decode($fila['alumno_codigo']), 1, 0, 'C');
    $pdf->Cell(67, 7, utf8_decode($alumno_nombre), 1, 0, 'C');
    $pdf->Cell(67, 7, utf8_decode($apoderado_nombre), 1, 0, 'C');
    $pdf->Cell(18, 7, utf8_decode($fila['apoderado_telefono']), 1, 0, 'C');
    $pdf->Cell(0, 7, utf8_decode($fila['detalle_estado_pago']), 1, 1, 'C');
}

$pdf->Output();
?>
