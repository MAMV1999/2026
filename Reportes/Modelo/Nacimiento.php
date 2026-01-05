<?php
require_once("../../database.php");

class Nacimiento
{
    public function __construct() {}

    public function listarNacimiento()
    {
        $sql = "SELECT 
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    ua.nombreyapellido AS nombre_alumno,
                    ua.nacimiento,
                    TIMESTAMPDIFF(YEAR, ua.nacimiento, CURDATE()) AS edad,
                    CASE 
                        WHEN STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(ua.nacimiento, '%m-%d')), '%Y-%m-%d') < CURDATE() THEN 
                            'YA PASÓ'
                        ELSE 
                            CONCAT(
                                DATEDIFF(
                                    STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(ua.nacimiento, '%m-%d')), '%Y-%m-%d'),
                                    CURDATE()
                                ),
                                ' DÍAS'
                            )
                    END AS dias_para_cumple
                FROM 
                    matricula_detalle md
                JOIN 
                    matricula m ON md.id_matricula = m.id
                JOIN 
                    institucion_seccion isec ON m.id_institucion_seccion = isec.id
                JOIN 
                    institucion_grado ig ON isec.id_institucion_grado = ig.id
                JOIN 
                    institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                JOIN 
                    institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                JOIN 
                    usuario_alumno ua ON md.id_usuario_alumno = ua.id
                WHERE 
                    md.estado = 1 
                    AND m.estado = 1 
                    AND isec.estado = 1 
                    AND ig.estado = 1 
                    AND iniv.estado = 1 
                    AND il.estado = 1 
                    AND ua.estado = 1
                ORDER BY 
                    MONTH(ua.nacimiento) ASC, 
                    DAY(ua.nacimiento) ASC";
        return ejecutarConsulta($sql);
    }
}
