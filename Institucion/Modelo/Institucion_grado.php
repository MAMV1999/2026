<?php
require_once("../../database.php");

class Institucion_grado
{
    public function __construct() {}

    // Método para guardar un nuevo grado de institución
    public function guardar($nombre, $id_institucion_nivel, $observaciones)
    {
        $sql = "INSERT INTO institucion_grado (nombre, id_institucion_nivel, observaciones) VALUES ('$nombre', '$id_institucion_nivel', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un grado de institución existente
    public function editar($id, $nombre, $id_institucion_nivel, $observaciones)
    {
        $sql = "UPDATE institucion_grado SET nombre='$nombre', id_institucion_nivel='$id_institucion_nivel', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un grado de institución específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion_grado WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los grados de institución
    public function listar()
    {
        $sql = "SELECT 
                ig.id,
                ig.nombre AS nombre_grado,
                il.nombre AS nombre_lectivo,
                inl.nombre AS nombre_nivel,
                ig.id_institucion_nivel,
                ig.observaciones,
                ig.fechacreado,
                ig.estado
            FROM institucion_grado ig
            LEFT JOIN institucion_nivel inl ON ig.id_institucion_nivel = inl.id
            LEFT JOIN institucion_lectivo il ON inl.id_institucion_lectivo = il.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un grado de institución
    public function desactivar($id)
    {
        $sql = "UPDATE institucion_grado SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un grado de institución
    public function activar($id)
    {
        $sql = "UPDATE institucion_grado SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar niveles activos con la estructura solicitada
    public function listarNivelesActivos()
    {
        $sql = "SELECT inl.id, CONCAT(il.nombre, ' - ', inl.nombre) AS nombre 
                FROM institucion_nivel inl 
                LEFT JOIN institucion_lectivo il ON inl.id_institucion_lectivo = il.id 
                WHERE inl.estado = '1'";
        return ejecutarConsulta($sql);
    }
}
