<?php

require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Mensualidad_reporte_x_grado.php");

class PDF extends FPDF
{
    public function Header()
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, utf8_decode('Reporte de Mensualidades General'), 0, 1, 'C');
        $this->Ln(2);
    }

    public function Footer()
    {
        // Hoja personalizada sin footer.
    }

    public function EncabezadoInstitucion($fila, $anchoTabla)
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

    public function EncabezadoTabla($meses, $w)
    {
        $this->SetFont('Arial', 'B', 7);

        $this->Cell($w['n'], 7, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell($w['nivel'], 7, utf8_decode('NIVEL'), 1, 0, 'C');
        $this->Cell($w['grado'], 7, utf8_decode('GRADO'), 1, 0, 'C');
        $this->Cell($w['alumno'], 7, utf8_decode('ALUMNO'), 1, 0, 'C');
        $this->Cell($w['apoderado'], 7, utf8_decode('APODERADO'), 1, 0, 'C');
        $this->Cell($w['telefono'], 7, utf8_decode('TELÉFONO'), 1, 0, 'C');
        $this->Cell($w['codigo'], 7, utf8_decode('DOCUMENTO'), 1, 0, 'C');

        foreach ($meses as $mes) {
            $textoMes = mb_strlen($mes, 'UTF-8') > 5
                ? mb_substr($mes, 0, 5, 'UTF-8') . '.'
                : $mes;

            $this->Cell($w['mes'], 7, utf8_decode($textoMes), 1, 0, 'C');
        }

        $this->Ln();
    }
}

/**
 * Convierte listas separadas por comas en arreglos.
 */
function separarLista($valor)
{
    $valor = (string)($valor ?? '');

    if ($valor === '') {
        return [];
    }

    return array_map('trim', explode(',', $valor));
}

/**
 * Carga los datos.
 */
$reporte = new Reportemensualidadxgrado();
$resultados = $reporte->listar();

$filas = [];

if ($resultados) {
    while ($row = $resultados->fetch_assoc()) {
        $filas[] = $row;
    }
}

if (count($filas) === 0) {
    die('No hay datos para mostrar.');
}

/**
 * Obtiene los meses que aparecerán como encabezados.
 */
$meses_encabezado = [];

foreach ($filas as $fila) {
    $mesesFila = separarLista($fila['meses'] ?? '');

    foreach ($mesesFila as $mes) {
        if ($mes !== '' && !in_array($mes, $meses_encabezado, true)) {
            $meses_encabezado[] = $mes;
        }
    }
}

/**
 * Anchos de columnas en milímetros.
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

$anchoTabla =
    $w['n'] +
    $w['nivel'] +
    $w['grado'] +
    $w['alumno'] +
    $w['apoderado'] +
    $w['telefono'] +
    $w['codigo'] +
    (count($meses_encabezado) * $w['mes']);

/**
 * Tamaño de página personalizado.
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

/**
 * Crea el PDF.
 */
$pdf = new PDF('P', 'mm', [$anchoHoja, $altoHoja]);
$pdf->SetMargins($margenIzquierdo, $margenSuperior, $margenDerecho);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

$pdf->EncabezadoInstitucion($filas[0], $anchoTabla);
$pdf->EncabezadoTabla($meses_encabezado, $w);
$pdf->SetFont('Arial', '', 7);

/**
 * Inicializa los totales por mes.
 */
$totalesPagados = [];
$totalesFacturacionRealizada = [];
$totalesFacturacionPendiente = [];

foreach ($meses_encabezado as $mes) {
    $totalesPagados[$mes] = 0;
    $totalesFacturacionRealizada[$mes] = 0;
    $totalesFacturacionPendiente[$mes] = 0;
}

/**
 * Imprime las filas de alumnos y calcula los totales.
 */
$contador = 1;

foreach ($filas as $fila) {
    $nivel = utf8_decode(mb_substr($fila['nombre_nivel'] ?? '', 0, 14, 'UTF-8'));
    $grado = utf8_decode(mb_substr($fila['nombre_grado'] ?? '', 0, 14, 'UTF-8'));
    $alumno = utf8_decode(mb_substr($fila['nombre_alumno'] ?? '', 0, 32, 'UTF-8'));
    $apoderado = utf8_decode(mb_substr($fila['nombre_apoderado'] ?? '', 0, 26, 'UTF-8'));
    $telefono = utf8_decode($fila['telefono_apoderado'] ?? '');

    // Documento del apoderado en lugar del código del alumno.
    $codigo = utf8_decode($fila['numero_documento_apoderado'] ?? '');

    $mesesFila = separarLista($fila['meses'] ?? '');
    $montosFila = separarLista($fila['montos'] ?? '');
    $estadosPagoFila = separarLista($fila['estados_pago'] ?? '');
    $estadosReciboFila = separarLista($fila['estados_recibo'] ?? '');

    $datosMes = [];

    foreach ($mesesFila as $idx => $mes) {
        if ($mes === '') {
            continue;
        }

        $monto = isset($montosFila[$idx]) && $montosFila[$idx] !== ''
            ? (float)$montosFila[$idx]
            : 0;

        $estadoPago = isset($estadosPagoFila[$idx])
            ? (string)$estadosPagoFila[$idx]
            : '';

        $estadoRecibo = isset($estadosReciboFila[$idx])
            ? (string)$estadosReciboFila[$idx]
            : '';

        $datosMes[$mes] = [
            'monto'  => $monto,
            'pagado' => $estadoPago,
            'recibo' => $estadoRecibo
        ];

        // Total pagado: solo mensualidades con pagado = 1.
        if ($estadoPago === '1') {
            $totalesPagados[$mes] += $monto;
        }

        // Facturación realizada: solo mensualidades con recibo = 1.
        if ($estadoRecibo === '1') {
            $totalesFacturacionRealizada[$mes] += $monto;
        }

        // Facturación pendiente: solo mensualidades con recibo = 0.
        if ($estadoRecibo === '0') {
            $totalesFacturacionPendiente[$mes] += $monto;
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
        $datos = $datosMes[$mes] ?? null;
        $montoTexto = '';

        // Mostrar el monto solo cuando pagado = 1 y el monto no sea cero.
        if (
            $datos !== null &&
            $datos['pagado'] === '1' &&
            (float)$datos['monto'] != 0
        ) {
            $montoTexto = number_format($datos['monto'], 2);
        }

        // Pintar de plomo cuando recibo = 0.
        if ($datos !== null && $datos['recibo'] === '0') {
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell($w['mes'], $altoFila, utf8_decode($montoTexto), 1, 0, 'C', true);
        } else {
            $pdf->Cell($w['mes'], $altoFila, utf8_decode($montoTexto), 1, 0, 'C');
        }
    }

    $pdf->Ln();
    $contador++;
}

/**
 * Filas de totales.
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

/**
 * Total de mensualidades pagadas.
 */
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(
    $anchoAntesMeses,
    $altoFila,
    utf8_decode('TOTAL DE MENSUALIDADES PAGADAS'),
    1,
    0,
    'R',
    true
);

foreach ($meses_encabezado as $mes) {
    $pdf->Cell(
        $w['mes'],
        $altoFila,
        number_format($totalesPagados[$mes], 2),
        1,
        0,
        'C',
        true
    );
}

$pdf->Ln();

/**
 * Facturación realizada.
 */
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(
    $anchoAntesMeses,
    $altoFila,
    utf8_decode('FACTURACIÓN REALIZADA'),
    1,
    0,
    'R',
    true
);

foreach ($meses_encabezado as $mes) {
    $pdf->Cell(
        $w['mes'],
        $altoFila,
        number_format($totalesFacturacionRealizada[$mes], 2),
        1,
        0,
        'C',
        true
    );
}

$pdf->Ln();

/**
 * Facturación pendiente.
 */
$pdf->SetFillColor(180, 180, 180);
$pdf->Cell(
    $anchoAntesMeses,
    $altoFila,
    utf8_decode('FACTURACIÓN PENDIENTE'),
    1,
    0,
    'R',
    true
);

foreach ($meses_encabezado as $mes) {
    $pdf->Cell(
        $w['mes'],
        $altoFila,
        number_format($totalesFacturacionPendiente[$mes], 2),
        1,
        0,
        'C',
        true
    );
}

$pdf->Ln();

$pdf->Output();