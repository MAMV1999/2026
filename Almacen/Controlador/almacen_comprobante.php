<?php
include_once("../Modelo/almacen_comprobante.php");

$almacenComprobante = new AlmacenComprobante();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $almacenComprobante->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Comprobante registrado correctamente" : "No se pudo registrar el comprobante";
        } else {
            $rspta = $almacenComprobante->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Comprobante actualizado correctamente" : "No se pudo actualizar el comprobante";
        }
        break;

    case 'desactivar':
        $rspta = $almacenComprobante->desactivar($id);
        echo $rspta ? "Comprobante desactivado correctamente" : "No se pudo desactivar el comprobante";
        break;

    case 'activar':
        $rspta = $almacenComprobante->activar($id);
        echo $rspta ? "Comprobante activado correctamente" : "No se pudo activar el comprobante";
        break;

    case 'mostrar':
        $rspta = $almacenComprobante->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $almacenComprobante->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre,
                "2" => ($reg->estado) ? '
                <button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>
                ' : '
                <button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>
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
}
?>