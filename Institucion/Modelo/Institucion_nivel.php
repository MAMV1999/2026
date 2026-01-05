<?php
require_once("../../database.php");

class Institucion_nivel
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo nivel de institución
    public function guardar($nombre, $id_institucion_lectivo, $observaciones)
    {
        $sql = "INSERT INTO institucion_nivel (nombre, id_institucion_lectivo, observaciones) VALUES ('$nombre', '$id_institucion_lectivo', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un nivel de institución existente
    public function editar($id, $nombre, $id_institucion_lectivo, $observaciones)
    {
        $sql = "UPDATE institucion_nivel SET nombre='$nombre', id_institucion_lectivo='$id_institucion_lectivo', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un nivel de institución específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion_nivel WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los niveles de institución
    public function listar()
    {
        $sql = "SELECT inl.id, inl.nombre, il.nombre AS institucion_lectivo, inl.observaciones, inl.estado, inl.fechacreado 
                FROM institucion_nivel inl 
                LEFT JOIN institucion_lectivo il ON inl.id_institucion_lectivo = il.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un nivel de institución
    public function desactivar($id)
    {
        $sql = "UPDATE institucion_nivel SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un nivel de institución
    public function activar($id)
    {
        $sql = "UPDATE institucion_nivel SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar las instituciones lectivas activas para el select
    public function listarInstitucionesLectivasActivas()
    {
        $sql = "SELECT id, nombre FROM institucion_lectivo WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }
}
?>
