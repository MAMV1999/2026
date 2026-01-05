<?php
require_once("../../database.php");

class ReporteMatricula
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    IFNULL(isec.nombre, 'SIN SECCION') AS seccion,
                    IFNULL(us.nombreyapellido, 'SIN ALUMNOS') AS alumno,
                    IFNULL(uxs.nombre, 'SIN SEXO') AS sexo,
                    IFNULL(mc.nombre, 'SIN CATEGORIA') AS categoria
                FROM institucion_grado ig
                JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id AND iniv.estado = '1'
                JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = '1'
                LEFT JOIN institucion_seccion isec ON ig.id = isec.id_institucion_grado AND isec.estado = '1'
                LEFT JOIN matricula m ON isec.id = m.id_institucion_seccion AND m.estado = '1'
                LEFT JOIN matricula_detalle md ON m.id = md.id_matricula AND md.estado = '1'
                LEFT JOIN usuario_alumno us ON md.id_usuario_alumno = us.id AND us.estado = '1'
                LEFT JOIN usuario_sexo uxs ON us.id_sexo = uxs.id AND uxs.estado = '1'
                LEFT JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = '1'
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, alumno ASC";
        return ejecutarConsulta($sql);
    }
}
