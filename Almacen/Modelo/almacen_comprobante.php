<?php
require_once("../../database.php");

class AlmacenComprobante
{
    public function __construct()
    {
    }

    public function guardar($nombre, $observaciones, $estado)
    {
        $sql = "INSERT INTO almacen_comprobante (nombre, observaciones, estado) VALUES ('$nombre', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    public function editar($id, $nombre, $observaciones, $estado)
    {
        $sql = "UPDATE almacen_comprobante SET nombre='$nombre', observaciones='$observaciones', estado='$estado' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM almacen_comprobante WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM almacen_comprobante";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE almacen_comprobante SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE almacen_comprobante SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }
}
?>