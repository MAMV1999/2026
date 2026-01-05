<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Documentos.php");

class PDF extends FPDF
{
    protected $institucionNombre;
    protected $institucionDireccion;
    protected $fecha_hora_actual;
    protected $background_image;

    // Constructor para inicializar el nombre y dirección de la institución
    function __construct($orientation='P', $unit='mm', $size='A4', $institucionNombre='', $institucionDireccion='', $fecha_hora_actual = null, $background_image = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->institucionNombre = $institucionNombre;
        $this->institucionDireccion = $institucionDireccion;
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->background_image = $background_image;

        // Ajustar los márgenes
        $this->SetMargins(15, 64, 15); // Márgenes izquierdo, superior y derecho
        $this->SetAutoPageBreak(true, 5); // Márgen inferior
    }

    // Cabecera de página
    function Header()
    {
        // Si hay una imagen de fondo configurada, agrégala
        if ($this->background_image) {
            $this->Image($this->background_image, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }

        // Subtítulo con el nombre de la institución
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 8, utf8_decode($this->institucionNombre), 0, 1, 'C');
        // Arial bold 15
        $this->SetFont('Arial', 'B', 14);
        // Título
        $this->Cell(0, 7, utf8_decode('ENTREGA DE DOCUMENTACIÓN'), 0, 1, 'C');
        // Dirección
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, utf8_decode($this->institucionDireccion), 0, 1, 'C');
        // Salto de línea
        $this->Ln(5);
    }

    // Pie de página
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Tabla de datos generales
    function GeneralData($data)
    {
        // Datos generales
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, utf8_decode('DATOS GENERALES'), 1, 1, 'C');
        
        foreach ($data as $label => $value) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(60, 8, utf8_decode($label), 1, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 8, utf8_decode($value), 1, 1, 'L');
        }
        $this->Ln(5);
    }

    // Tabla de documentación
    function DocumentationTable($data)
    {
        // Cabecera
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 8, utf8_decode('N.º'), 1, 0, 'C');
        $this->Cell(100, 8, utf8_decode('DOCUMENTO'), 1);
        $this->Cell(20, 8, utf8_decode('SI / NO'), 1, 0, 'C');
        $this->Cell(0, 8, utf8_decode('OBSERVACIONES'), 1, 0, 'C');
        $this->Ln();
        
        // Datos
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            $this->Cell(10, 8, utf8_decode($row['numero']), 1, 0, 'C');
            $this->Cell(100, 8, utf8_decode($row['nombre']), 1);
    
            // Definir color según el valor SI o NO
            $entregado = strtoupper(trim($row['entregado']));
            if ($entregado === 'NO') {
                // Plomo medio: RGB(192, 192, 192)
                $this->SetFillColor(192, 192, 192);
                $this->Cell(20, 8, utf8_decode($entregado), 1, 0, 'C', true);
            } else {
                // Fondo blanco
                $this->SetFillColor(255, 255, 255);
                $this->Cell(20, 8, utf8_decode($entregado), 1, 0, 'C', true);
            }
    
            $this->Cell(0, 8, utf8_decode($row['observaciones']), 1, 0, 'C');
            $this->Ln();
        }
    }
    
}

// Crear un objeto de la clase Documentos
$documentos = new Documentos();

// Obtener el ID del reporte
$id = $_GET['id'];

// Obtener el reporte dinámico
$reporte = $documentos->obtenerReporteDinamico($id);

// Obtener el nombre, dirección de la institución, nombre del apoderado y otros datos del primer resultado
$institucionNombre = '';
$institucionDireccion = '';
$apoderadoNombre = '';
$apoderadoTelefono = '';
$institucionLectivo = '';
$institucionNivel = '';
$institucionGrado = '';
$alumnoNombre = '';
$documentosData = [];
if ($reporte) {
    $fila = $reporte->fetch_assoc();
    $institucionNombre = isset($fila['institucion_nombre']) ? $fila['institucion_nombre'] : '';
    $institucionDireccion = isset($fila['institucion_direccion']) ? $fila['institucion_direccion'] : '';
    $apoderadoNombre = isset($fila['apoderado_nombre']) ? $fila['apoderado_nombre'] : '';
    $apoderadoTelefono = isset($fila['apoderado_telefono']) ? $fila['apoderado_telefono'] : '';
    $institucionLectivo = isset($fila['institucion_lectivo']) ? $fila['institucion_lectivo'] : '';
    $institucionNivel = isset($fila['institucion_nivel']) ? $fila['institucion_nivel'] : '';
    $institucionGrado = isset($fila['institucion_grado']) ? $fila['institucion_grado'] : '';
    $alumnoNombre = isset($fila['alumno_nombre']) ? $fila['alumno_nombre'] : '';

    // Procesar los datos de los documentos
    $i = 1;
    foreach ($fila as $columna => $valor) {
        if (!in_array($columna, ['id', 'institucion_nombre', 'institucion_telefono', 'institucion_correo', 'institucion_direccion', 'institucion_lectivo', 'institucion_nivel', 'institucion_grado', 'matricula_razon', 'apoderado_id', 'apoderado_nombre', 'apoderado_telefono', 'alumno_id', 'alumno_nombre', 'matricula_detalle_id', 'institucion_ruc', 'institucion_razon_social', 'institucion_seccion', 'apoderado_tipo', 'apoderado_documento_tipo', 'apoderado_numerodocumento', 'alumno_numerodocumento', 'alumno_documento_tipo'])) {
            if (strpos($columna, '_observaciones') === false) {
                $documentoNombre = $columna;
                $observacionesColumna = $columna . '_observaciones';
                $documentosData[] = [
                    'numero' => $i,
                    'nombre' => $documentoNombre,
                    'entregado' => isset($fila[$columna]) ? $fila[$columna] : '',
                    'observaciones' => isset($fila[$observacionesColumna]) ? $fila[$observacionesColumna] : ''
                ];
                $i++;
            }
        }
    }
    // Reiniciar el puntero del resultado
    $reporte->data_seek(0);
}

// Establecer la zona horaria a Lima, Perú
date_default_timezone_set('America/Lima');

// Obtener la fecha de emisión actual
$fechaEmision = date('d/m/Y');

// Ruta de la imagen de fondo
$background_image = 'reporte.png';

// Crear un nuevo objeto PDF en orientación vertical con el nombre y dirección de la institución
$pdf = new PDF('P', 'mm', 'A4', $institucionNombre, $institucionDireccion, $fechaEmision, $background_image);
$pdf->AliasNbPages();
$pdf->AddPage();

// Datos generales
$generalData = [
    'FECHA DE EMISIÓN' => $fechaEmision,
    'LECTIVO / NIVEL / GRADO' => "$institucionLectivo / $institucionNivel / $institucionGrado",
    'APODERADO(A)' => $apoderadoNombre,
    'ALUMNO(A)' => $alumnoNombre,
    'TELEFONO' => $apoderadoTelefono
];
$pdf->GeneralData($generalData);

// Generar la tabla de documentación en el PDF
$pdf->DocumentationTable($documentosData);

// Salida del documento
$pdf->Output('I', 'Documentos_' . str_replace(' ', '_', utf8_decode($alumnoNombre)) . '.pdf');
?>
