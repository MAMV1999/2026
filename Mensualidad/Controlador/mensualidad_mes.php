<?php
include_once("../Modelo/mensualidad_mes.php");

$mensualidadMes = new MensualidadMes();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_institucion_lectivo = isset($_POST["id_institucion_lectivo"]) ? limpiarcadena($_POST["id_institucion_lectivo"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarcadena($_POST["descripcion"]) : "";
$fechavencimiento = isset($_POST["fechavencimiento"]) ? limpiarcadena($_POST["fechavencimiento"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $mensualidadMes->guardar(
                $id_institucion_lectivo, strtoupper($nombre), 
                strtoupper($descripcion), $fechavencimiento, $observaciones, $estado
            );
            echo $rspta ? "Mensualidad registrada correctamente" : "No se pudo registrar la mensualidad";
        } else {
            $rspta = $mensualidadMes->editar(
                $id, $id_institucion_lectivo, strtoupper($nombre), 
                strtoupper($descripcion), $fechavencimiento, $observaciones, $estado
            );
            echo $rspta ? "Mensualidad actualizada correctamente" : "No se pudo actualizar la mensualidad";
        }
        break;

    case 'desactivar':
        $rspta = $mensualidadMes->desactivar($id);
        echo $rspta ? "Mensualidad desactivada correctamente" : "No se pudo desactivar la mensualidad";
        break;

    case 'activar':
        $rspta = $mensualidadMes->activar($id);
        echo $rspta ? "Mensualidad activada correctamente" : "No se pudo activar la mensualidad";
        break;

    case 'mostrar':
        $rspta = $mensualidadMes->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $mensualidadMes->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° ' . $reg->id,
                "1" => 'PERIODO ' . $reg->institucion_lectivo,
                "2" => $reg->nombre,
                "3" => $reg->descripcion,
                "4" => $reg->fechavencimiento,
                "5" => ($reg->estado) ? 
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

    // Listar instituciones lectivas activas para el formulario
    case 'listar_instituciones_lectivas_activas':
        $rspta = $mensualidadMes->listarInstitucionesLectivasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
?>
