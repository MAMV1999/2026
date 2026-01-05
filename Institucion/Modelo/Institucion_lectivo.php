<?php
require_once("../../database.php");

class Institucion_lectivo
{
    public function __construct()
    {
    }

    // Método para guardar una nueva institución lectiva
    public function guardar($nombre, $nombre_lectivo, $id_institucion, $observaciones)
    {
        $sql = "INSERT INTO institucion_lectivo (nombre, nombre_lectivo, id_institucion, observaciones) VALUES ('$nombre', '$nombre_lectivo', '$id_institucion', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar una institución lectiva existente
    public function editar($id, $nombre, $nombre_lectivo, $id_institucion, $observaciones)
    {
        $sql = "UPDATE institucion_lectivo SET nombre='$nombre', nombre_lectivo='$nombre_lectivo', id_institucion='$id_institucion', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de una institución lectiva específica
    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion_lectivo WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todas las instituciones lectivas
    public function listar()
    {
        $sql = "SELECT il.id, il.nombre, il.nombre_lectivo, i.nombre AS institucion, il.observaciones, il.estado, il.fechacreado 
                FROM institucion_lectivo il 
                LEFT JOIN institucion i ON il.id_institucion = i.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar una institución lectiva
    public function desactivar($id)
    {
        $sql = "UPDATE institucion_lectivo SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar una institución lectiva
    public function activar($id)
    {
        $sql = "UPDATE institucion_lectivo SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar las instituciones activas
    public function listarInstitucionesActivas()
    {
        $sql = "SELECT id, nombre FROM institucion WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }
}
?>
