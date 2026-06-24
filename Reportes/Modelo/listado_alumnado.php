<?php
require_once("../../database.php");

class Listado_general_alumnos
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    ual.nombreyapellido AS alumno_nombre,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono AS apoderado_telefono
                FROM matricula_detalle md
                INNER JOIN matricula m ON m.id = md.id_matricula
                INNER JOIN institucion_seccion isec ON isec.id = m.id_institucion_seccion
                INNER JOIN institucion_grado ig ON ig.id = isec.id_institucion_grado
                INNER JOIN institucion_nivel iniv ON iniv.id = ig.id_institucion_nivel
                INNER JOIN institucion_lectivo il ON il.id = iniv.id_institucion_lectivo
                INNER JOIN institucion i ON i.id = il.id_institucion
                INNER JOIN usuario_alumno ual ON ual.id = md.id_usuario_alumno
                INNER JOIN usuario_apoderado ua ON ua.id = md.id_usuario_apoderado
                WHERE md.estado = 1
                AND m.estado = 1
                AND isec.estado = 1
                AND ig.estado = 1
                AND iniv.estado = 1
                AND il.estado = 1
                AND i.estado = 1
                AND ual.estado = 1
                AND ua.estado = 1
                ORDER BY 
                    iniv.id ASC,
                    ig.id ASC,
                    isec.id ASC,
                    ual.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}