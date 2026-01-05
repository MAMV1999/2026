<?php
include_once("../Modelo/almacen_producto_categoria.php");

$almacenproductocategoria = new AlmacenProductoCategoria();

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];
        $rspta = $almacenproductocategoria->guardarEditarMasivo($detalles);

        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;


    case 'mostrar':
        $id = $_POST["categoria_id"];
        $rspta = $almacenproductocategoria->listar_frm($id);

        $data = array();
        while ($row = $rspta->fetch_object()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;

    case 'listar':
        $rspta = $almacenproductocategoria->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->categoria_nombre,
                "2" => $reg->cantidad_productos . ' PRODUCTOS',
                "3" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->categoria_id . ')">EDITAR</button>'
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

    case 'listar_categorias_activas':
        $rspta = $almacenproductocategoria->listarCategoriasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
