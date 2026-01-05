<?php
require_once("../../database.php");

class MatriculaMetodoPago
{
    public function __construct()
    {
    }

    public function guardar($nombre, $observaciones, $estado)
    {
        $sql = "INSERT INTO matricula_metodo_pago (nombre, observaciones, estado) VALUES ('$nombre', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    public function editar($id, $nombre, $observaciones, $estado)
    {
        $sql = "UPDATE matricula_metodo_pago SET nombre='$nombre', observaciones='$observaciones', estado='$estado' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM matricula_metodo_pago WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM matricula_metodo_pago";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE matricula_metodo_pago SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE matricula_metodo_pago SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }
}
?>
