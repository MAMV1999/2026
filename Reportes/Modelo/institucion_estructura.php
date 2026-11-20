<?php
require_once("../../database.php");

class ReporteInstitucionEstructura
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT
                    i.id AS institucion_id,
                    i.nombre AS institucion,
                    il.id AS lectivo_id,
                    il.nombre AS lectivo,
                    iniv.id AS nivel_id,
                    iniv.nombre AS nivel,
                    ig.id AS grado_id,
                    ig.nombre AS grado,
                    isec.id AS seccion_id,
                    isec.nombre AS seccion
                FROM institucion_lectivo il
                INNER JOIN institucion i ON il.id_institucion = i.id
                LEFT JOIN institucion_nivel iniv ON iniv.id_institucion_lectivo = il.id
                LEFT JOIN institucion_grado ig ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_seccion isec ON isec.id_institucion_grado = ig.id
                ORDER BY i.nombre ASC, il.nombre DESC, iniv.id ASC, ig.id ASC, isec.id ASC";
        return ejecutarConsulta($sql);
    }
}
?>
