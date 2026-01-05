<?php
include_once("../Modelo/documento.php");

$documento = new Documento();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_documento_responsable = isset($_POST["id_documento_responsable"]) ? limpiarcadena($_POST["id_documento_responsable"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
// Obtener el valor de obligatorio con el valor predeterminado de 0 si no está definido
$obligatorio = isset($_POST["obligatorio"]) ? limpiarcadena($_POST["obligatorio"]) : "0";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $documento->guardar(
                $id_documento_responsable, strtoupper($nombre), 
                $obligatorio, $observaciones, $estado
            );
            echo $rspta ? "Documento registrado correctamente" : "No se pudo registrar el documento";
        } else {
            $rspta = $documento->editar(
                $id, $id_documento_responsable, strtoupper($nombre), 
                $obligatorio, $observaciones, $estado
            );
            echo $rspta ? "Documento actualizado correctamente" : "No se pudo actualizar el documento";
        }
        break;

    case 'desactivar':
        $rspta = $documento->desactivar($id);
        echo $rspta ? "Documento desactivado correctamente" : "No se pudo desactivar el documento";
        break;

    case 'activar':
        $rspta = $documento->activar($id);
        echo $rspta ? "Documento activado correctamente" : "No se pudo activar el documento";
        break;

    case 'mostrar':
        $rspta = $documento->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $documento->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->responsable,
                "2" => $reg->nombre,
                "3" => $reg->obligatorio ? 'SI' : 'NO',
                "4" => ($reg->estado) ?
                    '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>' :
                    '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>'
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

    // Listar responsables activos para el formulario
    case 'listar_responsables_activos':
        $rspta = $documento->listarResponsablesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
?>
