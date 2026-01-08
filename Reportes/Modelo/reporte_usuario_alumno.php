<?php
require_once("../../database.php");

class Reportealumno
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    ua.id AS alumno_id,
                    uapo.nombreyapellido AS apoderado_nombre,
                    udoc.nombre AS documento_tipo,
                    ua.numerodocumento,
                    ua.nombreyapellido AS alumno_nombre,
                    DATE_FORMAT(ua.nacimiento, '%d/%m/%Y') AS nacimiento,
                    usex.nombre AS sexo,
                    ua.observaciones,
                    DATE_FORMAT(ua.fechacreado, '%d/%m/%Y') AS fecha_creado,
                    ua.estado
                FROM usuario_alumno ua
                LEFT JOIN usuario_apoderado uapo
                    ON ua.id_apoderado = uapo.id
                LEFT JOIN usuario_documento udoc
                    ON ua.id_documento = udoc.id
                LEFT JOIN usuario_sexo usex
                    ON ua.id_sexo = usex.id
                WHERE ua.estado = 1
                ORDER BY ua.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}
