<?php
require_once("../../database.php");

class DocumentoResponsable
{
    public function __construct()
    {
    }

    public function guardar($nombre, $observaciones, $estado)
    {
        $sql = "INSERT INTO documento_responsable (nombre, observaciones, estado) 
                VALUES ('$nombre', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    public function editar($id, $nombre, $observaciones, $estado)
    {
        $sql = "UPDATE documento_responsable SET nombre='$nombre', observaciones='$observaciones', estado='$estado' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM documento_responsable WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM documento_responsable";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE documento_responsable SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE documento_responsable SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }
}
?>
