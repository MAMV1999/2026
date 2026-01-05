<?php
include_once("../Modelo/matricula_editar.php");

$matriculaDetalle = new Matricula_detalle();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_matricula = isset($_POST["id_matricula"]) ? limpiarcadena($_POST["id_matricula"]) : "";
$id_matricula_categoria = isset($_POST["id_matricula_categoria"]) ? limpiarcadena($_POST["id_matricula_categoria"]) : "";
$id_usuario_apoderado = isset($_POST["id_usuario_apoderado"]) ? limpiarcadena($_POST["id_usuario_apoderado"]) : "";
$id_usuario_alumno = isset($_POST["id_usuario_alumno"]) ? limpiarcadena($_POST["id_usuario_alumno"]) : "";
//$id_usuario_apoderado_referido = isset($_POST["id_usuario_apoderado_referido"]) ? limpiarcadena($_POST["id_usuario_apoderado_referido"]) : "";
$id_usuario_apoderado_referido = isset($_POST["id_usuario_apoderado_referido"]) && $_POST["id_usuario_apoderado_referido"] !== "" ? limpiarcadena($_POST["id_usuario_apoderado_referido"]) : "NULL";
$descripcion = isset($_POST["descripcion"]) ? limpiarcadena($_POST["descripcion"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $matriculaDetalle->guardar($id_matricula, $id_matricula_categoria, $id_usuario_apoderado, $id_usuario_alumno, $id_usuario_apoderado_referido, $descripcion, $observaciones);
            echo $rspta ? "Detalle de matrícula registrado correctamente" : "No se pudo registrar el detalle de matrícula";
        } else {
            $rspta = $matriculaDetalle->editar($id, $id_matricula, $id_matricula_categoria, $id_usuario_apoderado, $id_usuario_alumno, $id_usuario_apoderado_referido, $descripcion, $observaciones);
            echo $rspta ? "Detalle de matrícula actualizado correctamente" : "No se pudo actualizar el detalle de matrícula";
        }
        break; 

    case 'desactivar':
        $rspta = $matriculaDetalle->desactivar($id);
        echo $rspta ? "Detalle de matrícula desactivado correctamente" : "No se pudo desactivar el detalle de matrícula";
        break;

    case 'activar':
        $rspta = $matriculaDetalle->activar($id);
        echo $rspta ? "Detalle de matrícula activado correctamente" : "No se pudo activar el detalle de matrícula";
        break;

    case 'mostrar':
        $rspta = $matriculaDetalle->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $matriculaDetalle->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nivel . ' - ' . $reg->grado,
                "2" => $reg->apoderado,
                "3" => $reg->alumno,
                "4" => ($reg->estado) ?
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>'
                    :
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

    case 'listar_matriculas_activas':
        $rspta = $matriculaDetalle->listarMatriculasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado . ' - ' . $reg->seccion . '</option>';
        }
        break;

    case 'listar_categorias_matricula_activas':
        $rspta = $matriculaDetalle->listarCategoriasMatriculaActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_apoderados_activos':
        $rspta = $matriculaDetalle->listarApoderadosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombreyapellido . '</option>';
        }
        break;

    case 'listar_alumnos_activos':
        $rspta = $matriculaDetalle->listarAlumnosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombreyapellido . '</option>';
        }
        break;

    case 'listar_apoderados_referidos_activos':
        $rspta = $matriculaDetalle->listarApoderadosReferidosActivos();
        echo '<option value="">NO TIENE REFERENCIA</option>'; // Primera opción vacía
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombreyapellido . ' (' . $reg->repeticiones . ')</option>';
        }
        break;
}
