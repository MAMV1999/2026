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

    function SetDataActual($data)
    {
        $this->data = is_array($data) ? $data : [];
    }

    function valor($campo, $default = '')
    {
        return isset($this->data[$campo]) && $this->data[$campo] !== null
            ? $this->data[$campo]
            : $default;
    }

    function texto($valor)
    {
        return utf8_decode((string)($valor ?? ''));
    }

    function campo($data, $campo, $default = '')
    {
        return isset($data[$campo]) && $data[$campo] !== null && $data[$campo] !== ''
            ? $data[$campo]
            : $default;
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 25);
        $this->Cell(0, 10, $this->texto($this->valor('institucion_nombre')), 0, 1, 'C');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, $this->texto($this->valor('institucion_direccion')), 0, 1, 'C');
        $this->Cell(0, 5, $this->texto($this->valor('institucion_ruc') . ' ' . $this->valor('institucion_razon_social')), 0, 1, 'C');
        $this->Cell(0, 5, $this->texto('CORREO ' . $this->valor('institucion_correo')), 0, 1, 'C');
        $this->Cell(0, 5, $this->texto('TELEFONO ' . $this->valor('institucion_telefono')), 0, 1, 'C');
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-23);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, $this->texto('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 1, 'C');
    }

    function Recibo($data)
    {
        $this->SetDataActual($data);

        $this->AddPage();

        $this->SetFont('Arial', 'B', 15);
        $this->Cell(
            0,
            5,
            $this->texto('MATRICULA ' . $this->campo($data, 'matricula_categoria_nombre') . ' ' . $this->campo($data, 'lectivo_nombre')),
            0,
            1,
            'C'
        );

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(
            0,
            5,
            $this->texto('REF. RECIBO N° ' . $this->campo($data, 'pago_numeracion', 'SIN RECIBO')),
            0,
            1,
            'C'
        );

        $this->Ln(4);

        $this->SectionTitle('POR MEDIO DEL SIGUIENTE DOCUMENTO YO:');
        $this->SectionData('NOMBRES Y APELLIDOS', $this->campo($data, 'apoderado_nombre_completo'));
        $this->SectionData('DOCUMENTO', $this->campo($data, 'apoderado_tipo_documento') . ' - ' . $this->campo($data, 'apoderado_numero_documento'));
        $this->SectionData('TELEFONO', $this->campo($data, 'apoderado_telefono'));
        $this->Ln(5);

        $this->SectionTitle('DOY CONSENTIMIENTO A MATRICULAR A MI MENOR HIJO(A):');
        $this->SectionData('APELLIDOS Y NOMBRES', $this->campo($data, 'alumno_nombre_completo'));
        $this->SectionData('DOCUMENTO', $this->campo($data, 'alumno_tipo_documento') . ' - ' . $this->campo($data, 'alumno_numero_documento'));
        $this->Ln(5);

        $this->SectionTitle('EN LA:');
        $this->SectionData('INSTITUCION', $this->campo($data, 'institucion_nombre'));
        $this->SectionData('PERIODO', $this->campo($data, 'lectivo_nombre'));
        $this->SectionData('NIVEL', $this->campo($data, 'nivel_nombre'));
        $this->SectionData('GRADO', $this->campo($data, 'grado_nombre'));
        $this->SectionData('SECCION', $this->campo($data, 'seccion_nombre'));
        $this->Ln(30);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, $this->texto('_________________________________________'), 0, 1, 'C');
        $this->Cell(0, 6, $this->texto($this->campo($data, 'apoderado_nombre_completo')), 0, 1, 'C');

        $this->SetFont('Arial', '', 11);
        $this->Cell(
            0,
            6,
            $this->texto($this->campo($data, 'apoderado_tipo_documento') . ' ' . $this->campo($data, 'apoderado_numero_documento')),
            0,
            1,
            'C'
        );

        $this->Ln(8);

        $this->SetFont('Arial', '', 7);
        $this->MultiCell(
            0,
            4,
            $this->texto('(*) AL REALIZAR LA FIRMA se comprende que RECIBÍ TODA INFORMACIÓN CORRECTAMENTE SOBRE LA MATRICULA ' . $this->campo($data, 'lectivo_nombre') . ' en el centro educativo.'),
            0
        );
        $this->MultiCell(0, 4, $this->texto('(*) NO HAY DEVOLUCIÓN DE DINERO, una vez realizado el pago.'), 0);
        $this->MultiCell(0, 4, $this->texto('(*) En el caso de que falte alguna FIRMA O PAGO RESPECTIVO, EL DOCUMENTO TIENE UNA VALIDEZ DE 48HRS DESDE LA FECHA DE EMISIÓN, vencido el plazo, el documento quedara sin efecto y se otorgara otro nuevo al apoderado(a).'), 0);
        $this->MultiCell(0, 4, $this->texto('(*) Solo el(la) apoderado(a) que se menciona en este documento, ES EL ÚNICO AUTORIZADO(A) A RETIRAR LA DOCUMENTACIÓN del(la) menor en caso de retiro (NO SE ACEPTARA OTRAS PERSONAS).'), 0);
        $this->MultiCell(0, 4, $this->texto('(*) En caso el apoderado(a) que se menciona en este documento, NO PUEDA HACER EL TRÁMITE DOCUMENTARIO. El representante del(la) apoderado(a) DEBERÁ PRESENTARSE CON UNA CARTA PODER, FIRMADA y con la COPIA DE DNI del apoderado(a) que se menciona en el presente documento.'), 0);

        $this->Ln(5);

        $this->SegundaHojaContrato($data);
    }

    function SegundaHojaContrato($data)
    {
        $this->SetDataActual($data);
        $this->AddPage();
    
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(
            0,
            8,
            $this->texto('CONTRATO EDUCATIVO ' . $this->campo($data, 'lectivo_nombre')),
            0,
            1,
            'C'
        );
    
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(
            0,
            5,
            $this->texto('La firma de este documento acepta los terminos y condiciones del contrato educativo ' . $this->campo($data, 'lectivo_nombre')),
            0,
            'C'
        );
    
        $this->TablaCentrada($data);
    
        $yDespuesTabla = $this->GetY() + 12;
    
        $anchoHuella = 38;
        $altoHuella = 42;
        $xHuella = ($this->GetPageWidth() - $anchoHuella) / 2;
    
        // Cuadro para huella digital
        $this->Rect($xHuella, $yDespuesTabla, $anchoHuella, $altoHuella);
    
        // Texto debajo del cuadro de huella
        $this->SetXY($xHuella, $yDespuesTabla + $altoHuella + 2);
        $this->SetFont('Arial', '', 8);
        $this->Cell(
            $anchoHuella,
            5,
            $this->texto('Huella digital índice derecho'),
            0,
            1,
            'C'
        );
    
        $this->SetY(-42);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(
            0,
            5,
            $this->texto('CONTRATO DE PRESTACION DE SERVICIOS EDUCATIVOS ' . $this->campo($data, 'lectivo_nombre') . ' - ' . $this->campo($data, 'institucion_nombre')),
            0,
            'C'
        );
    }

    function TablaCentrada($data)
    {
        $colWidth = 90;
        $rowHeights = [10, 5, 5, 5];
        $numCols = 2;
        $numRows = count($rowHeights);
        $tableWidth = $colWidth * $numCols;
        $tableHeight = array_sum($rowHeights);

        $xStart = ($this->GetPageWidth() - $tableWidth) / 2;
        $yStart = ($this->GetPageHeight() - $tableHeight) / 2;

        $this->SetXY($xStart, $yStart);
        $this->SetFont('Arial', 'B', 10);

        for ($row = 0; $row < $numRows; $row++) {
            $this->SetX($xStart);

            for ($col = 1; $col <= $numCols; $col++) {
                $content = '';

                if ($col == 1 && $row == 0) {
                    $content = '__________________________________';
                } elseif ($col == 2 && $row == 0) {
                    $content = '__________________________________';
                } elseif ($col == 1 && $row == 1) {
                    $content = $this->campo($data, 'usuario_docente_nombre');
                } elseif ($col == 2 && $row == 1) {
                    $content = $this->campo($data, 'apoderado_nombre_completo');
                } elseif ($col == 1 && $row == 2) {
                    $content = $this->campo($data, 'usuario_docente_cargo');
                } elseif ($col == 2 && $row == 2) {
                    $content = $this->campo($data, 'apoderado_tipo_nombre');
                } elseif ($col == 1 && $row == 3) {
                    $content = $this->campo($data, 'usuario_docente_documento') . ' : ' . $this->campo($data, 'usuario_docente_numerodocumento');
                } elseif ($col == 2 && $row == 3) {
                    $content = $this->campo($data, 'apoderado_tipo_documento') . ' : ' . $this->campo($data, 'apoderado_numero_documento');
                }

                $this->Cell($colWidth, $rowHeights[$row], $this->texto($content), 0, 0, 'C');
            }

            $this->Ln($rowHeights[$row]);
        }
    }

    function SectionTitle($label)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, $this->texto($label), 0, 1, 'L', false);
        $this->Ln(0);
    }

    function SectionData($label, $data)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 7, $this->texto($label), 1);
        $this->Cell(0, 7, $this->texto($data), 1, 1);
    }

    function SinDatos()
    {
        $this->SetDataActual([]);
        $this->AddPage();

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, $this->texto('NO SE ENCONTRARON CONTRATOS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $this->texto('No existen registros activos para mostrar.'), 0, 1, 'C');
    }
}

$modelo = new ReciboMatricula();
$result = $modelo->listartodosloscontratos();

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();

$total_registros = 0;

if ($result) {
    while ($data = $result->fetch_assoc()) {
        $total_registros++;
        $pdf->Recibo($data);
    }
}

if ($total_registros == 0) {
    $pdf->SinDatos();
}

$filename = 'contratos_matricula.pdf';

if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
exit;

?>