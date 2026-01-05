<?php
require_once("../../database.php");

class Reportedocente
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    ud.id AS docente_id,
                    ud.numerodocumento,
                    udoc.nombre AS documento_tipo,
                    ud.nombreyapellido AS docente_nombre,
                    DATE_FORMAT(ud.nacimiento, '%d/%m/%Y') AS fecha_nacimiento,
                    uec.nombre AS estado_civil,
                    usex.nombre AS sexo,
                    ud.direccion,
                    ud.telefono,
                    ud.correo,
                    ucar.nombre AS cargo,
                    utc.nombre AS tipo_contrato,
                    DATE_FORMAT(ud.fechainicio, '%d/%m/%Y') AS fecha_inicio,
                    DATE_FORMAT(ud.fechafin, '%d/%m/%Y') AS fecha_fin,
                    ud.sueldo,
                    ud.cuentabancaria,
                    ud.cuentainterbancaria,
                    ud.sunat_ruc,
                    ud.sunat_usuario,
                    ud.sunat_contraseña,
                    ud.usuario,
                    ud.clave,
                    ud.observaciones,
                    DATE_FORMAT(ud.fechacreado, '%d/%m/%Y') AS fecha_creado,
                    ud.estado
                FROM usuario_docente ud
                LEFT JOIN usuario_documento udoc ON ud.id_documento = udoc.id
                LEFT JOIN usuario_estado_civil uec ON ud.id_estado_civil = uec.id
                LEFT JOIN usuario_sexo usex ON ud.id_sexo = usex.id
                LEFT JOIN usuario_cargo ucar ON ud.id_cargo = ucar.id
                LEFT JOIN usuario_tipo_contrato utc ON ud.id_tipo_contrato = utc.id
                ORDER BY ud.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

}
