<?php
include_once("../Modelo/Matricula_mensualidad.php");

$mensualidadDetalle = new Mensualidad_detalle();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$matricula_mes_id = isset($_POST["matricula_mes_id"]) ? limpiarcadena($_POST["matricula_mes_id"]) : "";
$id_matricula_detalle = isset($_POST["id_matricula_detalle"]) ? limpiarcadena($_POST["id_matricula_detalle"]) : "";
$monto = isset($_POST["monto"]) ? limpiarcadena($_POST["monto"]) : "";
$pagado = isset($_POST["pagado"]) ? limpiarcadena($_POST["pagado"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];
        $rspta = $mensualidadDetalle->guardarEditarMasivo($detalles);

        echo $rspta ? "Registros actualizados correctamente" : "No se pudieron actualizar todos los registros";
        break;

    case 'mostrar':
        $rspta = $mensualidadDetalle->mostrar($id);
        $response = array();

        if ($rspta) {
            $ids = explode(',', $rspta['ids']);
            $ids_mensualidad_mes = explode(',', $rspta['ids_mensualidad_mes']);
            $meses = explode(',', $rspta['meses']);
            $fechas_vencimiento = explode(',', $rspta['fechas_vencimiento']);
            $montos = explode(',', $rspta['montos']);
            $estados_pagado = explode(',', $rspta['estados_pagado']);
            $estados_generales = explode(',', $rspta['estados_generales']);
            $observaciones = explode(',', $rspta['observaciones']);

            foreach ($montos as $index => $monto) {
                $response['detalles'][] = array(
                    "id" => $ids[$index],
                    "matricula_mes_id" => $ids_mensualidad_mes[$index],
                    "mes" => $meses[$index],
                    "fecha_vencimiento" => $fechas_vencimiento[$index],
                    "monto" => $monto,
                    "pagado" => $estados_pagado[$index],
                    "estado" => $estados_generales[$index],
                    "observaciones" => $observaciones[$index],
                );
            }

            $response['general'] = array(
                "id_matricula_detalle" => $rspta['id_matricula_detalle'],
                "lectivo" => $rspta['lectivo'],
                "nivel" => $rspta['nivel'],
                "grado" => $rspta['grado'],
                "seccion" => $rspta['seccion'],
                "apoderado" => array(
                    "tipo_documento" => $rspta['apoderado_tipo_documento'],
                    "numerodocumento" => $rspta['apoderado_numerodocumento'],
                    "nombreyapellido" => $rspta['apoderado_nombreyapellido'],
                    "telefono" => $rspta['apoderado_telefono'],
                ),
                "alumno" => array(
                    "tipo_documento" => $rspta['alumno_tipo_documento'],
                    "numerodocumento" => $rspta['alumno_numerodocumento'],
                    "nombreyapellido" => $rspta['alumno_nombreyapellido'],
                ),
            );
        }

        echo json_encode($response);
        break;

    case 'listar':
        $rspta = $mensualidadDetalle->listar();
        $data = array();
        $cont = 1;
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $cont,
                "1" => $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado,
                "2" => (strlen($reg->apoderado) > 40) ? substr($reg->apoderado, 0, 37) . '...' : $reg->apoderado,
                "3" => (strlen($reg->alumno) > 40) ? substr($reg->alumno, 0, 37) . '...' : $reg->alumno,
                "4" => $reg->codigo,
                "5" => '
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">OPCIONES</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" onclick="mostrar(' . $reg->id . ')">EDITAR</a></li>
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#pdf_' . $reg->id . '">PDF</a></li>
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bcp_' . $reg->id . '">BCP</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Separated link</a></li>
                            </ul>
                        </div>
                        
                        <!-- Modal PDF -->
                        <div class="modal fade" id="pdf_' . $reg->id . '" tabindex="-1" aria-labelledby="pdf_' . $reg->id . 'Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="' . $reg->id . 'Label">' . $reg->alumno . '</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe src="../../Reportes/Vista/Mensualidad_reporte_x_alumno.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal BCP -->
                        <div class="modal fade" id="bcp_' . $reg->id . '" tabindex="-1" aria-labelledby="bcp_' . $reg->id . 'Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="' . $reg->id . 'Label">' . $reg->alumno . '</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe src="../../Reportes/Vista/Mensualidad_reporte_bcp_id.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                '
            );
            $cont++;
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listar_meses_activos':
        $rspta = $mensualidadDetalle->listarMesesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_matricula_detalles_activos':
        $rspta = $mensualidadDetalle->listarMatriculaDetallesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado . ' - ' . $reg->seccion . ' - ' . $reg->apoderado . ' - ' . $reg->alumno . '</option>';
        }
        break;
}
