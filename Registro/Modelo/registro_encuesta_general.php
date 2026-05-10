<?php
require_once("../../database.php");

class RegistroEncuesta
{
    public function __construct() {}

    public function guardar($nombre, $fecha_inicio, $fecha_fin, $calificacion_menor, $calificacion_mayor, $observaciones, $docentes, $alumnos)
    {
        try {
            $nombre = limpiarcadena($nombre);
            $fecha_inicio = limpiarcadena($fecha_inicio);
            $fecha_fin = limpiarcadena($fecha_fin);
            $calificacion_menor = limpiarcadena($calificacion_menor);
            $calificacion_mayor = limpiarcadena($calificacion_mayor);
            $observaciones = limpiarcadena($observaciones);

            $sql = "INSERT INTO registro_encuesta_general 
                    (nombre, fecha_inicio, fecha_fin, calificacion_menor, calificacion_mayor, observaciones)
                    VALUES 
                    ('$nombre', '$fecha_inicio', '$fecha_fin', '$calificacion_menor', '$calificacion_mayor', '$observaciones')";

            $encuesta_id = ejecutarConsulta_retornarID($sql);

            foreach ($docentes as $docente_id) {
                $docente_id = limpiarcadena($docente_id);

                $sqlDocente = "INSERT INTO registro_encuesta_docentes
                               (encuesta_general_id, usuario_docente_id)
                               VALUES
                               ('$encuesta_id', '$docente_id')";
                ejecutarConsulta($sqlDocente);
            }

            foreach ($alumnos as $matricula_detalle_id) {
                $matricula_detalle_id = limpiarcadena($matricula_detalle_id);

                $sqlAlumno = "INSERT INTO registro_encuesta_alumno
                              (encuesta_general_id, matricula_detalle_id)
                              VALUES
                              ('$encuesta_id', '$matricula_detalle_id')";
                ejecutarConsulta($sqlAlumno);
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function editar($id, $nombre, $fecha_inicio, $fecha_fin, $calificacion_menor, $calificacion_mayor, $observaciones, $docentes, $alumnos)
    {
        try {
            $id = limpiarcadena($id);
            $nombre = limpiarcadena($nombre);
            $fecha_inicio = limpiarcadena($fecha_inicio);
            $fecha_fin = limpiarcadena($fecha_fin);
            $calificacion_menor = limpiarcadena($calificacion_menor);
            $calificacion_mayor = limpiarcadena($calificacion_mayor);
            $observaciones = limpiarcadena($observaciones);

            $sql = "UPDATE registro_encuesta_general SET
                        nombre = '$nombre',
                        fecha_inicio = '$fecha_inicio',
                        fecha_fin = '$fecha_fin',
                        calificacion_menor = '$calificacion_menor',
                        calificacion_mayor = '$calificacion_mayor',
                        observaciones = '$observaciones'
                    WHERE id = '$id'";

            ejecutarConsulta($sql);

            ejecutarConsulta("DELETE FROM registro_encuesta_docentes WHERE encuesta_general_id = '$id'");
            ejecutarConsulta("DELETE FROM registro_encuesta_alumno WHERE encuesta_general_id = '$id'");

            foreach ($docentes as $docente_id) {
                $docente_id = limpiarcadena($docente_id);

                $sqlDocente = "INSERT INTO registro_encuesta_docentes
                               (encuesta_general_id, usuario_docente_id)
                               VALUES
                               ('$id', '$docente_id')";
                ejecutarConsulta($sqlDocente);
            }

            foreach ($alumnos as $matricula_detalle_id) {
                $matricula_detalle_id = limpiarcadena($matricula_detalle_id);

                $sqlAlumno = "INSERT INTO registro_encuesta_alumno
                              (encuesta_general_id, matricula_detalle_id)
                              VALUES
                              ('$id', '$matricula_detalle_id')";
                ejecutarConsulta($sqlAlumno);
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function listar()
    {
        $sql = "SELECT 
                    eg.id,
                    eg.nombre,
                    DATE_FORMAT(eg.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                    DATE_FORMAT(eg.fecha_fin, '%d/%m/%Y') AS fecha_fin,
                    eg.fecha_fin AS fecha_fin_original,
                    DATEDIFF(eg.fecha_fin, CURDATE()) AS dias_restantes,
                    eg.calificacion_menor,
                    eg.calificacion_mayor,
                    eg.estado,
                    eg.fechacreado
                FROM registro_encuesta_general eg
                ORDER BY eg.id DESC";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT * FROM registro_encuesta_general WHERE id = '$id'";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDocentes()
    {
        $sql = "SELECT 
                    id,
                    nombreyapellido
                FROM usuario_docente
                WHERE estado = '1'
                ORDER BY nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }

    public function listarAlumnos()
    {
        $sql = "SELECT 
                    md.id AS matricula_detalle_id,
                    alu.nombreyapellido AS alumno,
                    niv.nombre AS nivel,
                    gra.nombre AS grado,
                    sec.nombre AS seccion
                FROM matricula_detalle md
                INNER JOIN usuario_alumno alu ON md.id_usuario_alumno = alu.id
                INNER JOIN matricula m ON md.id_matricula = m.id
                INNER JOIN institucion_seccion sec ON m.id_institucion_seccion = sec.id
                INNER JOIN institucion_grado gra ON sec.id_institucion_grado = gra.id
                INNER JOIN institucion_nivel niv ON gra.id_institucion_nivel = niv.id
                WHERE md.estado = '1'
                  AND alu.estado = '1'
                  AND m.estado = '1'
                ORDER BY niv.id ASC, gra.id ASC, sec.id ASC, alu.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }

    public function listarDocentesSeleccionados($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT usuario_docente_id 
                FROM registro_encuesta_docentes
                WHERE encuesta_general_id = '$id'";

        return ejecutarConsulta($sql);
    }

    public function listarAlumnosSeleccionados($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT matricula_detalle_id 
                FROM registro_encuesta_alumno
                WHERE encuesta_general_id = '$id'";

        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $id = limpiarcadena($id);

        ejecutarConsulta("UPDATE registro_encuesta_general SET estado = '1' WHERE id = '$id'");
        ejecutarConsulta("UPDATE registro_encuesta_docentes SET estado = '1' WHERE encuesta_general_id = '$id'");
        ejecutarConsulta("UPDATE registro_encuesta_alumno SET estado = '1' WHERE encuesta_general_id = '$id'");

        return true;
    }

    public function desactivar($id)
    {
        $id = limpiarcadena($id);

        ejecutarConsulta("UPDATE registro_encuesta_general SET estado = '0' WHERE id = '$id'");
        ejecutarConsulta("UPDATE registro_encuesta_docentes SET estado = '0' WHERE encuesta_general_id = '$id'");
        ejecutarConsulta("UPDATE registro_encuesta_alumno SET estado = '0' WHERE encuesta_general_id = '$id'");

        return true;
    }
}