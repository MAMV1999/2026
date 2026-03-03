<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/mensualidad_detalle_deudores.php");

class PDF extends FPDF
{
    public function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('REPORTE DE ALUMNOS DEUDORES X NIVEL'), 0, 1, 'C');
        $this->Ln(2);
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

/**
 * Corta el texto para que quepa en el ancho de la celda (mm) según la fuente actual del PDF.
 * Importante: FPDF trabaja en ISO-8859-1, por eso convertimos con utf8_decode.
 */
function cortarPorAncho($pdf, $texto, $anchoCelda, $sufijo = '...')
{
    $texto = (string)$texto;

    // Convertir a encoding que usa FPDF para medir y dibujar
    $textoFPDF = utf8_decode($texto);
    $sufijoFPDF = utf8_decode($sufijo);

    // Si ya entra, devolver tal cual
    if ($pdf->GetStringWidth($textoFPDF) <= $anchoCelda) {
        return $textoFPDF;
    }

    // Asegurar que el sufijo también entre
    $anchoSufijo = $pdf->GetStringWidth($sufijoFPDF);
    $maxAnchoTexto = $anchoCelda - $anchoSufijo;
    if ($maxAnchoTexto < 1) {
        return $sufijoFPDF; // caso extremo
    }

    // Ir recortando hasta que entre
    // (usamos mb_* en UTF-8 para no cortar caracteres multibyte)
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

// PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();

// Datos
$modelo = new Mensualidad_detalle_deudores();
$data = $modelo->listar_mensualidad_detalle_deudores();

$pdf->SetFont('Arial', '', 8);

$numero = 1;
$grupo_actual = "";

// Recorremos
while ($fila = $data->fetch_assoc()) {

    $mes   = (string)$fila['mensualidad_mes_nombre'];
    $nivel = (string)$fila['nivel_nombre'];
    $grupo_nuevo = $mes . '||' . $nivel;

    // Si cambia MES o NIVEL => nueva hoja
    if ($grupo_actual !== $grupo_nuevo) {
        $grupo_actual = $grupo_nuevo;
        $numero = 1;

        // Para el primer grupo no agregues página extra
        if ($pdf->PageNo() > 1 || $pdf->GetY() > 30) {
            $pdf->AddPage();
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, utf8_decode("MES: $mes   |   NIVEL: $nivel"), 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->encabezadoTabla();
        $pdf->SetFont('Arial', '', 8);
    }

    // === Anchos de columnas (deben coincidir con tus Cell) ===
    $wN = 8;
    $wGrado = 15;
    $wApod = 59;
    $wTel = 20;
    $wAlumno = 59;
    $wCod = 20;
    // Estado es 0 (auto) => no recortamos por ancho fijo, pero sí podemos si quieres

    // Preparar textos (recorte por ancho real)
    $grado     = cortarPorAncho($pdf, $fila['grado_nombre'], $wGrado);
    $apoderado = cortarPorAncho($pdf, $fila['apoderado_nombre'], $wApod);
    $telefono  = cortarPorAncho($pdf, $fila['apoderado_telefono'], $wTel);
    $alumno    = cortarPorAncho($pdf, $fila['alumno_nombre'], $wAlumno);
    $codigo    = cortarPorAncho($pdf, $fila['alumno_codigo'], $wCod);
    $estado    = utf8_decode((string)$fila['detalle_estado_pago']);

    // Fila (YA NO uses utf8_decode aquí porque ya devolvemos en encoding FPDF)
    $pdf->Cell($wN, 7, $numero++, 1, 0, 'C');
    $pdf->Cell($wGrado, 7, $grado, 1, 0, 'C');
    $pdf->Cell($wApod, 7, $apoderado, 1, 0, 'C');
    $pdf->Cell($wTel, 7, $telefono, 1, 0, 'C');
    $pdf->Cell($wAlumno, 7, $alumno, 1, 0, 'C');
    $pdf->Cell($wCod, 7, $codigo, 1, 0, 'C');
    $pdf->Cell(0,  7, $estado, 1, 1, 'C');
}

$pdf->Output();
?>