<?php
require_once("../../database.php");

class Mensualidad_detalle_deudores
{
    public function __construct() {}

    public function listar_mensualidad_detalle_deudores()
    {
        date_default_timezone_set('America/Lima');

        /*
            Mes actual en número.

            Ejemplo:
            Hoy: 03/06/2026
            $mes_actual_numero = 6

            Entonces se muestran meses menores a 6:
            Marzo = 3
            Abril = 4
            Mayo = 5

            Junio = 6 NO sale.
            Junio recién saldrá desde el 01/07/2026 a las 00:00.
        */
        $mes_actual_numero = date('n');

        $sql = "SELECT 
                    il.nombre AS lectivo_nombre,
                    iniv.id AS nivel_id,
                    iniv.nombre AS nivel_nombre,
                    ig.id AS grado_id,
                    ig.nombre AS grado_nombre,
                    isec.id AS seccion_id,
                    isec.nombre AS seccion_nombre,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono AS apoderado_telefono,
                    ual.numerodocumento AS alumno_codigo,
                    ual.nombreyapellido AS alumno_nombre,
                    mm.id AS mensualidad_mes_id,
                    mm.nombre AS mensualidad_mes_nombre,
                    mm.fecha_vencimiento AS mensualidad_mes_fecha_vencimiento,

                    CASE 
                        WHEN LOWER(mm.nombre) LIKE '%enero%' THEN 1
                        WHEN LOWER(mm.nombre) LIKE '%febrero%' THEN 2
                        WHEN LOWER(mm.nombre) LIKE '%marzo%' THEN 3
                        WHEN LOWER(mm.nombre) LIKE '%abril%' THEN 4
                        WHEN LOWER(mm.nombre) LIKE '%mayo%' THEN 5
                        WHEN LOWER(mm.nombre) LIKE '%junio%' THEN 6
                        WHEN LOWER(mm.nombre) LIKE '%julio%' THEN 7
                        WHEN LOWER(mm.nombre) LIKE '%agosto%' THEN 8
                        WHEN LOWER(mm.nombre) LIKE '%septiembre%' THEN 9
                        WHEN LOWER(mm.nombre) LIKE '%setiembre%' THEN 9
                        WHEN LOWER(mm.nombre) LIKE '%octubre%' THEN 10
                        WHEN LOWER(mm.nombre) LIKE '%noviembre%' THEN 11
                        WHEN LOWER(mm.nombre) LIKE '%diciembre%' THEN 12
                        ELSE 0
                    END AS mensualidad_mes_orden,

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

                    AND (
                        CASE 
                            WHEN LOWER(mm.nombre) LIKE '%enero%' THEN 1
                            WHEN LOWER(mm.nombre) LIKE '%febrero%' THEN 2
                            WHEN LOWER(mm.nombre) LIKE '%marzo%' THEN 3
                            WHEN LOWER(mm.nombre) LIKE '%abril%' THEN 4
                            WHEN LOWER(mm.nombre) LIKE '%mayo%' THEN 5
                            WHEN LOWER(mm.nombre) LIKE '%junio%' THEN 6
                            WHEN LOWER(mm.nombre) LIKE '%julio%' THEN 7
                            WHEN LOWER(mm.nombre) LIKE '%agosto%' THEN 8
                            WHEN LOWER(mm.nombre) LIKE '%septiembre%' THEN 9
                            WHEN LOWER(mm.nombre) LIKE '%setiembre%' THEN 9
                            WHEN LOWER(mm.nombre) LIKE '%octubre%' THEN 10
                            WHEN LOWER(mm.nombre) LIKE '%noviembre%' THEN 11
                            WHEN LOWER(mm.nombre) LIKE '%diciembre%' THEN 12
                            ELSE 0
                        END
                    ) < '$mes_actual_numero'

                    AND (
                        CASE 
                            WHEN LOWER(mm.nombre) LIKE '%enero%' THEN 1
                            WHEN LOWER(mm.nombre) LIKE '%febrero%' THEN 2
                            WHEN LOWER(mm.nombre) LIKE '%marzo%' THEN 3
                            WHEN LOWER(mm.nombre) LIKE '%abril%' THEN 4
                            WHEN LOWER(mm.nombre) LIKE '%mayo%' THEN 5
                            WHEN LOWER(mm.nombre) LIKE '%junio%' THEN 6
                            WHEN LOWER(mm.nombre) LIKE '%julio%' THEN 7
                            WHEN LOWER(mm.nombre) LIKE '%agosto%' THEN 8
                            WHEN LOWER(mm.nombre) LIKE '%septiembre%' THEN 9
                            WHEN LOWER(mm.nombre) LIKE '%setiembre%' THEN 9
                            WHEN LOWER(mm.nombre) LIKE '%octubre%' THEN 10
                            WHEN LOWER(mm.nombre) LIKE '%noviembre%' THEN 11
                            WHEN LOWER(mm.nombre) LIKE '%diciembre%' THEN 12
                            ELSE 0
                        END
                    ) > 0

                ORDER BY 
                    iniv.id ASC,
                    mensualidad_mes_orden ASC,
                    mm.id ASC,
                    ig.id ASC,
                    isec.id ASC,
                    ual.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}
?>