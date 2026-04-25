<?php

require('../../General/fpdf/fpdf.php');
require_once("../Modelo/reporte_completo_matricula.php");

class PDF extends FPDF
{
    protected $fecha_hora_actual;

    private function txt($s)
    {
        $s = (string)$s;
        return iconv('UTF-8', 'Windows-1252//TRANSLIT', $s);
    }

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, $this->txt('FECHA Y HORA DE GENERACIÓN: ' . $this->fecha_hora_actual), 0, 1, 'C');
        $this->Cell(0, 5, $this->txt('PÁGINA ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function HojaMatricula($data)
    {
        $this->AddPage();

        // ENCABEZADO INSTITUCIONAL
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 8, $this->txt($data['institucion_nombre']), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->txt($data['razon_social'] . ' - RUC: ' . $data['institucion_ruc']), 0, 1, 'C');
        $this->Cell(0, 5, $this->txt($data['institucion_direccion']), 0, 1, 'C');
        $this->Cell(0, 5, $this->txt('TELÉFONO: ' . $data['institucion_telefono'] . ' | CORREO: ' . $data['institucion_correo']), 0, 1, 'C');

        $this->Ln(5);

        // TÍTULO
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 9, $this->txt('INFORMACIÓN GENERAL'), 1, 1, 'C');

        $this->Ln(5);

        // DATOS ACADÉMICOS
        $this->SectionTitle('DATOS DE MATRICULA');
        $this->SectionData('TIPO', $data['matricula_categoria']);
        $this->SectionData('NIVEL', $data['nivel']);
        $this->SectionData('GRADO', $data['grado']);
        $this->SectionData('SECCIÓN', $data['seccion']);

        $this->Ln(4);

        // DATOS DEL ALUMNO
        $this->SectionTitle('DATOS DEL ALUMNO');
        $this->SectionData('TIPO DOCUMENTO', $data['nombred_alumno']);
        $this->SectionData('N° DOCUMENTO', $data['numerod_alumno']);
        $this->SectionData('NOMBRE Y APELLIDO', $data['alumno_nombre']);
        $this->SectionData('FECHA DE NACIMIENTO', $data['alumno_nacimiento']);
        $this->SectionData('EDAD', $data['alumno_edad']);

        $this->Ln(4);

        // DATOS DEL APODERADO
        $this->SectionTitle('DATOS DEL APODERADO');
        $this->SectionData('TIPO DOCUMENTO', $data['nombred_apoderado']);
        $this->SectionData('N° DOCUMENTO', $data['numerod_apoderado']);
        $this->SectionData('NOMBRE Y APELLIDO', $data['apoderado_nombre']);
        $this->SectionData('TELÉFONO', $data['apoderado_telefono']);

        $this->Ln(4);

        // DATOS DEL TUTOR
        $this->SectionTitle('DATOS DEL TUTOR');
        $this->SectionData('TIPO DOCUMENTO', $data['nombred_tutor']);
        $this->SectionData('N° DOCUMENTO', $data['numerod_tutor']);
        $this->SectionData('NOMBRE Y APELLIDO', $data['tutor_nombre']);
        $this->SectionData('TELÉFONO', $data['tutor_telefono']);

        $this->AddPage();

        
        $this->SectionTitle('PAGO MATRICULA');
        $this->SectionData('FECHA', $data['fechas_pago']);
        $this->SectionData('N° RECIBO', $data['numeraciones_pago']);
        $this->SectionData('MONTO', 'S/ ' . $data['montos_pago']);
        $this->SectionData('METODO', $data['metodos_pago']);

        $this->Ln(5);

        // TABLA MENSUALIDADES
        $this->SectionTitle('MENSUALIDADES');
        $this->FilaCodigo($data['numerod_alumno']);
        $this->TablaMensualidades(
            $data['meses'],
            $data['montos'],
            $data['estados_pago'],
            $data['observaciones_mensualidad']
        );

        $this->Ln(5);

        // TABLA DOCUMENTOS
        $this->SectionTitle('DOCUMENTOS');
        $this->TablaDocumentos(
            $data['documentos'],
            $data['estados_documentos'],
            $data['observaciones_documentos']
        );
    }

    function SectionTitle($label)
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 8, $this->txt($label), 1, 1, 'L', true);
    }

    function SectionData($label, $data)
    {
        if ($data === null || $data === '') {
            $data = '-';
        }

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(55, 7, $this->txt($label), 1, 0, 'L');

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 7, $this->txt($data), 1, 1, 'L');
    }

    function FilaCodigo($codigo)
    {
        if ($codigo === null || $codigo === '') {
            $codigo = '-';
        }

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 7, $this->txt('CÓDIGO'), 1, 0, 'L');

        $this->SetFont('Arial', '', 9);
        $this->Cell(155, 7, $this->txt($codigo), 1, 1, 'L');
    }

    function TablaMensualidades($meses, $montos, $estados, $observaciones)
    {
        $arrMeses = $this->separar($meses);
        $arrMontos = $this->separar($montos);
        $arrEstados = $this->separar($estados);
        $arrObs = $this->separar($observaciones);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 7, $this->txt('N°'), 1, 0, 'C');
        $this->Cell(50, 7, $this->txt('MES'), 1, 0, 'C');
        $this->Cell(35, 7, $this->txt('MONTO'), 1, 0, 'C');
        $this->Cell(35, 7, $this->txt('ESTADO'), 1, 0, 'C');
        $this->Cell(60, 7, $this->txt('OBSERVACIÓN'), 1, 1, 'C');

        $this->SetFont('Arial', '', 8);

        if (count($arrMeses) == 0) {
            $this->Cell(190, 7, $this->txt('No hay mensualidades registradas.'), 1, 1, 'C');
            return;
        }

        for ($i = 0; $i < count($arrMeses); $i++) {
            $monto = isset($arrMontos[$i]) ? $arrMontos[$i] : '-';
            $estado = isset($arrEstados[$i]) ? $arrEstados[$i] : '-';
            $obs = isset($arrObs[$i]) ? $arrObs[$i] : '-';

            if ($estado == '1') {
                $estado = 'PAGADO';
            } elseif ($estado == '0') {
                $estado = 'PENDIENTE';
            }

            $this->Cell(10, 7, $i + 1, 1, 0, 'C');
            $this->Cell(50, 7, $this->txt($arrMeses[$i]), 1, 0, 'C');
            $this->Cell(35, 7, $this->txt('S/ ' . $monto), 1, 0, 'C');
            $this->Cell(35, 7, $this->txt($estado), 1, 0, 'C');
            $this->Cell(60, 7, $this->txt($obs), 1, 1, 'L');
        }
    }

    function TablaDocumentos($documentos, $estados, $observaciones)
    {
        $arrDocs = $this->separar($documentos);
        $arrEstados = $this->separar($estados);
        $arrObs = $this->separar($observaciones);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 7, $this->txt('N°'), 1, 0, 'C');
        $this->Cell(80, 7, $this->txt('DOCUMENTO'), 1, 0, 'C');
        $this->Cell(40, 7, $this->txt('ESTADO'), 1, 0, 'C');
        $this->Cell(60, 7, $this->txt('OBSERVACIÓN'), 1, 1, 'C');

        $this->SetFont('Arial', '', 8);

        if (count($arrDocs) == 0) {
            $this->Cell(190, 7, $this->txt('No hay documentos registrados.'), 1, 1, 'C');
            return;
        }

        for ($i = 0; $i < count($arrDocs); $i++) {
            $estado = isset($arrEstados[$i]) ? $arrEstados[$i] : '-';
            $obs = isset($arrObs[$i]) ? $arrObs[$i] : '-';

            if ($estado == '1') {
                $estado = 'ENTREGADO';
            } elseif ($estado == '0') {
                $estado = 'NO ENTREGADO';
            }

            $this->Cell(10, 7, $i + 1, 1, 0, 'C');
            $this->Cell(80, 7, $this->txt($arrDocs[$i]), 1, 0, 'L');
            $this->Cell(40, 7, $this->txt($estado), 1, 0, 'C');
            $this->Cell(60, 7, $this->txt($obs), 1, 1, 'L');
        }
    }

    function separar($cadena)
    {
        if ($cadena === null || trim($cadena) === '') {
            return array();
        }

        return array_map('trim', explode(',', $cadena));
    }
}

// ID recibido por GET
$id = $_GET['id'];
$modelo = new Reportecompleto();
$data = $modelo->listar($id)->fetch_assoc();

date_default_timezone_set('America/Lima');
$fecha_hora_actual = date('d/m/Y H:i:s');

$pdf = new PDF('P', 'mm', 'A4', $fecha_hora_actual);
$pdf->AliasNbPages();
$pdf->HojaMatricula($data);

$filename = 'Hoja_Matricula_' . $data['alumno_nombre'] . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
