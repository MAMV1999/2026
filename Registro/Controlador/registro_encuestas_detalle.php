<?php
include_once("../Modelo/registro_encuestas_detalle.php");

$registro = new RegistroEncuestasDetalle();

switch ($_GET["op"]) {

    case 'listar':

        $rspta = $registro->listarEncuestas();
        $data = array();

        while ($reg = $rspta->fetch_object()) {

            $data[] = array(
                "0" => count($data) + 1,
                "1" => htmlspecialchars($reg->nombre, ENT_QUOTES, 'UTF-8'),
                "2" => $reg->fecha_inicio . " - " . $reg->fecha_fin,
                "3" => $reg->calificacion_menor . " - " . $reg->calificacion_mayor . " estrellas",
                "4" => $reg->total_alumnos,
                "5" => $reg->total_respuestas,
                "6" => '<button class="btn btn-info btn-sm" onclick="mostrar('.$reg->id.')">VER RESPUESTAS</button>'
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

    case 'mostrar':

        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if ($id == "") {
            echo json_encode(array("error" => "No se recibió el identificador de la encuesta."));
            break;
        }

        $cabecera = $registro->mostrarEncuesta($id);

        if (!$cabecera) {
            echo json_encode(array("error" => "No se encontró la encuesta seleccionada."));
            break;
        }

        $rspta = $registro->listarRespuestas($id);
        $respuestas = array();

        while ($reg = $rspta->fetch_object()) {
            $respuestas[] = array(
                "id" => $reg->id,
                "encuesta_alumno_id" => $reg->encuesta_alumno_id,
                "alumno" => $reg->alumno,
                "nivel" => $reg->nivel,
                "grado" => $reg->grado,
                "seccion" => $reg->seccion,
                "docente" => $reg->docente,
                "numero_calificacion" => $reg->numero_calificacion,
                "comentario" => $reg->comentario,
                "fecha_respuesta" => $reg->fecha_respuesta
            );
        }

        echo json_encode(array(
            "cabecera" => $cabecera,
            "respuestas" => $respuestas
        ));

        break;
}
?>
