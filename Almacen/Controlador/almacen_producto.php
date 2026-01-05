<?php
include_once("../Modelo/almacen_producto.php");

$almacenProducto = new AlmacenProducto();

$detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $rspta = $almacenProducto->guardarEditarMasivo($detalles);
        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;

    case 'desactivar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $almacenProducto->desactivar($id);
        echo $rspta ? "Producto desactivado correctamente" : "No se pudo desactivar el producto";
        break;

    case 'activar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $almacenProducto->activar($id);
        echo $rspta ? "Producto activado correctamente" : "No se pudo activar el producto";
        break;

    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $almacenProducto->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $almacenProducto->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => $reg->categoria,
                "3" => "S/. " . number_format($reg->precio_compra, 2),
                "4" => "S/. " . number_format($reg->precio_venta, 2),
                "5" => $reg->stock,
                "6" => ($reg->estado)
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

    case 'listar_categorias_activas':
        $rspta = $almacenProducto->listarCategoriasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
?>
