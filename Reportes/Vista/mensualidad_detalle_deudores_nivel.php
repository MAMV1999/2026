<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/mensualidad_detalle_deudores_nivel.php");

date_default_timezone_set('America/Lima');

class PDF extends FPDF
{
    public $mostrarCabecera = true;

    public function Header()
    {
        if ($this->mostrarCabecera == false) {
            return;
        }

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('REPORTE DE ALUMNOS DEUDORES X NIVEL'), 0, 1, 'C');
        $this->Ln(2);
    }

    public function caratulaNivel($nivel, $fecha_reporte)
    {
        $nivel_mayuscula = function_exists('mb_strtoupper')
            ? mb_strtoupper($nivel, 'UTF-8')
            : strtoupper($nivel);

        $this->SetY(75);

        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 12, utf8_decode('REPORTE DE ALUMNOS DEUDORES'), 0, 1, 'C');

        $this->Ln(8);

        $this->SetFont('Arial', 'B', 24);
        $this->Cell(0, 15, utf8_decode('NIVEL: ' . $nivel_mayuscula), 0, 1, 'C');

        $this->Ln(8);

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 8, utf8_decode('A continuación inicia el detalle del nivel ' . $nivel), 0, 1, 'C');

        $this->Ln(15);

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, utf8_decode('Fecha de reporte: ' . $fecha_reporte), 0, 1, 'C');
    }

    public function encabezadoTabla()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(200, 220, 255);

        $this->Cell(8, 8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(15, 8, utf8_decode('GRADO'), 1, 0, 'C', true);
        $this->Cell(59, 8, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('TELÉFONO'), 1, 0, 'C', true);
        $this->Cell(59, 8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(0,  8, utf8_decode('ESTADO'), 1, 1, 'C', true);
    }
}

function cortarPorAncho($pdf, $texto, $anchoCelda, $sufijo = '...')
{
    $texto = (string)$texto;

    $textoFPDF = utf8_decode($texto);
    $sufijoFPDF = utf8_decode($sufijo);

    if ($pdf->GetStringWidth($textoFPDF) <= $anchoCelda) {
        return $textoFPDF;
    }

    $anchoSufijo = $pdf->GetStringWidth($sufijoFPDF);
    $maxAnchoTexto = $anchoCelda - $anchoSufijo;

    if ($maxAnchoTexto < 1) {
        return $sufijoFPDF;
    }

    $len = mb_strlen($texto, 'UTF-8');

    while ($len > 0) {
        $corte = mb_substr($texto, 0, $len, 'UTF-8');
        $corteFPDF = utf8_decode($corte);

        if ($pdf->GetStringWidth($corteFPDF) <= $maxAnchoTexto) {
            return $corteFPDF . $sufijoFPDF;
        }

        $len--;
    }

    return $sufijoFPDF;
}

function imprimirEncabezadoMes($pdf, $nivel, $mes)
{
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, utf8_decode("NIVEL: $nivel   |   MES: $mes"), 0, 1, 'L');
    $pdf->Ln(1);

    $pdf->encabezadoTabla();
    $pdf->SetFont('Arial', '', 8);
}

function verificarEspacioMes($pdf)
{
    if ($pdf->GetY() + 23 > 287) {
        $pdf->AddPage();
    }
}

function verificarEspacioFila($pdf, $nivel, $mes)
{
    if ($pdf->GetY() + 7 > 287) {
        $pdf->AddPage();
        imprimirEncabezadoMes($pdf, $nivel, $mes);
    }
}

// PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(false);

// Datos
$modelo = new Mensualidad_detalle_deudores();
$data = $modelo->listar_mensualidad_detalle_deudores();

$fecha_reporte = date('d/m/Y H:i');

$niveles = array();
$hay_registros = false;

/*
    PRIMERO AGRUPAMOS TODO.

    Así evitamos este error:
    Inicial - Marzo
    Primaria - Marzo
    Inicial - Abril
    Primaria - Abril

    Y lo convertimos en:
    Inicial:
        Marzo
        Abril
        Mayo
    Primaria:
        Marzo
        Abril
        Mayo
*/
while ($fila = $data->fetch_assoc()) {

    $hay_registros = true;

    $nivel = (string)$fila['nivel_nombre'];
    $mes = (string)$fila['mensualidad_mes_nombre'];

    if (!isset($niveles[$nivel])) {
        $niveles[$nivel] = array();
    }

    if (!isset($niveles[$nivel][$mes])) {
        $niveles[$nivel][$mes] = array();
    }

    $niveles[$nivel][$mes][] = $fila;
}

// Si no hay registros
if ($hay_registros == false) {
    $pdf->mostrarCabecera = true;
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Ln(20);
    $pdf->Cell(0, 8, utf8_decode('NO HAY ALUMNOS DEUDORES PARA MOSTRAR'), 0, 1, 'C');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(4);
    $pdf->Cell(0, 6, utf8_decode('Solo se muestran meses vencidos antes del mes actual.'), 0, 1, 'C');

    $pdf->Output();
    exit;
}

// IMPRIMIMOS NIVEL POR NIVEL
foreach ($niveles as $nivel => $meses) {

    // 1. Carátula del nivel
    $pdf->mostrarCabecera = false;
    $pdf->AddPage();
    $pdf->caratulaNivel($nivel, $fecha_reporte);

    // 2. Página donde van TODOS los meses de ese nivel
    $pdf->mostrarCabecera = true;
    $pdf->AddPage();

    // Recorremos todos los meses del nivel actual
    foreach ($meses as $mes => $filas_mes) {

        verificarEspacioMes($pdf);

        imprimirEncabezadoMes($pdf, $nivel, $mes);

        $numero = 1;

        foreach ($filas_mes as $fila) {

            verificarEspacioFila($pdf, $nivel, $mes);

            // === Anchos de columnas ===
            $wN = 8;
            $wGrado = 15;
            $wApod = 59;
            $wTel = 20;
            $wAlumno = 59;
            $wCod = 20;

            // Preparar textos
            $grado     = cortarPorAncho($pdf, $fila['grado_nombre'], $wGrado);
            $apoderado = cortarPorAncho($pdf, $fila['apoderado_nombre'], $wApod);
            $telefono  = cortarPorAncho($pdf, $fila['apoderado_telefono'], $wTel);
            $alumno    = cortarPorAncho($pdf, $fila['alumno_nombre'], $wAlumno);
            $codigo    = cortarPorAncho($pdf, $fila['alumno_codigo'], $wCod);
            $estado    = utf8_decode((string)$fila['detalle_estado_pago']);

            // Fila
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell($wN, 7, $numero++, 1, 0, 'C');
            $pdf->Cell($wGrado, 7, $grado, 1, 0, 'C');
            $pdf->Cell($wApod, 7, $apoderado, 1, 0, 'C');
            $pdf->Cell($wTel, 7, $telefono, 1, 0, 'C');
            $pdf->Cell($wAlumno, 7, $alumno, 1, 0, 'C');
            $pdf->Cell($wCod, 7, $codigo, 1, 0, 'C');
            $pdf->Cell(0,  7, $estado, 1, 1, 'C');
        }

        $pdf->Ln(3);
    }
}

$pdf->Output();
?>