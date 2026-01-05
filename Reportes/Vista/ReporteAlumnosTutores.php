<?php
require('../../General/fpdf/fpdf.php');
require_once("../Modelo/ReporteAlumnosTutores.php");

class PDF extends FPDF {
    protected $fecha_actual;
    protected $currentSubTitle; // Subtítulo que se imprime en cada Header()

    public function __construct($fecha_actual) {
        parent::__construct('L','mm','A4'); // Horizontal, mm, A4
        $this->fecha_actual    = $fecha_actual;
        $this->SetMargins(5,5,5);
        $this->currentSubTitle = '';
    }

    // Método para asignar el subtítulo desde fuera de la clase
    public function setCurrentSubTitle($text) {
        $this->currentSubTitle = $text;
    }

    // Encabezado
    function Header() {
        // Título principal
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, utf8_decode('LISTADO DE ALUMNOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 4, utf8_decode('LISTADO SUJETO A ACTUALIZACIONES'), 0, 1, 'C');
        $this->Ln(1);

        // Subtítulo (si existe) arriba de la tabla
        if (!empty($this->currentSubTitle)) {
            $this->SetFont('Arial', 'B', 10);
            $this->MultiCell(0, 6, utf8_decode($this->currentSubTitle), 0, 'L');
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
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(188, 188, 188);

        // Ajusta columnas según tu contenido
        $this->Cell(10,  8, utf8_decode('N°'), 1, 0, 'C', true);
        $this->Cell(22,  8, utf8_decode('MATRICULA'), 1, 0, 'C', true);
        $this->Cell(70,  8, utf8_decode('ALUMNO'), 1, 0, 'C', true);
        $this->Cell(22,  8, utf8_decode('GENERO'), 1, 0, 'C', true);
        $this->Cell(22,  8, utf8_decode('NACIMIENTO'), 1, 0, 'C', true);
        $this->Cell(22,  8, utf8_decode('EDAD'), 1, 0, 'C', true);
        $this->Cell(22,  8, utf8_decode('PARENTESCO'), 1, 0, 'C', true);
        $this->Cell(70,  8, utf8_decode('APODERADO'), 1, 0, 'C', true);
        $this->Cell(0,  8, utf8_decode('TELEFONO'), 1, 1, 'C', true);
    }

    // Dibuja una fila en la tabla
    function PrintRow($row, $contador) {
        $this->SetFont('Arial', '', 7);

        $this->Cell(10,  6, $contador, 1, 0, 'C');
        $this->Cell(22,  6, utf8_decode($row['categoria']), 1, 0, 'C');
        $this->Cell(70,  6, utf8_decode($row['alumno']), 1, 0, 'C');
        $this->Cell(22,  6, utf8_decode($row['sexo_alumno']), 1, 0, 'C');
        $this->Cell(22,  6, utf8_decode($row['fecha_nacimiento_alumno']), 1, 0, 'C');
        $this->Cell(22,  6, utf8_decode($row['edad_actual_alumno'].' AÑOS'), 1, 0, 'C');
        $this->Cell(22,  6, utf8_decode($row['tipo_apoderado']), 1, 0, 'C');
        $this->Cell(70,  6, utf8_decode($row['apoderado']), 1, 0, 'C');
        $this->Cell(0,  6, utf8_decode($row['telefono_apoderado']), 1, 1, 'C');
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
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.$filename.'"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', $filename);
