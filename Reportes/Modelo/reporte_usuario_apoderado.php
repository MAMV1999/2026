<?php
require_once("../../database.php");

class Reporteapoderado
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    ua.id AS apoderado_id,
                    uat.nombre AS apoderado_tipo,
                    udoc.nombre AS documento_tipo,
                    ua.numerodocumento,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono,
                    IFNULL(alu.total_alumnos, 0) AS total_alumnos,
                    ua.observaciones,
                    DATE_FORMAT(ua.fechacreado, '%d/%m/%Y') AS fecha_creado,
                    ua.estado
                FROM usuario_apoderado ua
                LEFT JOIN usuario_apoderado_tipo uat 
                    ON ua.id_apoderado_tipo = uat.id
                LEFT JOIN usuario_documento udoc 
                    ON ua.id_documento = udoc.id
                LEFT JOIN (
                    SELECT 
                        id_apoderado,
                        COUNT(*) AS total_alumnos
                    FROM usuario_alumno
                    WHERE estado = 1
                    GROUP BY id_apoderado
                ) alu ON alu.id_apoderado = ua.id
                WHERE ua.estado = 1
                ORDER BY ua.nombreyapellido ASC";

        return ejecutarConsulta($sql);
    }
}
