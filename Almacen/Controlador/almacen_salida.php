<?php
include_once("../Modelo/almacen_salida.php");

$almacensalida = new AlmacenSalida();

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $almacen_salida_id = $_POST['almacen_salida_id'] ?? "";
        $usuario_apoderado_id = $_POST['usuario_apoderado_id'] ?? null;
        $almacen_comprobante_id = $_POST['almacen_comprobante_id'] ?? null;
        $numeracion = $_POST['numeracion'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $almacen_metodo_pago_id = $_POST['almacen_metodo_pago_id'] ?? null;
        $total = $_POST['total'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $productos = $_POST['productos'] ?? [];

        if ($usuario_apoderado_id && $almacen_comprobante_id && $numeracion && $fecha && $almacen_metodo_pago_id && $total && !empty($productos)) {

            if (empty($almacen_salida_id)) {
                $resultado = $almacensalida->guardar($usuario_apoderado_id, $almacen_comprobante_id, $numeracion, $fecha, $almacen_metodo_pago_id, $total, $observaciones, $productos);
                echo $resultado ? "Registro guardado correctamente" : "Error al guardar el registro";
            } else {
                $resultado = $almacensalida->editar($almacen_salida_id, $usuario_apoderado_id, $almacen_comprobante_id, $numeracion, $fecha, $almacen_metodo_pago_id, $total, $observaciones, $productos);
                echo $resultado["ok"] ? $resultado["msg"] : $resultado["msg"];
            }

        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    // NUEVO: MOSTRAR PARA EDITAR
    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(["cabecera" => null, "detalle" => [], "error" => "ID vacío"]);
            break;
        }

        $cab = $almacensalida->mostrar($id);

        $detalle = [];
        $rsDet = $almacensalida->listar_detalle($id);
        while ($d = $rsDet->fetch_object()) {
            $detalle[] = $d;
        }

        echo json_encode(["cabecera" => $cab, "detalle" => $detalle, "error" => null]);
        break;

    case 'listar':
        $rspta = $almacensalida->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre_apoderado,
                "2" => $reg->nombre_comprobante . ' - ' . $reg->numeracion,
                "3" => $reg->fecha,
                "4" => $reg->metodo_pago . ' - S/. ' . $reg->total,
                "5" => $reg->estado == 1 ?
                    '
                    <button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button>
                    <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESAC.</button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#' . $reg->numeracion . '">PDF</button>

                    <!-- Modal -->
                    <div class="modal fade" id="' . $reg->numeracion . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">' . $reg->nombre_apoderado . '</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="../../Reportes/Vista/Recibo_salida.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ' : '
                    
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#' . $reg->numeracion . '">PDF ANULADO</button>

                    <!-- Modal -->
                    <div class="modal fade" id="' . $reg->numeracion . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">' . $reg->nombre_apoderado . '</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="../../Reportes/Vista/Recibo_salida.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
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

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'listar_almacen_producto':
        $rspta = $almacensalida->listar_almacen_producto();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->producto,
                "1" => $reg->categoria,
                "2" => 'S./ ' . $reg->precio_venta,
                "3" => '" ' . $reg->stock . ' "',
                "4" => '<button class="btn btn-warning btn-sm" onclick="agregardetalle(\'' . $reg->id_producto . '\',\'' . $reg->producto . '\',\'' . $reg->descripcion . '\',\'' . $reg->stock . '\',\'' . $reg->precio_venta . '\')">AGREGAR</button>'
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'listar_buscador_apoderado':
        $rspta = $almacensalida->listar_buscador_apoderado();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->apoderado,
                "2" => $reg->alumnos,
                "3" => '<button class="btn btn-warning btn-sm" onclick="agregarapoderado(\'' . $reg->id . '\',\'' . $reg->apoderado . '\')">AGREGAR</button>'
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'listar_usuario_apoderado':
        $rspta = $almacensalida->listar_usuario_apoderado();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_apoderado . '">' . $reg->nombreyapellido . '</option>';
        }
        break;

    case 'listar_almacen_comprobante':
        $rspta = $almacensalida->listar_almacen_comprobante();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_comprobante . '">' . $reg->nombre_comprobante . '</option>';
        }
        break;

    case 'listar_almacen_metodo_pago':
        $rspta = $almacensalida->listar_almacen_metodo_pago();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_metodo_pago . '">' . $reg->metodo_pago . '</option>';
        }
        break;

    case 'numeracion':
        $rspta = $almacensalida->numeracion();
        echo $rspta;
        break;

    case 'activar':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $rspta = $almacensalida->activar($id);
            echo $rspta ? "Registro activado correctamente" : "No se pudo activar el registro";
        } else {
            echo "ID no proporcionado.";
        }
        break;

    case 'desactivar':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $rspta = $almacensalida->desactivar($id);
            echo $rspta ? "Registro desactivado correctamente" : "No se pudo desactivar el registro";
        } else {
            echo "ID no proporcionado.";
        }
        break;
}
