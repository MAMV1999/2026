<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/usuario_docente.php");

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
        $this->Cell(0, 5, utf8_decode('Fecha y Hora de generación: ' . $this->fecha_hora_actual), 0, 1, 'C');
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Docente($data)
    {
        $this->AddPage();

        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, utf8_decode('INFORMACIÓN DEL DOCENTE'), 0, 1, 'C');
        $this->SetFont('Arial', '', 15);
        $this->Cell(0, 7, utf8_decode($data['nombreyapellido']), 0, 1, 'C');
        $this->Ln(5);

        $this->SectionTitle('DATOS PERSONALES');
        $this->SectionData('DOCUMENTO', $data['tipo_documento'].' '.$data['numerodocumento']);
        $this->SectionData('NOMBRE Y APELLIDO', $data['nombreyapellido']);
        $this->SectionData('NACIMIENTO', $data['nacimiento'].' - '.$data['edad'].' AÑOS');
        $this->SectionData('ESTADO CIVIL', $data['estado_civil']);
        $this->SectionData('SEXO', $data['sexo']);
        $this->Ln(5);

        $this->SectionTitle('DATOS DE CONTACTO');
        $this->SectionData('DIRECCION', $data['direccion']);
        $this->SectionData('TELEFONO', $data['telefono']);
        $this->SectionData('CORREO', $data['correo']);
        $this->Ln(5);

        $this->SectionTitle('INFORMACIÓN LABORAL');
        $this->SectionData('CARGO', $data['cargo']);
        $this->SectionData('TIPO DE CONTRATO', $data['tipo_contrato']);
        $this->SectionData('FECHA INICIO - FIN', $data['fechainicio'].' - '.$data['fechafin']);
        $this->Ln(5);

        $this->SectionTitle('DATOS BANCARIOS');
        $this->SectionData('N° BANCARIO', $data['cuentabancaria']);
        $this->SectionData('N° INTERBANCARIO', $data['cuentainterbancaria']);
        $this->SectionData('RUC', $data['sunat_ruc']);
        $this->SectionData('RUC USUARIO', $data['sunat_usuario']);
        $this->Ln(5);

        $this->SectionTitle('CREDENCIALES DE USUARIO');
        $this->SectionData('USUARIO', $data['usuario']);
        $this->SectionData('CLAVE', $data['clave']);
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
        $this->Cell(50, 8, utf8_decode($label), 1);
        $this->Cell(0, 8, utf8_decode($data), 1, 1);
    }
}

// Obtener el ID del usuario docente desde la URL o por defecto
$id = $_GET['id'];

// Crear instancia del modelo y obtener datos
$modelo = new UsuarioDocente();
$result = $modelo->listarUsuarioDocente($id);
$data = $result->fetch_assoc();

if (!$data) {
    die("No se encontraron datos para el ID proporcionado.");
}

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

// Crear el PDF
$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->Docente($data);

// Definir el nombre del archivo
$filename = 'Docente_' . utf8_decode($data['nombreyapellido']) . '.pdf';

// Salida del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
