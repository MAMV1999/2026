<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Mensualidad_reporte_x_grado.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, utf8_decode('Reporte de Mensualidades General'), 0, 1, 'C');
        $this->Ln(2);
    }

    function Footer()
    {
        // Hoja personalizada sin footer.
    }

    function EncabezadoInstitucion($fila, $anchoTabla)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($anchoTabla, 7, utf8_decode('DATOS DE LA INSTITUCIÓN'), 0, 1, 'L');

        $this->SetFont('Arial', '', 8);

        $this->Cell(35, 6, utf8_decode('INSTITUCIÓN:'), 1);
        $this->Cell(95, 6, utf8_decode($fila['nombre_institucion'] ?? ''), 1);
        $this->Cell(20, 6, utf8_decode('RUC:'), 1);
        $this->Cell(45, 6, utf8_decode($fila['ruc_institucion'] ?? ''), 1, 1);

        $this->Cell(35, 6, utf8_decode('TELÉFONO:'), 1);
        $this->Cell(95, 6, utf8_decode($fila['telefono_institucion'] ?? ''), 1);
        $this->Cell(20, 6, utf8_decode('CORREO:'), 1);
        $this->Cell(45, 6, utf8_decode($fila['correo_institucion'] ?? ''), 1, 1);

        $this->Cell(35, 6, utf8_decode('DIRECCIÓN:'), 1);
        $this->Cell(160, 6, utf8_decode($fila['direccion_institucion'] ?? ''), 1, 1);

        $this->Ln(4);
    }

    function EncabezadoTabla($meses, $w)
    {
        $this->SetFont('Arial', 'B', 7);

        $this->Cell($w['n'], 7, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell($w['nivel'], 7, utf8_decode('NIVEL'), 1, 0, 'C');
        $this->Cell($w['grado'], 7, utf8_decode('GRADO'), 1, 0, 'C');
        $this->Cell($w['alumno'], 7, utf8_decode('ALUMNO'), 1, 0, 'C');
        $this->Cell($w['apoderado'], 7, utf8_decode('APODERADO'), 1, 0, 'C');
        $this->Cell($w['telefono'], 7, utf8_decode('TELÉFONO'), 1, 0, 'C');
        $this->Cell($w['codigo'], 7, utf8_decode('CÓDIGO'), 1, 0, 'C');

        foreach ($meses as $mes) {
            $textoMes = mb_strlen($mes) > 5 ? mb_substr($mes, 0, 5) . '.' : $mes;
            $this->Cell($w['mes'], 7, utf8_decode($textoMes), 1, 0, 'C');
        }

        $this->Ln();
    }
}

/*
|--------------------------------------------------------------------------
| Cargar datos
|--------------------------------------------------------------------------
*/

$reporte = new Reportemensualidadxgrado();
$resultados = $reporte->listar();

$filas = [];

if ($resultados) {
    while ($row = $resultados->fetch_assoc()) {
        $filas[] = $row;
    }
}

if (count($filas) == 0) {
    die("No hay datos para mostrar.");
}

/*
|--------------------------------------------------------------------------
| Obtener todos los meses del reporte
|--------------------------------------------------------------------------
*/

$meses_encabezado = [];

foreach ($filas as $fila) {
    $mesesFila = array_map('trim', explode(',', $fila['meses'] ?? ''));

    foreach ($mesesFila as $mes) {
        if ($mes !== '' && !in_array($mes, $meses_encabezado)) {
            $meses_encabezado[] = $mes;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Anchos de columnas en milímetros
|--------------------------------------------------------------------------
*/

$w = [
    'n'         => 8,
    'nivel'     => 24,
    'grado'     => 24,
    'alumno'    => 55,
    'apoderado' => 45,
    'telefono'  => 24,
    'codigo'    => 25,
    'mes'       => 15
];

/*
|--------------------------------------------------------------------------
| Calcular ancho real de tabla
|--------------------------------------------------------------------------
*/

$anchoTabla =
    $w['n'] +
    $w['nivel'] +
    $w['grado'] +
    $w['alumno'] +
    $w['apoderado'] +
    $w['telefono'] +
    $w['codigo'] +
    (count($meses_encabezado) * $w['mes']);

/*
|--------------------------------------------------------------------------
| Calcular tamaño exacto de hoja
|--------------------------------------------------------------------------
*/

$margenIzquierdo = 5;
$margenSuperior = 5;
$margenDerecho = 5;
$margenInferior = 8;

$altoTitulo = 10;
$altoInstitucion = 28;
$altoEspacios = 6;
$altoEncabezadoTabla = 7;
$altoFila = 6;

$filasTotalesExtra = 3;

$anchoHoja = $anchoTabla + $margenIzquierdo + $margenDerecho;

$altoHoja =
    $margenSuperior +
    $altoTitulo +
    $altoInstitucion +
    $altoEspacios +
    $altoEncabezadoTabla +
    (count($filas) * $altoFila) +
    ($filasTotalesExtra * $altoFila) +
    $margenInferior;

/*
|--------------------------------------------------------------------------
| Crear PDF
|--------------------------------------------------------------------------
*/

$pdf = new PDF('P', 'mm', [$anchoHoja, $altoHoja]);

$pdf->SetMargins($margenIzquierdo, $margenSuperior, $margenDerecho);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

$pdf->EncabezadoInstitucion($filas[0], $anchoTabla);
$pdf->EncabezadoTabla($meses_encabezado, $w);

$pdf->SetFont('Arial', '', 7);

$contador = 1;

/*
|--------------------------------------------------------------------------
| Totales por mes
|--------------------------------------------------------------------------
*/

$totalesPagados = [];
$totalesPendientes = [];
$totalesGeneral = [];

foreach ($meses_encabezado as $mes) {
    $totalesPagados[$mes] = 0;
    $totalesPendientes[$mes] = 0;
    $totalesGeneral[$mes] = 0;
}

/*
|--------------------------------------------------------------------------
| Filas de alumnos
|--------------------------------------------------------------------------
*/

foreach ($filas as $fila) {
    $nivel = utf8_decode(mb_substr($fila['nombre_nivel'] ?? '', 0, 14));
    $grado = utf8_decode(mb_substr($fila['nombre_grado'] ?? '', 0, 14));
    $alumno = utf8_decode(mb_substr($fila['nombre_alumno'] ?? '', 0, 32));
    $apoderado = utf8_decode(mb_substr($fila['nombre_apoderado'] ?? '', 0, 26));
    $telefono = utf8_decode($fila['telefono_apoderado'] ?? '');
    $codigo = utf8_decode($fila['numero_documento_alumno'] ?? '');

    $mesesFila = array_map('trim', explode(',', $fila['meses'] ?? ''));
    $montosFila = array_map('trim', explode(',', $fila['montos'] ?? ''));
    $estadosFila = array_map('trim', explode(',', $fila['estados_pago'] ?? ''));

    $datosMes = [];

    foreach ($mesesFila as $idx => $mes) {
        if ($mes !== '') {
            $monto = isset($montosFila[$idx]) ? (float)$montosFila[$idx] : 0;
            $estado = $estadosFila[$idx] ?? '';

            $datosMes[$mes] = [
                'monto'  => $monto,
                'estado' => $estado
            ];

            if ((string)$estado === '1') {
                $totalesPagados[$mes] += $monto;
            }

            if ((string)$estado === '0') {
                $totalesPendientes[$mes] += $monto;
            }

            $totalesGeneral[$mes] += $monto;
        }
    }

    $pdf->Cell($w['n'], $altoFila, $contador, 1, 0, 'C');
    $pdf->Cell($w['nivel'], $altoFila, $nivel, 1, 0, 'C');
    $pdf->Cell($w['grado'], $altoFila, $grado, 1, 0, 'C');
    $pdf->Cell($w['alumno'], $altoFila, $alumno, 1, 0, 'L');
    $pdf->Cell($w['apoderado'], $altoFila, $apoderado, 1, 0, 'L');
    $pdf->Cell($w['telefono'], $altoFila, $telefono, 1, 0, 'C');
    $pdf->Cell($w['codigo'], $altoFila, $codigo, 1, 0, 'C');

    foreach ($meses_encabezado as $mes) {
        $monto = $datosMes[$mes]['monto'] ?? '';
        $estado = $datosMes[$mes]['estado'] ?? '';

        if ($monto !== '') {
            $montoTexto = number_format((float)$monto, 2);
        } else {
            $montoTexto = '';
        }

        if ((string)$estado === '0') {
            // Pendiente
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell($w['mes'], $altoFila, utf8_decode($montoTexto), 1, 0, 'C', true);
        } else {
            // Cancelado o vacío
            $pdf->Cell($w['mes'], $altoFila, utf8_decode($montoTexto), 1, 0, 'C');
        }
    }

    $pdf->Ln();
    $contador++;
}

/*
|--------------------------------------------------------------------------
| Filas de totales
|--------------------------------------------------------------------------
*/

$anchoAntesMeses =
    $w['n'] +
    $w['nivel'] +
    $w['grado'] +
    $w['alumno'] +
    $w['apoderado'] +
    $w['telefono'] +
    $w['codigo'];

$pdf->SetFont('Arial', 'B', 7);

/*
|--------------------------------------------------------------------------
| Total pagado
|--------------------------------------------------------------------------
*/

$pdf->SetFillColor(220, 220, 220);
$pdf->Cell($anchoAntesMeses, $altoFila, utf8_decode('TOTAL MENSUALIDADES PAGADAS'), 1, 0, 'R', true);

foreach ($meses_encabezado as $mes) {
    $pdf->Cell($w['mes'], $altoFila, number_format($totalesPagados[$mes], 2), 1, 0, 'C', true);
}

$pdf->Ln();

/*
|--------------------------------------------------------------------------
| Total pendiente
|--------------------------------------------------------------------------
*/

$pdf->SetFillColor(200, 200, 200);
$pdf->Cell($anchoAntesMeses, $altoFila, utf8_decode('TOTAL MENSUALIDADES PENDIENTES'), 1, 0, 'R', true);

foreach ($meses_encabezado as $mes) {
    $pdf->Cell($w['mes'], $altoFila, number_format($totalesPendientes[$mes], 2), 1, 0, 'C', true);
}

$pdf->Ln();

/*
|--------------------------------------------------------------------------
| Total general
|--------------------------------------------------------------------------
*/

$pdf->SetFillColor(180, 180, 180);
$pdf->Cell($anchoAntesMeses, $altoFila, utf8_decode('TOTAL PAGADAS + PENDIENTES'), 1, 0, 'R', true);

foreach ($meses_encabezado as $mes) {
    $pdf->Cell($w['mes'], $altoFila, number_format($totalesGeneral[$mes], 2), 1, 0, 'C', true);
}

$pdf->Ln();

$pdf->Output();