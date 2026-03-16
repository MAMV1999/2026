<?php
require_once("../../database.php");

class Acceso
{
    public function __construct()
    {
    }

    // Función para verificar usuario y contraseña
    public function verificar($usuario, $clave)
    {
        $sql = "SELECT 
                    t.docente_id,
                    t.tipo_documento,
                    t.docente_documento,
                    t.docente_nombre,
                    t.docente_fecha_nacimiento,
                    t.docente_estado_civil,
                    t.docente_sexo,
                    t.docente_direccion,
                    t.docente_telefono,
                    t.docente_correo,
                    t.docente_cargo,
                    t.docente_tipo_contrato,
                    t.docente_fecha_inicio,
                    t.docente_fecha_fin,
                    t.docente_cuenta_bancaria,
                    t.docente_cuenta_interbancaria,
                    t.docente_sunat_ruc,
                    t.docente_sunat_usuario,
                    t.docente_sunat_contraseña,
                    t.docente_estado
                FROM
                (
                    /* DOCENTE */
                    SELECT
                        d.id AS docente_id,
                        ud.nombre AS tipo_documento,
                        d.numerodocumento AS docente_documento,
                        d.nombreyapellido AS docente_nombre,
                        DATE_FORMAT(d.nacimiento, '%d/%m/%Y') AS docente_fecha_nacimiento,
                        uec.nombre AS docente_estado_civil,
                        us.nombre AS docente_sexo,
                        d.direccion AS docente_direccion,
                        d.telefono AS docente_telefono,
                        d.correo AS docente_correo,
                        uc.nombre AS docente_cargo,
                        utc.nombre AS docente_tipo_contrato,
                        d.fechainicio AS docente_fecha_inicio,
                        d.fechafin AS docente_fecha_fin,
                        d.cuentabancaria AS docente_cuenta_bancaria,
                        d.cuentainterbancaria AS docente_cuenta_interbancaria,
                        d.sunat_ruc AS docente_sunat_ruc,
                        d.sunat_usuario AS docente_sunat_usuario,
                        d.sunat_contraseña AS docente_sunat_contraseña,
                        CASE 
                            WHEN d.estado = 1 THEN 'ACTIVO'
                            WHEN d.estado = 0 THEN 'INACTIVO'
                            ELSE 'desconocido'
                        END AS docente_estado,
                        1 AS orden_tipo
                    FROM usuario_docente d
                    INNER JOIN usuario_documento ud 
                        ON ud.id = d.id_documento AND ud.estado = 1
                    LEFT JOIN usuario_estado_civil uec 
                        ON uec.id = d.id_estado_civil AND uec.estado = 1
                    LEFT JOIN usuario_sexo us 
                        ON us.id = d.id_sexo AND us.estado = 1
                    LEFT JOIN usuario_cargo uc 
                        ON uc.id = d.id_cargo AND uc.estado = 1
                    LEFT JOIN usuario_tipo_contrato utc 
                        ON utc.id = d.id_tipo_contrato AND utc.estado = 1
                    WHERE 
                        d.usuario = '$usuario'
                        AND d.clave = '$clave'
                        AND d.fechainicio <= CURDATE()
                        AND d.fechafin >= CURDATE()
                        AND d.estado = 1

                    UNION ALL

                    /* ALUMNO */
                    SELECT
                        a.id AS docente_id,
                        ud.nombre AS tipo_documento,
                        a.numerodocumento AS docente_documento,
                        a.nombreyapellido AS docente_nombre,
                        DATE_FORMAT(a.nacimiento, '%d/%m/%Y') AS docente_fecha_nacimiento,
                        '' AS docente_estado_civil,
                        us.nombre AS docente_sexo,
                        '' AS docente_direccion,
                        a.telefono AS docente_telefono,
                        '' AS docente_correo,
                        (
                            SELECT c.nombre
                            FROM usuario_cargo c
                            WHERE c.id = (
                                SELECT MAX(c2.id)
                                FROM usuario_cargo c2
                                WHERE c2.estado = 1
                            )
                        ) AS docente_cargo,
                        '' AS docente_tipo_contrato,
                        '2026-03-01' AS docente_fecha_inicio,
                        '2026-12-31' AS docente_fecha_fin,
                        '' AS docente_cuenta_bancaria,
                        '' AS docente_cuenta_interbancaria,
                        '' AS docente_sunat_ruc,
                        '' AS docente_sunat_usuario,
                        '' AS docente_sunat_contraseña,
                        CASE 
                            WHEN a.estado = 1 THEN 'ACTIVO'
                            WHEN a.estado = 0 THEN 'INACTIVO'
                            ELSE 'desconocido'
                        END AS docente_estado,
                        2 AS orden_tipo
                    FROM usuario_alumno a
                    INNER JOIN usuario_documento ud 
                        ON ud.id = a.id_documento AND ud.estado = 1
                    LEFT JOIN usuario_sexo us 
                        ON us.id = a.id_sexo AND us.estado = 1
                    WHERE 
                        a.usuario = '$usuario'
                        AND a.clave = '$clave'
                        AND a.estado = 1
                ) AS t
                ORDER BY t.orden_tipo ASC, t.docente_nombre ASC
                LIMIT 1";
        return ejecutarConsulta($sql);
    }
}
?>
