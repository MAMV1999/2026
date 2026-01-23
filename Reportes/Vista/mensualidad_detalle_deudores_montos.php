<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/mensualidad_detalle_deudores.php");

$pdf = new FPDF(); // PORTRAIT (vertical)
$pdf->SetMargins(5, 5, 5);
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

// Acumuladores
$total_mes = 0;
$totales_por_mes = []; // ["MARZO" => 250.00, ...]

// Función para imprimir encabezado de tabla
function encabezado_tabla($pdf) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(200, 220, 255);

    // Anchos (A4: 210mm - márgenes 10mm = 200mm útiles)
    $pdf->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C', true);
    $pdf->Cell(18, 8, utf8_decode('CÓDIGO'), 1, 0, 'C', true);
    $pdf->Cell(55, 8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
    $pdf->Cell(55, 8, utf8_decode('APODERADO'), 1, 0, 'C', true);
    $pdf->Cell(18, 8, utf8_decode('TELÉFONO'), 1, 0, 'C', true);
    $pdf->Cell(18, 8, utf8_decode('MONTO'), 1, 0, 'C', true);
    $pdf->Cell(28, 8, utf8_decode('ESTADO'), 1, 1, 'C', true);
}

// Función para cortar texto a X caracteres
function limitar_texto($texto, $limite = 35) {
    return mb_strimwidth($texto, 0, $limite, "...", "UTF-8");
}

// Función para imprimir fila total del mes
function imprimir_total_mes($pdf, $mes, $total_mes) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(230, 230, 230);

    // 8+18+55+55+18 = 154
    $pdf->Cell(154, 7, utf8_decode('TOTAL ' . $mes), 1, 0, 'R', true);

    // MONTO 18
    $pdf->Cell(18, 7, number_format($total_mes, 2, '.', ','), 1, 0, 'C', true);

    // ESTADO 28
    $pdf->Cell(28, 7, '', 1, 1, 'C', true);
}

while ($fila = $data->fetch_assoc()) {

    // Monto (asegurar numérico)
    $monto = isset($fila['detalle_monto']) ? (float)$fila['detalle_monto'] : 0;

    // Si cambia el mes
    if ($mes_actual != $fila['mensualidad_mes_nombre']) {

        // Antes de cambiar, imprimir total del mes anterior (si ya había uno)
        if ($mes_actual != '') {
            imprimir_total_mes($pdf, $mes_actual, $total_mes);
            $pdf->Ln(2);
        }

        $mes_actual = $fila['mensualidad_mes_nombre'];
        $numero = 1;
        $total_mes = 0;

        // Título del mes
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 8, utf8_decode("MES: " . $mes_actual), 0, 1, 'L');

        encabezado_tabla($pdf);
        $pdf->SetFont('Arial', '', 8);
    }

    // Control simple de salto de página
    if ($pdf->GetY() > 270) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 8, utf8_decode("MES: " . $mes_actual), 0, 1, 'L');
        encabezado_tabla($pdf);
        $pdf->SetFont('Arial', '', 8);
    }

    $alumno_nombre = limitar_texto($fila['alumno_nombre'], 30);
    $apoderado_nombre = limitar_texto($fila['apoderado_nombre'], 30);

    // Acumular total del mes
    $total_mes += $monto;

    $pdf->Cell(8, 7, $numero++, 1, 0, 'C');
    $pdf->Cell(18, 7, utf8_decode($fila['alumno_codigo']), 1, 0, 'C');
    $pdf->Cell(55, 7, utf8_decode($alumno_nombre), 1, 0, 'C');
    $pdf->Cell(55, 7, utf8_decode($apoderado_nombre), 1, 0, 'C');
    $pdf->Cell(18, 7, utf8_decode($fila['apoderado_telefono']), 1, 0, 'C');
    $pdf->Cell(18, 7, number_format($monto, 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(28, 7, utf8_decode($fila['detalle_estado_pago']), 1, 1, 'C');

    // Guardar/actualizar total por mes (en el orden que viene del SELECT)
    if (!isset($totales_por_mes[$mes_actual])) {
        $totales_por_mes[$mes_actual] = 0;
    }
    $totales_por_mes[$mes_actual] += $monto;
}

// Al finalizar el while, imprimir total del último mes (si hubo datos)
if ($mes_actual != '') {
    imprimir_total_mes($pdf, $mes_actual, $total_mes);
}

// Resumen final
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode('RESUMEN GENERAL POR MES'), 0, 1, 'C');
$pdf->Ln(2);

// Encabezado resumen
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(100, 8, utf8_decode('MES'), 1, 0, 'C', true);
$pdf->Cell(100, 8, utf8_decode('TOTAL (S/)'), 1, 1, 'C', true);

// Filas resumen
$pdf->SetFont('Arial', '', 9);
$gran_total = 0;
foreach ($totales_por_mes as $mes => $total) {
    $gran_total += (float)$total;
    $pdf->Cell(100, 7, utf8_decode($mes), 1, 0, 'C');
    $pdf->Cell(100, 7, number_format((float)$total, 2, '.', ','), 1, 1, 'C');
}

// Gran total
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(100, 8, utf8_decode('TOTAL GENERAL'), 1, 0, 'C', true);
$pdf->Cell(100, 8, number_format($gran_total, 2, '.', ','), 1, 1, 'C', true);

$pdf->Output();
?>
