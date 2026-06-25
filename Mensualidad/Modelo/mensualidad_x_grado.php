<?php
require_once("../../database.php");

class Mensualidadxapoderado
{
    public function __construct() {}

    public function guardarEditarMasivo($detalles)
    {
        try {
            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? $detalle['id'] : null;
                $monto = isset($detalle['monto']) ? $detalle['monto'] : null;
                $pagado = isset($detalle['pagado']) ? $detalle['pagado'] : null;
                $observaciones = isset($detalle['observaciones']) ? $detalle['observaciones'] : null;

                $sql = "UPDATE mensualidad_detalle 
                        SET monto = '$monto', 
                            pagado = '$pagado', 
                            observaciones = '$observaciones' 
                        WHERE id = '$id'";

                if (!ejecutarConsulta($sql)) {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Ahora lista por grado
    public function listar()
    {
        $sql = "SELECT 
                    il.id AS lectivo_id,
                    il.nombre AS lectivo_nombre,
                    il.nombre_lectivo AS lectivo_nombre_detallado,

                    iniv.id AS nivel_id,
                    iniv.nombre AS nivel_nombre,

                    ig.id AS grado_id,
                    ig.nombre AS grado_nombre,

                    institucion.id AS institucion_id,
                    institucion.nombre AS institucion_nombre

                FROM institucion_grado ig
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                INNER JOIN institucion ON il.id_institucion = institucion.id

                WHERE ig.estado = 1
                AND iniv.estado = 1
                AND il.estado = 1
                AND institucion.estado = 1

                ORDER BY 
                    il.nombre ASC,
                    iniv.id ASC,
                    ig.id ASC";

        return ejecutarConsulta($sql);
    }

    // Ahora recibe el id del grado
    public function listar_frm($id_grado)
    {
        $sql = "SELECT 
                    uat.nombre AS tipo_apoderado,
                    ud.nombre AS tipo_documento,
                    ua.numerodocumento AS numerodocumento,
                    ua.nombreyapellido AS nombre_apoderado,
                    ua.telefono AS telefono,

                    il.id AS id_lectivo,
                    il.nombre AS lectivo,

                    iniv.id AS id_nivel,
                    iniv.nombre AS nivel,

                    ig.id AS id_grado,
                    ig.nombre AS grado,

                    isec.id AS id_seccion,
                    isec.nombre AS seccion,

                    ual.numerodocumento AS codigo,
                    ual.nombreyapellido AS nombre_alumno,

                    GROUP_CONCAT(md.id ORDER BY mm.id ASC SEPARATOR '|||') AS ids_mensualidad_detalle,
                    GROUP_CONCAT(mm.id ORDER BY mm.id ASC SEPARATOR '|||') AS ids_mes,
                    GROUP_CONCAT(mm.nombre ORDER BY mm.id ASC SEPARATOR '|||') AS meses,
                    GROUP_CONCAT(md.monto ORDER BY mm.id ASC SEPARATOR '|||') AS montos,
                    GROUP_CONCAT(md.pagado ORDER BY mm.id ASC SEPARATOR '|||') AS estados_pago,
                    GROUP_CONCAT(IFNULL(md.observaciones, '') ORDER BY mm.id ASC SEPARATOR '|||') AS observaciones

                FROM mensualidad_detalle md
                INNER JOIN matricula_detalle mtd ON md.id_matricula_detalle = mtd.id
                INNER JOIN usuario_alumno ual ON mtd.id_usuario_alumno = ual.id
                INNER JOIN matricula m ON mtd.id_matricula = m.id
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                INNER JOIN matricula_mes mm ON md.matricula_mes_id = mm.id
                INNER JOIN usuario_apoderado ua ON mtd.id_usuario_apoderado = ua.id
                INNER JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id
                INNER JOIN usuario_documento ud ON ua.id_documento = ud.id

                WHERE ig.id = '$id_grado'

                AND ua.estado = 1
                AND uat.estado = 1
                AND ud.estado = 1
                AND md.estado = 1
                AND mtd.estado = 1
                AND ual.estado = 1
                AND m.estado = 1
                AND isec.estado = 1
                AND ig.estado = 1
                AND iniv.estado = 1
                AND il.estado = 1
                AND mm.estado = 1

                GROUP BY 
                    mtd.id,
                    ua.id,
                    ua.nombreyapellido,
                    ua.numerodocumento,
                    ua.telefono,
                    uat.nombre,
                    ud.nombre,
                    il.id,
                    il.nombre,
                    iniv.id,
                    iniv.nombre,
                    ig.id,
                    ig.nombre,
                    isec.id,
                    isec.nombre,
                    ual.numerodocumento,
                    ual.nombreyapellido

                ORDER BY 
                    il.nombre ASC,
                    iniv.id ASC,
                    ig.id ASC,
                    isec.id ASC,
                    ual.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}