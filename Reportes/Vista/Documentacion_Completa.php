<?php
// Documentacion_Completa.php
// Une varios PDFs en un solo PDF final.
// Requiere FPDI (setasign/fpdi). No se puede con FPDF puro.

// 1) FPDF + FPDI
require_once('../../General/fpdf/fpdf.php');

// Ajusta esta ruta según dónde coloques FPDI.
// Ejemplo recomendado: /General/fpdi/src/autoload.php o /General/fpdi/autoload.php
require_once('../../General/fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

date_default_timezone_set('America/Lima');

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($id === '') { http_response_code(400); exit; }
$id_url = urlencode($id);

// Lista de reportes (URLs relativas desde esta carpeta)
$reportes = [
    'ReciboMatricula.php',
    'ReciboMatricula_copy.php',
    'Constancia_vacante.php',
    'Constancia_Matricula.php',
    'Constancia_Estudios.php',
    'Mensualidad_reporte_x_alumno.php',
    'Documentos.php',
];

// Genera el PDF final unificado
$pdfFinal = new Fpdi('P', 'mm', 'A4');
$pdfFinal->SetAutoPageBreak(true, 15);

// Función: obtener bytes PDF de un reporte local (por HTTP interno)
function fetchPdfBytes($url) {
    // Requiere allow_url_fopen habilitado. En XAMPP normalmente sí.
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 30,
        ]
    ]);

    $bytes = @file_get_contents($url, false, $ctx);
    return $bytes === false ? null : $bytes;
}

// Construir base URL actual (para llamar a los reportes por HTTP y capturar su PDF)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Ruta del directorio actual en URL (ej: /2025/Reportes/Vista/)
$dirUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

// Unir PDFs
foreach ($reportes as $archivo) {

    // URL absoluta al reporte
    $url = $scheme . '://' . $host . $dirUrl . '/' . $archivo . '?id=' . $id_url;

    $pdfBytes = fetchPdfBytes($url);

    if ($pdfBytes === null || strlen($pdfBytes) < 100) {
        // Si falla un reporte, agrega página de aviso y continúa
        $pdfFinal->AddPage();
        $pdfFinal->SetFont('Arial', 'B', 12);
        $pdfFinal->Cell(0, 10, utf8_decode("No se pudo cargar: $archivo"), 0, 1, 'C');
        continue;
    }

    // Guardar temporalmente para que FPDI lo pueda leer como archivo
    $tmp = tempnam(sys_get_temp_dir(), 'pdf_');
    file_put_contents($tmp, $pdfBytes);

    try {
        $pageCount = $pdfFinal->setSourceFile($tmp);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdfFinal->importPage($pageNo);
            $size = $pdfFinal->getTemplateSize($tplId);

            // Mantener orientación original por página
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $pdfFinal->AddPage($orientation, [$size['width'], $size['height']]);
            $pdfFinal->useTemplate($tplId);
        }
    } catch (Exception $e) {
        $pdfFinal->AddPage();
        $pdfFinal->SetFont('Arial', 'B', 12);
        $pdfFinal->MultiCell(0, 6, utf8_decode("Error al importar $archivo: " . $e->getMessage()), 0, 'C');
    }

    @unlink($tmp);
}

// Output unificado
$filename = 'DOCUMENTACION_COMPLETA_' . $id . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdfFinal->Output('I', $filename);
