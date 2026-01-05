<?php
include_once("../Modelo/almacen_categoria.php");

$almacenCategoria = new AlmacenCategoria();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $almacenCategoria->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Categoría registrada correctamente" : "No se pudo registrar la categoría";
        } else {
            $rspta = $almacenCategoria->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Categoría actualizada correctamente" : "No se pudo actualizar la categoría";
        }
        break;

    case 'desactivar':
        $rspta = $almacenCategoria->desactivar($id);
        echo $rspta ? "Categoría desactivada correctamente" : "No se pudo desactivar la categoría";
        break;

    case 'activar':
        $rspta = $almacenCategoria->activar($id);
        echo $rspta ? "Categoría activada correctamente" : "No se pudo activar la categoría";
        break;

    case 'mostrar':
        $rspta = $almacenCategoria->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $almacenCategoria->listar();
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