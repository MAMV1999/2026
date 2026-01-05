<?php
include_once("../Modelo/reg_documento.php");

$registrodocumento = new Registrodocumento();

switch ($_GET["op"]) {

    case 'guardar':
        $matricula_detalle_id = $_POST['matricula_detalle_id'] ?? null;
        $documentos = $_POST['documentos'] ?? [];
    
        if ($matricula_detalle_id && !empty($documentos)) {
            $resultado = $registrodocumento->guardarDocumentoDetalle($matricula_detalle_id, $documentos);
            echo $resultado ? "Documentos guardados correctamente" : "Error al guardar los documentos";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;
    


    case 'mostrar':
        $id = $_POST['id'];

        // 1. Obtener todos los documentos posibles
        $rspta_documentos = $registrodocumento->listar_documento();
        $documentos = array();
        while ($reg = $rspta_documentos->fetch_object()) {
            $documentos[] = $reg;
        }

        // 2. Obtener los detalles de los documentos entregados
        $rspta_detalles = $registrodocumento->listar_documento_detalle($id);
        $detalles = array();
        while ($reg = $rspta_detalles->fetch_object()) {
            $detalles[$reg->id_documento] = $reg; // Asocia cada documento por su ID
        }

        // 3. Obtener información de la matrícula
        $info = $registrodocumento->listar_id_matricula_detalle($id);

        // 4. Estructurar la respuesta
        $data = array(
            "documentos" => $documentos,   // Todos los documentos disponibles
            "detalles" => $detalles,       // Detalles de los documentos entregados
            "lectivo" => $info['lectivo'],
            "nivel" => $info['nivel'],
            "grado" => $info['grado'],
            "seccion" => $info['seccion'],
            "apoderado" => $info['apoderado_nombre'],
            "apoderado_telefono" => $info['apoderado_telefono'],
            "alumno" => $info['alumno_nombre'],
            "id_matricula_detalle" => $info['id_matricula_detalle'],
            "categoria_matricula" => $info['categoria_matricula']
        );

        // 5. Enviar respuesta como JSON
        echo json_encode($data);
        break;

    case 'listar':
        $rspta = $registrodocumento->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado,
                "2" => $reg->apoderado_nombre,
                "3" => $reg->alumno_nombre,
                "4" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id_matricula_detalle . ')">EDITAR</button>
                
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#' . $reg->id_matricula_detalle . '">REPORTE</button>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="' . $reg->id_matricula_detalle . '" tabindex="-1" aria-labelledby="' . $reg->id_matricula_detalle . 'Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="' . $reg->id_matricula_detalle . 'Label">' . $reg->alumno_nombre . '</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="../../Reportes/Vista/Documentos.php?id=' . $reg->id_matricula_detalle . '" type="application/pdf" width="100%" height="600px"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                                </div>
                                </div>
                            </div>
                            </div>
                        '
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;
}
