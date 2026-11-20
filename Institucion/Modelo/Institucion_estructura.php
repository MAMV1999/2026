<?php
require_once("../../database.php");

class Institucion_estructura
{
    public function __construct() {}

    public function listarLectivosActivos()
    {
        $sql = "SELECT 
                    il.id,
                    CONCAT(i.nombre, ' - ', il.nombre) AS nombre
                FROM institucion_lectivo il
                INNER JOIN institucion i ON il.id_institucion = i.id
                WHERE il.estado = '1'
                AND i.estado = '1'
                ORDER BY i.nombre ASC, il.nombre DESC";
        return ejecutarConsulta($sql);
    }

    public function listarEstructura($id_institucion_lectivo)
    {
        $sql = "SELECT
                    iniv.id AS nivel_id,
                    iniv.nombre AS nivel_nombre,
                    iniv.observaciones AS nivel_observaciones,
                    iniv.estado AS nivel_estado,

                    ig.id AS grado_id,
                    ig.nombre AS grado_nombre,
                    ig.observaciones AS grado_observaciones,
                    ig.estado AS grado_estado,

                    isec.id AS seccion_id,
                    isec.nombre AS seccion_nombre,
                    isec.observaciones AS seccion_observaciones,
                    isec.estado AS seccion_estado
                FROM institucion_nivel iniv
                LEFT JOIN institucion_grado ig ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_seccion isec ON isec.id_institucion_grado = ig.id
                WHERE iniv.id_institucion_lectivo = '$id_institucion_lectivo'
                ORDER BY iniv.id ASC, ig.id ASC, isec.id ASC";
        return ejecutarConsulta($sql);
    }

    public function guardarNivel($nombre, $id_institucion_lectivo, $observaciones)
    {
        $sql = "INSERT INTO institucion_nivel (nombre, id_institucion_lectivo, observaciones)
                VALUES ('$nombre', '$id_institucion_lectivo', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    public function editarNivel($id, $nombre, $id_institucion_lectivo, $observaciones)
    {
        $sql = "UPDATE institucion_nivel SET
                    nombre='$nombre',
                    id_institucion_lectivo='$id_institucion_lectivo',
                    observaciones='$observaciones'
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrarNivel($id)
    {
        $sql = "SELECT * FROM institucion_nivel WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function guardarGrado($nombre, $id_institucion_nivel, $observaciones)
    {
        $sql = "INSERT INTO institucion_grado (nombre, id_institucion_nivel, observaciones)
                VALUES ('$nombre', '$id_institucion_nivel', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    public function editarGrado($id, $nombre, $id_institucion_nivel, $observaciones)
    {
        $sql = "UPDATE institucion_grado SET
                    nombre='$nombre',
                    id_institucion_nivel='$id_institucion_nivel',
                    observaciones='$observaciones'
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrarGrado($id)
    {
        $sql = "SELECT * FROM institucion_grado WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function guardarSeccion($nombre, $id_institucion_grado, $observaciones)
    {
        $sql = "INSERT INTO institucion_seccion (nombre, id_institucion_grado, observaciones)
                VALUES ('$nombre', '$id_institucion_grado', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    public function editarSeccion($id, $nombre, $id_institucion_grado, $observaciones)
    {
        $sql = "UPDATE institucion_seccion SET
                    nombre='$nombre',
                    id_institucion_grado='$id_institucion_grado',
                    observaciones='$observaciones'
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrarSeccion($id)
    {
        $sql = "SELECT * FROM institucion_seccion WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function cambiarEstado($tipo, $id, $estado)
    {
        if ($tipo == "nivel") {
            $sql = "UPDATE institucion_nivel SET estado='$estado' WHERE id='$id'";
        } elseif ($tipo == "grado") {
            $sql = "UPDATE institucion_grado SET estado='$estado' WHERE id='$id'";
        } elseif ($tipo == "seccion") {
            $sql = "UPDATE institucion_seccion SET estado='$estado' WHERE id='$id'";
        } else {
            return false;
        }

        return ejecutarConsulta($sql);
    }
}
?>
