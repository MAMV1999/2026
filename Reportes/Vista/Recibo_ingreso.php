<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Recibo_ingreso.php");

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
        $this->Cell(0, 5, utf8_decode('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 1, 'C');
        $this->Cell(0, 10, utf8_decode('PÁGINA ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function ReciboSalida($info, $data, $productos)
    {
        $this->AddPage();

        // Título
        $this->SetFont('Arial', 'B', 30);
        $this->Cell(0, 13, utf8_decode($info['institucion_nombre']), 0, 1, 'C');
        $this->Ln(3);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 5, utf8_decode($info['institucion_razon_social'] . ' ' . $info['institucion_ruc']), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode($info['institucion_direccion']), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('TELEFONO: ' . $info['institucion_telefono']), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('CORREO: ' . $info['institucion_correo']), 0, 1, 'C');
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 8, utf8_decode($data['comprobante_nombre'].' N° ' . $data['salida_numeracion']), 0, 1, 'C');
        $this->Ln(5);

        // Información general
        $this->SectionTitle('INFORMACIÓN GENERAL');
        $this->SectionData('APODERADO', $data['apoderado_nombre']);
        $this->SectionData('TELEFONO', $data['apoderado_telefono']);
        $this->SectionData('COMPROBANTE', $data['comprobante_nombre'].' - N° '.$data['salida_numeracion']);
        $this->SectionData('FECHA', $data['salida_fecha']);
        $this->Ln(5);

        // Detalles de productos
        $this->SectionTitle('DETALLES DE PRODUCTOS');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell(110, 8, utf8_decode('PRODUCTO'), 1, 0, 'C');
        $this->Cell(20, 8, utf8_decode('CANT.'), 1, 0, 'C');
        $this->Cell(23, 8, utf8_decode('PRE. UNIT.'), 1, 0, 'C');
        $this->Cell(29, 8, utf8_decode('SUBTOTAL'), 1, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $contador = 1;
        foreach ($productos as $producto) {
            $subtotal = $producto['detalle_stock'] * $producto['detalle_precio_unitario'];
            $this->Cell(8, 7, $contador++, 1, 0, 'C');
            $this->Cell(110, 7, utf8_decode($producto['producto_nombre']), 1, 0, 'C');
            $this->Cell(20, 7, $producto['detalle_stock'], 1, 0, 'C');
            $this->Cell(23, 7, 'S/ ' . number_format($producto['detalle_precio_unitario'], 2), 1, 0, 'C');
            $this->Cell(29, 7, 'S/ ' . number_format($subtotal, 2), 1, 1, 'C');
        }
        $this->Ln(5);

        // Información general
        $this->SectionTitle('INFORMACIÓN DE PAGO');
        $this->SectionData('TOTAL', 'S/ ' . number_format($data['salida_total'], 2));
        $this->SectionData('MÉTODO DE PAGO', $data['metodo_pago_nombre']);
        $this->SectionData('ESTADO', $data['salida_estado_observaciones']);
        $this->Ln(5);

        $this->SectionTitle('OBSERVACIONES');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 8, utf8_decode($data['salida_observaciones']), 1);
        $this->Ln(5);
    }

    function SectionTitle($label)
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(188, 188, 188);
        $this->Cell(0, 10, utf8_decode($label), 1, 1, 'L', true);
        $this->Ln(0);
    }

    function SectionData($label, $data)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, utf8_decode($label), 1);
        $this->Cell(0, 7, utf8_decode($data), 1, 1);
    }
}

// Obtener el ID de la salida
$id = $_GET['id'];

// Crear instancia del modelo y obtener datos
$modelo = new Recibosalida();
$info = $modelo->listar_institucion()->fetch_assoc();
$data = $modelo->listar_almacen_ingreso($id)->fetch_assoc();
$productos = $modelo->listar_almacen_ingreso_detalle($id);

// Generar el PDF
date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->ReciboSalida($info, $data, $productos);

$filename = '' . $data['comprobante_nombre'] . '_' . $data['salida_numeracion'] . '_' . $data['apoderado_nombre'] . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
