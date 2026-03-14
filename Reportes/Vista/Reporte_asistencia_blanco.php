<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/Reporte_asistencia_blanco.php");

class PDF extends FPDF
{
    public $mesActual = '';
    public $datosGrupo = [];

    public function Header()
    {
        if (empty($this->datosGrupo)) {
            return;
        }

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, utf8_decode('REGISTRO DE ASISTENCIA'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->Cell(35, 6, utf8_decode('INSTITUCIÓN:'), 0, 0);
        $this->Cell(90, 6, utf8_decode($this->datosGrupo['institucion']), 0, 0);

        $this->Cell(20, 6, 'NIVEL:', 0, 0);
        $this->Cell(40, 6, utf8_decode($this->datosGrupo['nivel']), 0, 0);

        $this->Cell(15, 6, 'MES:', 0, 0);
        $this->Cell(0, 6, utf8_decode($this->mesActual), 0, 1);

        $this->Cell(35, 6, 'GRADO:', 0, 0);
        $this->Cell(90, 6, utf8_decode($this->datosGrupo['grado']), 0, 0);

        $this->Cell(20, 6, utf8_decode('SECCIÓN:'), 0, 0);
        $this->Cell(40, 6, utf8_decode($this->datosGrupo['seccion']), 0, 1);

        $this->Cell(35, 6, 'TUTOR:', 0, 0);
        $this->Cell(90, 6, utf8_decode($this->datosGrupo['tutor']), 0, 0);

        $this->Cell(20, 6, utf8_decode('TELÉFONO:'), 0, 0);
        $this->Cell(0, 6, utf8_decode($this->datosGrupo['telefono_tutor']), 0, 1);

        $this->Ln(2);
    }

    public function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    public function tablaMes($filas, $columnasMes)
    {
        $anchoNumero = 8;
        $anchoAlumno = 80;

        $anchoHoja = $this->GetPageWidth();
        $margenIzq = $this->lMargin;
        $margenDer = $this->rMargin;

        $anchoDisponible = $anchoHoja - $margenIzq - $margenDer;

        $espacioDias = $anchoDisponible - ($anchoNumero + $anchoAlumno);

        $anchoDia = $espacioDias / count($columnasMes);

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);

        $this->Cell($anchoNumero, 10, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell($anchoAlumno, 10, 'ALUMNO', 1, 0, 'C', true);

        foreach ($columnasMes as $columna) {
            $dia = substr($columna, -2);
            $this->Cell($anchoDia, 10, $dia, 1, 0, 'C', true);
        }

        $this->Ln();

        $this->SetFont('Arial', '', 7);
        $n = 1;

        foreach ($filas as $fila) {
            $this->Cell($anchoNumero, 8, $n, 1, 0, 'C');
            $this->Cell($anchoAlumno, 8, utf8_decode($fila['alumno']), 1, 0, 'L');

            foreach ($columnasMes as $columna) {
                $valor = isset($fila[$columna]) ? $fila[$columna] : '';
                $this->Cell($anchoDia, 8, utf8_decode($valor), 1, 0, 'C');
            }

            $this->Ln();
            $n++;
        }
    }
}

function nombreMes($prefijo)
{
    $meses = [
        'MAR' => 'MARZO 2026',
        'ABR' => 'ABRIL 2026',
        'MAY' => 'MAYO 2026',
        'JUN' => 'JUNIO 2026',
        'JUL' => 'JULIO 2026',
        'AGO' => 'AGOSTO 2026',
        'SEP' => 'SEPTIEMBRE 2026',
        'OCT' => 'OCTUBRE 2026',
        'NOV' => 'NOVIEMBRE 2026',
        'DIC' => 'DICIEMBRE 2026',
    ];

    return isset($meses[$prefijo]) ? $meses[$prefijo] : $prefijo;
}

$modelo = new Asistencia_blanco();
$rspta = $modelo->listarAsistencia_blanco();

$registros = [];
$columnasDias = [];

while ($row = $rspta->fetch_assoc()) {
    if (empty($columnasDias)) {
        foreach ($row as $campo => $valor) {
            if (preg_match('/^(MAR|ABR|MAY|JUN|JUL|AGO|SEP|OCT|NOV|DIC)_\d{2}$/', $campo)) {
                $columnasDias[] = $campo;
            }
        }
    }
    $registros[] = $row;
}

$grupos = [];

foreach ($registros as $fila) {
    $claveGrupo =
        $fila['institucion'] . '||' .
        $fila['nivel'] . '||' .
        $fila['grado'] . '||' .
        $fila['seccion'] . '||' .
        $fila['tutor'] . '||' .
        $fila['telefono_tutor'];

    $grupos[$claveGrupo][] = $fila;
}

$mesesOrden = ['MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(true, 10);

foreach ($grupos as $clave => $filasGrupo) {
    $primeraFila = $filasGrupo[0];

    $datosGrupo = [
        'institucion'    => $primeraFila['institucion'],
        'nivel'          => $primeraFila['nivel'],
        'grado'          => $primeraFila['grado'],
        'seccion'        => $primeraFila['seccion'],
        'tutor'          => $primeraFila['tutor'],
        'telefono_tutor' => $primeraFila['telefono_tutor'],
    ];

    foreach ($mesesOrden as $mes) {
        $columnasMes = array_filter($columnasDias, function ($col) use ($mes) {
            return strpos($col, $mes . '_') === 0;
        });

        if (empty($columnasMes)) {
            continue;
        }

        $pdf->datosGrupo = $datosGrupo;
        $pdf->mesActual = nombreMes($mes);

        $pdf->AddPage();
        $pdf->tablaMes($filasGrupo, $columnasMes);
    }
}

$pdf->Output('I', 'reporte_asistencia_blanco.pdf');
?>