<?php
include_once("../Modelo/mensualidad_x_apoderado.php");

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

        $cont = 1;

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->tipo_documento . ' - ' . $reg->numero_documento,
                "2" => $reg->tipo_apoderado . ' - ' . $reg->nombre_apoderado,
                "3" => 'Telf. ' . $reg->telefono_apoderado,
                "4" => $reg->total_alumnos . ' ALUMNOS',
                "5" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id_apoderado . ')">EDITAR</button>'
            );
            $cont++;
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
        $id = $_POST["id_apoderado"];
        $rspta = $mensualidadxapoderado->listar_frm($id);

        $data = array();
        while ($row = $rspta->fetch_object()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;
}
