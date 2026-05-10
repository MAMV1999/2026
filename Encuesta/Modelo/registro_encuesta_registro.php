<?php
require_once("../../database.php");

class RegistroEncuestaAlumnoResponder
{
    public function __construct() {}

    public function obtenerMatriculaDetalleAlumno($usuario_alumno_id)
    {
        $usuario_alumno_id = limpiarcadena($usuario_alumno_id);

        $sql = "SELECT id 
                FROM matricula_detalle 
                WHERE id_usuario_alumno = '$usuario_alumno_id'
                  AND estado = '1'
                LIMIT 1";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarEncuestas($usuario_alumno_id)
    {
        $usuario_alumno_id = limpiarcadena($usuario_alumno_id);

        $sql = "SELECT 
                    eg.id,
                    eg.nombre,
                    DATE_FORMAT(eg.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                    DATE_FORMAT(eg.fecha_fin, '%d/%m/%Y') AS fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor,
                    rea.id AS encuesta_alumno_id,

                    (
                        SELECT COUNT(*)
                        FROM registro_encuesta_docentes red
                        WHERE red.encuesta_general_id = eg.id
                          AND red.estado = '1'
                    ) AS total_docentes,

                    (
                        SELECT COUNT(*)
                        FROM registro_encuesta_registro rer
                        WHERE rer.encuesta_general_id = eg.id
                          AND rer.encuesta_alumno_id = rea.id
                          AND rer.estado = '1'
                    ) AS total_respondidos

                FROM registro_encuesta_alumno rea
                INNER JOIN registro_encuesta_general eg 
                    ON rea.encuesta_general_id = eg.id

                WHERE rea.matricula_detalle_id IN (
                    SELECT md.id 
                    FROM matricula_detalle md
                    WHERE md.id_usuario_alumno = '$usuario_alumno_id'
                      AND md.estado = '1'
                )
                AND rea.estado = '1'
                AND eg.estado = '1'
                AND CURDATE() BETWEEN eg.fecha_inicio AND eg.fecha_fin

                ORDER BY eg.id DESC";

        return ejecutarConsulta($sql);
    }

    public function mostrarEncuesta($encuesta_id, $usuario_alumno_id)
    {
        $encuesta_id = limpiarcadena($encuesta_id);
        $usuario_alumno_id = limpiarcadena($usuario_alumno_id);

        $sql = "SELECT 
                    eg.id,
                    eg.nombre,
                    eg.fecha_inicio,
                    eg.fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor,
                    rea.id AS encuesta_alumno_id
                FROM registro_encuesta_alumno rea
                INNER JOIN registro_encuesta_general eg 
                    ON rea.encuesta_general_id = eg.id
                INNER JOIN matricula_detalle md 
                    ON rea.matricula_detalle_id = md.id
                WHERE eg.id = '$encuesta_id'
                  AND md.id_usuario_alumno = '$usuario_alumno_id'
                  AND rea.estado = '1'
                  AND eg.estado = '1'
                  AND CURDATE() BETWEEN eg.fecha_inicio AND eg.fecha_fin
                LIMIT 1";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDocentesEncuesta($encuesta_id, $encuesta_alumno_id)
    {
        $encuesta_id = limpiarcadena($encuesta_id);
        $encuesta_alumno_id = limpiarcadena($encuesta_alumno_id);

        $sql = "SELECT 
                    red.id AS encuesta_docente_id,
                    ud.nombreyapellido AS docente,
                    rer.numero_calificacion,
                    rer.comentario
                FROM registro_encuesta_docentes red
                INNER JOIN usuario_docente ud 
                    ON red.usuario_docente_id = ud.id
                LEFT JOIN registro_encuesta_registro rer 
                    ON rer.encuesta_docente_id = red.id
                    AND rer.encuesta_alumno_id = '$encuesta_alumno_id'
                    AND rer.estado = '1'
                WHERE red.encuesta_general_id = '$encuesta_id'
                  AND red.estado = '1'
                  AND ud.estado = '1'
                ORDER BY ud.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }

    public function guardarRespuestas($encuesta_id, $encuesta_alumno_id, $respuestas)
    {
        try {
            $encuesta_id = limpiarcadena($encuesta_id);
            $encuesta_alumno_id = limpiarcadena($encuesta_alumno_id);

            foreach ($respuestas as $encuesta_docente_id => $datos) {

                $encuesta_docente_id = limpiarcadena($encuesta_docente_id);
                $calificacion = limpiarcadena($datos["calificacion"]);
                $comentario = limpiarcadena($datos["comentario"]);

                $sqlExiste = "SELECT id 
                              FROM registro_encuesta_registro
                              WHERE encuesta_general_id = '$encuesta_id'
                                AND encuesta_docente_id = '$encuesta_docente_id'
                                AND encuesta_alumno_id = '$encuesta_alumno_id'
                                AND estado = '1'
                              LIMIT 1";

                $existe = ejecutarConsultaSimpleFila($sqlExiste);

                if ($existe) {
                    $sql = "UPDATE registro_encuesta_registro SET
                                numero_calificacion = '$calificacion',
                                comentario = '$comentario'
                            WHERE id = '$existe->id'";
                } else {
                    $sql = "INSERT INTO registro_encuesta_registro
                            (
                                encuesta_general_id,
                                encuesta_docente_id,
                                encuesta_alumno_id,
                                numero_calificacion,
                                comentario
                            )
                            VALUES
                            (
                                '$encuesta_id',
                                '$encuesta_docente_id',
                                '$encuesta_alumno_id',
                                '$calificacion',
                                '$comentario'
                            )";
                }

                ejecutarConsulta($sql);
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}
?>