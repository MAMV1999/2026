<?php
require_once("../../database.php");

class AlmacenCategoria
{
    public function __construct()
    {
    }

    public function guardar($nombre, $observaciones, $estado)
    {
        $sql = "INSERT INTO almacen_categoria (nombre, observaciones, estado) VALUES ('$nombre', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    public function editar($id, $nombre, $observaciones, $estado)
    {
        $sql = "UPDATE almacen_categoria SET nombre='$nombre', observaciones='$observaciones', estado='$estado' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM almacen_categoria WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM almacen_categoria";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE almacen_categoria SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE almacen_categoria SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }
}
?>