<?php
require_once("../../database.php");

class Reportesimulacro
{
    public function __construct() {}

    public function listar($id_seccion)
    {
        $sql = "SELECT 
                    i.nombre AS nombre_institucion,
                    i.telefono AS telefono_institucion,
                    i.correo AS correo_institucion,
                    i.ruc AS ruc_institucion,
                    i.razon_social AS razon_social_institucion,
                    i.direccion AS direccion_institucion,

                    il.nombre AS nombre_lectivo,
                    iniv.nombre AS nombre_nivel,
                    ig.nombre AS nombre_grado,
                    ise.nombre AS nombre_seccion,

                    ua.nombreyapellido AS nombre_apoderado,
                    ua.telefono AS telefono_apoderado,

                    ual.nombreyapellido AS nombre_alumno,

                    udoc.nombreyapellido AS nombre_tutor

                FROM matricula_detalle mtd
                INNER JOIN matricula m ON mtd.id_matricula = m.id
                INNER JOIN institucion_seccion ise ON m.id_institucion_seccion = ise.id
                INNER JOIN institucion_grado ig ON ise.id_institucion_grado = ig.id
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                INNER JOIN institucion i ON il.id_institucion = i.id

                INNER JOIN usuario_alumno ual ON mtd.id_usuario_alumno = ual.id
                INNER JOIN usuario_apoderado ua ON mtd.id_usuario_apoderado = ua.id

                LEFT JOIN usuario_docente udoc ON m.id_usuario_docente = udoc.id AND udoc.estado = 1

                WHERE ise.id = '$id_seccion'

                AND mtd.estado = 1
                AND m.estado = 1
                AND ise.estado = 1
                AND ig.estado = 1
                AND iniv.estado = 1
                AND il.estado = 1
                AND i.estado = 1
                AND ual.estado = 1
                AND ua.estado = 1

                ORDER BY 
                    ual.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}
?>