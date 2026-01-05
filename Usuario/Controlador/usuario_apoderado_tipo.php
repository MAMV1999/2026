<?php
include_once("../Modelo/usuario_apoderado_tipo.php");

$usuarioApoderadoTipo = new UsuarioApoderadoTipo();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioApoderadoTipo->guardar(strtoupper($nombre), $observaciones, $estado);
            echo $rspta ? "Tipo de apoderado registrado correctamente" : "No se pudo registrar el tipo de apoderado";
        } else {
            $rspta = $usuarioApoderadoTipo->editar($id, strtoupper($nombre), $observaciones, $estado);
            echo $rspta ? "Tipo de apoderado actualizado correctamente" : "No se pudo actualizar el tipo de apoderado";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioApoderadoTipo->desactivar($id);
        echo $rspta ? "Tipo de apoderado desactivado correctamente" : "No se pudo desactivar el tipo de apoderado";
        break;

    case 'activar':
        $rspta = $usuarioApoderadoTipo->activar($id);
        echo $rspta ? "Tipo de apoderado activado correctamente" : "No se pudo activar el tipo de apoderado";
        break;

    case 'mostrar':
        $rspta = $usuarioApoderadoTipo->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioApoderadoTipo->listar();
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
