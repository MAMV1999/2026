<?php
include_once("../Modelo/matricula_documentos_responsable.php");

$matriculaDocumentosResponsable = new MatriculaDocumentosResponsable();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $matriculaDocumentosResponsable->guardar($nombre, $observaciones, $estado);
            echo $rspta ? "Documento de responsable registrado correctamente" : "No se pudo registrar el documento de responsable";
        } else {
            $rspta = $matriculaDocumentosResponsable->editar($id, $nombre, $observaciones, $estado);
            echo $rspta ? "Documento de responsable actualizado correctamente" : "No se pudo actualizar el documento de responsable";
        }
        break;

    case 'desactivar':
        $rspta = $matriculaDocumentosResponsable->desactivar($id);
        echo $rspta ? "Documento de responsable desactivado correctamente" : "No se pudo desactivar el documento de responsable";
        break;

    case 'activar':
        $rspta = $matriculaDocumentosResponsable->activar($id);
        echo $rspta ? "Documento de responsable activado correctamente" : "No se pudo activar el documento de responsable";
        break;

    case 'mostrar':
        $rspta = $matriculaDocumentosResponsable->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $matriculaDocumentosResponsable->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->nombre,
                "1" => $reg->observaciones,
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
