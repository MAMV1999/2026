<?php
include_once("../Modelo/usuario_cargo.php");

$usuarioCargo = new UsuarioCargo();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {

    case 'guardar':
        $nombre = $_POST['nombre'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $menus = $_POST['menus'] ?? [];

        if ($nombre && !empty($menus)) {
            $resultado = $usuarioCargo->guardar($nombre, $observaciones, $menus);
            echo $resultado ? "Cargo registrado correctamente" : "Error al guardar el registro";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    case 'editar':
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $menus = $_POST['menus'] ?? [];

        if ($id && $nombre && !empty($menus)) {
            $resultado = $usuarioCargo->editar($id, $nombre, $observaciones, $menus);
            echo $resultado ? "Cargo actualizado correctamente" : "Error al actualizar el registro";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(array("error" => "ID vacío"));
            break;
        }

        $data = $usuarioCargo->mostrar($id);
        echo json_encode($data);
        break;

    case 'listar':
        $rspta = $usuarioCargo->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => ($reg->estado) ?
                    '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button>
                     <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>'
                    :
                    '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button>
                     <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>'
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

    case 'listar_usuario_menu_activos':
        $rspta = $usuarioCargo->listar_usuario_menu_activos();
        $cont = 1;
        $rows = "";

        while ($reg = $rspta->fetch_object()) {
            $nombre_menu = htmlspecialchars($reg->nombre, ENT_QUOTES, 'UTF-8');
            $ruta = htmlspecialchars($reg->ruta ?? '', ENT_QUOTES, 'UTF-8');

            $rows .= "
                <tr>
                    <th scope='row'>{$cont}</th>

                    <td>
                        <input type='hidden' id='menus[{$cont}][id_usuario_menu]' name='menus[{$cont}][id_usuario_menu]' value='{$reg->id}'>
                        {$nombre_menu}" . ($ruta ? " <small class='text-muted'>({$ruta})</small>" : "") . "
                    </td>

                    <td>
                        <input style='width: 30px; height: 30px;' type='radio' name='menus[{$cont}][ingreso]' value='0' class='form-check-input' checked> NO
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input style='width: 30px; height: 30px;' type='radio' name='menus[{$cont}][ingreso]' value='1' class='form-check-input'> SÍ
                    </td>

                    <td>
                        <input type='text' name='menus[{$cont}][observaciones]' placeholder='Observaciones' class='form-control'>
                    </td>
                </tr>";
            $cont++;
        }

        echo $rows;
        break;

    case 'desactivar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo "ID vacío";
            break;
        }

        $resultado = $usuarioCargo->desactivar($id);
        echo $resultado ? "Cargo desactivado correctamente" : "Error al desactivar";
        break;

    case 'activar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo "ID vacío";
            break;
        }

        $resultado = $usuarioCargo->activar($id);
        echo $resultado ? "Cargo activado correctamente" : "Error al activar";
        break;
}
