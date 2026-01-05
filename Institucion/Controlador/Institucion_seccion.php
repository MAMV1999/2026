<?php
include_once("../Modelo/Institucion_seccion.php");

$institucionSeccion = new Institucion_seccion();
$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$id_institucion_grado = isset($_POST["id_institucion_grado"]) ? limpiarcadena($_POST["id_institucion_grado"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $institucionSeccion->guardar(strtoupper($nombre), $id_institucion_grado, $observaciones);
            echo $rspta ? "Sección de institución registrada correctamente" : "No se pudo registrar la sección de institución";
        } else {
            $rspta = $institucionSeccion->editar($id, strtoupper($nombre), $id_institucion_grado, $observaciones);
            echo $rspta ? "Sección de institución actualizada correctamente" : "No se pudo actualizar la sección de institución";
        }
        break;

    case 'desactivar':
        $rspta = $institucionSeccion->desactivar($id);
        echo $rspta ? "Sección de institución desactivada correctamente" : "No se pudo desactivar la sección de institución";
        break;

    case 'activar':
        $rspta = $institucionSeccion->activar($id);
        echo $rspta ? "Sección de institución activada correctamente" : "No se pudo activar la sección de institución";
        break;

    case 'mostrar':
        $rspta = $institucionSeccion->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $institucionSeccion->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre_lectivo.' - '.$reg->nombre_nivel.' - '.$reg->nombre_grado,
                "2" => $reg->nombre,
                "3" => ($reg->estado) ?
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

    case 'listar_grados_activos':
        $rspta = $institucionSeccion->listarGradosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
