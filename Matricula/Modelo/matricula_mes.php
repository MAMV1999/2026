<?php
require_once("../../database.php");

class MatriculaMes
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo mes
    public function guardar($institucion_lectivo_id, $nombre, $fecha_vencimiento, $mora, $observaciones, $estado = 1)
    {
        $sql = "INSERT INTO matricula_mes 
                (institucion_lectivo_id, nombre, fecha_vencimiento, mora, observaciones, estado)
                VALUES 
                ('$institucion_lectivo_id', '$nombre', '$fecha_vencimiento', '$mora', '$observaciones', '$estado')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un mes existente
    public function editar($id, $institucion_lectivo_id, $nombre, $fecha_vencimiento, $mora, $observaciones, $estado)
    {
        $sql = "UPDATE matricula_mes 
                SET institucion_lectivo_id='$institucion_lectivo_id',
                    nombre='$nombre',
                    fecha_vencimiento='$fecha_vencimiento',
                    mora='$mora',
                    observaciones='$observaciones',
                    estado='$estado'
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar un registro específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM matricula_mes WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los meses
    public function listar()
    {
        $sql = "SELECT 
                    mm.id,
                    il.nombre AS institucion_lectivo,
                    mm.nombre,
                    DATE_FORMAT(mm.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
                    mm.mora,
                    mm.estado
                FROM matricula_mes mm
                INNER JOIN institucion_lectivo il 
                ON mm.institucion_lectivo_id = il.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar
    public function desactivar($id)
    {
        $sql = "UPDATE matricula_mes SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar
    public function activar($id)
    {
        $sql = "UPDATE matricula_mes SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar instituciones lectivas activas
    public function listarInstitucionesLectivasActivas()
    {
        $sql = "SELECT id, nombre FROM institucion_lectivo WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
