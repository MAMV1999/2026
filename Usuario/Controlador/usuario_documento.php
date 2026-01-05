<?php
include_once("../Modelo/usuario_documento.php");

$usuarioDocumento = new UsuarioDocumento();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioDocumento->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Documento registrado correctamente" : "No se pudo registrar el documento";
        } else {
            $rspta = $usuarioDocumento->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Documento actualizado correctamente" : "No se pudo actualizar el documento";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioDocumento->desactivar($id);
        echo $rspta ? "Documento desactivado correctamente" : "No se pudo desactivar el documento";
        break;

    case 'activar':
        $rspta = $usuarioDocumento->activar($id);
        echo $rspta ? "Documento activado correctamente" : "No se pudo activar el documento";
        break;

    case 'mostrar':
        $rspta = $usuarioDocumento->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioDocumento->listar();
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
