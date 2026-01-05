<?php
require_once("../../database.php");

class Facturacion_x_apoderado
{
    public function __construct() {}

    public function guardarEditarMasivo($detalles)
    {
        try {
            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? $detalle['id'] : null;
                $recibo = isset($detalle['recibo']) ? $detalle['recibo'] : null;

                // Actualizar el registro con el ID correspondiente
                $sql = "UPDATE mensualidad_detalle SET recibo = '$recibo' WHERE id = '$id'";
                if (!ejecutarConsulta($sql)) {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    // Método para listar todas las mensualidades
    public function listar()
    {
        $sql = "SELECT 
                    ua.id AS apoderado_id,
                    ua.nombreyapellido,
                    uat.nombre AS tipo_apoderado,
                    COUNT(DISTINCT mdeta.id_usuario_alumno) AS cantidad_alumnos
                FROM mensualidad_detalle md
                INNER JOIN matricula_detalle mdeta ON md.id_matricula_detalle = mdeta.id AND mdeta.estado = 1
                INNER JOIN usuario_apoderado ua ON mdeta.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id AND uat.estado = 1
                WHERE md.monto >= 1 AND md.pagado = 1 AND md.estado = 1
                GROUP BY  ua.id, ua.nombreyapellido, ua.telefono, uat.nombre
                ORDER BY ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_frm($id)
    {
        $sql = "SELECT
                    uat.nombre AS tipo_apoderado,
                    ud.nombre AS tipo_documento,
                    ua.numerodocumento AS numerodocumento,
                    ua.nombreyapellido AS nombre_apoderado,
                    ua.telefono AS telefono,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    ual.numerodocumento AS codigo,
                    ual.nombreyapellido AS nombre_alumno,

                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN CONCAT('MENSUALIDAD ', mm.nombre, ' ', il.nombre, ' - ', iniv.nombre, ' - ', ig.nombre) END ORDER BY mm.id ASC SEPARATOR ', ' ) AS descripcion_mensualidad,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN md.id END ORDER BY mm.id ASC SEPARATOR ', ' ) AS ids_mensualidad_detalle,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN mm.id END ORDER BY mm.id ASC SEPARATOR ', ' ) AS ids_mes,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN mm.nombre END ORDER BY mm.id ASC SEPARATOR ', ' ) AS meses,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN md.monto END ORDER BY mm.id ASC SEPARATOR ', ' ) AS montos,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN md.pagado END ORDER BY mm.id ASC SEPARATOR ', ' ) AS estados_pago,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN md.recibo END ORDER BY mm.id ASC SEPARATOR ', ' ) AS estados_recibo,
                    GROUP_CONCAT( CASE WHEN md.monto > 0 THEN md.observaciones END ORDER BY mm.id ASC SEPARATOR ', ' ) AS observaciones
                
                FROM mensualidad_detalle md
                JOIN matricula_detalle mtd ON md.id_matricula_detalle = mtd.id
                JOIN usuario_alumno ual ON mtd.id_usuario_alumno = ual.id
                JOIN matricula m ON mtd.id_matricula = m.id
                JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
                JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                JOIN mensualidad_mes mm ON md.id_mensualidad_mes = mm.id
                JOIN usuario_apoderado ua ON mtd.id_usuario_apoderado = ua.id
                JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id
                JOIN usuario_documento ud ON ua.id_documento = ud.id
                
                WHERE ua.id = '$id'
                
                AND ua.estado = 1 AND md.estado = 1 AND mtd.estado = 1 AND ual.estado = 1
                AND m.estado = 1 AND isec.estado = 1 AND ig.estado = 1 AND iniv.estado = 1
                AND il.estado = 1 AND mm.estado = 1 AND md.pagado = 1
                
                GROUP BY
                    ua.id, ua.nombreyapellido, ua.numerodocumento, ua.telefono,
                    uat.nombre, ud.nombre, il.nombre, iniv.nombre, ig.nombre,
                    isec.nombre, ual.nombreyapellido
                
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
