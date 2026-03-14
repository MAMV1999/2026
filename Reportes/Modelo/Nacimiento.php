<?php
require_once("../../database.php");

class Nacimiento
{
    public function __construct() {}

    public function listarNacimiento()
    {
        $sql = "SELECT
                    i.nombre AS institucion,
                    niv.nombre AS nivel,
                    g.nombre AS grado,
                    s.nombre AS seccion,
                
                    CONCAT(d.nombreyapellido, ' - ', IFNULL(d.telefono, 'SIN TELÉFONO')) AS tutor,
                
                    a.nombreyapellido AS alumno,
                
                    DATE_FORMAT(a.nacimiento, '%d/%m/%Y') AS cumpleanios,
                
                    MONTH(a.nacimiento) AS mes_nacimiento,
                
                    CONCAT(TIMESTAMPDIFF(YEAR, a.nacimiento, CURDATE()), ' años') AS edad,
                
                    CONCAT(
                        TIMESTAMPDIFF(YEAR, a.nacimiento, CURDATE()), ' años, ',
                        TIMESTAMPDIFF(
                            MONTH,
                            DATE_ADD(a.nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, a.nacimiento, CURDATE()) YEAR),
                            CURDATE()
                        ), ' meses, ',
                        DATEDIFF(
                            CURDATE(),
                            DATE_ADD(
                                DATE_ADD(a.nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, a.nacimiento, CURDATE()) YEAR),
                                INTERVAL TIMESTAMPDIFF(
                                    MONTH,
                                    DATE_ADD(a.nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, a.nacimiento, CURDATE()) YEAR),
                                    CURDATE()
                                ) MONTH
                            )
                        ), ' días'
                    ) AS edad_detallada
                
                FROM matricula_detalle md
                INNER JOIN matricula m
                    ON md.id_matricula = m.id
                INNER JOIN institucion_seccion s
                    ON m.id_institucion_seccion = s.id
                INNER JOIN institucion_grado g
                    ON s.id_institucion_grado = g.id
                INNER JOIN institucion_nivel niv
                    ON g.id_institucion_nivel = niv.id
                INNER JOIN institucion_lectivo il
                    ON niv.id_institucion_lectivo = il.id
                INNER JOIN institucion i
                    ON il.id_institucion = i.id
                INNER JOIN usuario_docente d
                    ON m.id_usuario_docente = d.id
                INNER JOIN usuario_alumno a
                    ON md.id_usuario_alumno = a.id
                
                WHERE
                    md.estado = 1
                    AND m.estado = 1
                    AND s.estado = 1
                    AND g.estado = 1
                    AND niv.estado = 1
                    AND il.estado = 1
                    AND i.estado = 1
                    AND d.estado = 1
                    AND a.estado = 1
                
                ORDER BY
                    niv.nombre ASC,
                    g.nombre ASC,
                    s.nombre ASC,
                    MONTH(a.nacimiento) ASC,
                    a.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
