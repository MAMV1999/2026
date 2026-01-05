<?php
require_once("../../database.php");

class Institucion_seccion
{
    public function __construct() {}

    // Método para guardar una nueva sección de institución
    public function guardar($nombre, $id_institucion_grado, $observaciones)
    {
        $sql = "INSERT INTO institucion_seccion (nombre, id_institucion_grado, observaciones) VALUES ('$nombre', '$id_institucion_grado', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar una sección de institución existente
    public function editar($id, $nombre, $id_institucion_grado, $observaciones)
    {
        $sql = "UPDATE institucion_seccion SET nombre='$nombre', id_institucion_grado='$id_institucion_grado', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de una sección específica
    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion_seccion WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todas las secciones
    public function listar()
    {
        $sql = "SELECT 
                isec.*,
                il.nombre AS nombre_lectivo,
                inl.nombre AS nombre_nivel,
                ig.nombre AS nombre_grado
            FROM institucion_seccion isec
            LEFT JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
            LEFT JOIN institucion_nivel inl ON ig.id_institucion_nivel = inl.id
            LEFT JOIN institucion_lectivo il ON inl.id_institucion_lectivo = il.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar una sección de institución
    public function desactivar($id)
    {
        $sql = "UPDATE institucion_seccion SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar una sección de institución
    public function activar($id)
    {
        $sql = "UPDATE institucion_seccion SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar grados activos con la estructura solicitada
    public function listarGradosActivos()
    {
        $sql = "SELECT ig.id, CONCAT(il.nombre, ' - ', inl.nombre, ' - ', ig.nombre) AS nombre 
                FROM institucion_grado ig 
                LEFT JOIN institucion_nivel inl ON ig.id_institucion_nivel = inl.id 
                LEFT JOIN institucion_lectivo il ON inl.id_institucion_lectivo = il.id 
                WHERE ig.estado = '1'";
        return ejecutarConsulta($sql);
    }
}
