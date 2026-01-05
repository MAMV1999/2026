<?php
include_once("../Modelo/Institucion_grado.php");

$institucionGrado = new Institucion_grado();
$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$id_institucion_nivel = isset($_POST["id_institucion_nivel"]) ? limpiarcadena($_POST["id_institucion_nivel"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $institucionGrado->guardar(strtoupper($nombre), $id_institucion_nivel, $observaciones);
            echo $rspta ? "Grado de institución registrado correctamente" : "No se pudo registrar el grado de institución";
        } else {
            $rspta = $institucionGrado->editar($id, strtoupper($nombre), $id_institucion_nivel, $observaciones);
            echo $rspta ? "Grado de institución actualizado correctamente" : "No se pudo actualizar el grado de institución";
        }
        break;

    case 'desactivar':
        $rspta = $institucionGrado->desactivar($id);
        echo $rspta ? "Grado de institución desactivado correctamente" : "No se pudo desactivar el grado de institución";
        break;

    case 'activar':
        $rspta = $institucionGrado->activar($id);
        echo $rspta ? "Grado de institución activado correctamente" : "No se pudo activar el grado de institución";
        break;

    case 'mostrar':
        $rspta = $institucionGrado->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $institucionGrado->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre_lectivo . ' - ' . $reg->nombre_nivel,
                "2" => $reg->nombre_grado,
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


    case 'listar_niveles_activos':
        $rspta = $institucionGrado->listarNivelesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
