<?php
require_once("../../database.php");

class Documento
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo documento
    public function guardar($id_documento_responsable, $nombre, $obligatorio, $observaciones, $estado = 1) {
        $sql = "INSERT INTO documento (id_documento_responsable, nombre, obligatorio, observaciones, estado) VALUES ('$id_documento_responsable', '$nombre', '$obligatorio', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un documento existente
    public function editar($id, $id_documento_responsable, $nombre, $obligatorio, $observaciones, $estado)
    {
        $sql = "UPDATE documento 
                SET id_documento_responsable='$id_documento_responsable', 
                    nombre='$nombre', 
                    obligatorio='$obligatorio', 
                    observaciones='$observaciones', 
                    estado='$estado' 
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un documento específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM documento WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los documentos
    public function listar()
    {
        $sql = "SELECT 
                d.id,
                dr.nombre AS responsable,
                d.nombre,
                d.obligatorio,
                d.estado
            FROM documento d
            INNER JOIN documento_responsable dr 
            ON d.id_documento_responsable = dr.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un documento
    public function desactivar($id)
    {
        $sql = "UPDATE documento SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un documento
    public function activar($id)
    {
        $sql = "UPDATE documento SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los responsables activos para los campos de selección en el formulario
    public function listarResponsablesActivos()
    {
        $sql = "SELECT id, nombre FROM documento_responsable WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
