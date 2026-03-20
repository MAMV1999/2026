<?php
include_once("../Modelo/usuario_estado_civil.php");

$usuarioEstadoCivil = new UsuarioEstadoCivil();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioEstadoCivil->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Estado civil registrado correctamente" : "No se pudo registrar el estado civil";
        } else {
            $rspta = $usuarioEstadoCivil->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Estado civil actualizado correctamente" : "No se pudo actualizar el estado civil";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioEstadoCivil->desactivar($id);
        echo $rspta ? "Estado civil desactivado correctamente" : "No se pudo desactivar el estado civil";
        break;

    case 'activar':
        $rspta = $usuarioEstadoCivil->activar($id);
        echo $rspta ? "Estado civil activado correctamente" : "No se pudo activar el estado civil";
        break;

    case 'mostrar':
        $rspta = $usuarioEstadoCivil->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioEstadoCivil->listar();
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
