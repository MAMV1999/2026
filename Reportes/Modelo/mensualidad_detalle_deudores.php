<?php
require_once("../../database.php");

class Mensualidad_detalle_deudores
{
    public function __construct() {}

    public function listar_mensualidad_detalle_deudores()
    {
        $sql = "SELECT 
                    il.nombre AS lectivo_nombre,
                    iniv.nombre AS nivel_nombre,
                    ig.nombre AS grado_nombre,
                    isec.nombre AS seccion_nombre,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono AS apoderado_telefono,
                    ual.numerodocumento AS alumno_codigo,
                    ual.nombreyapellido AS alumno_nombre,
                    mm.nombre AS mensualidad_mes_nombre,
                    md.monto AS detalle_monto,
                    CASE 
                        WHEN md.pagado = 1 THEN 'PAGADO'
                        WHEN md.pagado = 0 THEN 'PENDIENTE'
                        ELSE 'DESCONOCIDO'
                    END AS detalle_estado_pago,
                    md.observaciones AS detalle_observaciones,
                    CASE 
                        WHEN md.estado = 1 THEN 'ACTIVO'
                        WHEN md.estado = 0 THEN 'INACTIVO'
                        ELSE 'DESCONOCIDO'
                    END AS detalle_estado
                FROM mensualidad_detalle md
                LEFT JOIN matricula_mes mm 
                    ON md.matricula_mes_id = mm.id
                LEFT JOIN matricula_detalle mde 
                    ON md.id_matricula_detalle = mde.id
                LEFT JOIN usuario_apoderado ua 
                    ON mde.id_usuario_apoderado = ua.id
                LEFT JOIN usuario_alumno ual 
                    ON mde.id_usuario_alumno = ual.id
                LEFT JOIN matricula m 
                    ON mde.id_matricula = m.id
                LEFT JOIN institucion_seccion isec 
                    ON m.id_institucion_seccion = isec.id
                LEFT JOIN institucion_grado ig 
                    ON isec.id_institucion_grado = ig.id
                LEFT JOIN institucion_nivel iniv 
                    ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_lectivo il 
                    ON iniv.id_institucion_lectivo = il.id
                WHERE 
                    md.estado = 1
                    AND mm.estado = 1
                    AND mde.estado = 1
                    AND ua.estado = 1
                    AND ual.estado = 1
                    AND m.estado = 1
                    AND isec.estado = 1
                    AND ig.estado = 1
                    AND iniv.estado = 1
                    AND il.estado = 1
                    AND md.pagado = 0
                ORDER BY 
                    md.matricula_mes_id ASC,
                    il.nombre ASC,
                    iniv.nombre ASC,
                    ig.nombre ASC,
                    isec.nombre ASC,
                    ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
