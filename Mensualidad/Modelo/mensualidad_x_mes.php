<?php
require_once("../../database.php");

class Mensualidadxmes
{
    public function __construct() {}

    public function guardarEditarMasivo($detalles)
    {
        try {
            foreach ($detalles as $detalle) {
                $id = $detalle['id'];
                $monto = $detalle['monto'];
                $pagado = $detalle['pagado'];

                // Si el ID está vacío, es un nuevo registro
                if (empty($id)) {
                    $sql = "INSERT INTO mensualidad_detalle (id_mensualidad_mes, id_matricula_detalle, monto, pagado) VALUES ('$detalle[id_mensualidad_mes]', '$detalle[id_matricula_detalle]', '$monto', '$pagado')";
                } else {
                    // Actualización de registro existente
                    $sql = "UPDATE mensualidad_detalle SET monto = '$monto', pagado = '$pagado' WHERE id = '$id'";
                }

                if (!ejecutarConsulta($sql)) {
                    // Manejo simple de error
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            // Manejo de excepción
            return false;
        }
    }


    // Método para listar todas las mensualidades
    public function listar()
    {
        $sql = "SELECT
                md.id_mensualidad_mes,
                CONCAT(mm.nombre, ' ', il.nombre) AS nombre_mes,
                COUNT(CASE WHEN md.pagado = 0 THEN 1 END) AS deudor,
                SUM(CASE WHEN md.pagado = 0 THEN md.monto ELSE 0 END) AS suma_deuda,
                COUNT(CASE WHEN md.pagado = 1 THEN 1 END) AS cancelado,
                SUM(CASE WHEN md.pagado = 1 THEN md.monto ELSE 0 END) AS suma_cancelado,
                CASE WHEN SUM(md.pagado) = COUNT(md.id) THEN 1 ELSE 0 END AS mes_cerrado
                FROM mensualidad_detalle md
                JOIN mensualidad_mes mm ON md.id_mensualidad_mes = mm.id AND mm.estado = 1
                JOIN institucion_lectivo il ON mm.id_institucion_lectivo = il.id
                WHERE md.estado = 1
                GROUP BY md.id_mensualidad_mes, mm.nombre, il.nombre
                ORDER BY md.id_mensualidad_mes";
        return ejecutarConsulta($sql);
    }

    public function listar_frm($id)
    {
        $sql = "SELECT
                md.id AS mensualidad_detalle_id,
                md.id_mensualidad_mes,
                il.nombre AS institucion_lectivo_nombre,
                mm.nombre AS mensualidad_nombre,
                mm.descripcion AS mensualidad_descripcion,
                mm.pago_mantenimiento AS pago_mantenimiento,
                mm.fechavencimiento AS mensualidad_fechavencimiento,
                il.nombre AS lectivo_nombre,
                inn.nombre AS nivel_nombre,
                ig.nombre AS grado_nombre,
                isec.nombre AS seccion_nombre,
                ud.nombre AS apoderado_tipo_documento,
                ua.numerodocumento AS apoderado_numero_documento,
                ua.telefono AS apoderado_telefono,
                ua.nombreyapellido AS apoderado_nombre,
                uad.nombre AS alumno_tipo_documento,
                ua2.numerodocumento AS alumno_numero_documento,
                ua2.nombreyapellido AS alumno_nombre,
                md.monto,
                md.pagado,
                md.observaciones,
                md.estado
                FROM mensualidad_detalle md
                INNER JOIN mensualidad_mes mm ON md.id_mensualidad_mes = mm.id AND mm.estado = 1
                INNER JOIN institucion_lectivo il ON mm.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_detalle mdet ON md.id_matricula_detalle = mdet.id AND mdet.estado = 1
                INNER JOIN institucion_seccion isec ON mdet.id_matricula = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id AND ig.estado = 1
                INNER JOIN institucion_nivel inn ON ig.id_institucion_nivel = inn.id AND inn.estado = 1
                INNER JOIN usuario_apoderado ua ON mdet.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_documento ud ON ua.id_documento = ud.id AND ud.estado = 1
                INNER JOIN usuario_alumno ua2 ON mdet.id_usuario_alumno = ua2.id AND ua2.estado = 1
                INNER JOIN usuario_documento uad ON ua2.id_documento = uad.id AND uad.estado = 1
                WHERE md.id_mensualidad_mes = '$id' AND md.estado = 1
                ORDER BY il.nombre ASC, inn.nombre ASC, ig.nombre ASC, isec.nombre ASC, ua2.nombreyapellido ASC, md.pagado ASC";
        return ejecutarConsulta($sql);
    }
}
