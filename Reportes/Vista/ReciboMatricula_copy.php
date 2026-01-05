<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/ReciboMatricula.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;
    protected $data;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null, $data = [])
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->data = $data;
    }

    function Header()
    {
        // Encabezado común en todas las páginas
        $this->SetFont('Arial', 'B', 25);
        $this->Cell(0, 10, utf8_decode($this->data['institucion_nombre']), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode($this->data['institucion_direccion']), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode($this->data['institucion_ruc'] . ' ' . $this->data['institucion_razon_social']), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('CORREO ' . $this->data['institucion_correo']), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('TELEFONO ' . $this->data['institucion_telefono']), 0, 1, 'C');
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-23);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Fecha y Hora de generación: ' . $this->fecha_hora_actual), 0, 1, 'C');
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function Recibo($data)
    {
        $this->AddPage();

        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 5, utf8_decode('MATRICULA ' . $data['matricula_categoria_nombre'] . ' ' . $data['lectivo_nombre']), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode('REF. RECIBO N° ' . $data['pago_numeracion']), 0, 1, 'C');
        $this->Ln(4);

        $this->SectionTitle('POR MEDIO DEL SIGUIENTE DOCUMENTO YO:');
        $this->SectionData('NOMBRES Y APELLIDOS', $data['apoderado_nombre_completo']);
        $this->SectionData('DOCUMENTO', $data['apoderado_tipo_documento'] . ' - ' . $data['apoderado_numero_documento']);
        $this->SectionData('TELEFONO', $data['apoderado_telefono']);
        $this->Ln(5);

        $this->SectionTitle('DOY CONSENTIMIENTO A MATRICULAR A MI MENOR HIJO(A):');
        $this->SectionData('APELLIDOS Y NOMBRES', $data['alumno_nombre_completo']);
        $this->SectionData('DOCUMENTO', $data['alumno_tipo_documento'] . ' - ' . $data['alumno_numero_documento']);
        $this->Ln(5);

        $this->SectionTitle('EN LA:');
        $this->SectionData('INSTITUCION', $data['institucion_nombre']);
        $this->SectionData('PERIODO', $data['lectivo_nombre']);
        $this->SectionData('NIVEL', $data['nivel_nombre']);
        $this->SectionData('GRADO', $data['grado_nombre']);
        $this->SectionData('SECCION', $data['seccion_nombre']);
        $this->Ln(30);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, utf8_decode('_________________________________________'), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode($data['apoderado_nombre_completo']), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, utf8_decode($data['apoderado_tipo_documento'] . ' ' . $data['apoderado_numero_documento']), 0, 1, 'C');
        $this->Ln(8);

        // Manejo del texto largo para la descripción
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(0, 4, utf8_decode('(*) AL REALIZAR LA FIRMA se comprende que RECIBÍ TODA INFORMACIÓN CORECTAMENTE SOBRE LA MATRICULA ' . $data['lectivo_nombre'] . ' en el centro educativo.'), 0);
        $this->MultiCell(0, 4, utf8_decode('(*) NO HAY DEVOLUCIÓN DE DINERO, una vez realizado el pago.'), 0);
        $this->MultiCell(0, 4, utf8_decode('(*) En el caso de que falte alguna FIRMA O PAGO RESPECTIVO, EL DOCUMENTO TIENE UNA VALIDEZ DE 48HRS DESDE LA FECHA DE EMISIÓN, vencido el plazo, el documento quedara sin efecto y se otorgara otro nuevo al apoderado(a).'), 0);
        $this->MultiCell(0, 4, utf8_decode('(*) Solo el(la) apoderado(a) que se menciona en este documento, ES EL ÚNICO AUTORIZADO(A) A RETIRAR LA DOCUMENTACIÓN del(la) menor en caso de retiro (NO SE ACEPTARA OTRAS PERSONAS).'), 0);
        $this->MultiCell(0, 4, utf8_decode('(*) En caso el apoderado(a) que se menciona en este documento, NO PUEDA HACER EL TRÁMITE DOCUMENTARIO. El representante del(la) apoderado(a) DEBERÁ PRESENTARSE CON UNA CARTA PODER, FIRMADA y con la COPIA DE DNI del apoderado(a) que se menciona en el presente documento.'), 0);
        $this->Ln(5);

        // Agregar una nueva página con la tabla
        $this->AddPage();
        $this->TablaCentrada($data);
    }

    function TablaCentrada($data)
    {
        // Configuración de la tabla
        $colWidth = 90; // Ancho de cada columna
        $rowHeights = [10, 5, 5, 5]; // Alturas de las filas, puedes personalizarlas
        $numCols = 2; // Número de columnas
        $numRows = count($rowHeights); // Número de filas basado en alturas personalizadas
        $tableWidth = $colWidth * $numCols; // Ancho total de la tabla
        $tableHeight = array_sum($rowHeights); // Altura total de la tabla
    
        // Calcular posición para centrar la tabla
        $xStart = ($this->GetPageWidth() - $tableWidth) / 2;
        $yStart = ($this->GetPageHeight() - $tableHeight) / 2;
    
        // Dibujar la tabla
        $this->SetXY($xStart, $yStart);
        $this->SetFont('Arial', 'B', 10);
    
        for ($row = 0; $row < $numRows; $row++) {
            $this->SetX($xStart); // Asegurar que cada fila comience desde el mismo X
            for ($col = 1; $col <= $numCols; $col++) {
                // Contenido dinámico para cada celda
                if ($col == 1 && $row == 0) { $content = '__________________________________'; }
                elseif ($col == 2 && $row == 0) { $content = '__________________________________'; }

                elseif ($col == 1 && $row == 1) { $content = $data['usuario_docente_nombre']; }
                elseif ($col == 2 && $row == 1) { $content = $data['apoderado_nombre_completo']; }

                elseif ($col == 1 && $row == 2) { $content = $data['usuario_docente_cargo']; }
                elseif ($col == 2 && $row == 2) { $content = $data['apoderado_tipo_nombre']; }

                elseif ($col == 1 && $row == 3) { $content = $data['usuario_docente_documento'].' : '.$data['usuario_docente_numerodocumento']; }
                elseif ($col == 2 && $row == 3) { $content = $data['apoderado_tipo_documento'].' : '.$data['apoderado_numero_documento']; }

                else { $content = "Fila " . ($row + 1) . ", Columna $col"; }
    
                // Dibujar la celda con altura personalizada
                $this->Cell($colWidth, $rowHeights[$row], utf8_decode($content), 0, 0, 'C');
            }
            $this->Ln($rowHeights[$row]); // Moverse a la siguiente fila
        }
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

// Crear instancia del modelo y obtener datos
$modelo = new ReciboMatricula();
$result = $modelo->listarPorIdMatriculaDettalle($id);
$data = $result->fetch_assoc();

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual, $data);
$pdf->AliasNbPages();
$pdf->Recibo($data);

$filename = utf8_decode($data['alumno_nombre_completo']) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
