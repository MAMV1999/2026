<?php
include_once("../Modelo/Usuario_alumno.php");

$usuarioAlumno = new Usuario_alumno();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_apoderado = isset($_POST["id_apoderado"]) ? limpiarcadena($_POST["id_apoderado"]) : "";
$id_documento = isset($_POST["id_documento"]) ? limpiarcadena($_POST["id_documento"]) : "";
$numerodocumento = isset($_POST["numerodocumento"]) ? limpiarcadena($_POST["numerodocumento"]) : "";
$nombreyapellido = isset($_POST["nombreyapellido"]) ? limpiarcadena($_POST["nombreyapellido"]) : "";
$id_sexo = isset($_POST["id_sexo"]) ? limpiarcadena($_POST["id_sexo"]) : "";
$usuario = isset($_POST["usuario"]) ? limpiarcadena($_POST["usuario"]) : "";
$clave = isset($_POST["clave"]) ? limpiarcadena($_POST["clave"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioAlumno->guardar($id_apoderado, $id_documento, $numerodocumento, strtoupper($nombreyapellido), $id_sexo, strtolower($usuario), $clave, $observaciones);
            echo $rspta ? "Alumno registrado correctamente" : "No se pudo registrar el alumno";
        } else {
            $rspta = $usuarioAlumno->editar($id, $id_apoderado, $id_documento, $numerodocumento, strtoupper($nombreyapellido), $id_sexo, strtolower($usuario), $clave, $observaciones);
            echo $rspta ? "Alumno actualizado correctamente" : "No se pudo actualizar el alumno";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioAlumno->desactivar($id);
        echo $rspta ? "Alumno desactivado correctamente" : "No se pudo desactivar el alumno";
        break;

    case 'activar':
        $rspta = $usuarioAlumno->activar($id);
        echo $rspta ? "Alumno activado correctamente" : "No se pudo activar el alumno";
        break;

    case 'mostrar':
        $rspta = $usuarioAlumno->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioAlumno->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombreyapellido,
                "2" => $reg->tipo_documento . ' - ' . $reg->numerodocumento,
                "3" => $reg->apoderado,
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

    case 'listar_apoderados_activos':
        $rspta = $usuarioAlumno->listarApoderadosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombreyapellido . '</option>';
        }
        break;

    case 'listar_tipos_documentos_activos':
        $rspta = $usuarioAlumno->listarTiposDocumentosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_sexos_activos':
        $rspta = $usuarioAlumno->listarSexosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
