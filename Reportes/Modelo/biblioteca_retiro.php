<?php
require_once("../../database.php");

class ReporteBibliotecaRetiro
{
    public function __construct()
    {
    }

    public function listar()
    {
        $sql = "SELECT
                    br.id AS retiro_id,
                    br.codigo_retiro,
                    DATE_FORMAT(br.fecha, '%d/%m/%Y') AS fecha,
                    ud.numerodocumento,
                    ud.nombreyapellido AS docente,
                    bl.codigo,
                    bl.nombre AS libro,
                    brd.cantidad,
                    brd.observaciones AS observaciones_detalle,
                    br.observaciones AS observaciones_retiro
                FROM biblioteca_retiro br
                INNER JOIN biblioteca_retiro_detalle brd ON br.id = brd.biblioteca_retiro_id
                INNER JOIN usuario_docente ud ON br.usuario_docente_id = ud.id
                INNER JOIN biblioteca_libro bl ON brd.biblioteca_libro_id = bl.id
                WHERE br.estado=1
                AND brd.estado=1
                ORDER BY ud.nombreyapellido ASC, br.fecha DESC, br.id DESC, bl.nombre ASC, bl.codigo ASC";
        return ejecutarConsulta($sql);
    }
}
?>