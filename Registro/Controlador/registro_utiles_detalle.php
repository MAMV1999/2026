<?php
include_once("../Modelo/registro_utiles_detalle.php");

$registroutil = new Registroutil();

switch ($_GET["op"]) {

    case 'guardar':
        $matricula_detalle_id = $_POST['matricula_detalle_id'] ?? null;
        $utiles = $_POST['utiles'] ?? [];

        if ($matricula_detalle_id && !empty($utiles)) {
            $resultado = $registroutil->guardarUtilesDetalle($matricula_detalle_id, $utiles);
            echo $resultado ? "Útiles guardados correctamente" : "Error al guardar los útiles";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    case 'mostrar':
        $id = $_POST['id'];

        // 1) Obtener información del alumno y su matrícula
        $info = $registroutil->listar_info_matricula_detalle($id);

        if (!$info || !isset($info['matricula_id'])) {
            echo json_encode(["error" => "No se encontró información para el ID enviado."]);
            break;
        }

        $matricula_id = $info['matricula_id'];

        // 2) Obtener útiles disponibles para ESA matrícula (registro_utiles)
        $rspta_utiles = $registroutil->listar_registro_utiles_por_matricula($matricula_id);
        $utiles = [];
        while ($reg = $rspta_utiles->fetch_object()) {
            $utiles[] = $reg;
        }

        // 3) Obtener detalle ya guardado para el alumno (registro_utiles_detalle)
        $rspta_detalles = $registroutil->listar_registro_utiles_detalle($id);
        $detalles = [];
        while ($reg = $rspta_detalles->fetch_object()) {
            $detalles[$reg->id_registro_utiles] = $reg; // clave: id del útil
        }

        // 4) Respuesta JSON
        $data = [
            "utiles" => $utiles,
            "detalles" => $detalles,
            "lectivo" => $info['lectivo'],
            "nivel" => $info['nivel'],
            "grado" => $info['grado'],
            "seccion" => $info['seccion'],
            "apoderado" => $info['apoderado_nombre'],
            "apoderado_telefono" => $info['apoderado_telefono'],
            "alumno" => $info['alumno_nombre'],
            "id_matricula_detalle" => $info['id_matricula_detalle'],
            "categoria_matricula" => $info['categoria_matricula'],
            "matricula_id" => $info['matricula_id']
        ];

        echo json_encode($data);
        break;

    case 'listar':
        $rspta = $registroutil->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "0" => count($data) + 1,
                "1" => $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado . ' - ' . $reg->seccion,
                "2" => $reg->apoderado_nombre,
                "3" => $reg->alumno_nombre,
                "4" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id_matricula_detalle . ')">EDITAR</button>

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#utiles_' . $reg->id_matricula_detalle . '">REPORTE</button>

                        <!-- Modal -->
                        <div class="modal fade" id="utiles_' . $reg->id_matricula_detalle . '" tabindex="-1" aria-labelledby="utiles_' . $reg->id_matricula_detalle . 'Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="utiles_' . $reg->id_matricula_detalle . 'Label">' . $reg->alumno_nombre . '</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe src="../../Reportes/Vista/Utiles.php?id=' . $reg->id_matricula_detalle . '" type="application/pdf" width="100%" height="600px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                            </div>
                            </div>
                        </div>
                        </div>'
            ];
        }

        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ]);
        break;
}
