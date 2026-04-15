<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/DocumentosTodos.php");

class PDF extends FPDF
{
    public $tituloGrupo = '';

    function Header()
    {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, utf8_decode('REPORTE DE DOCUMENTOS ENTREGADOS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 8, utf8_decode('Lista de alumnos y documentación entregada'), 0, 1, 'C');

        if (!empty($this->tituloGrupo)) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, utf8_decode('TIPO: ' . $this->tituloGrupo), 0, 1, 'C');
        }

        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function ReportTable($data, $documentosNombres)
    {
        $w = [20, 20, 70, 24]; // NIVEL, GRADO, NOMBRE, TIPO
        foreach ($documentosNombres as $doc) {
            $w[] = 22;
        }

        $this->SetFillColor(200, 220, 255);
        $this->SetFont('Arial', 'B', 9);

        // Encabezado principal
        $this->Cell($w[0], 15, utf8_decode('NIVEL'), 1, 0, 'C', true);
        $this->Cell($w[1], 15, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell($w[2], 15, utf8_decode('NOMBRE Y APELLIDO'), 1, 0, 'C', true);
        $this->Cell($w[3], 15, utf8_decode('TIPO'), 1, 0, 'C', true);

        $this->SetFont('Arial', 'B', 8);
        $xStart = $this->GetX();
        $yStart = $this->GetY();

        foreach ($documentosNombres as $i => $doc) {
            $xPos = $xStart + ($w[4] * $i);
            $this->SetXY($xPos, $yStart);
            $this->Cell($w[4], 15, '', 1, 0, 'C', true);
            $this->SetXY($xPos, $yStart);
            $this->MultiCell($w[4], 5, utf8_decode($doc), 0, 'C', false);
            $this->SetXY($xPos + $w[4], $yStart);
        }

        $this->Ln(20);

        // Filas
        $this->SetFont('Arial', '', 8);
        foreach ($data as $row) {
            foreach ($row as $i => $cell) {
                if ($cell == 'NO') {
                    $this->SetFillColor(220, 220, 220);
                } else {
                    $this->SetFillColor(255, 255, 255);
                }

                $this->Cell($w[$i], 8, utf8_decode($cell), 1, 0, 'C', true);
            }
            $this->Ln();
        }
    }
}

// Obtener datos
$documentos = new Documentos();
$resultado = $documentos->obtenerReporteDinamico();

$documentosNombres = [];
$dataAgrupada = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {

        // Obtener nombres de documentos dinámicos solo una vez
        if (empty($documentosNombres)) {
            foreach ($fila as $columna => $valor) {
                if (
                    !in_array($columna, [
                        'alumno_numerodocumento',
                        'apoderado_nombre',
                        'apoderado_telefono',
                        'apoderado_numerodocumento',
                        'alumno_id',
                        'alumno_documento_tipo',
                        'institucion_nivel',
                        'institucion_seccion',
                        'apoderado_id',
                        'apoderado_tipo',
                        'apoderado_documento_tipo',
                        'institucion_lectivo',
                        'institucion_direccion',
                        'institucion_razon_social',
                        'institucion_ruc',
                        'institucion_nombre',
                        'institucion_telefono',
                        'institucion_correo',
                        'institucion_grado',
                        'matricula_detalle_id',
                        'matricula_categoria',
                        'alumno_nombre'
                    ]) && strpos($columna, '_observaciones') === false
                ) {
                    $documentosNombres[] = $columna;
                }
            }
        }

        $filaData = [
            $fila['institucion_nivel'],
            $fila['institucion_grado'],
            $fila['alumno_nombre'],
            $fila['matricula_categoria']
        ];

        foreach ($documentosNombres as $doc) {
            $filaData[] = isset($fila[$doc]) ? $fila[$doc] : 'NO';
        }

        // Agrupar por tipo de matrícula
        $grupo = $fila['matricula_categoria'];

        if (!isset($dataAgrupada[$grupo])) {
            $dataAgrupada[$grupo] = [];
        }

        $dataAgrupada[$grupo][] = $filaData;
    }
}

// Crear PDF
$pdf = new PDF('L', 'mm', 'A3');
$pdf->AliasNbPages();

// Crear una hoja por cada tipo
foreach ($dataAgrupada as $grupo => $filas) {
    $pdf->tituloGrupo = $grupo;
    $pdf->AddPage();
    $pdf->ReportTable($filas, $documentosNombres);
}

$pdf->Output('I', 'Reporte_Documentos.pdf');
?>