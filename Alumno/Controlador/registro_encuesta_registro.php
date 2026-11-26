<?php
session_start();

include_once("../Modelo/registro_encuesta_registro.php");

$registro = new RegistroEncuestaAlumno();

$usuario_alumno_id = $_SESSION["alumno_id"] ?? $_SESSION["docente_id"] ?? $_SESSION["id"] ?? "";

if ($usuario_alumno_id == "") {
    echo "Sesión de alumno no encontrada";
    exit;
}

switch ($_GET["op"]) {

    case 'listar':

        $rspta = $registro->listarEncuestasAlumno($usuario_alumno_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {

            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => $reg->fecha_inicio . " - " . $reg->fecha_fin,
                "3" => $reg->calificacion_menor . " - " . $reg->calificacion_mayor . " estrellas",
                "4" => '<button class="btn btn-primary btn-sm" onclick="responder('.$reg->encuesta_general_id.', '.$reg->encuesta_alumno_id.')">
                            RESPONDER
                        </button>'
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

        $encuesta_general_id = $_POST["encuesta_general_id"] ?? "";
        $encuesta_alumno_id = $_POST["encuesta_alumno_id"] ?? "";

        $cabecera = $registro->mostrarEncuesta(
            $encuesta_general_id,
            $encuesta_alumno_id,
            $usuario_alumno_id
        );

        if (!$cabecera) {
            echo json_encode(array(
                "estado" => false,
                "mensaje" => "La encuesta no está disponible."
            ));
            exit;
        }

        if ($registro->yaRespondio($encuesta_general_id, $encuesta_alumno_id)) {
            echo json_encode(array(
                "estado" => false,
                "mensaje" => "Usted ya respondió esta encuesta."
            ));
            exit;
        }

        $docentes = array();

        $rsptaDocentes = $registro->listarDocentesEncuesta($encuesta_general_id);

        while ($reg = $rsptaDocentes->fetch_object()) {
            $docentes[] = array(
                "encuesta_docente_id" => $reg->encuesta_docente_id,
                "docente" => $reg->docente
            );
        }

        echo json_encode(array(
            "estado" => true,
            "cabecera" => $cabecera,
            "docentes" => $docentes
        ));

        break;

    case 'guardar':

        $encuesta_general_id = $_POST["encuesta_general_id"] ?? "";
        $encuesta_alumno_id = $_POST["encuesta_alumno_id"] ?? "";

        $calificaciones = $_POST["calificacion"] ?? array();
        $comentarios = $_POST["comentario"] ?? array();

        if ($encuesta_general_id == "" || $encuesta_alumno_id == "" || empty($calificaciones)) {
            echo "Datos incompletos";
            exit;
        }

        $rspta = $registro->guardarRespuestas(
            $encuesta_general_id,
            $encuesta_alumno_id,
            $usuario_alumno_id,
            $calificaciones,
            $comentarios
        );

        echo $rspta;

        break;
}