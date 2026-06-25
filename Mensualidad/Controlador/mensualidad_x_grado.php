<?php
include_once("../Modelo/mensualidad_x_grado.php");

$mensualidadxapoderado = new Mensualidadxapoderado();

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];
        $rspta = $mensualidadxapoderado->guardarEditarMasivo($detalles);

        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;

    case 'listar':
        $rspta = $mensualidadxapoderado->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => 'PERIODO LECTIVO ' . $reg->lectivo_nombre,
                "2" => $reg->nivel_nombre,
                "3" => $reg->grado_nombre,
                "4" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->grado_id . ')">EDITAR</button>'
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

    case 'mostrar':
        $id_grado = $_POST["id_grado"];
        $rspta = $mensualidadxapoderado->listar_frm($id_grado);

        $data = array();

        while ($row = $rspta->fetch_object()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;
}