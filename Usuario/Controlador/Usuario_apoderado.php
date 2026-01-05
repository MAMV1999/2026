<?php
include_once("../Modelo/Usuario_apoderado.php");

$usuarioApoderado = new Usuario_apoderado();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_apoderado_tipo = isset($_POST["id_apoderado_tipo"]) ? limpiarcadena($_POST["id_apoderado_tipo"]) : "";
$id_documento = isset($_POST["id_documento"]) ? limpiarcadena($_POST["id_documento"]) : "";
$numerodocumento = isset($_POST["numerodocumento"]) ? limpiarcadena($_POST["numerodocumento"]) : "";
$nombreyapellido = isset($_POST["nombreyapellido"]) ? limpiarcadena($_POST["nombreyapellido"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarcadena($_POST["telefono"]) : "";
$usuario = isset($_POST["usuario"]) ? limpiarcadena($_POST["usuario"]) : "";
$clave = isset($_POST["clave"]) ? limpiarcadena($_POST["clave"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioApoderado->guardar($id_apoderado_tipo, $id_documento, $numerodocumento, strtoupper($nombreyapellido), $telefono, strtolower($usuario), $clave, $observaciones);
            echo $rspta ? "Usuario apoderado registrado correctamente" : "No se pudo registrar el usuario apoderado";
        } else {
            $rspta = $usuarioApoderado->editar($id, $id_apoderado_tipo, $id_documento, $numerodocumento, strtoupper($nombreyapellido), $telefono, strtolower($usuario), $clave, $observaciones);
            echo $rspta ? "Usuario apoderado actualizado correctamente" : "No se pudo actualizar el usuario apoderado";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioApoderado->desactivar($id);
        echo $rspta ? "Usuario apoderado desactivado correctamente" : "No se pudo desactivar el usuario apoderado";
        break;

    case 'activar':
        $rspta = $usuarioApoderado->activar($id);
        echo $rspta ? "Usuario apoderado activado correctamente" : "No se pudo activar el usuario apoderado";
        break;

    case 'mostrar':
        $rspta = $usuarioApoderado->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioApoderado->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->id,
                "1" => $reg->tipo_apoderado.' - '.$reg->nombreyapellido,
                "2" => $reg->tipo_documento.' - '.$reg->numerodocumento,
                "3" => 'Telf. '.$reg->telefono,
                "4" => ($reg->estado) ?
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>' :
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>'
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

    case 'listar_tipos_apoderados_activos':
        $rspta = $usuarioApoderado->listarTiposApoderadosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_tipos_documentos_activos':
        $rspta = $usuarioApoderado->listarTiposDocumentosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_sexos_activos':
        $rspta = $usuarioApoderado->listarSexosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_estados_civiles_activos':
        $rspta = $usuarioApoderado->listarEstadosCivilesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
?>
