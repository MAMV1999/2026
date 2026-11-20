<?php
require_once("../../database.php");

class ReporteInstitucionLectivo
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT
                    il.id,
                    i.nombre AS institucion,
                    il.nombre AS lectivo,
                    il.nombre_lectivo,
                    il.observaciones,
                    DATE_FORMAT(il.fechacreado, '%d/%m/%Y') AS fecha,
                    il.estado
                FROM institucion_lectivo il
                INNER JOIN institucion i ON il.id_institucion = i.id
                ORDER BY i.nombre ASC, il.nombre DESC";
        return ejecutarConsulta($sql);
    }
}
?>
