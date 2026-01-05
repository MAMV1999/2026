<?php
include_once("../Modelo/Facturacion_x_mes.php");

$facturacion_x_mes = new Facturacion_x_mes();

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];
        $rspta = $facturacion_x_mes->guardarEditarMasivo($detalles);

        echo $rspta ? "Registros actualizados correctamente" : "No se pudieron actualizar todos los registros";
        break;

    case 'listar':
        $rspta = $facturacion_x_mes->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->id_mensualidad_mes,
                "1" => $reg->mensualidad_mes_nombre,

                "2" => $reg->cantidad_sin_recibo . ' PENDIENTES',
                "3" => $reg->cantidad_con_recibo . ' EMITIDOS',

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
        $rspta = $facturacion_x_mes->listar_frm($id_mensualidad_mes);

        $data = array();
        while ($row = $rspta->fetch_object()) {
            $data[] = $row; // Almacena cada fila en el arreglo.
        }

        echo json_encode($data); // Devuelve los datos al frontend en formato JSON.
        break;
}
