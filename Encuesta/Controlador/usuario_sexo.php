<?php
include_once("../Modelo/usuario_sexo.php");

$usuarioSexo = new UsuarioSexo();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioSexo->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Sexo registrado correctamente" : "No se pudo registrar el sexo";
        } else {
            $rspta = $usuarioSexo->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Sexo actualizado correctamente" : "No se pudo actualizar el sexo";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioSexo->desactivar($id);
        echo $rspta ? "Sexo desactivado correctamente" : "No se pudo desactivar el sexo";
        break;

    case 'activar':
        $rspta = $usuarioSexo->activar($id);
        echo $rspta ? "Sexo activado correctamente" : "No se pudo activar el sexo";
        break;

    case 'mostrar':
        $rspta = $usuarioSexo->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioSexo->listar();
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
