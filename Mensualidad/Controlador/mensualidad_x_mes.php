<?php
include_once("../Modelo/mensualidad_x_mes.php");

$mensualidadxmes = new Mensualidadxmes();

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];
        $rspta = $mensualidadxmes->guardarEditarMasivo($detalles);

        echo $rspta ? "Registros actualizados correctamente" : "No se pudieron actualizar todos los registros";
        break;

    case 'listar':
        $rspta = $mensualidadxmes->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->id_mensualidad_mes,
                "1" => $reg->nombre_mes,
                "2" => $reg->deudor . ' DEUDORES',
                "3" => $reg->cancelado . ' CANCELADOS',
                "4" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id_mensualidad_mes . ')">EDITAR</button>'
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
        $id_mensualidad_mes = $_POST["id_mensualidad_mes"]; // Captura el ID enviado desde JavaScript.
        $rspta = $mensualidadxmes->listar_frm($id_mensualidad_mes);

        $data = array();
        while ($row = $rspta->fetch_object()) {
            $data[] = $row; // Almacena cada fila en el arreglo.
        }

        echo json_encode($data); // Devuelve los datos al frontend en formato JSON.
        break;
}
