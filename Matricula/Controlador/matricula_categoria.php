<?php
include_once("../Modelo/matricula_categoria.php");

$matriculaCategoria = new MatriculaCategoria();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $matriculaCategoria->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Categoría de matrícula registrada correctamente" : "No se pudo registrar la categoría de matrícula";
        } else {
            $rspta = $matriculaCategoria->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Categoría de matrícula actualizada correctamente" : "No se pudo actualizar la categoría de matrícula";
        }
        break;

    case 'desactivar':
        $rspta = $matriculaCategoria->desactivar($id);
        echo $rspta ? "Categoría de matrícula desactivada correctamente" : "No se pudo desactivar la categoría de matrícula";
        break;

    case 'activar':
        $rspta = $matriculaCategoria->activar($id);
        echo $rspta ? "Categoría de matrícula activada correctamente" : "No se pudo activar la categoría de matrícula";
        break;

    case 'mostrar':
        $rspta = $matriculaCategoria->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $matriculaCategoria->listar();
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
