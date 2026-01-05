<?php
include_once("../Modelo/Institucion_lectivo.php");

$institucionLectivo = new Institucion_lectivo();
$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$nombre_lectivo = isset($_POST["nombre_lectivo"]) ? limpiarcadena($_POST["nombre_lectivo"]) : "";
$id_institucion = isset($_POST["id_institucion"]) ? limpiarcadena($_POST["id_institucion"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $institucionLectivo->guardar(strtoupper($nombre), strtoupper($nombre_lectivo), $id_institucion, $observaciones);
            echo $rspta ? "Institución lectiva registrada correctamente" : "No se pudo registrar la institución lectiva";
        } else {
            $rspta = $institucionLectivo->editar($id, strtoupper($nombre), strtoupper($nombre_lectivo), $id_institucion, $observaciones);
            echo $rspta ? "Institución lectiva actualizada correctamente" : "No se pudo actualizar la institución lectiva";
        }
        break;

    case 'desactivar':
        $rspta = $institucionLectivo->desactivar($id);
        echo $rspta ? "Institución lectiva desactivada correctamente" : "No se pudo desactivar la institución lectiva";
        break;

    case 'activar':
        $rspta = $institucionLectivo->activar($id);
        echo $rspta ? "Institución lectiva activada correctamente" : "No se pudo activar la institución lectiva";
        break;

    case 'mostrar':
        $rspta = $institucionLectivo->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $institucionLectivo->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $nombre_lectivo = strlen($reg->nombre_lectivo) > 40 ? substr($reg->nombre_lectivo, 0, 40) . '...' : $reg->nombre_lectivo;
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre,
                "2" => $nombre_lectivo,
                "3" => $reg->institucion,
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


    case 'listar_instituciones_activas':
        $rspta = $institucionLectivo->listarInstitucionesActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
