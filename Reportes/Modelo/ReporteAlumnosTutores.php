<?php
require_once("../../database.php");

class Reportealumnostutores
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT
                    il.nombre AS nombre_lectivo,
                    iniv.nombre AS nombre_nivel,
                    ig.nombre AS nombre_grado,
                    isec.nombre AS nombre_seccion,

                    udc.nombre AS tipo_documento_docente,
                    udoc.numerodocumento AS numerodocumento_docente,
                    udoc.nombreyapellido AS docente,
                    DATE_FORMAT(udoc.nacimiento, '%d/%m/%Y') AS fecha_nacimiento_docente,
                    IFNULL(TIMESTAMPDIFF(YEAR, udoc.nacimiento, CURDATE()), 0) AS edad_actual_docente,
                    udoc.telefono AS telefono_docente,
                    udoc.correo AS correo_docente,
                    ucargo.nombre AS cargo_docente,

                    mc.nombre AS categoria,

                    ua.nombreyapellido AS alumno,
                    uda.nombre AS tipo_documento_alumno,
                    ua.numerodocumento AS numerodocumento_alumno,
                    DATE_FORMAT(ua.nacimiento, '%d/%m/%Y') AS fecha_nacimiento_alumno,
                    IFNULL(TIMESTAMPDIFF(YEAR, ua.nacimiento, CURDATE()), 0) AS edad_actual_alumno,
                    us.nombre AS sexo_alumno,
                    
                    up.nombreyapellido AS apoderado,
                    uat.nombre AS tipo_apoderado,
                    udp.nombre AS tipo_documento_apoderado,
                    up.numerodocumento AS numerodocumento_apoderado,
                    up.telefono AS telefono_apoderado
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id AND ig.estado = 1
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_docente udoc ON m.id_usuario_docente = udoc.id AND udoc.estado = 1
                INNER JOIN usuario_documento udc ON udoc.id_documento = udc.id AND udc.estado = 1
                INNER JOIN usuario_cargo ucargo ON udoc.id_cargo = ucargo.id AND ucargo.estado = 1
                INNER JOIN usuario_alumno ua ON md.id_usuario_alumno = ua.id AND ua.estado = 1
                INNER JOIN usuario_documento uda ON ua.id_documento = uda.id AND uda.estado = 1
                INNER JOIN usuario_sexo us ON ua.id_sexo = us.id AND us.estado = 1
                INNER JOIN usuario_apoderado up ON md.id_usuario_apoderado = up.id AND up.estado = 1
                INNER JOIN usuario_apoderado_tipo uat ON up.id_apoderado_tipo = uat.id AND uat.estado = 1
                INNER JOIN usuario_documento udp ON up.id_documento = udp.id AND udp.estado = 1
                WHERE md.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

}
