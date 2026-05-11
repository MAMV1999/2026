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

            $docentes = array_unique($docentes);
            $alumnos = array_unique($alumnos);

            $sqlDuplicado = "SELECT id 
                             FROM registro_encuesta_general
                             WHERE nombre = '$nombre'
                               AND fecha_inicio = '$fecha_inicio'
                               AND fecha_fin = '$fecha_fin'
                               AND estado = 1
                             LIMIT 1";

            $duplicado = ejecutarConsultaSimpleFila($sqlDuplicado);

            if ($duplicado) {
                return "Ya existe una encuesta activa con el mismo nombre y fechas. Use EDITAR, no AGREGAR.";
            }

            $sql = "INSERT INTO registro_encuesta_general 
                    (
                        nombre, 
                        fecha_inicio, 
                        fecha_fin, 
                        calificacion_menor, 
                        calificacion_mayor, 
                        observaciones
                    )
                    VALUES 
                    (
                        '$nombre', 
                        '$fecha_inicio', 
                        '$fecha_fin', 
                        '$calificacion_menor', 
                        '$calificacion_mayor', 
                        '$observaciones'
                    )";

            $encuesta_id = ejecutarConsulta_retornarID($sql);

            $this->actualizarDocentesEncuesta($encuesta_id, $docentes);
            $this->actualizarAlumnosEncuesta($encuesta_id, $alumnos);

            return "Encuesta registrada correctamente";

        } catch (Exception $e) {
            return "Error al guardar la encuesta";
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

            $docentes = array_unique($docentes);
            $alumnos = array_unique($alumnos);

            $sql = "UPDATE registro_encuesta_general SET
                        nombre = '$nombre',
                        fecha_inicio = '$fecha_inicio',
                        fecha_fin = '$fecha_fin',
                        calificacion_menor = '$calificacion_menor',
                        calificacion_mayor = '$calificacion_mayor',
                        observaciones = '$observaciones'
                    WHERE id = '$id'";

            ejecutarConsulta($sql);

            /*
                IMPORTANTE:
                Ya no se usa DELETE porque registro_encuesta_registro
                puede tener respuestas que dependen de estas filas.

                Ahora se desactiva/reactiva para no romper las respuestas.
            */
            $this->actualizarDocentesEncuesta($id, $docentes);
            $this->actualizarAlumnosEncuesta($id, $alumnos);

            return "Encuesta actualizada correctamente";

        } catch (Exception $e) {
            return "Error al actualizar la encuesta";
        }
    }

    private function actualizarDocentesEncuesta($encuesta_id, $docentes)
    {
        $encuesta_id = limpiarcadena($encuesta_id);

        ejecutarConsulta("UPDATE registro_encuesta_docentes 
                          SET estado = 0 
                          WHERE encuesta_general_id = '$encuesta_id'");

        foreach ($docentes as $docente_id) {
            $docente_id = limpiarcadena($docente_id);

            $sqlExiste = "SELECT id 
                          FROM registro_encuesta_docentes
                          WHERE encuesta_general_id = '$encuesta_id'
                            AND usuario_docente_id = '$docente_id'
                          ORDER BY id ASC
                          LIMIT 1";

            $existe = ejecutarConsultaSimpleFila($sqlExiste);

            if ($existe) {
                $id_relacion = $existe["id"];

                ejecutarConsulta("UPDATE registro_encuesta_docentes
                                  SET estado = 1
                                  WHERE id = '$id_relacion'");

                ejecutarConsulta("UPDATE registro_encuesta_docentes
                                  SET estado = 0
                                  WHERE encuesta_general_id = '$encuesta_id'
                                    AND usuario_docente_id = '$docente_id'
                                    AND id <> '$id_relacion'");
            } else {
                $sqlInsert = "INSERT INTO registro_encuesta_docentes
                              (
                                  encuesta_general_id, 
                                  usuario_docente_id,
                                  estado
                              )
                              VALUES
                              (
                                  '$encuesta_id', 
                                  '$docente_id',
                                  1
                              )";

                ejecutarConsulta($sqlInsert);
            }
        }
    }

    private function actualizarAlumnosEncuesta($encuesta_id, $alumnos)
    {
        $encuesta_id = limpiarcadena($encuesta_id);

        ejecutarConsulta("UPDATE registro_encuesta_alumno 
                          SET estado = 0 
                          WHERE encuesta_general_id = '$encuesta_id'");

        foreach ($alumnos as $matricula_detalle_id) {
            $matricula_detalle_id = limpiarcadena($matricula_detalle_id);

            $sqlExiste = "SELECT id 
                          FROM registro_encuesta_alumno
                          WHERE encuesta_general_id = '$encuesta_id'
                            AND matricula_detalle_id = '$matricula_detalle_id'
                          ORDER BY id ASC
                          LIMIT 1";

            $existe = ejecutarConsultaSimpleFila($sqlExiste);

            if ($existe) {
                $id_relacion = $existe["id"];

                ejecutarConsulta("UPDATE registro_encuesta_alumno
                                  SET estado = 1
                                  WHERE id = '$id_relacion'");

                ejecutarConsulta("UPDATE registro_encuesta_alumno
                                  SET estado = 0
                                  WHERE encuesta_general_id = '$encuesta_id'
                                    AND matricula_detalle_id = '$matricula_detalle_id'
                                    AND id <> '$id_relacion'");
            } else {
                $sqlInsert = "INSERT INTO registro_encuesta_alumno
                              (
                                  encuesta_general_id, 
                                  matricula_detalle_id,
                                  estado
                              )
                              VALUES
                              (
                                  '$encuesta_id', 
                                  '$matricula_detalle_id',
                                  1
                              )";

                ejecutarConsulta($sqlInsert);
            }
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

        $sql = "SELECT * 
                FROM registro_encuesta_general 
                WHERE id = '$id'";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDocentes()
    {
        $sql = "SELECT 
                    id,
                    nombreyapellido
                FROM usuario_docente
                WHERE estado = 1
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
                WHERE md.estado = 1
                  AND alu.estado = 1
                  AND m.estado = 1
                ORDER BY niv.id ASC, gra.id ASC, sec.id ASC, alu.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }

    public function listarDocentesSeleccionados($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT usuario_docente_id 
                FROM registro_encuesta_docentes
                WHERE encuesta_general_id = '$id'
                  AND estado = 1
                GROUP BY usuario_docente_id";

        return ejecutarConsulta($sql);
    }

    public function listarAlumnosSeleccionados($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT matricula_detalle_id 
                FROM registro_encuesta_alumno
                WHERE encuesta_general_id = '$id'
                  AND estado = 1
                GROUP BY matricula_detalle_id";

        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $id = limpiarcadena($id);

        ejecutarConsulta("UPDATE registro_encuesta_general 
                          SET estado = 1 
                          WHERE id = '$id'");

        return true;
    }

    public function desactivar($id)
    {
        $id = limpiarcadena($id);

        ejecutarConsulta("UPDATE registro_encuesta_general 
                          SET estado = 0 
                          WHERE id = '$id'");

        return true;
    }
}