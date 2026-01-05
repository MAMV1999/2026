<?php
require_once("../../database.php");

class Mensualidad_detalle_pagado
{
    public function __construct() {}

    public function listar_mensualidad_detalle_pagado()
    {
        $sql = "SELECT 
                    institucion_lectivo.nombre AS lectivo_nombre,
                    institucion_nivel.nombre AS nivel_nombre,
                    institucion_grado.nombre AS grado_nombre,
                    institucion_seccion.nombre AS seccion_nombre,
                    usuario_apoderado.nombreyapellido AS apoderado_nombre,
                    usuario_apoderado.telefono AS apoderado_telefono,
                    usuario_alumno.numerodocumento AS alumno_codigo,
                    usuario_alumno.nombreyapellido AS alumno_nombre,
                    mensualidad_mes.nombre AS mensualidad_mes_nombre,
                    mensualidad_detalle.monto AS detalle_monto,
                    CASE 
                        WHEN mensualidad_detalle.pagado = 1 THEN 'PAGADO'
                        WHEN mensualidad_detalle.pagado = 0 THEN 'PENDIENTE'
                        ELSE 'DESCONOCIDO'
                    END AS detalle_estado_pago,
                    mensualidad_detalle.observaciones AS detalle_observaciones,
                    CASE 
                        WHEN mensualidad_detalle.estado = 1 THEN 'ACTIVO'
                        WHEN mensualidad_detalle.estado = 0 THEN 'INACTIVO'
                        ELSE 'desconocido'
                    END AS detalle_estado
                FROM mensualidad_detalle
                LEFT JOIN mensualidad_mes ON mensualidad_detalle.id_mensualidad_mes = mensualidad_mes.id
                LEFT JOIN matricula_detalle ON mensualidad_detalle.id_matricula_detalle = matricula_detalle.id
                LEFT JOIN usuario_apoderado ON matricula_detalle.id_usuario_apoderado = usuario_apoderado.id
                LEFT JOIN usuario_alumno ON matricula_detalle.id_usuario_alumno = usuario_alumno.id
                LEFT JOIN matricula ON matricula_detalle.id_matricula = matricula.id
                LEFT JOIN institucion_seccion ON matricula.id_institucion_seccion = institucion_seccion.id
                LEFT JOIN institucion_grado ON institucion_seccion.id_institucion_grado = institucion_grado.id
                LEFT JOIN institucion_nivel ON institucion_grado.id_institucion_nivel = institucion_nivel.id
                LEFT JOIN institucion_lectivo ON institucion_nivel.id_institucion_lectivo = institucion_lectivo.id
                WHERE 
                    mensualidad_detalle.estado = 1 AND
                    mensualidad_mes.estado = 1 AND
                    matricula_detalle.estado = 1 AND
                    usuario_apoderado.estado = 1 AND
                    usuario_alumno.estado = 1 AND
                    matricula.estado = 1 AND
                    institucion_seccion.estado = 1 AND
                    institucion_grado.estado = 1 AND
                    institucion_nivel.estado = 1 AND
                    institucion_lectivo.estado = 1 AND
                    mensualidad_detalle.pagado = 1
                ORDER BY 
                    mensualidad_detalle.id_mensualidad_mes ASC,
                    institucion_lectivo.nombre ASC,
                    institucion_nivel.nombre ASC,
                    institucion_grado.nombre ASC,
                    institucion_seccion.nombre ASC,
                    usuario_alumno.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
