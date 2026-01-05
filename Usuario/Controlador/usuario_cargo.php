<?php
include_once("../Modelo/usuario_cargo.php");

$usuarioCargo = new UsuarioCargo();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioCargo->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Cargo registrado correctamente" : "No se pudo registrar el cargo";
        } else {
            $rspta = $usuarioCargo->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Cargo actualizado correctamente" : "No se pudo actualizar el cargo";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioCargo->desactivar($id);
        echo $rspta ? "Cargo desactivado correctamente" : "No se pudo desactivar el cargo";
        break;

    case 'activar':
        $rspta = $usuarioCargo->activar($id);
        echo $rspta ? "Cargo activado correctamente" : "No se pudo activar el cargo";
        break;

    case 'mostrar':
        $rspta = $usuarioCargo->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioCargo->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => ($reg->estado) ? '
                <button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button>
                <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>
                ' : '
                <button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button>
                <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>
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
