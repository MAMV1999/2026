<?php
include_once("../Modelo/biblioteca_libro.php");

$bibliotecaLibro = new BibliotecaLibro();

$detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $rspta = $bibliotecaLibro->guardarEditarMasivo($detalles);
        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;

    case 'desactivar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $bibliotecaLibro->desactivar($id);
        echo $rspta ? "Libro desactivado correctamente" : "No se pudo desactivar el libro";
        break;

    case 'activar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $bibliotecaLibro->activar($id);
        echo $rspta ? "Libro activado correctamente" : "No se pudo activar el libro";
        break;

    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $bibliotecaLibro->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $bibliotecaLibro->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->codigo,
                "2" => $reg->nombre,
                "3" => $reg->stock,
                "4" => ($reg->estado)
                    ? '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>'
                    : '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-success btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>'
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
