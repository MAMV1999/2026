<?php
require_once("../../database.php");

class Recibomatricula
{
    public function __construct()
    {
    }

    public function listarPorIdMatriculaDettalle($id)
    {
        $sql = "SELECT 
                -- Información de la matrícula detalle
                md.id AS matricula_detalle_id,
                md.descripcion AS matricula_detalle_descripcion,
                md.observaciones AS matricula_detalle_observaciones,
                md.fechacreado AS matricula_detalle_fechacreado,
                
                -- Información del alumno
                ua.nombreyapellido AS alumno_nombre_completo,
                ua.numerodocumento AS alumno_numero_documento,
                ud_a.nombre AS alumno_tipo_documento,
                ua.nacimiento AS alumno_fecha_nacimiento,

                -- Información del apoderado
                ap.nombreyapellido AS apoderado_nombre_completo,
                ap.numerodocumento AS apoderado_numero_documento,
                ud_ap.nombre AS apoderado_tipo_documento,
                ap.telefono AS apoderado_telefono,
                uat.nombre AS apoderado_tipo_nombre,
                ud_ap.nombre AS apoderado_documento_nombre,

                -- Información del ciclo lectivo, nivel, grado y sección
                il.nombre AS lectivo_nombre,
                il.nombre_lectivo AS lectivo_nombre_ano,
                inl.nombre AS nivel_nombre,
                ig.nombre AS grado_nombre,
                isec.nombre AS seccion_nombre,

                -- Información de la institución
                inst.nombre AS institucion_nombre,
                inst.direccion AS institucion_direccion,
                inst.telefono AS institucion_telefono,
                inst.correo AS institucion_correo,
                inst.razon_social AS institucion_razon_social,
                inst.ruc AS institucion_ruc,
                
                -- Información del usuario docente
                udoc.nombreyapellido AS usuario_docente_nombre,
                udoc.numerodocumento AS usuario_docente_numerodocumento,
                ucargo.nombre AS usuario_docente_cargo,
                ud_docente.nombre AS usuario_docente_documento,

                -- Información de los pagos
                mp.numeracion AS pago_numeracion,
                DATE_FORMAT(mp.fecha, '%d/%m/%Y') AS pago_fecha,
                mp.descripcion AS pago_descripcion,
                mp.observaciones AS pago_observaciones,
                mp.monto AS pago_monto,
                mmp.nombre AS metodo_pago_nombre,

                -- Información de la categoría de matrícula
                mc.nombre AS matricula_categoria_nombre,
                mc.observaciones AS matricula_categoria_observaciones
                
            FROM 
                matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                INNER JOIN institucion_nivel inl ON ig.id_institucion_nivel = inl.id
                INNER JOIN institucion_lectivo il ON inl.id_institucion_lectivo = il.id
                INNER JOIN institucion inst ON il.id_institucion = inst.id
                -- Relación con usuario docente
                LEFT JOIN usuario_docente udoc ON inst.id_usuario_docente = udoc.id
                -- Relación con usuario_cargo
                LEFT JOIN usuario_cargo ucargo ON udoc.id_cargo = ucargo.id
                -- Relación con el documento del docente
                LEFT JOIN usuario_documento ud_docente ON udoc.id_documento = ud_docente.id
                INNER JOIN usuario_alumno ua ON md.id_usuario_alumno = ua.id
                INNER JOIN usuario_documento ud_a ON ua.id_documento = ud_a.id
                INNER JOIN usuario_apoderado ap ON md.id_usuario_apoderado = ap.id
                INNER JOIN usuario_apoderado_tipo uat ON ap.id_apoderado_tipo = uat.id
                INNER JOIN usuario_documento ud_ap ON ap.id_documento = ud_ap.id
                LEFT JOIN matricula_pago mp ON mp.id_matricula_detalle = md.id
                LEFT JOIN matricula_metodo_pago mmp ON mp.id_matricula_metodo_pago = mmp.id
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id
            WHERE 
                    md.id = '$id'";
        return ejecutarConsulta($sql);
    }

    public function listarDocumentosDeMatricula()
    {
        $sql = "SELECT 
                    d.*,
                    dr.nombre AS documento_responsable_nombre,
                    CONCAT(
                        UPPER(LEFT(SUBSTRING_INDEX(dr.nombre, ' ', 1), 1)), '.',
                        IF(LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(dr.nombre, ' ', 2), ' ', -1)) > 0,
                        CONCAT(UPPER(LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(dr.nombre, ' ', 2), ' ', -1), 1)), '.'),
                        '')
                    ) AS documento_responsable_iniciales,
                    CASE 
                        WHEN d.obligatorio = 1 THEN '(*)'
                        ELSE ''
                    END AS obligatorio_marcado
                FROM 
                    documento d
                LEFT JOIN 
                    documento_responsable dr 
                ON 
                    d.id_documento_responsable = dr.id
                WHERE 
                    d.estado = 1
                ORDER BY 
                    d.id ASC";
        return ejecutarConsulta($sql);
    }
}
?>
