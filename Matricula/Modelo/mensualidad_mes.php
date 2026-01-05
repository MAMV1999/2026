<?php
require_once("../../database.php");

class MensualidadMes
{
    public function __construct()
    {
    }

    public function guardar($nombre, $descripcion, $observaciones, $estado)
    {
        $sql = "INSERT INTO mensualidad_mes (nombre, descripcion, observaciones, estado) 
                VALUES ('$nombre', '$descripcion', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    public function editar($id, $nombre, $descripcion, $observaciones, $estado)
    {
        $sql = "UPDATE mensualidad_mes SET nombre='$nombre', descripcion='$descripcion', observaciones='$observaciones', estado='$estado' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM mensualidad_mes WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM mensualidad_mes";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE mensualidad_mes SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE mensualidad_mes SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }
}
?>
