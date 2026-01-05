<?php
include_once("../Modelo/Institucion_nivel.php");

$institucionNivel = new Institucion_nivel();
$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$id_institucion_lectivo = isset($_POST["id_institucion_lectivo"]) ? limpiarcadena($_POST["id_institucion_lectivo"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $institucionNivel->guardar(strtoupper($nombre), $id_institucion_lectivo, $observaciones);
            echo $rspta ? "Nivel de institución registrado correctamente" : "No se pudo registrar el nivel de institución";
        } else {
            $rspta = $institucionNivel->editar($id, strtoupper($nombre), $id_institucion_lectivo, $observaciones);
            echo $rspta ? "Nivel de institución actualizado correctamente" : "No se pudo actualizar el nivel de institución";
        }
        break;

    case 'desactivar':
        $rspta = $institucionNivel->desactivar($id);
        echo $rspta ? "Nivel de institución desactivado correctamente" : "No se pudo desactivar el nivel de institución";
        break;

    case 'activar':
        $rspta = $institucionNivel->activar($id);
        echo $rspta ? "Nivel de institución activado correctamente" : "No se pudo activar el nivel de institución";
        break;

    case 'mostrar':
        $rspta = $institucionNivel->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $institucionNivel->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => 'PE. LECTIVO ' . $reg->institucion_lectivo,
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


    case 'listar_instituciones_lectivas_activas':
        $rspta = $institucionNivel->listarInstitucionesLectivasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
