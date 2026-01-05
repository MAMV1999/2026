<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Reporte_salida_x_apoderado.php");

class PDF extends FPDF
{
    public $totalesPorMetodo = [];

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode(strtoupper('REPORTE DE VENTAS X APODERADO')), 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('PÁGINA ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    // Encabezado para la tabla de totales por método
    function MetodoPagoHeader()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $ancho_celda = 40;
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('METODO DE PAGO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('TOTAL PAGADO')), 1, 0, 'C', true);
        $this->Ln();
    }

    // Fila para totales por método con formato actualizado
    function MetodoPagoRow($metodo, $total)
    {
        $ancho_celda = 40;
        $this->SetFont('Arial', '', 8);
        $this->Cell($ancho_celda, 8, utf8_decode($metodo), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode('S/. ' . number_format($total, 2, '.', ',')), 1, 0, 'C');
        $this->Ln();
    }

    // Mostrar totales por método con fila de total general
    function MostrarTotalesPorMetodo()
    {
        if (!empty($this->totalesPorMetodo)) {
            $this->Ln(5);
            $this->MetodoPagoHeader();
            $totalGeneral = 0;
            foreach ($this->totalesPorMetodo as $metodo => $total) {
                $this->MetodoPagoRow($metodo, $total);
                $totalGeneral += $total;
            }
            // Agregar fila total general
            $this->SetFont('Arial', 'B', 8);
            $this->SetFillColor(188, 188, 188);
            $ancho_celda = 40;
            $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('Total General')), 1, 0, 'C', true);
            $this->Cell($ancho_celda, 8, utf8_decode('S/. ' . number_format($totalGeneral, 2, '.', ',')), 1, 0, 'C', true);
        }
    }

    // Encabezado de apoderado
    function ApoderadoHeader()
    {
        $ancho_celda = 44.25;
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(0, 8, utf8_decode(strtoupper('DATOS DEL APODERADO')), 1, 0, 'C', true);
        $this->Ln();
        $this->SetFillColor(188, 188, 188);
        $this->Cell(25, 8, utf8_decode(strtoupper('PARENTESCO')), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode(strtoupper('DOCUMENTO')), 1, 0, 'C', true);
        $this->Cell(28, 8, utf8_decode(strtoupper('N° DOCUMENTO')), 1, 0, 'C', true);
        $this->Cell(80, 8, utf8_decode(strtoupper('NOMBRE Y APELLIDO')), 1, 0, 'C', true);
        $this->Cell(0, 8, utf8_decode(strtoupper('TELEFONO')), 1, 0, 'C', true);
        $this->Ln();
    }

    // Filas de apoderado
    function ApoderadoRow($row)
    {
        $ancho_celda = 44.25;
        $this->SetFont('Arial', '', 8);
        $this->Cell(25, 6, utf8_decode($row['apoderado_tipo']), 1, 0, 'C');
        $this->Cell(25, 6, utf8_decode($row['documento_tipo']), 1, 0, 'C');
        $this->Cell(28, 6, utf8_decode($row['numerodocumento']), 1, 0, 'C');
        $this->Cell(80, 6, utf8_decode($row['apoderado_nombre']), 1, 0, 'C');
        $this->Cell(0, 6, utf8_decode($row['telefono']), 1, 0, 'C');
        $this->Ln();
    }

    // Encabezado para alumnos con columna Sub Total
    function AlumnosHeader()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(0, 8, utf8_decode(strtoupper('DATOS DE LOS PRODUCTOS')), 1, 0, 'C', true);
        $this->Ln();

        $this->Cell(20, 8, utf8_decode(strtoupper('FECHA')), 1, 0, 'C', true);
        $this->Cell(16, 8, utf8_decode(strtoupper('N°')), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode(strtoupper('CANT.')), 1, 0, 'C', true);
        $this->Cell(80, 8, utf8_decode(strtoupper('PRODUCTO')), 1, 0, 'C', true);
        $this->Cell(21, 8, utf8_decode(strtoupper('P. UNITARIO')), 1, 0, 'C', true);
        $this->Cell(21, 8, utf8_decode(strtoupper('SUB TOTAL')), 1, 0, 'C', true); // Nueva columna
        $this->Cell(0, 8, utf8_decode(strtoupper('METODO')), 1, 0, 'C', true);
        $this->Ln();
    }

    // Filas de alumnos con cálculo de Sub Total
    function AlumnosRow($row)
    {
        $this->SetFont('Arial', '', 8);
        $subtotal = $row['stock'] * $row['precio_unitario']; // Calcular el Sub Total

        $this->Cell(20, 6, utf8_decode($row['salida_fecha']), 1, 0, 'C');
        $this->Cell(16, 6, utf8_decode($row['salida_numeracion']), 1, 0, 'C');
        $this->Cell(15, 6, utf8_decode($row['stock']), 1, 0, 'C');
        $this->Cell(80, 6, utf8_decode($row['producto_nombre']), 1, 0, 'C');
        $this->Cell(21, 6, utf8_decode('S/. ' . $row['precio_unitario']), 1, 0, 'C');
        $this->Cell(21, 6, utf8_decode('S/. ' . number_format($subtotal, 2, '.', ',')), 1, 0, 'C'); // Nueva columna
        $this->Cell(0, 6, utf8_decode($row['metodo_pago']), 1, 0, 'C');
        $this->Ln();

        // Acumular totales por método (se suma el subtotal en lugar de precio unitario)
        if (!isset($this->totalesPorMetodo[$row['metodo_pago']])) {
            $this->totalesPorMetodo[$row['metodo_pago']] = 0;
        }
        $this->totalesPorMetodo[$row['metodo_pago']] += $subtotal;
    }
}

// Crear objeto del modelo
$reporte = new Reportesalidaxapoderado();
$datos = $reporte->listar();

// Crear PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();

// Inicializar apoderado actual
$apoderadoActual = '';
while ($row = $datos->fetch_assoc()) {
    if ($row['apoderado_nombre'] !== $apoderadoActual) {
        if (!empty($apoderadoActual)) {
            $pdf->MostrarTotalesPorMetodo();
        }
        $pdf->AddPage();
        $pdf->ApoderadoHeader();
        $pdf->ApoderadoRow($row);
        $pdf->Ln(5);
        $pdf->AlumnosHeader();
        $pdf->totalesPorMetodo = [];
    }
    $pdf->AlumnosRow($row);
    $apoderadoActual = $row['apoderado_nombre'];
}

// Mostrar los totales del último apoderado
$pdf->MostrarTotalesPorMetodo();

$pdf->Output();
