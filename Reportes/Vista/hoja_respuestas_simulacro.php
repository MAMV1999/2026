<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/hoja_respuestas_simulacro.php");

class PDFSimulacro extends FPDF
{
    protected $fecha_actual;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_actual = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_actual = $fecha_actual;

        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(true, 10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Circle($x, $y, $r, $style = '')
    {
        $this->Ellipse($x, $y, $r, $r, $style);
    }

    function Ellipse($x, $y, $rx, $ry, $style = '')
    {
        if ($style == 'F') {
            $op = 'f';
        } elseif ($style == 'FD' || $style == 'DF') {
            $op = 'B';
        } else {
            $op = 'S';
        }

        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;

        $k = $this->k;
        $h = $this->h;

        $this->_out(sprintf('%.2F %.2F m', ($x + $rx) * $k, ($h - $y) * $k));

        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $rx) * $k,
            ($h - ($y - $ly)) * $k,
            ($x + $lx) * $k,
            ($h - ($y - $ry)) * $k,
            $x * $k,
            ($h - ($y - $ry)) * $k
        ));

        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x - $lx) * $k,
            ($h - ($y - $ry)) * $k,
            ($x - $rx) * $k,
            ($h - ($y - $ly)) * $k,
            ($x - $rx) * $k,
            ($h - $y) * $k
        ));

        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x - $rx) * $k,
            ($h - ($y + $ly)) * $k,
            ($x - $lx) * $k,
            ($h - ($y + $ry)) * $k,
            $x * $k,
            ($h - ($y + $ry)) * $k
        ));

        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $lx) * $k,
            ($h - ($y + $ry)) * $k,
            ($x + $rx) * $k,
            ($h - ($y + $ly)) * $k,
            ($x + $rx) * $k,
            ($h - $y) * $k
        ));

        $this->_out($op);
    }

    function ajustarTexto($texto, $ancho)
    {
        $texto = trim($texto);

        if ($this->GetStringWidth(utf8_decode($texto)) <= $ancho) {
            return $texto;
        }

        while ($this->GetStringWidth(utf8_decode($texto . '...')) > $ancho && strlen($texto) > 0) {
            $texto = substr($texto, 0, -1);
        }

        return $texto . '...';
    }

    function addEncabezado($datos)
    {
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 7, utf8_decode($datos['nombre_institucion']), 0, 1, 'C');

        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 7, 'SIMULACRO', 0, 1, 'C');

        $this->SetFont('Arial', '', 13);
        $this->Cell(0, 7, utf8_decode($this->fecha_actual), 0, 1, 'C');

        $this->Ln(2);
    }

    function addDatosPersonales($datos)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 7, 'DATOS PERSONALES', 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(255, 255, 255);

        $this->Cell(40, 7, 'AULA', 1, 0, 'L');
        $this->Cell(0, 7, utf8_decode($datos['nombre_nivel'] . ' - ' . $datos['nombre_grado'] . ' - ' . $datos['nombre_seccion']), 1, 1, 'L');

        $this->Cell(40, 7, 'TUTOR', 1, 0, 'L');
        $this->Cell(0, 7, utf8_decode($datos['nombre_tutor']), 1, 1, 'L');

        $this->Cell(40, 7, 'ALUMNO(A)', 1, 0, 'L');
        $this->Cell(0, 7, utf8_decode($datos['nombre_alumno']), 1, 1, 'L');

        $this->Cell(40, 7, 'APODERADO', 1, 0, 'L');
        $this->Cell(0, 7, utf8_decode($datos['nombre_apoderado']), 1, 1, 'L');

        $this->Cell(40, 7, 'TELEFONO', 1, 0, 'L');
        $this->Cell(0, 7, utf8_decode($datos['telefono_apoderado']), 1, 1, 'L');

        $this->Ln(3);
    }

    function addTituloCursoConNota($x, $y, $nombre_curso)
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 10);

        /*
            Cada columna mide 60 mm.
            Distribución:
            - Curso: 35 mm
            - Cuadro de nota: 20 mm x 9 mm
            Ya no se imprime la palabra NOTA.
        */
        $nombre_curso = $this->ajustarTexto($nombre_curso, 34);

        $this->Cell(35, 9, utf8_decode($nombre_curso), 0, 0, 'L');

        // Cuadro grande para que el docente coloque la nota
        $this->Cell(20, 9, '', 1, 0, 'C');
    }

    function addHojaRespuestas($estructura)
    {
        $cursos = $this->parsearEstructura($estructura);

        if (empty($cursos)) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 8, 'Estructura de preguntas no valida.', 0, 1, 'C');
            return;
        }

        $cantidad_preguntas = 0;
        $cursos_inicio = array();

        foreach ($cursos as $curso) {
            if ($curso['fin'] > $cantidad_preguntas) {
                $cantidad_preguntas = $curso['fin'];
            }

            $cursos_inicio[$curso['inicio']] = $curso['curso'];
        }

        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 7, 'HOJA DE RESPUESTAS', 1, 1, 'L', true);

        $this->Ln(2);

        $opciones = array('A', 'B', 'C', 'D');

        $inicioX = 15;
        $inicioY = $this->GetY();

        $anchoColumna = 60;
        $altoFila = 8.1;

        $radio = 3.25;
        $separacionOpciones = 9.2;

        $preguntasPorColumna = ceil($cantidad_preguntas / 3);

        $extraYColumna = array(0, 0, 0);

        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.30);

        for ($i = 1; $i <= $cantidad_preguntas; $i++) {
            $columna = floor(($i - 1) / $preguntasPorColumna);
            $fila = ($i - 1) % $preguntasPorColumna;

            $x = $inicioX + ($columna * $anchoColumna);
            $y = $inicioY + ($fila * $altoFila) + $extraYColumna[$columna];

            // Nombre del curso + cuadro de nota
            if (isset($cursos_inicio[$i])) {
                $this->addTituloCursoConNota($x, $y, $cursos_inicio[$i]);

                // Se aumenta el espacio vertical por el cuadro grande
                $extraYColumna[$columna] = $extraYColumna[$columna] + 9.8;
                $y = $y + 9.8;
            }

            // Número de pregunta
            $this->SetXY($x, $y);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(10, 6.5, $i . '.', 0, 0, 'R');

            // Opciones A B C D
            $xOpciones = $x + 14;

            foreach ($opciones as $index => $opcion) {
                $cx = $xOpciones + ($index * $separacionOpciones);
                $cy = $y + 3.25;

                $this->Circle($cx, $cy, $radio);

                $this->SetXY($cx - 2.4, $cy - 2.45);
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(4.8, 4.8, $opcion, 0, 0, 'C');
            }
        }
    }

    function parsearEstructura($estructura)
    {
        $lineas = explode("\n", $estructura);
        $cursos = array();

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            if ($linea != '') {
                $partes = explode('-', $linea);

                if (count($partes) >= 2) {
                    $rango = array_pop($partes);
                    $curso = implode('-', $partes);

                    $rango_partes = explode(',', $rango);

                    if (count($rango_partes) == 2) {
                        $inicio = intval(trim($rango_partes[0]));
                        $fin = intval(trim($rango_partes[1]));

                        if ($inicio > 0 && $fin > 0 && $inicio <= $fin) {
                            $cursos[] = array(
                                'curso' => trim($curso),
                                'inicio' => $inicio,
                                'fin' => $fin
                            );
                        }
                    }
                }
            }
        }

        return $cursos;
    }
}

function fechaActualLiteral()
{
    date_default_timezone_set('America/Lima');

    $dias = array(
        'Sunday' => 'domingo',
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miércoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sábado'
    );

    $meses = array(
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    );

    $dia_semana = $dias[date('l')];
    $dia = date('d');
    $mes = $meses[date('F')];
    $anio = date('Y');

    return $dia_semana . ', ' . $dia . ' de ' . $mes . ' del ' . $anio;
}

$modelo = new Reportesimulacro();

$id_seccion = isset($_GET['id_seccion']) ? limpiarcadena($_GET['id_seccion']) : "";
$estructura = isset($_GET['estructura']) ? $_GET['estructura'] : "";

$estructura = urldecode($estructura);

if (trim($estructura) == "") {
    echo "Estructura de preguntas no válida.";
    exit();
}

$resultado = $modelo->listar($id_seccion);

$datos = array();

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
}

if (!empty($datos)) {
    $fecha_actual = fechaActualLiteral();

    $pdf = new PDFSimulacro('P', 'mm', 'A4', $fecha_actual);
    $pdf->AliasNbPages();

    foreach ($datos as $fila) {
        $pdf->AddPage();
        $pdf->addEncabezado($fila);
        $pdf->addDatosPersonales($fila);
        $pdf->addHojaRespuestas($estructura);
    }

    $pdf->Output('I', utf8_decode('SIMULACRO.pdf'));
} else {
    echo "No se encontraron alumnos para la sección especificada.";
}
?>