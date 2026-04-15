<?php
require_once("../../database.php");

class Reportedocente
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT
                    x.id,
                    x.id_documento,
                    x.documento_nombre,
                    x.numerodocumento,
                    x.nombreyapellido,
                    x.nacimiento,
                    x.id_estado_civil,
                    x.estado_civil_nombre,
                    x.id_sexo,
                    x.sexo_nombre,
                    x.direccion,
                    x.telefono,
                    x.correo,
                    x.id_cargo,
                    x.cargo_nombre,
                    x.id_tipo_contrato,
                    x.tipo_contrato_nombre,
                    x.fechainicio,
                    x.fechafin,
                    CASE
                        WHEN x.fechainicio_raw IS NULL OR x.fechafin_raw IS NULL THEN NULL
                        ELSE CONCAT(
                            TIMESTAMPDIFF(YEAR, x.fechainicio_raw, x.fechafin_raw), ' años, ',
                            TIMESTAMPDIFF(
                                MONTH,
                                DATE_ADD(x.fechainicio_raw, INTERVAL TIMESTAMPDIFF(YEAR, x.fechainicio_raw, x.fechafin_raw) YEAR),
                                x.fechafin_raw
                            ), ' meses, ',
                            DATEDIFF(
                                x.fechafin_raw,
                                DATE_ADD(
                                    DATE_ADD(
                                        x.fechainicio_raw,
                                        INTERVAL TIMESTAMPDIFF(YEAR, x.fechainicio_raw, x.fechafin_raw) YEAR
                                    ),
                                    INTERVAL TIMESTAMPDIFF(
                                        MONTH,
                                        DATE_ADD(x.fechainicio_raw, INTERVAL TIMESTAMPDIFF(YEAR, x.fechainicio_raw, x.fechafin_raw) YEAR),
                                        x.fechafin_raw
                                    ) MONTH
                                )
                            ), ' días'
                        )
                    END AS tiempo_laborado,
                    x.sueldo,
                    x.cuentabancaria,
                    x.cuentainterbancaria,
                    x.sunat_ruc,
                    x.sunat_usuario,
                    x.sunat_contraseña,
                    x.usuario,
                    x.clave,
                    x.observaciones,
                    x.fechacreado,
                    x.estado
                FROM
                (
                    SELECT
                        ud.id,
                        ud.id_documento,
                        udoc.nombre AS documento_nombre,
                        ud.numerodocumento,
                        ud.nombreyapellido,
                        DATE_FORMAT(ud.nacimiento, '%d/%m/%Y') AS nacimiento,
                        ud.id_estado_civil,
                        uec.nombre AS estado_civil_nombre,
                        ud.id_sexo,
                        usx.nombre AS sexo_nombre,
                        ud.direccion,
                        ud.telefono,
                        ud.correo,
                        ud.id_cargo,
                        uc.nombre AS cargo_nombre,
                        ud.id_tipo_contrato,
                        utc.nombre AS tipo_contrato_nombre,
                        DATE_FORMAT(ud.fechainicio, '%d/%m/%Y') AS fechainicio,
                        DATE_FORMAT(ud.fechafin, '%d/%m/%Y') AS fechafin,
                        ud.fechainicio AS fechainicio_raw,
                        ud.fechafin AS fechafin_raw,
                        ud.sueldo,
                        ud.cuentabancaria,
                        ud.cuentainterbancaria,
                        ud.sunat_ruc,
                        ud.sunat_usuario,
                        ud.sunat_contraseña,
                        ud.usuario,
                        ud.clave,
                        ud.observaciones,
                        DATE_FORMAT(ud.fechacreado, '%d/%m/%Y') AS fechacreado,
                        ud.estado
                    FROM usuario_docente ud
                    INNER JOIN usuario_documento udoc
                        ON ud.id_documento = udoc.id
                    AND udoc.estado = 1
                    INNER JOIN usuario_estado_civil uec
                        ON ud.id_estado_civil = uec.id
                    AND uec.estado = 1
                    INNER JOIN usuario_sexo usx
                        ON ud.id_sexo = usx.id
                    AND usx.estado = 1
                    INNER JOIN usuario_cargo uc
                        ON ud.id_cargo = uc.id
                    AND uc.estado = 1
                    LEFT JOIN usuario_tipo_contrato utc
                        ON ud.id_tipo_contrato = utc.id
                    AND utc.estado = 1
                    WHERE
                        ud.estado = 1
                        AND (ud.id_tipo_contrato IS NULL OR utc.id IS NOT NULL)
                ) x
                ORDER BY x.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

}
