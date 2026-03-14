<?php
require_once("../../database.php");

class Asistencia_blanco
{
    public function __construct() {}
    
    public function listarAsistencia_blanco()
{
    $columnas = "";

    $inicio = strtotime("2026-03-01");
    $fin = strtotime("2026-12-31");

    $meses = [
        '03' => 'MAR',
        '04' => 'ABR',
        '05' => 'MAY',
        '06' => 'JUN',
        '07' => 'JUL',
        '08' => 'AGO',
        '09' => 'SEP',
        '10' => 'OCT',
        '11' => 'NOV',
        '12' => 'DIC'
    ];

    for ($fecha = $inicio; $fecha <= $fin; $fecha += 86400) {

        $dia_semana = date("N", $fecha);

        if ($dia_semana <= 5) {

            $mesNumero = date("m", $fecha);
            $mes = $meses[$mesNumero];
            $dia = date("d", $fecha);

            $columnas .= ", '' AS `{$mes}_{$dia}`";
        }
    }

    $sql = "
    SELECT
        i.nombre AS institucion,
        niv.nombre AS nivel,
        g.nombre AS grado,
        s.nombre AS seccion,
        d.nombreyapellido AS tutor,
        IFNULL(d.telefono,'') AS telefono_tutor,
        a.nombreyapellido AS alumno
        $columnas
    FROM matricula_detalle md
    INNER JOIN matricula m ON m.id = md.id_matricula AND m.estado = 1
    INNER JOIN institucion_seccion s ON s.id = m.id_institucion_seccion AND s.estado = 1
    INNER JOIN institucion_grado g ON g.id = s.id_institucion_grado AND g.estado = 1
    INNER JOIN institucion_nivel niv ON niv.id = g.id_institucion_nivel AND niv.estado = 1
    INNER JOIN institucion_lectivo il ON il.id = niv.id_institucion_lectivo AND il.estado = 1
    INNER JOIN institucion i ON i.id = il.id_institucion AND i.estado = 1
    INNER JOIN usuario_docente d ON d.id = m.id_usuario_docente AND d.estado = 1
    INNER JOIN usuario_alumno a ON a.id = md.id_usuario_alumno AND a.estado = 1
    WHERE md.estado = 1
    ORDER BY
        niv.nombre ASC,
        g.nombre ASC,
        s.nombre ASC,
        a.nombreyapellido ASC
    ";

    return ejecutarConsulta($sql);
}
}