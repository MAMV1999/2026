<?php
require_once("../../database.php");

class RegistroEncuestaAlumno
{
    public function __construct() {}

    public function listarEncuestasAlumno($usuario_alumno_id)
    {
        $usuario_alumno_id = limpiarcadena($usuario_alumno_id);

        $sql = "SELECT 
                    eg.id AS encuesta_general_id,
                    MIN(rea.id) AS encuesta_alumno_id,
                    eg.nombre,
                    DATE_FORMAT(eg.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                    DATE_FORMAT(eg.fecha_fin, '%d/%m/%Y') AS fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor,
                    COUNT(rer.id) AS total_respondido
                FROM registro_encuesta_general eg
                INNER JOIN registro_encuesta_alumno rea 
                    ON rea.encuesta_general_id = eg.id
                INNER JOIN matricula_detalle md 
                    ON md.id = rea.matricula_detalle_id
                LEFT JOIN registro_encuesta_registro rer 
                    ON rer.encuesta_general_id = eg.id
                    AND rer.encuesta_alumno_id = rea.id
                    AND rer.estado = 1
                WHERE md.id_usuario_alumno = '$usuario_alumno_id'
                  AND eg.estado = 1
                  AND rea.estado = 1
                  AND md.estado = 1
                  AND CURDATE() BETWEEN eg.fecha_inicio AND eg.fecha_fin
                GROUP BY 
                    eg.id,
                    eg.nombre,
                    eg.fecha_inicio,
                    eg.fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor
                HAVING total_respondido = 0
                ORDER BY eg.id DESC";

        return ejecutarConsulta($sql);
    }

    public function mostrarEncuesta($encuesta_general_id, $encuesta_alumno_id, $usuario_alumno_id)
    {
        $encuesta_general_id = limpiarcadena($encuesta_general_id);
        $encuesta_alumno_id = limpiarcadena($encuesta_alumno_id);
        $usuario_alumno_id = limpiarcadena($usuario_alumno_id);

        $sql = "SELECT 
                    eg.id AS encuesta_general_id,
                    rea.id AS encuesta_alumno_id,
                    eg.nombre,
                    eg.fecha_inicio,
                    eg.fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor
                FROM registro_encuesta_general eg
                INNER JOIN registro_encuesta_alumno rea 
                    ON rea.encuesta_general_id = eg.id
                INNER JOIN matricula_detalle md 
                    ON md.id = rea.matricula_detalle_id
                WHERE eg.id = '$encuesta_general_id'
                  AND rea.id = '$encuesta_alumno_id'
                  AND md.id_usuario_alumno = '$usuario_alumno_id'
                  AND eg.estado = 1
                  AND rea.estado = 1
                  AND md.estado = 1
                  AND CURDATE() BETWEEN eg.fecha_inicio AND eg.fecha_fin
                LIMIT 1";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDocentesEncuesta($encuesta_general_id)
    {
        $encuesta_general_id = limpiarcadena($encuesta_general_id);

        /*
            GROUP BY ud.id evita que el mismo docente salga 2 o 3 veces
            si por registros anteriores quedó duplicado en registro_encuesta_docentes.
        */
        $sql = "SELECT 
                    MIN(red.id) AS encuesta_docente_id,
                    ud.id AS usuario_docente_id,
                    ud.nombreyapellido AS docente
                FROM registro_encuesta_docentes red
                INNER JOIN usuario_docente ud 
                    ON ud.id = red.usuario_docente_id
                WHERE red.encuesta_general_id = '$encuesta_general_id'
                  AND red.estado = 1
                  AND ud.estado = 1
                GROUP BY ud.id, ud.nombreyapellido
                ORDER BY ud.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }

    public function yaRespondio($encuesta_general_id, $encuesta_alumno_id)
    {
        $encuesta_general_id = limpiarcadena($encuesta_general_id);
        $encuesta_alumno_id = limpiarcadena($encuesta_alumno_id);

        $sql = "SELECT COUNT(*) AS total
                FROM registro_encuesta_registro
                WHERE encuesta_general_id = '$encuesta_general_id'
                  AND encuesta_alumno_id = '$encuesta_alumno_id'
                  AND estado = 1";

        $fila = ejecutarConsultaSimpleFila($sql);

        return $fila["total"] > 0;
    }

    public function guardarRespuestas($encuesta_general_id, $encuesta_alumno_id, $usuario_alumno_id, $calificaciones, $comentarios)
    {
        try {
            $encuesta_general_id = limpiarcadena($encuesta_general_id);
            $encuesta_alumno_id = limpiarcadena($encuesta_alumno_id);
            $usuario_alumno_id = limpiarcadena($usuario_alumno_id);

            $encuesta = $this->mostrarEncuesta($encuesta_general_id, $encuesta_alumno_id, $usuario_alumno_id);

            if (!$encuesta) {
                return "La encuesta no está disponible o ya venció.";
            }

            if ($this->yaRespondio($encuesta_general_id, $encuesta_alumno_id)) {
                return "Usted ya respondió esta encuesta.";
            }

            $calificacion_menor = intval($encuesta["calificacion_menor"]);
            $calificacion_mayor = intval($encuesta["calificacion_mayor"]);

            foreach ($calificaciones as $encuesta_docente_id => $numero_calificacion) {

                $encuesta_docente_id = limpiarcadena($encuesta_docente_id);
                $numero_calificacion = intval(limpiarcadena($numero_calificacion));
                $comentario = isset($comentarios[$encuesta_docente_id]) ? limpiarcadena($comentarios[$encuesta_docente_id]) : "";

                if ($numero_calificacion < $calificacion_menor || $numero_calificacion > $calificacion_mayor) {
                    return "La calificación enviada no está dentro del rango permitido.";
                }

                $sqlValidarDocente = "SELECT id 
                                      FROM registro_encuesta_docentes
                                      WHERE id = '$encuesta_docente_id'
                                        AND encuesta_general_id = '$encuesta_general_id'
                                        AND estado = 1
                                      LIMIT 1";

                $docenteValido = ejecutarConsultaSimpleFila($sqlValidarDocente);

                if (!$docenteValido) {
                    return "Uno de los docentes no pertenece a esta encuesta.";
                }

                $sqlExisteRespuesta = "SELECT id 
                                       FROM registro_encuesta_registro
                                       WHERE encuesta_general_id = '$encuesta_general_id'
                                         AND encuesta_docente_id = '$encuesta_docente_id'
                                         AND encuesta_alumno_id = '$encuesta_alumno_id'
                                         AND estado = 1
                                       LIMIT 1";

                $respuestaExiste = ejecutarConsultaSimpleFila($sqlExisteRespuesta);

                if (!$respuestaExiste) {
                    $sql = "INSERT INTO registro_encuesta_registro
                            (
                                encuesta_general_id,
                                encuesta_docente_id,
                                encuesta_alumno_id,
                                numero_calificacion,
                                comentario,
                                estado
                            )
                            VALUES
                            (
                                '$encuesta_general_id',
                                '$encuesta_docente_id',
                                '$encuesta_alumno_id',
                                '$numero_calificacion',
                                '$comentario',
                                1
                            )";

                    ejecutarConsulta($sql);
                }
            }

            return "Encuesta enviada correctamente";

        } catch (Exception $e) {
            return "Error al guardar la encuesta";
        }
    }
}