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
        $this->SetMargins(20, 60, 20); // Márgenes izquierdo, superior y derecho
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
        $this->Ln(8);
    
        $this->SetFont('Arial', 'BU', 18);
        $this->Cell(0, 5, utf8_decode('CONSTANCIA DE VACANTE'), 0, 1, 'C');
        $this->Ln(8);
    
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 5, utf8_decode('La directora encargada de la '.$data['institucion_nombre'].', en pleno uso de sus facultades y debidamente autorizada por las normativas vigentes, hace constar lo siguiente:'), 0, 'J');
        $this->Ln(5);
    
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 5, utf8_decode('Que se ha otorgado una vacante al(la) alumno(a) '.$data['alumno_nombre_completo'].', identificado(a) con '.$data['alumno_tipo_documento'].' N.º '.$data['alumno_numero_documento'].', para '.$data['grado_nombre'].', correspondiente al nivel educativo '.$data['nivel_nombre'].', en el marco del año académico '.$data['lectivo_nombre'].'.'), 0, 'J');
        $this->Ln(5);
    
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 5, utf8_decode('Este documento tiene por finalidad acreditar la asignación de la vacante y se entrega al(la) apoderado(a) del menor, Sr./Sra. '.$data['apoderado_nombre_completo'].', identificado(a) con '.$data['apoderado_tipo_documento'].' N.º '.$data['apoderado_numero_documento'].', para que proceda con las gestiones correspondientes dentro de los plazos establecidos por la institución.'), 0, 'J');
        $this->Ln(5);
    
        $this->SetFont('Arial', 'B', 11);
        $this->MultiCell(0, 5, utf8_decode('REQUISITOS PARA FORMALIZAR LA MATRÍCULA:'), 0, 'L');
    
        // Listar documentos de matrícula
        $modelo = new ReciboMatricula();
        $documentos = $modelo->listarDocumentosDeMatricula();
        $this->SetFont('Arial', '', 10);
    
        while ($doc = $documentos->fetch_assoc()) {
            $this->MultiCell(0, 5, utf8_decode($doc['obligatorio_marcado'] . $doc['nombre'].' - '.$doc['documento_responsable_iniciales']), 0, 'L');
        }
    
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

$filename = utf8_decode('VACANTE_'.$data['alumno_nombre_completo']) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
