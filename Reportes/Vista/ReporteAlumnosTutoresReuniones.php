<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/ReporteAlumnosTutores.php");

class PDF extends FPDF {
    protected $fecha_actual;
    protected $currentSubTitle; // Subtítulo que se imprime en cada Header()

    public function __construct($fecha_actual) {
        parent::__construct('P','mm','A4'); // Horizontal, mm, A4
        $this->fecha_actual    = $fecha_actual;
        $this->SetMargins(5,5,5);
        $this->currentSubTitle = '';
    }

    // Método para asignar el subtítulo desde fuera de la clase
    public function setCurrentSubTitle($text) {
        $this->currentSubTitle = $text;
    }

    // Función para convertir la fecha en texto
    private function fechaEnTexto($fecha) {
        $dias = [
            '1' => 'UNO', '2' => 'DOS', '3' => 'TRES', '4' => 'CUATRO', '5' => 'CINCO',
            '6' => 'SEIS', '7' => 'SIETE', '8' => 'OCHO', '9' => 'NUEVE', '10' => 'DIEZ',
            '11' => 'ONCE', '12' => 'DOCE', '13' => 'TRECE', '14' => 'CATORCE', '15' => 'QUINCE',
            '16' => 'DIECISÉIS', '17' => 'DIECISIETE', '18' => 'DIECIOCHO', '19' => 'DIECINUEVE', '20' => 'VEINTE',
            '21' => 'VEINTIUNO', '22' => 'VEINTIDÓS', '23' => 'VEINTITRÉS', '24' => 'VEINTICUATRO', '25' => 'VEINTICINCO',
            '26' => 'VEINTISÉIS', '27' => 'VEINTISIETE', '28' => 'VEINTIOCHO', '29' => 'VEINTINUEVE', '30' => 'TREINTA', '31' => 'TREINTA Y UNO'
        ];

        $meses = [
            '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL',
            '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'
        ];

        $partes = explode('/', $fecha);
        $dia = $dias[intval($partes[0])];
        $mes = $meses[$partes[1]];
        $año = intval($partes[2]); // Convertir el año a número entero

        return ucfirst("$dia DE $mes");
    }

    // Encabezado
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode('REUNIÓN DE APODERADOS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $fechaTexto = $this->fechaEnTexto(date('d/m/Y'));
        $this->Cell(0, 5, utf8_decode("$fechaTexto"), 0, 1, 'C');
        $this->Ln(1);

        // Subtítulo (si existe) arriba de la tabla
        if (!empty($this->currentSubTitle)) {
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(0, 5, utf8_decode($this->currentSubTitle), 0, 'L');
            $this->Ln(2);
        }

        // Cabecera de la tabla
        $this->HeaderTable();
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('FECHA Y HORA: ' . $this->fecha_actual), 0, 0, 'L');
        $this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // Cabecera de la tabla
    function HeaderTable() {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(188, 188, 188);

        $this->Cell(10,  8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(75,  8, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(75,  8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(0,  8, utf8_decode('FIRMA'), 1, 1, 'C', true);
    }

    // Dibuja una fila en la tabla
    function PrintRow($row, $contador) {
        $this->SetFont('Arial', '', 8);

        $this->Cell(10,  13, $contador, 1, 0, 'C');
        $this->Cell(75,  13, utf8_decode($row['apoderado']), 1, 0, 'C');
        $this->Cell(75,  13, utf8_decode($row['alumno']), 1, 0, 'C');
        $this->Cell(0,  13, '', 1, 1, 'C');
    }
}

// 1. Obtener datos del modelo
$modelo = new Reportealumnostutores();
$result = $modelo->listar();
if (!$result) {
    die("Error al obtener los datos.");
}

// Convertir resultados en un array
$rows = [];
while ($fila = $result->fetch_assoc()) {
    $rows[] = $fila;
}

// 2. Ordenar por la combinación (lectivo, nivel, grado, seccion, docente)
usort($rows, function($a, $b) {
    $grupoA = $a['nombre_lectivo'].'|'.$a['nombre_nivel'].'|'.$a['nombre_grado'].'|'.$a['nombre_seccion'].'|'.$a['docente'];
    $grupoB = $b['nombre_lectivo'].'|'.$b['nombre_nivel'].'|'.$b['nombre_grado'].'|'.$b['nombre_seccion'].'|'.$b['docente'];
    return strcmp($grupoA, $grupoB);
});

// 3. Crear el PDF
date_default_timezone_set('America/Lima');
$fecha_actual = date('d/m/Y H:i:s');

$pdf = new PDF($fecha_actual);
$pdf->AliasNbPages();

// Variables de agrupamiento
$lastGroup = null;
$contador  = 1;

foreach ($rows as $row) {
    // Determinamos la clave del grupo actual
    $currentGroup = $row['nombre_lectivo'].'|'.$row['nombre_nivel'].'|'.$row['nombre_grado'].'|'.$row['nombre_seccion'].'|'.$row['docente'];

    // ¿Es la primera vez o cambió el grupo?
    if ($lastGroup === null || $currentGroup !== $lastGroup) {
        // Construir el subtítulo con los datos del grupo
        $subtitulo = 
            "LECTIVO: ".$row['nombre_lectivo']."\n".
            "NIVEL: ".$row['nombre_nivel']."\n".
            "GRADO: ".$row['nombre_grado']."\n".
            "SECCIÓN: ".$row['nombre_seccion']."\n".
            "DOCENTE: ".$row['docente'];

        // Lo configuramos en la clase
        $pdf->setCurrentSubTitle($subtitulo);

        // Nueva página (forzamos a que se dibuje el subtítulo + cabecera)
        $pdf->AddPage();

        // Reinicia el contador si deseas que cada grupo empiece en #1
        $contador = 1;

        // Actualizamos el grupo anterior
        $lastGroup = $currentGroup;
    }

    // Ahora sí, imprimimos la fila
    $pdf->PrintRow($row, $contador);
    $contador++;
}

// 4. Salida del PDF
$filename = 'Reporte_Alumnos_Tutores.pdf';
$pdf->Output('I', $filename);
