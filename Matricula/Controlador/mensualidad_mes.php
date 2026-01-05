<?php
include_once("../Modelo/mensualidad_mes.php");

$mensualidadMes = new MensualidadMes();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarcadena($_POST["descripcion"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $mensualidadMes->guardar(strtoupper($nombre), strtoupper($descripcion), $observaciones, $estado);
            echo $rspta ? "Mes registrado correctamente" : "No se pudo registrar el mes";
        } else {
            $rspta = $mensualidadMes->editar($id, strtoupper($nombre), strtoupper($descripcion), $observaciones, $estado);
            echo $rspta ? "Mes actualizado correctamente" : "No se pudo actualizar el mes";
        }
        break;

    case 'desactivar':
        $rspta = $mensualidadMes->desactivar($id);
        echo $rspta ? "Mes desactivado correctamente" : "No se pudo desactivar el mes";
        break;

    case 'activar':
        $rspta = $mensualidadMes->activar($id);
        echo $rspta ? "Mes activado correctamente" : "No se pudo activar el mes";
        break;

    case 'mostrar':
        $rspta = $mensualidadMes->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $mensualidadMes->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre,
                "2" => $reg->descripcion,
                "3" => ($reg->estado) ? '
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
