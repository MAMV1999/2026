<?php
include_once("../Modelo/matricula_documentos.php");

$matriculaDocumentos = new MatriculaDocumentos();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_matricula_documentos_responsable = isset($_POST["id_matricula_documentos_responsable"]) ? limpiarcadena($_POST["id_matricula_documentos_responsable"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
// Obtener el valor de obligatorio con el valor predeterminado de 0 si no está definido
$obligatorio = isset($_POST["obligatorio"]) ? limpiarcadena($_POST["obligatorio"]) : "0";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $matriculaDocumentos->guardar(
                $id_matricula_documentos_responsable, strtoupper($nombre), 
                $obligatorio, $observaciones, $estado
            );
            echo $rspta ? "Documento de matrícula registrado correctamente" : "No se pudo registrar el documento de matrícula";
        } else {
            $rspta = $matriculaDocumentos->editar(
                $id, $id_matricula_documentos_responsable, strtoupper($nombre), 
                $obligatorio, $observaciones, $estado
            );
            echo $rspta ? "Documento de matrícula actualizado correctamente" : "No se pudo actualizar el documento de matrícula";
        }
        break;

    case 'desactivar':
        $rspta = $matriculaDocumentos->desactivar($id);
        echo $rspta ? "Documento de matrícula desactivado correctamente" : "No se pudo desactivar el documento de matrícula";
        break;

    case 'activar':
        $rspta = $matriculaDocumentos->activar($id);
        echo $rspta ? "Documento de matrícula activado correctamente" : "No se pudo activar el documento de matrícula";
        break;

    case 'mostrar':
        $rspta = $matriculaDocumentos->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $matriculaDocumentos->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->responsable,
                "1" => $reg->nombre,
                "2" => $reg->obligatorio ? 'SI' : 'NO',
                "3" => ($reg->estado) ?
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
        $rspta = $matriculaDocumentos->listarResponsablesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
?>
