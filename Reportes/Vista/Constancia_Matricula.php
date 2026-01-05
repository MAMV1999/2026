<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/ReciboMatricula.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;
    protected $background_image;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null, $background_image = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->background_image = $background_image;
    
        // Ajustar los márgenes
        $this->SetMargins(20, 80, 20); // Márgenes izquierdo, superior y derecho
        $this->SetAutoPageBreak(true, 5); // Márgen inferior
    }
    

    function Header()
    {
        // Si hay una imagen de fondo configurada, agrégala
        if ($this->background_image) {
            $this->Image($this->background_image, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }
    }

    function Recibo($data)
    {
        $this->AddPage();
    
        $this->SetFont('Arial', 'B', 11);
        $this->MultiCell(0, 5, utf8_decode('"' . $data['lectivo_nombre_ano'] . '"'), 0, 'C');
        $this->Ln(20);
    
        $this->SetFont('Arial', 'BU', 18);
        $this->Cell(0, 5, utf8_decode('CONSTANCIA DE MATRÍCULA'), 0, 1, 'C');
        $this->Ln(15);
    
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 5, utf8_decode('La directora encargada de la ' . $data['institucion_nombre'] . ', en pleno uso de sus facultades y debidamente autorizada por las normativas vigentes, deja constancia de lo siguiente:'), 0, 'J');
        $this->Ln(5);
    
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 5, utf8_decode('Que el/la alumno(a) ' . $data['alumno_nombre_completo'] . ', identificado(a) con ' . $data['alumno_tipo_documento'] . ' N.º ' . $data['alumno_numero_documento'] . ', se encuentra matriculado(a) en nuestra institución en el aula de ' . $data['grado_nombre'] . ', correspondiente al nivel educativo ' . $data['nivel_nombre'] . ', dentro del marco del año académico ' . $data['lectivo_nombre'] . '.'), 0, 'J');
        $this->Ln(5);
    
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 5, utf8_decode('Se expide el presente documento para los fines legales y administrativos que correspondan.'), 0, 'J');
        
        $this->Ln(30);
        $this->SetFont('Arial', 'B', 11);
        $this->MultiCell(0, 5, utf8_decode('__________________________________'), 0, 'C');
        $this->MultiCell(0, 5, utf8_decode($data['usuario_docente_nombre']), 0, 'C');
        $this->MultiCell(0, 5, utf8_decode($data['usuario_docente_cargo']), 0, 'C');
    
    }
    

    function SectionTitle($label)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, utf8_decode($label), 0, 1, 'L', false);
        $this->Ln(0);
    }

    function SectionData($label, $data)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 7, utf8_decode($label), 1);
        $this->Cell(0, 7, utf8_decode($data), 1, 1);
    }
}

// Obtener el ID de la matrícula
$id = $_GET['id'];
$modelo = new ReciboMatricula();
$result = $modelo->listarPorIdMatriculaDettalle($id);
$data = $result->fetch_assoc();

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

// Ruta de la imagen de fondo
$background_image = 'menbrete.jpg';

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual, $background_image);
$pdf->AliasNbPages();
$pdf->Recibo($data);

$filename = utf8_decode('MATRÍCULA_'.$data['alumno_nombre_completo']) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
