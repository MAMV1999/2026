<?php
include_once("../Modelo/Matricula_pago.php");

$matriculaPago = new Matricula_pago();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_matricula_detalle = isset($_POST["id_matricula_detalle"]) ? limpiarcadena($_POST["id_matricula_detalle"]) : "";
$numeracion = isset($_POST["numeracion"]) ? limpiarcadena($_POST["numeracion"]) : "";
$fecha = isset($_POST["fecha"]) ? limpiarcadena($_POST["fecha"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarcadena($_POST["descripcion"]) : "";
$monto = isset($_POST["monto"]) ? limpiarcadena($_POST["monto"]) : "";
$id_matricula_metodo_pago = isset($_POST["id_matricula_metodo_pago"]) ? limpiarcadena($_POST["id_matricula_metodo_pago"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $matriculaPago->guardar($id_matricula_detalle, $numeracion, $fecha, $descripcion, $monto, $id_matricula_metodo_pago, $observaciones);
            echo $rspta ? "Pago registrado correctamente" : "No se pudo registrar el pago";
        } else {
            $rspta = $matriculaPago->editar($id, $id_matricula_detalle, $numeracion, $fecha, $descripcion, $monto, $id_matricula_metodo_pago, $observaciones);
            echo $rspta ? "Pago actualizado correctamente" : "No se pudo actualizar el pago";
        }
        break;

    case 'desactivar':
        $rspta = $matriculaPago->desactivar($id);
        echo $rspta ? "Pago desactivado correctamente" : "No se pudo desactivar el pago";
        break;

    case 'activar':
        $rspta = $matriculaPago->activar($id);
        echo $rspta ? "Pago activado correctamente" : "No se pudo activar el pago";
        break;

    case 'mostrar':
        $rspta = $matriculaPago->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $matriculaPago->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° ' . $reg->numeracion,
                "1" => $reg->fecha,
                "2" => $reg->apoderado,
                "3" => $reg->alumno,
                "4" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button>
                
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#' . $reg->numeracion . '">RECIBO</button>

                        <!-- Modal -->
                        <div class="modal fade" id="' . $reg->numeracion . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">' . $reg->apoderado . '</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="../../Reportes/Vista/ReciboMatricula.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                                </div>
                                </div>
                            </div>
                        </div>
                '
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listar_metodos_pago_activos':
        $rspta = $matriculaPago->listarMetodosPagoActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_matricula_detalles_activos':
        $rspta = $matriculaPago->listarMatriculaDetallesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado . ' - ' . $reg->seccion . ' - ' . $reg->apoderado . ' - ' . $reg->alumno . '</option>';
        }
        break;
}
