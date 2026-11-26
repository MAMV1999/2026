<?php
require_once("../../database.php");

class ReporteUtiles
{
    public function mostrar($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                    md.id AS id_matricula_detalle,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    igr.nombre AS grado,
                    isec.nombre AS seccion,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono AS apoderado_telefono,
                    ual.nombreyapellido AS alumno_nombre,
                    mc.nombre AS categoria_matricula
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado igr ON isec.id_institucion_grado = igr.id AND igr.estado = 1
                INNER JOIN institucion_nivel iniv ON igr.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                WHERE md.id = '$id'
                AND md.estado = 1
                LIMIT 1";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_detalle($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                    ru.nombre AS util,
                    COALESCE(rud.stock, 0) AS stock,
                    COALESCE(rud.observaciones, '') AS observaciones
                FROM matricula_detalle md
                INNER JOIN registro_utiles ru ON ru.id_matricula = md.id_matricula AND ru.estado = 1
                LEFT JOIN registro_utiles_detalle rud ON rud.id_registro_utiles = ru.id
                    AND rud.id_matricula_detalle = md.id AND rud.estado = 1
                WHERE md.id = '$id' AND md.estado = 1
                ORDER BY ru.id ASC";

        return ejecutarConsulta($sql);
    }
}
?>
