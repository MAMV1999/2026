<?php
require_once("../../database.php");

class Facturacion_x_mes
{
    public function __construct() {}

    public function guardarEditarMasivo($detalles)
    {
        try {
            foreach ($detalles as $detalle) {

                $id = isset($detalle['id']) ? $detalle['id'] : "";
                if (empty($id) && isset($detalle['mensualidad_detalle_id'])) {
                    $id = $detalle['mensualidad_detalle_id'];
                }

                $recibo = isset($detalle['recibo']) ? $detalle['recibo'] : 0;
                $recibo = ($recibo == 1 || $recibo == "1" || $recibo === true) ? 1 : 0;

                if (empty($id)) {

                    $matricula_mes_id = "";
                    if (isset($detalle['matricula_mes_id'])) {
                        $matricula_mes_id = $detalle['matricula_mes_id'];
                    } else if (isset($detalle['id_mensualidad_mes'])) {
                        $matricula_mes_id = $detalle['id_mensualidad_mes'];
                    }

                    $id_matricula_detalle = isset($detalle['id_matricula_detalle']) ? $detalle['id_matricula_detalle'] : "";
                    $monto = isset($detalle['monto']) ? $detalle['monto'] : 0;
                    $pagado = isset($detalle['pagado']) ? $detalle['pagado'] : 1;
                    $pagado = ($pagado == 1 || $pagado == "1" || $pagado === true) ? 1 : 0;

                    if (empty($matricula_mes_id) || empty($id_matricula_detalle)) {
                        return false;
                    }

                    $sql = "INSERT INTO mensualidad_detalle 
                            (
                                matricula_mes_id, 
                                id_matricula_detalle, 
                                monto, 
                                pagado, 
                                recibo, 
                                estado
                            ) 
                            VALUES 
                            (
                                '$matricula_mes_id', 
                                '$id_matricula_detalle', 
                                '$monto', 
                                '$pagado', 
                                '$recibo', 
                                '1'
                            )";

                } else {

                    $sql = "UPDATE mensualidad_detalle 
                            SET recibo = '$recibo' 
                            WHERE id = '$id' 
                            AND estado = 1";
                }

                if (!ejecutarConsulta($sql)) {
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }


    public function listar()
    {
        $sql = "SELECT 
                    md.matricula_mes_id AS id_mensualidad_mes,
                    CONCAT(mm.nombre, ' ', il.nombre) AS mensualidad_mes_nombre,
                    COUNT(CASE WHEN md.pagado = 1 THEN 1 END) AS cantidad_pagado,
                    COUNT(CASE WHEN md.pagado = 0 THEN 1 END) AS cantidad_no_pagado,
                    COUNT(CASE WHEN md.recibo = 1 THEN 1 END) AS cantidad_con_recibo,
                    COUNT(CASE WHEN md.recibo = 0 THEN 1 END) AS cantidad_sin_recibo
                FROM mensualidad_detalle md
                INNER JOIN matricula_mes mm 
                    ON md.matricula_mes_id = mm.id 
                    AND mm.estado = 1
                INNER JOIN institucion_lectivo il 
                    ON mm.institucion_lectivo_id = il.id 
                    AND il.estado = 1
                WHERE md.estado = 1 
                AND md.monto >= 1 
                AND md.pagado = 1
                GROUP BY md.matricula_mes_id, mm.nombre, il.nombre
                ORDER BY md.matricula_mes_id ASC";

        return ejecutarConsulta($sql);
    }


    public function listar_frm($id)
    {
        $sql = "SELECT
                    md.id AS mensualidad_detalle_id,
                    md.id AS id,
                    md.matricula_mes_id AS id_mensualidad_mes,
                    md.matricula_mes_id,
                    md.id_matricula_detalle,

                    il.nombre AS institucion_lectivo_nombre,
                    mm.nombre AS mensualidad_nombre,
                    mm.observaciones AS mensualidad_descripcion,
                    0 AS pago_mantenimiento,
                    mm.fecha_vencimiento AS mensualidad_fechavencimiento,
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

                    CONCAT(
                        'MENSUALIDAD ', 
                        mm.nombre, 
                        ' ', 
                        il.nombre, 
                        ' - ', 
                        inn.nombre, 
                        ' - ', 
                        ig.nombre, 
                        ' - ', 
                        ua2.nombreyapellido
                    ) AS descripcion_mensualidad,

                    md.monto,
                    md.pagado,
                    md.recibo,
                    md.observaciones,
                    md.estado

                FROM mensualidad_detalle md

                INNER JOIN matricula_mes mm 
                    ON md.matricula_mes_id = mm.id 
                    AND mm.estado = 1

                INNER JOIN institucion_lectivo il 
                    ON mm.institucion_lectivo_id = il.id 
                    AND il.estado = 1

                INNER JOIN matricula_detalle mdet 
                    ON md.id_matricula_detalle = mdet.id 
                    AND mdet.estado = 1

                INNER JOIN matricula mat 
                    ON mdet.id_matricula = mat.id 
                    AND mat.estado = 1

                INNER JOIN institucion_seccion isec 
                    ON mat.id_institucion_seccion = isec.id 
                    AND isec.estado = 1

                INNER JOIN institucion_grado ig 
                    ON isec.id_institucion_grado = ig.id 
                    AND ig.estado = 1

                INNER JOIN institucion_nivel inn 
                    ON ig.id_institucion_nivel = inn.id 
                    AND inn.estado = 1

                INNER JOIN usuario_apoderado ua 
                    ON mdet.id_usuario_apoderado = ua.id 
                    AND ua.estado = 1

                INNER JOIN usuario_documento ud 
                    ON ua.id_documento = ud.id 
                    AND ud.estado = 1

                INNER JOIN usuario_alumno ua2 
                    ON mdet.id_usuario_alumno = ua2.id 
                    AND ua2.estado = 1

                INNER JOIN usuario_documento uad 
                    ON ua2.id_documento = uad.id 
                    AND uad.estado = 1

                WHERE md.matricula_mes_id = '$id' 
                AND md.estado = 1 
                AND md.monto >= 1 
                AND md.pagado = 1

                ORDER BY inn.id ASC, ig.id ASC, isec.id ASC, ua2.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}