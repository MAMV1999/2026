<?php
require_once("../../database.php");

class MatriculaDocumentos
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo documento de matrícula
    public function guardar(
        $id_matricula_documentos_responsable, $nombre, $obligatorio, $observaciones, $estado = 1
    ) {
        $sql = "INSERT INTO matricula_documentos (id_matricula_documentos_responsable, nombre, obligatorio, observaciones, estado) VALUES ('$id_matricula_documentos_responsable', '$nombre', '$obligatorio', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un documento de matrícula existente
    public function editar(
        $id, $id_matricula_documentos_responsable, $nombre, $obligatorio, 
        $observaciones, $estado
    ) {
        $sql = "UPDATE matricula_documentos SET id_matricula_documentos_responsable='$id_matricula_documentos_responsable', nombre='$nombre', obligatorio='$obligatorio', observaciones='$observaciones', estado='$estado' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un documento específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM matricula_documentos WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los documentos de matrícula
    public function listar()
    {
        $sql = "SELECT 
                md.id,
                mdr.nombre AS responsable,
                md.nombre,
                md.obligatorio,
                md.estado
            FROM matricula_documentos md
            INNER JOIN matricula_documentos_responsable mdr ON md.id_matricula_documentos_responsable = mdr.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un documento
    public function desactivar($id)
    {
        $sql = "UPDATE matricula_documentos SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un documento
    public function activar($id)
    {
        $sql = "UPDATE matricula_documentos SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los responsables activos para los campos de selección en el formulario
    public function listarResponsablesActivos()
    {
        $sql = "SELECT id, nombre FROM matricula_documentos_responsable WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
