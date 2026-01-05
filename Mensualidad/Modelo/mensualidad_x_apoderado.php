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

                // Actualizar el registro con el ID correspondiente
                $sql = "UPDATE mensualidad_detalle SET monto = '$monto', pagado = '$pagado', observaciones = '$observaciones' WHERE id = '$id'";
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
                ua.id AS id_apoderado,
                uat.nombre AS tipo_apoderado,
                ud.nombre AS tipo_documento,
                ua.numerodocumento AS numero_documento,
                ua.nombreyapellido AS nombre_apoderado,
                ua.telefono AS telefono_apoderado,
                COUNT(DISTINCT md.id_usuario_alumno) AS total_alumnos
                FROM matricula_detalle md
                JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id
                JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id
                JOIN usuario_documento ud ON ua.id_documento = ud.id
                WHERE ua.estado = 1 AND md.estado = 1
                GROUP BY ua.id, ua.nombreyapellido, ua.telefono, ua.numerodocumento, uat.nombre, ud.nombre
                ORDER BY nombre_apoderado ASC";
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
                GROUP_CONCAT(md.id ORDER BY mm.id ASC SEPARATOR ', ') AS ids_mensualidad_detalle, 
                GROUP_CONCAT(mm.id ORDER BY mm.id ASC SEPARATOR ', ') AS ids_mes, 
                GROUP_CONCAT(mm.nombre ORDER BY mm.id ASC SEPARATOR ', ') AS meses, 
                GROUP_CONCAT(md.monto ORDER BY mm.id ASC SEPARATOR ', ') AS montos, 
                GROUP_CONCAT(md.pagado ORDER BY mm.id ASC SEPARATOR ', ') AS estados_pago,
                GROUP_CONCAT(md.observaciones ORDER BY mm.id ASC SEPARATOR ', ') AS observaciones
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
                WHERE ua.id = '$id' AND ua.estado = 1 AND md.estado = 1 AND mtd.estado = 1 AND ual.estado = 1 AND m.estado = 1 AND isec.estado = 1 AND ig.estado = 1 AND iniv.estado = 1 AND il.estado = 1 AND mm.estado = 1
                GROUP BY ua.id, ua.nombreyapellido, ua.numerodocumento, ua.telefono, uat.nombre, ud.nombre, il.nombre, iniv.nombre, ig.nombre, isec.nombre, ual.nombreyapellido
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
