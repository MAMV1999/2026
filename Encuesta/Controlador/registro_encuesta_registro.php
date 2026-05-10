<?php
session_start();
include_once("../Modelo/registro_encuesta_registro.php");

$registro = new RegistroEncuestaAlumnoResponder();

$usuario_alumno_id = $_SESSION["docente_id"] ?? "";

switch ($_GET["op"]) {

    case 'listar':

        if ($usuario_alumno_id == "") {
            echo json_encode(array("aaData" => array()));
            exit;
        }

        $rspta = $registro->listarEncuestas($usuario_alumno_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {

            $estado_respuesta = ($reg->total_respondidos >= $reg->total_docentes)
                ? '<span class="badge bg-success">Completado</span>'
                : '<span class="badge bg-warning text-dark">Pendiente</span>';

            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => $reg->fecha_inicio . " - " . $reg->fecha_fin,
                "3" => $reg->calificacion_menor . " - " . $reg->calificacion_mayor . " estrellas",
                "4" => $estado_respuesta,
                "5" => '<button class="btn btn-primary btn-sm" onclick="responder('.$reg->id.')">RESPONDER</button>'
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));

        break;

    case 'mostrar':

        $encuesta_id = $_POST["id"] ?? "";

        $cabecera = $registro->mostrarEncuesta($encuesta_id, $usuario_alumno_id);

        if (!$cabecera) {
            echo json_encode(array(
                "status" => "error",
                "message" => "La encuesta no está disponible para este alumno."
            ));
            exit;
        }

        $docentes = array();
        $rspta = $registro->listarDocentesEncuesta($encuesta_id, $cabecera->encuesta_alumno_id);

        while ($reg = $rspta->fetch_object()) {
            $docentes[] = $reg;
        }

        echo json_encode(array(
            "status" => "success",
            "cabecera" => $cabecera,
            "docentes" => $docentes
        ));

        break;

    case 'guardar':

        $encuesta_id = $_POST["encuesta_id"] ?? "";
        $encuesta_alumno_id = $_POST["encuesta_alumno_id"] ?? "";
        $calificaciones = $_POST["calificacion"] ?? array();
        $comentarios = $_POST["comentario"] ?? array();

        if ($usuario_alumno_id == "") {
            echo "Sesión no válida.";
            exit;
        }

        $cabecera = $registro->mostrarEncuesta($encuesta_id, $usuario_alumno_id);

        if (!$cabecera) {
            echo "La encuesta no está habilitada o no corresponde al alumno.";
            exit;
        }

        $respuestas = array();

        foreach ($calificaciones as $encuesta_docente_id => $calificacion) {

            if ($calificacion == "") {
                echo "Debe calificar a todos los docentes.";
                exit;
            }

            if ($calificacion < $cabecera->calificacion_menor || $calificacion > $cabecera->calificacion_mayor) {
                echo "La calificación está fuera del rango permitido.";
                exit;
            }

            $respuestas[$encuesta_docente_id] = array(
                "calificacion" => $calificacion,
                "comentario" => $comentarios[$encuesta_docente_id] ?? ""
            );
        }

        if (empty($respuestas)) {
            echo "No hay respuestas para guardar.";
            exit;
        }

        $rspta = $registro->guardarRespuestas($encuesta_id, $encuesta_alumno_id, $respuestas);

        echo $rspta ? "Encuesta enviada correctamente" : "Error al guardar la encuesta";

        break;
}
?>