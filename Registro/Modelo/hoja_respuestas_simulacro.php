<?php
require_once("../../database.php");

class Simulacro
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    ise.id AS id_seccion,

                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    ise.nombre AS seccion,

                    i.nombre AS institucion

                FROM institucion_seccion ise
                INNER JOIN institucion_grado ig ON ise.id_institucion_grado = ig.id
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                INNER JOIN institucion i ON il.id_institucion = i.id

                WHERE ise.estado = 1
                AND ig.estado = 1
                AND iniv.estado = 1
                AND il.estado = 1
                AND i.estado = 1

                ORDER BY 
                    il.nombre ASC,
                    iniv.id ASC,
                    ig.id ASC,
                    ise.id ASC";

        return ejecutarConsulta($sql);
    }
}
?>