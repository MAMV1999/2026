<?php
require_once("../../General/fpdf/fpdf.php");
require_once("../Modelo/Mensualidad_reporte_x_alumno.php");

class PDFMensualidadAlumno extends FPDF
{
    protected $fecha_hora_actual;
    protected $background_image;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $fecha_hora_actual = null, $background_image = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->fecha_hora_actual = $fecha_hora_actual;
        $this->background_image = $background_image;
    
        // Ajustar los márgenes
        $this->SetMargins(15, 65, 15); // Márgenes izquierdo, superior y derecho
        $this->SetAutoPageBreak(true, 5); // Márgen inferior
    }

    // Cabecera del documento
    function Header()
    {
        // Si hay una imagen de fondo configurada, agrégala
        if ($this->background_image) {
            $this->Image($this->background_image, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }
        
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, 'REPORTE DE MENSUALIDAD', 0, 1, 'C');
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

    // Agregar título
    function addTitulo($institucion)
    {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, utf8_decode($institucion['nombre_institucion']), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, utf8_decode("{$institucion['direccion_institucion']}"), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("{$institucion['razon_social_institucion']} - {$institucion['ruc_institucion']}"), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("TELÉFONO: {$institucion['telefono_institucion']}"), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode("CORREO: {$institucion['correo_institucion']}"), 0, 1, 'C');
        $this->Ln(5);
    }

    // Agregar información general
    function addInformacionGeneral($datos)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 220, 220); // plomo claro
        $this->Cell(0, 8, 'INFORMACION GENERAL', 1, 1, 'L', true);
        $this->SetFont('Arial', '', 10);
        $borde = '1';
        $this->Cell(50, 8, utf8_decode("MATRICULA"), $borde, 0, 'L', true);
        $this->Cell(0, 8, utf8_decode("{$datos['nombre_lectivo']} - {$datos['nombre_nivel']} - {$datos['nombre_grado']}"), $borde, 1);

        $this->Cell(50, 8, utf8_decode("APODERADO"), $borde, 0, 'L', true);
        $this->Cell(0, 8, utf8_decode("{$datos['tipo_apoderado']} - {$datos['nombre_apoderado']}"), $borde, 1);

        $this->Cell(50, 8, utf8_decode("ALUMNO(A)"), $borde, 0, 'L', true);
        $this->Cell(0, 8, utf8_decode("{$datos['nombre_alumno']}"), $borde, 1);

        $this->Cell(50, 8, utf8_decode("TELEFONO"), $borde, 0, 'L', true);
        $this->Cell(0, 8, utf8_decode("{$datos['telefono_apoderado']}"), $borde, 1);

        $this->Cell(50, 8, utf8_decode("CODIGO"), $borde, 0, 'L', true);
        $this->Cell(0, 8, utf8_decode("{$datos['numero_documento_alumno']}"), $borde, 1);
        $this->SetFillColor(255, 255, 255); // restaurar fondo blanco
        $this->Ln(5);
    }

    // Agregar tabla de mensualidades
    function addTablaMensualidades($mensualidades)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 220, 220); // plomo claro
        $this->Cell(39, 8, 'MES', 1, 0, 'C', true);
        $this->Cell(23, 8, 'MONTO', 1, 0, 'C', true);
        $this->Cell(28, 8, 'ESTADO', 1, 0, 'C', true);
        $this->Cell(0, 8, 'OBSERVACIONES', 1, 0, 'C', true);
        $this->SetFillColor(255, 255, 255); // restaurar fondo blanco
        $this->Ln();

        $this->SetFont('Arial', '', 10);

        foreach ($mensualidades as $mensualidad) {
            $estado = strtoupper(trim($mensualidad['estado']));
            $esPendiente = ($estado == 'PENDIENTE');
        
            $this->Cell(39, 8, utf8_decode($mensualidad['mes'].' '.$mensualidad['nombre_lectivo']), 1, 0, 'C');
            $this->Cell(23, 8, 'S/ ' . number_format($mensualidad['monto'], 2), 1, 0, 'C');
        
            // Si es pendiente, aplicar fondo gris claro solo a esta celda
            if ($esPendiente) {
                $this->SetFillColor(220, 220, 220); // plomo claro
                $this->Cell(28, 8, utf8_decode($mensualidad['estado']), 1, 0, 'C', true);
                $this->SetFillColor(255, 255, 255); // restaurar fondo blanco
            } else {
                $this->Cell(28, 8, utf8_decode($mensualidad['estado']), 1, 0, 'C');
            }
        
            $this->Cell(0, 8, utf8_decode($mensualidad['observacion']), 1, 0, 'C');
            $this->Ln();
        }
        
    }
}

// Obtener datos
$modelo = new Reportemensualidadxalumno();
$id_matricula_detalle = $_GET['id'];

// Obtener los datos del modelo y convertirlos en un array
$resultado = $modelo->listar($id_matricula_detalle);
$datos = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
}

if (!empty($datos)) {
    $institucion = [
        'nombre_institucion' => $datos[0]['nombre_institucion'],
        'telefono_institucion' => $datos[0]['telefono_institucion'],
        'correo_institucion' => $datos[0]['correo_institucion'],
        'ruc_institucion' => $datos[0]['ruc_institucion'],
        'razon_social_institucion' => $datos[0]['razon_social_institucion'],
        'direccion_institucion' => $datos[0]['direccion_institucion'],
    ];

    $informacion_general = [
        'nombre_lectivo' => $datos[0]['nombre_lectivo'],
        'nombre_nivel' => $datos[0]['nombre_nivel'],
        'nombre_grado' => $datos[0]['nombre_grado'],
        'nombre_seccion' => $datos[0]['nombre_seccion'],
        'nombre_alumno' => $datos[0]['nombre_alumno'],
        'tipo_documento_alumno' => $datos[0]['tipo_documento_alumno'],
        'numero_documento_alumno' => $datos[0]['numero_documento_alumno'],
        'tipo_apoderado' => $datos[0]['tipo_apoderado'],
        'nombre_apoderado' => $datos[0]['nombre_apoderado'],
        'tipo_documento_apoderado' => $datos[0]['tipo_documento_apoderado'],
        'numero_documento_apoderado' => $datos[0]['numero_documento_apoderado'],
        'telefono_apoderado' => $datos[0]['telefono_apoderado'],
    ];

    $mensualidades = [];
    $nombre_lectivo = $datos[0]['nombre_lectivo'];
    $meses = explode(', ', $datos[0]['meses']);
    $montos = explode(', ', $datos[0]['montos']);
    $estados = explode(', ', $datos[0]['estados_pago_legibles']);
    $observaciones = explode(', ', $datos[0]['observaciones']);

    for ($i = 0; $i < count($meses); $i++) {
        $mensualidades[] = [
            'nombre_lectivo' => $nombre_lectivo,
            'mes' => $meses[$i],
            'monto' => $montos[$i],
            'estado' => $estados[$i],
            'observacion' => $observaciones[$i],
        ];
    }

    date_default_timezone_set('America/Lima');
    $fecha_hora_actual = date('d/m/Y H:i:s');

    // Ruta de la imagen de fondo
    $background_image = 'reporte.png';

    // Crear PDF
    $pdf = new PDFMensualidadAlumno('P', 'mm', 'A4', $fecha_hora_actual, $background_image);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->addTitulo($institucion);
    $pdf->addInformacionGeneral($informacion_general);
    $pdf->addTablaMensualidades($mensualidades);
    $pdf->Output('I', utf8_decode('REPORTE DE MENSUALIDAD '.$informacion_general['nombre_alumno']) . '.pdf');
} else {
    echo "No se encontraron datos para el ID especificado.";
}
?>
