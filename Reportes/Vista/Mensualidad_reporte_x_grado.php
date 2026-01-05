<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Mensualidad_reporte_x_grado.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode('Reporte de Mensualidades por Sección'), 0, 1, 'C');
        $this->Ln(2);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function EncabezadoGrupo($fila)
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, utf8_decode('DATOS DE LA INSTITUCIÓN'), 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->Cell(40, 7, utf8_decode('INSTITUCIÓN:'), 1);
        $this->Cell(120, 7, utf8_decode($fila['nombre_institucion']), 1);
        $this->Cell(25, 7, utf8_decode('RUC:'), 1);
        $this->Cell(60, 7, utf8_decode($fila['ruc_institucion']), 1, 1);

        $this->Cell(40, 7, utf8_decode('TELÉFONO:'), 1);
        $this->Cell(120, 7, utf8_decode($fila['telefono_institucion']), 1);
        $this->Cell(25, 7, utf8_decode('CORREO:'), 1);
        $this->Cell(60, 7, utf8_decode($fila['correo_institucion']), 1, 1);

        $this->Cell(40, 7, utf8_decode('DIRECCIÓN:'), 1);
        $this->Cell(205, 7, utf8_decode($fila['direccion_institucion']), 1, 1);

        $this->Ln(3);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, utf8_decode('GRUPO'), 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->Cell(47.8, 7, utf8_decode('NIVEL'), 1, 0, 'C');
        $this->Cell(47.8, 7, utf8_decode($fila['nombre_nivel']), 1, 0, 'C');
        $this->Cell(47.8, 7, utf8_decode('GRADO'), 1, 0, 'C');
        $this->Cell(47.8, 7, utf8_decode($fila['nombre_grado']), 1, 0, 'C');
        $this->Cell(47.8, 7, utf8_decode('SECCIÓN'), 1, 0, 'C');
        $this->Cell(47.8, 7, utf8_decode($fila['nombre_seccion']), 1, 1, 'C');

        $this->Ln(4);
    }

    function EncabezadoTabla($meses)
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(7, 7, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell(50, 7, utf8_decode('ALUMNO'), 1, 0, 'C');
        $this->Cell(40, 7, utf8_decode('APODERADO'), 1, 0, 'C');
        $this->Cell(20, 7, utf8_decode('TELÉF.'), 1, 0, 'C');
        $this->Cell(20, 7, utf8_decode('CÓDIGO'), 1, 0, 'C');

        foreach ($meses as $mes) {
            $m = (mb_strlen($mes) > 5) ? mb_substr($mes, 0, 5) . '.' : $mes;
            $this->Cell(15, 7, utf8_decode($m), 1, 0, 'C');
        }
        $this->Ln();
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$reporte = new Reportemensualidadxgrado();
$resultados = $reporte->listar();

$lectivo_actual = null;
$nivel_actual   = null;
$grado_actual   = null;
$seccion_actual = null;

$contador = 1;
$meses_encabezado = [];

foreach ($resultados as $fila) {
    $cambia_grupo = (
        $lectivo_actual !== $fila['nombre_lectivo'] ||
        $nivel_actual   !== $fila['nombre_nivel']   ||
        $grado_actual   !== $fila['nombre_grado']   ||
        $seccion_actual !== $fila['nombre_seccion']
    );

    if ($cambia_grupo) {
        if ($lectivo_actual !== null) {
            $pdf->AddPage();
        }

        $lectivo_actual = $fila['nombre_lectivo'];
        $nivel_actual   = $fila['nombre_nivel'];
        $grado_actual   = $fila['nombre_grado'];
        $seccion_actual = $fila['nombre_seccion'];
        $contador = 1;

        $pdf->EncabezadoGrupo($fila);

        $meses_encabezado = array_map('trim', explode(', ', $fila['meses'] ?? ''));
        $pdf->EncabezadoTabla($meses_encabezado);
    }

    $pdf->SetFont('Arial', '', 8);

    $nombre_alumno   = utf8_decode(mb_substr($fila['nombre_alumno'], 0, 25));
    $nombre_apod     = utf8_decode(mb_substr($fila['nombre_apoderado'], 0, 20));
    $tel_apod        = utf8_decode($fila['telefono_apoderado']);
    $codigo_alumno   = utf8_decode($fila['numero_documento_alumno']); // puedes cambiarlo si tienes otro código

    $montos  = array_map('trim', explode(', ', $fila['montos'] ?? ''));
    $estados = array_map('trim', explode(', ', $fila['estados_pago'] ?? ''));

    $pdf->Cell(7, 6, $contador, 1, 0, 'C');
    $pdf->Cell(50, 6, $nombre_alumno, 1, 0, 'L');
    $pdf->Cell(40, 6, $nombre_apod, 1, 0, 'L');
    $pdf->Cell(20, 6, $tel_apod, 1, 0, 'C');
    $pdf->Cell(20, 6, $codigo_alumno, 1, 0, 'C');

    foreach ($meses_encabezado as $idx => $m) {
        $monto  = isset($montos[$idx])  ? $montos[$idx]  : '';
        $estado = isset($estados[$idx]) ? $estados[$idx] : 0;

        if ((string)$estado === '0') {
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(15, 6, utf8_decode($monto), 1, 0, 'C', true);
        } else {
            $pdf->Cell(15, 6, utf8_decode($monto), 1, 0, 'C');
        }
    }

    $pdf->Ln();
    $contador++;
}

$pdf->Output();
