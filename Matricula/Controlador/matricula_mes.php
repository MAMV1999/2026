<?php
include_once("../Modelo/matricula_mes.php");

$matriculaMes = new MatriculaMes();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$institucion_lectivo_id = isset($_POST["institucion_lectivo_id"]) ? limpiarcadena($_POST["institucion_lectivo_id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$fecha_vencimiento = isset($_POST["fecha_vencimiento"]) ? limpiarcadena($_POST["fecha_vencimiento"]) : "";
$mora = isset($_POST["mora"]) ? limpiarcadena($_POST["mora"]) : "0.00";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $matriculaMes->guardar(
                $institucion_lectivo_id,
                strtoupper($nombre),
                $fecha_vencimiento,
                $mora,
                $observaciones,
                $estado
            );
            echo $rspta ? "Mes de matrícula registrado correctamente" : "No se pudo registrar el mes de matrícula";
        } else {
            $rspta = $matriculaMes->editar(
                $id,
                $institucion_lectivo_id,
                strtoupper($nombre),
                $fecha_vencimiento,
                $mora,
                $observaciones,
                $estado
            );
            echo $rspta ? "Mes de matrícula actualizado correctamente" : "No se pudo actualizar el mes de matrícula";
        }
        break;

    case 'desactivar':
        $rspta = $matriculaMes->desactivar($id);
        echo $rspta ? "Mes de matrícula desactivado correctamente" : "No se pudo desactivar el mes de matrícula";
        break;

    case 'activar':
        $rspta = $matriculaMes->activar($id);
        echo $rspta ? "Mes de matrícula activado correctamente" : "No se pudo activar el mes de matrícula";
        break;

    case 'mostrar':
        $rspta = $matriculaMes->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $matriculaMes->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => 'PERIODO ' . $reg->institucion_lectivo,
                "2" => $reg->nombre,
                "3" => $reg->fecha_vencimiento,
                "4" => $reg->mora,
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
        $rspta = $matriculaMes->listarInstitucionesLectivasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
