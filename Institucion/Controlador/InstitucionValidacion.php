<?php
include_once("../Modelo/InstitucionValidacion.php");

$institucionValidacion = new InstitucionValidacion();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $institucionValidacion->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Validación registrada correctamente" : "No se pudo registrar la validación";
        } else {
            $rspta = $institucionValidacion->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Validación actualizada correctamente" : "No se pudo actualizar la validación";
        }
        break;

    case 'desactivar':
        $rspta = $institucionValidacion->desactivar($id);
        echo $rspta ? "Validación desactivada correctamente" : "No se pudo desactivar la validación";
        break;

    case 'activar':
        $rspta = $institucionValidacion->activar($id);
        echo $rspta ? "Validación activada correctamente" : "No se pudo activar la validación";
        break;

    case 'mostrar':
        $rspta = $institucionValidacion->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $institucionValidacion->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N°'.$reg->id,
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
