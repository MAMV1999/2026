<?php
include_once("../Modelo/Facturacion_x_apoderado.php");

$facturacion_x_apoderado = new Facturacion_x_apoderado();

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];
        $rspta = $facturacion_x_apoderado->guardarEditarMasivo($detalles);

        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;


    case 'listar':
        $rspta = $facturacion_x_apoderado->listar();
        $data = array();

        $cont = 1;

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->tipo_apoderado . ' - ' . $reg->nombreyapellido,
                "2" => $reg->cantidad_alumnos . ' ALUMNOS',
                "3" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->apoderado_id . ')">EDITAR</button>'
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
        $rspta = $facturacion_x_apoderado->listar_frm($id);

        $data = array();
        while ($row = $rspta->fetch_object()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;
}
