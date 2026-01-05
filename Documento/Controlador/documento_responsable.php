<?php
include_once("../Modelo/documento_responsable.php");

$documentoResponsable = new DocumentoResponsable();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $documentoResponsable->guardar(strtoupper($nombre), $observaciones, $estado);
            echo $rspta ? "Responsable del documento registrado correctamente" : "No se pudo registrar el responsable del documento";
        } else {
            $rspta = $documentoResponsable->editar($id, strtoupper($nombre), $observaciones, $estado);
            echo $rspta ? "Responsable del documento actualizado correctamente" : "No se pudo actualizar el responsable del documento";
        }
        break;

    case 'desactivar':
        $rspta = $documentoResponsable->desactivar($id);
        echo $rspta ? "Responsable del documento desactivado correctamente" : "No se pudo desactivar el responsable del documento";
        break;

    case 'activar':
        $rspta = $documentoResponsable->activar($id);
        echo $rspta ? "Responsable del documento activado correctamente" : "No se pudo activar el responsable del documento";
        break;

    case 'mostrar':
        $rspta = $documentoResponsable->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $documentoResponsable->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
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
