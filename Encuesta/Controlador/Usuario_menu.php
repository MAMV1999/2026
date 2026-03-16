<?php
include_once("../Modelo/Usuario_menu.php");

$usuarioMenu = new UsuarioMenu();

$detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $rspta = $usuarioMenu->guardarEditarMasivo($detalles);
        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;

    case 'desactivar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $usuarioMenu->desactivar($id);
        echo $rspta ? "Menú desactivado correctamente" : "No se pudo desactivar el menú";
        break;

    case 'activar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $usuarioMenu->activar($id);
        echo $rspta ? "Menú activado correctamente" : "No se pudo activar el menú";
        break;

    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $usuarioMenu->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioMenu->listar();
        $data = array();
        $cont = 1;

        while ($reg = $rspta->fetch_object()) {

            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => ($reg->estado)
                    ? '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> 
                       <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>'
                    : '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> 
                       <button class="btn btn-success btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>'
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

    case 'listar_todos':
        $rspta = $usuarioMenu->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "id" => $reg->id,
                "nombre" => $reg->nombre,
                "icono" => $reg->icono,
                "ruta" => $reg->ruta,
                "observaciones" => $reg->observaciones,
                "fechacreado" => $reg->fechacreado,
                "estado" => $reg->estado
            );
        }

        echo json_encode($data);
        break;
}
