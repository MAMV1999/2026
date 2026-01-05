<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/ReporteMatriculaXApoderado.php");

class PDF extends FPDF
{
    public $totalesPorMetodo = [];

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode(strtoupper('PAGOS AGRUPADOS POR APODERADO')), 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
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
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('PARENTESCO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('DOCUMENTO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('N° DOCUMENTO')), 1, 0, 'C', true);
        $this->Cell(100, 8, utf8_decode(strtoupper('NOMBRE Y APELLIDO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('TELEFONO')), 1, 0, 'C', true);
        $this->Ln();
    }

    // Filas de apoderado
    function ApoderadoRow($row)
    {
        $ancho_celda = 44.25;
        $this->SetFont('Arial', '', 8);
        $this->Cell($ancho_celda, 8, utf8_decode($row['apoderado_tipo']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['apoderado_documento']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['apoderado_dni']), 1, 0, 'C');
        $this->Cell(100, 8, utf8_decode($row['apoderado_nombre']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['apoderado_telefono']), 1, 0, 'C');
        $this->Ln();
    }

    // Encabezado para alumnos
    function AlumnosHeader()
    {
        $ancho_celda = 25.285;
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(0, 8, utf8_decode(strtoupper('DATOS DEL ALUMNO')), 1, 0, 'C', true);
        $this->Ln();
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('LECTIVO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('NIVEL')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('GRADO')), 1, 0, 'C', true);
        $this->Cell(100, 8, utf8_decode(strtoupper('NOMBRE Y APELLIDO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('N° RECIBO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('FECHA')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('MONTO')), 1, 0, 'C', true);
        $this->Cell($ancho_celda, 8, utf8_decode(strtoupper('METODO')), 1, 0, 'C', true);
        $this->Ln();
    }

    // Filas de alumnos
    function AlumnosRow($row)
    {
        $ancho_celda = 25.285;
        $this->SetFont('Arial', '', 8);
        $this->Cell($ancho_celda, 8, utf8_decode($row['institucion_lectivo']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['institucion_nivel']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['institucion_grado']), 1, 0, 'C');
        $this->Cell(100, 8, utf8_decode($row['alumno_nombre']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['pago_numeracion']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['pago_fecha']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode('S/. ' . $row['pago_monto']), 1, 0, 'C');
        $this->Cell($ancho_celda, 8, utf8_decode($row['metodo_pago']), 1, 0, 'C');
        $this->Ln();

        // Acumular totales por método
        if (!isset($this->totalesPorMetodo[$row['metodo_pago']])) {
            $this->totalesPorMetodo[$row['metodo_pago']] = 0;
        }
        $this->totalesPorMetodo[$row['metodo_pago']] += $row['pago_monto'];
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

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('PÁGINA ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}

// Crear objeto del modelo
$reporte = new ReporteMatricula();
$datos = $reporte->matriculadospagos_apoderado();

// Crear PDF
$pdf = new PDF('L', 'mm', 'A4');
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
