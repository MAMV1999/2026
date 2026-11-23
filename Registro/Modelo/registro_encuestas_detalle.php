<?php
require_once("../../database.php");

class RegistroEncuestasDetalle
{
    public function __construct() {}

    public function listarEncuestas()
    {
        $sql = "SELECT
                    eg.id,
                    eg.nombre,
                    DATE_FORMAT(eg.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                    DATE_FORMAT(eg.fecha_fin, '%d/%m/%Y') AS fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor,
                    eg.estado,
                    COUNT(DISTINCT rer.encuesta_alumno_id) AS total_alumnos,
                    COUNT(rer.id) AS total_respuestas
                FROM registro_encuesta_general eg
                LEFT JOIN registro_encuesta_registro rer
                    ON rer.encuesta_general_id = eg.id
                    AND rer.estado = 1
                GROUP BY
                    eg.id,
                    eg.nombre,
                    eg.fecha_inicio,
                    eg.fecha_fin,
                    eg.calificacion_menor,
                    eg.calificacion_mayor,
                    eg.estado
                ORDER BY eg.id DESC";

        return ejecutarConsulta($sql);
    }

    public function mostrarEncuesta($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                    id,
                    nombre,
                    DATE_FORMAT(fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                    DATE_FORMAT(fecha_fin, '%d/%m/%Y') AS fecha_fin,
                    calificacion_menor,
                    calificacion_mayor,
                    observaciones
                FROM registro_encuesta_general
                WHERE id = '$id'
                LIMIT 1";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarRespuestas($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                    rer.id,
                    rer.encuesta_alumno_id,
                    alu.nombreyapellido AS alumno,
                    niv.nombre AS nivel,
                    gra.nombre AS grado,
                    sec.nombre AS seccion,
                    ud.nombreyapellido AS docente,
                    rer.numero_calificacion,
                    rer.comentario,
                    DATE_FORMAT(rer.fechacreado, '%d/%m/%Y %H:%i') AS fecha_respuesta
                FROM registro_encuesta_registro rer
                INNER JOIN registro_encuesta_alumno rea
                    ON rer.encuesta_alumno_id = rea.id
                INNER JOIN matricula_detalle md
                    ON rea.matricula_detalle_id = md.id
                INNER JOIN usuario_alumno alu
                    ON md.id_usuario_alumno = alu.id
                LEFT JOIN matricula m
                    ON md.id_matricula = m.id
                LEFT JOIN institucion_seccion sec
                    ON m.id_institucion_seccion = sec.id
                LEFT JOIN institucion_grado gra
                    ON sec.id_institucion_grado = gra.id
                LEFT JOIN institucion_nivel niv
                    ON gra.id_institucion_nivel = niv.id
                INNER JOIN registro_encuesta_docentes red
                    ON rer.encuesta_docente_id = red.id
                INNER JOIN usuario_docente ud
                    ON red.usuario_docente_id = ud.id
                WHERE rer.encuesta_general_id = '$id'
                  AND rer.estado = 1
                ORDER BY rer.encuesta_alumno_id ASC, ud.nombreyapellido ASC, rer.id ASC";

        return ejecutarConsulta($sql);
    }}
?>
