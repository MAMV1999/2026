<?php
require_once("../../database.php");

class MensualidadMes
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo mes de mensualidad
    public function guardar($id_institucion_lectivo, $nombre, $descripcion, $fechavencimiento, $observaciones, $estado = 1)
    {
        $sql = "INSERT INTO mensualidad_mes (id_institucion_lectivo, nombre, descripcion, fechavencimiento, observaciones, estado) 
                VALUES ('$id_institucion_lectivo', '$nombre', '$descripcion', '$fechavencimiento', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un mes de mensualidad existente
    public function editar($id, $id_institucion_lectivo, $nombre, $descripcion, $fechavencimiento, $observaciones, $estado)
    {
        $sql = "UPDATE mensualidad_mes 
                SET id_institucion_lectivo='$id_institucion_lectivo', 
                    nombre='$nombre', 
                    descripcion='$descripcion', 
                    fechavencimiento='$fechavencimiento',
                    observaciones='$observaciones', 
                    estado='$estado' 
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un mes de mensualidad específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM mensualidad_mes WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los meses de mensualidad
    public function listar()
    {
        $sql = "SELECT 
                    mm.id,
                    il.nombre AS institucion_lectivo,
                    mm.nombre,
                    mm.descripcion,
                    DATE_FORMAT(mm.fechavencimiento, '%d/%m/%Y') AS fechavencimiento,
                    mm.estado
                FROM mensualidad_mes mm
                INNER JOIN institucion_lectivo il 
                ON mm.id_institucion_lectivo = il.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un mes de mensualidad
    public function desactivar($id)
    {
        $sql = "UPDATE mensualidad_mes SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un mes de mensualidad
    public function activar($id)
    {
        $sql = "UPDATE mensualidad_mes SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar las instituciones lectivas activas para los campos de selección en el formulario
    public function listarInstitucionesLectivasActivas()
    {
        $sql = "SELECT id, nombre FROM institucion_lectivo WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
