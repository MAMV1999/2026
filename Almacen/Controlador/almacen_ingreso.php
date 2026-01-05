<?php
include_once("../Modelo/almacen_ingreso.php");

$almaceningreso = new AlmacenIngreso();

switch ($_GET["op"]) {

    case 'guardar':
        $usuario_apoderado_id = $_POST['usuario_apoderado_id'] ?? null;
        $almacen_comprobante_id = $_POST['almacen_comprobante_id'] ?? null;
        $numeracion = $_POST['numeracion'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $almacen_metodo_pago_id = $_POST['almacen_metodo_pago_id'] ?? null;
        $total = $_POST['total'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $productos = $_POST['productos'] ?? [];

        if ($usuario_apoderado_id && $almacen_comprobante_id && $numeracion && $fecha && $almacen_metodo_pago_id && $total && !empty($productos)) {
            $resultado = $almaceningreso->guardar($usuario_apoderado_id, $almacen_comprobante_id, $numeracion, $fecha, $almacen_metodo_pago_id, $total, $observaciones, $productos);
            echo $resultado ? "Registro guardado correctamente" : "Error al guardar el registro";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;


    case 'listar':
        $rspta = $almaceningreso->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre_apoderado,
                "2" => $reg->nombre_comprobante . ' - ' . $reg->numeracion,
                "3" => $reg->fecha,
                "4" => 'S/. ' . $reg->total,
                "5" => $reg->estado == 1 ? '
                    <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>
                    
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
                                                        <iframe src="../../Reportes/Vista/Recibo_ingreso.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                    
                    ' : '<button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>'
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
        $rspta = $almaceningreso->listar_almacen_producto();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->producto,
                "1" => $reg->categoria,
                "2" => 'S./ ' . $reg->precio_compra,
                "3" => '" ' . $reg->stock . ' "',
                "4" => '<button class="btn btn-warning btn-sm" onclick="agregardetalle(\'' . $reg->id_producto . '\',\'' . $reg->producto . '\',\'' . $reg->descripcion . '\',\'' . $reg->stock . '\',\'' . $reg->precio_compra . '\')">AGREGAR</button>'
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
        $rspta = $almaceningreso->listar_usuario_apoderado();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_apoderado . '">' . $reg->nombreyapellido . '</option>';
        }
        break;

    case 'listar_almacen_comprobante':
        $rspta = $almaceningreso->listar_almacen_comprobante();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_comprobante . '">' . $reg->nombre_comprobante . '</option>';
        }
        break;

    case 'listar_almacen_metodo_pago':
        $rspta = $almaceningreso->listar_almacen_metodo_pago();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_metodo_pago . '">' . $reg->metodo_pago . '</option>';
        }
        break;

    case 'numeracion':
        $rspta = $almaceningreso->numeracion();
        echo $rspta;
        break;

    case 'activar':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $rspta = $almaceningreso->activar($id);
            echo $rspta ? "Registro activado correctamente" : "No se pudo activar el registro";
        } else {
            echo "ID no proporcionado.";
        }
        break;

    case 'desactivar':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $rspta = $almaceningreso->desactivar($id);
            echo $rspta ? "Registro desactivado correctamente" : "No se pudo desactivar el registro";
        } else {
            echo "ID no proporcionado.";
        }
        break;
}
