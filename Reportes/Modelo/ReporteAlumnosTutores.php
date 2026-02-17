<?php
require_once("../../database.php");

class Reportealumnostutores
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                i.nombre AS institucion,
                il.nombre AS lectivo,
                iniv.nombre AS nivel,
                ig.nombre AS grado,
                isec.nombre AS seccion,
                ua.nombreyapellido AS alumno, DATE_FORMAT(ua.nacimiento, '%d/%m/%Y') AS fecha_nacimiento,
                us.nombre AS sexo_alumno,
                uap.nombreyapellido AS apoderado,
                uap.telefono AS telefono_apoderado,
                uat.nombre AS tipo_apoderado,
                ud.nombreyapellido AS docente,
                ud.telefono AS telefono_docente,
                mc.nombre AS categoria_matricula
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id AND ig.estado = 1
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN institucion i ON il.id_institucion = i.id AND i.estado = 1
                INNER JOIN usuario_docente ud ON m.id_usuario_docente = ud.id AND ud.estado = 1
                INNER JOIN usuario_alumno ua ON md.id_usuario_alumno = ua.id AND ua.estado = 1
                INNER JOIN usuario_sexo us ON ua.id_sexo = us.id AND us.estado = 1
                INNER JOIN usuario_apoderado uap ON md.id_usuario_apoderado = uap.id AND uap.estado = 1
                INNER JOIN usuario_apoderado_tipo uat ON uap.id_apoderado_tipo = uat.id AND uat.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                WHERE md.estado = 1
                ORDER BY 
                i.nombre ASC,
                il.nombre ASC,
                iniv.nombre ASC,
                ig.nombre ASC,
                isec.nombre ASC,
                ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

}
