<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Reporte_salida_x_producto.php");

class PDF extends FPDF
{
    public $totalesPorMetodo = [];
    public $contador = 1; // Para numeración de filas

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode(strtoupper('REPORTE DE VENTAS X PRODUCTO')), 0, 1, 'C');
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

    function MetodoPagoHeader()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $ancho_celda = 40;
        $this->Cell($ancho_celda, 7, utf8_decode(strtoupper('METODO DE PAGO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 7, utf8_decode(strtoupper('TOTAL PAGADO')), 1, 0, 'C', true);
        $this->Ln();
    }

    function MetodoPagoRow($metodo, $total)
    {
        $ancho_celda = 40;
        $this->SetFont('Arial', '', 8);
        $this->Cell($ancho_celda, 6, utf8_decode($metodo), 1, 0, 'C');
        $this->Cell($ancho_celda, 6, utf8_decode('S/. ' . number_format($total, 2, '.', ',')), 1, 0, 'C');
        $this->Ln();
    }

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
            $this->SetFont('Arial', 'B', 8);
            $this->SetFillColor(188, 188, 188);
            $ancho_celda = 40;
            $this->Cell($ancho_celda, 7, utf8_decode(strtoupper('Total General')), 1, 0, 'C', true);
            $this->Cell($ancho_celda, 7, utf8_decode('S/. ' . number_format($totalGeneral, 2, '.', ',')), 1, 0, 'C', true);
        }
    }

    function ApoderadoHeader()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(0, 8, utf8_decode(strtoupper('PRODUCTO')), 1, 0, 'C', true);
        $this->Ln();
    }

    function ApoderadoRow($row)
    {
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 6, utf8_decode($row['producto_nombre']), 1, 0, 'C');
        $this->Ln();
    }

    function AlumnosHeader()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(0, 8, utf8_decode(strtoupper('DATOS DE LOS PRODUCTOS')), 1, 0, 'C', true);
        $this->Ln();

        $this->Cell(9, 8, utf8_decode(strtoupper('N°')), 1, 0, 'C', true);
        $this->Cell(70, 8, utf8_decode(strtoupper('APODERADO')), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode(strtoupper('FECHA')), 1, 0, 'C', true);
        $this->Cell(16, 8, utf8_decode(strtoupper('N°')), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode(strtoupper('CANT.')), 1, 0, 'C', true);
        $this->Cell(21, 8, utf8_decode(strtoupper('P. UNITARIO')), 1, 0, 'C', true);
        $this->Cell(21, 8, utf8_decode(strtoupper('SUB TOTAL')), 1, 0, 'C', true);
        $this->Cell(0, 8, utf8_decode(strtoupper('METODO')), 1, 0, 'C', true);
        $this->Ln();
    }

    function AlumnosRow($row)
    {
        $this->SetFont('Arial', '', 8);
        $subtotal = $row['stock'] * $row['precio_unitario'];
        $apoderado = substr($row['apoderado_nombre'], 0, 38);

        $this->Cell(9, 6, $this->contador, 1, 0, 'C'); // Numeración
        $this->Cell(70, 6, utf8_decode($apoderado), 1, 0, 'C');
        $this->Cell(20, 6, utf8_decode($row['salida_fecha']), 1, 0, 'C');
        $this->Cell(16, 6, utf8_decode($row['salida_numeracion']), 1, 0, 'C');
        $this->Cell(15, 6, utf8_decode($row['stock']), 1, 0, 'C');
        $this->Cell(21, 6, utf8_decode('S/. ' . $row['precio_unitario']), 1, 0, 'C');
        $this->Cell(21, 6, utf8_decode('S/. ' . number_format($subtotal, 2, '.', ',')), 1, 0, 'C');

        $metodo = substr($row['metodo_pago'], 0, 8); // Limitar a 8 caracteres
        $this->Cell(0, 6, utf8_decode($metodo), 1, 0, 'C');
        $this->Ln();

        if (!isset($this->totalesPorMetodo[$row['metodo_pago']])) {
            $this->totalesPorMetodo[$row['metodo_pago']] = 0;
        }
        $this->totalesPorMetodo[$row['metodo_pago']] += $subtotal;

        $this->contador++; // Incrementar número
    }
}

// Crear objeto del modelo
$reporte = new Reportesalidaxproducto();
$datos = $reporte->listar();

// Crear PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();

$apoderadoActual = '';
while ($row = $datos->fetch_assoc()) {
    if ($row['producto_nombre'] !== $apoderadoActual) {
        if (!empty($apoderadoActual)) {
            $pdf->MostrarTotalesPorMetodo();
        }
        $pdf->AddPage();
        $pdf->ApoderadoHeader();
        $pdf->ApoderadoRow($row);
        $pdf->Ln(5);
        $pdf->AlumnosHeader();
        $pdf->totalesPorMetodo = [];
        $pdf->contador = 1; // Reiniciar numeración por producto
    }
    $pdf->AlumnosRow($row);
    $apoderadoActual = $row['producto_nombre'];
}

// Mostrar los totales del último producto
$pdf->MostrarTotalesPorMetodo();

$pdf->Output();
