<?php
require_once("../../database.php");

class Reportemensualidadxgrado
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    md.id_matricula_detalle,

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
                    
                    ud_alumno.nombre AS tipo_documento_alumno,
                    ma.numerodocumento AS numero_documento_alumno,
                    ma.nombreyapellido AS nombre_alumno,
                    
                    uat.nombre AS tipo_apoderado,
                    ud_apoderado.nombre AS tipo_documento_apoderado,
                    ua.numerodocumento AS numero_documento_apoderado,
                    ua.nombreyapellido AS nombre_apoderado,
                    ua.telefono AS telefono_apoderado,
                    
                    GROUP_CONCAT(md.id ORDER BY mm.id ASC SEPARATOR ', ') AS ids_mensualidad_detalle, 
                    GROUP_CONCAT(mm.id ORDER BY mm.id ASC SEPARATOR ', ') AS ids_mes, 
                    GROUP_CONCAT(mm.nombre ORDER BY mm.id ASC SEPARATOR ', ') AS meses, 
                    GROUP_CONCAT(md.monto ORDER BY mm.id ASC SEPARATOR ', ') AS montos, 
                    GROUP_CONCAT(md.pagado ORDER BY mm.id ASC SEPARATOR ', ') AS estados_pago,
                    GROUP_CONCAT(
                        CASE 
                            WHEN md.pagado = 0 THEN 'PENDIENTE'
                            WHEN md.pagado = 1 THEN 'CANCELADO'
                            ELSE '-.-'
                        END
                        ORDER BY mm.id ASC SEPARATOR ', '
                    ) AS estados_pago_legibles,
                    GROUP_CONCAT(md.observaciones ORDER BY mm.id ASC SEPARATOR ', ') AS observaciones
                FROM mensualidad_detalle md
                LEFT JOIN mensualidad_mes mm ON md.id_mensualidad_mes = mm.id AND mm.estado = 1
                LEFT JOIN matricula_detalle mdd ON md.id_matricula_detalle = mdd.id AND mdd.estado = 1
                LEFT JOIN usuario_alumno ma ON mdd.id_usuario_alumno = ma.id AND ma.estado = 1
                LEFT JOIN usuario_documento ud_alumno ON ma.id_documento = ud_alumno.id AND ud_alumno.estado = 1
                LEFT JOIN usuario_apoderado ua ON mdd.id_usuario_apoderado = ua.id AND ua.estado = 1
                LEFT JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id AND uat.estado = 1
                LEFT JOIN usuario_documento ud_apoderado ON ua.id_documento = ud_apoderado.id AND ud_apoderado.estado = 1
                LEFT JOIN matricula m ON mdd.id_matricula = m.id AND m.estado = 1
                LEFT JOIN institucion_seccion ise ON m.id_institucion_seccion = ise.id AND ise.estado = 1
                LEFT JOIN institucion_grado ig ON ise.id_institucion_grado = ig.id AND ig.estado = 1
                LEFT JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id AND iniv.estado = 1
                LEFT JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                LEFT JOIN institucion i ON il.id_institucion = i.id AND i.estado = 1
                WHERE md.estado = 1
                GROUP BY md.id_matricula_detalle, i.nombre, i.telefono, i.correo, i.ruc, i.razon_social, i.direccion, 
                    il.nombre, iniv.nombre, ig.nombre, ise.nombre, ud_alumno.nombre, ma.numerodocumento, ma.nombreyapellido, 
                    uat.nombre, ud_apoderado.nombre, ua.numerodocumento, ua.nombreyapellido
                ORDER BY  il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, ise.nombre ASC, ma.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
