<?php
include_once("../Modelo/Institucion.php");

$institucion = new Institucion();
$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$id_usuario_docente = isset($_POST["id_usuario_docente"]) ? limpiarcadena($_POST["id_usuario_docente"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarcadena($_POST["telefono"]) : "";
$correo = isset($_POST["correo"]) ? limpiarcadena($_POST["correo"]) : "";
$ruc = isset($_POST["ruc"]) ? limpiarcadena($_POST["ruc"]) : "";
$razon_social = isset($_POST["razon_social"]) ? limpiarcadena($_POST["razon_social"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarcadena($_POST["direccion"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $institucion->guardar(strtoupper($nombre), $id_usuario_docente, $telefono, strtoupper($correo), $ruc, strtoupper($razon_social), strtoupper($direccion), $observaciones);
            echo $rspta ? "Institución registrada correctamente" : "No se pudo registrar la institución";
        } else {
            $rspta = $institucion->editar($id, strtoupper($nombre), $id_usuario_docente, $telefono, strtoupper($correo), $ruc, strtoupper($razon_social), strtoupper($direccion), $observaciones);
            echo $rspta ? "Institución actualizada correctamente" : "No se pudo actualizar la institución";
        }
        break;

    case 'desactivar':
        $rspta = $institucion->desactivar($id);
        echo $rspta ? "Institución desactivada correctamente" : "No se pudo desactivar la institución";
        break;

    case 'activar':
        $rspta = $institucion->activar($id);
        echo $rspta ? "Institución activada correctamente" : "No se pudo activar la institución";
        break;

    case 'mostrar':
        $rspta = $institucion->mostrar($id);
        // Codificar el resultado usando JSON
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $institucion->listar();
        // Vamos a declarar un array
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre,
                "2" => $reg->usuario_docente,
                "3" => $reg->ruc . " - " . $reg->razon_social,
                "4" => ($reg->estado) ? 
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>' :
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>'
            );
        }
        $results = array(
            "sEcho" => 1, // Información para el datatables
            "iTotalRecords" => count($data), // Enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), // Enviamos el total de registros a visualizar
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listar_docentes_activos':
        $rspta = $institucion->listarDocentesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombreyapellido . ' - ' . $reg->cargo . '</option>';
        }
        break;
}
?>
