<?php
require_once("../../database.php");

class ReporteBibliotecaRecibo
{
    public function mostrar($id)
    {
        $id = limpiarcadena($id);
        $sql = "SELECT
                    br.id,
                    br.codigo_retiro,
                    DATE_FORMAT(br.fecha, '%d/%m/%Y') AS fecha,
                    DATE_FORMAT(br.fecha_devolucion, '%d/%m/%Y %H:%i') AS fecha_devolucion,
                    br.observaciones,
                    br.estado,
                    br.devuelto,
                    ud.telefono,
                    ud.nombreyapellido AS docente
                FROM biblioteca_retiro br
                INNER JOIN usuario_docente ud ON br.usuario_docente_id = ud.id
                WHERE br.id='$id'
                LIMIT 1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_detalle($id)
    {
        $id = limpiarcadena($id);
        $sql = "SELECT
                    bl.codigo,
                    bl.nombre AS libro,
                    brd.cantidad,
                    brd.observaciones
                FROM biblioteca_retiro_detalle brd
                INNER JOIN biblioteca_libro bl ON brd.biblioteca_libro_id = bl.id
                WHERE brd.biblioteca_retiro_id='$id'
                AND brd.estado=1
                ORDER BY bl.nombre ASC, bl.codigo ASC";
        return ejecutarConsulta($sql);
    }
}
?>