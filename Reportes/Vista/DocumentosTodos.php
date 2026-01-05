<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/DocumentosTodos.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, utf8_decode('REPORTE DE DOCUMENTOS ENTREGADOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 8, utf8_decode('Lista de alumnos y documentación entregada'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function ReportTable($header, $data, $documentosNombres)
    {
        $w = [20, 20, 70, 24]; // Anchos de Nivel, Grado, Alumno
        foreach ($documentosNombres as $doc) {
            $w[] = 22; // Ancho para documentos
        }

        $this->SetFillColor(200, 220, 255);
        $this->SetFont('Arial', 'B', 9);

        // **Primera fila del encabezado** (Nivel, Grado, Alumno + Espacio para documentos)
        $this->Cell($w[0], 15, utf8_decode('NIVEL'), 1, 0, 'C', true);
        $this->Cell($w[1], 15, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell($w[2], 15, utf8_decode('NOMBRE Y APELLIDO'), 1, 0, 'C', true);
        $this->Cell($w[3], 15, utf8_decode('TIPO'), 1, 0, 'C', true);

        // **Celdas combinadas para los encabezados de documentos**
        $this->SetFont('Arial', 'B', 8);
        $xStart = $this->GetX(); // Guardar la posición X inicial
        $yStart = $this->GetY(); // Guardar la posición Y inicial

        foreach ($documentosNombres as $i => $doc) {
            $xPos = $xStart + ($w[4] * $i); // Calcular la posición X de cada celda
            $this->SetXY($xPos, $yStart);

            // Dibuja una celda vacía con borde y fondo de color
            $this->Cell($w[4], 15, '', 1, 0, 'C', true);

            // Ajusta la posición nuevamente para MultiCell()
            $this->SetXY($xPos, $yStart);
            $this->MultiCell($w[4], 5, utf8_decode($doc), 0, 'C', false);

            // Asegura que la siguiente celda inicie en la posición correcta
            $this->SetXY($xPos + $w[4], $yStart);
        }

        $this->Ln(20);


        // Dibujar filas de datos
        $this->SetFont('Arial', '', 8);
        foreach ($data as $row) {
            foreach ($row as $i => $cell) {
                // Aplicar color de fondo solo si el valor es "NO"
                if ($cell == 'NO') {
                    $this->SetFillColor(220, 220, 220); // Gris claro
                } else {
                    $this->SetFillColor(255, 255, 255); // Fondo blanco normal
                }

                // Dibujar la celda con el color de fondo correspondiente
                $this->Cell($w[$i], 8, utf8_decode($cell), 1, 0, 'C', true);
            }
            $this->Ln();
        }
    }
}

// Obtener los datos desde la base de datos
$documentos = new Documentos();
$resultado = $documentos->obtenerReporteDinamico();

$header = ['Nivel', 'Grado', 'Alumno', 'Tipo'];
$data = [];
$documentosNombres = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        if (empty($documentosNombres)) {
            foreach ($fila as $columna => $valor) {
                if (!in_array($columna, [
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
                ]) && strpos($columna, '_observaciones') === false) {
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

        $data[] = $filaData;
    }
}

// Crear el PDF en orientación horizontal
$pdf = new PDF('L', 'mm', 'A3');
$pdf->AliasNbPages();
$pdf->AddPage();

// Generar la tabla con encabezados corregidos
$pdf->ReportTable($header, $data, $documentosNombres);

// Salida del documento
$pdf->Output('I', 'Reporte_Documentos.pdf');
