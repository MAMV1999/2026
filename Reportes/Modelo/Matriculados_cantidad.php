<?php
require_once("../../database.php");

class InstitucionLectivo
{
    public function __construct()
    {
    }

    public function listarLectivoNivelGradoSeccion()
    {
        $sql = "SELECT 
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    igr.nombre AS grado,
                    ise.nombre AS seccion,
                    COUNT(md.id_usuario_alumno) AS cantidad_alumnos
                FROM 
                    institucion_lectivo il
                JOIN 
                    institucion_nivel iniv ON iniv.id_institucion_lectivo = il.id
                JOIN 
                    institucion_grado igr ON igr.id_institucion_nivel = iniv.id
                JOIN 
                    institucion_seccion ise ON ise.id_institucion_grado = igr.id
                LEFT JOIN 
                    matricula m ON m.id_institucion_seccion = ise.id
                LEFT JOIN 
                    matricula_detalle md ON md.id_matricula = m.id AND md.estado = 1
                GROUP BY 
                    il.id, iniv.id, igr.id, ise.id
                ORDER BY 
                    il.nombre, iniv.nombre, igr.nombre, ise.nombre";
        return ejecutarConsulta($sql);
    }
}
?>
