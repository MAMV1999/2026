<?php
include_once("../Modelo/almacen_metodo_pago.php");

$almacenMetodoPago = new AlmacenMetodoPago();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $almacenMetodoPago->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Método de pago registrado correctamente" : "No se pudo registrar el método de pago";
        } else {
            $rspta = $almacenMetodoPago->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Método de pago actualizado correctamente" : "No se pudo actualizar el método de pago";
        }
        break;

    case 'desactivar':
        $rspta = $almacenMetodoPago->desactivar($id);
        echo $rspta ? "Método de pago desactivado correctamente" : "No se pudo desactivar el método de pago";
        break;

    case 'activar':
        $rspta = $almacenMetodoPago->activar($id);
        echo $rspta ? "Método de pago activado correctamente" : "No se pudo activar el método de pago";
        break;

    case 'mostrar':
        $rspta = $almacenMetodoPago->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $almacenMetodoPago->listar();
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