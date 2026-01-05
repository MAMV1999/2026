<?php
require_once("../../database.php");

class Apoderadosmatriculados
{
    public function __construct()
    {
    }

    public function listar()
    {
        $sql = "SELECT t.tipo, t.nombreyapellido, t.telefono
                    FROM (
                    /* 1) DOCENTES ÚNICOS */
                    SELECT 'DOCENTE' AS tipo,
                            d.nombreyapellido,
                            d.telefono
                    FROM (
                        SELECT TRIM(nombreyapellido) AS nombreyapellido,
                            MAX(NULLIF(TRIM(COALESCE(telefono,'')),'')) AS telefono
                        FROM usuario_docente
                        WHERE estado = 1 AND TRIM(nombreyapellido) <> ''
                        GROUP BY UPPER(TRIM(nombreyapellido))
                    ) AS d
                    
                    UNION ALL
                    
                    /* 2) APODERADOS ÚNICOS QUE NO ESTÉN EN DOCENTES */
                    SELECT 'APODERADO' AS tipo,
                            a.nombreyapellido,
                            a.telefono
                    FROM (
                        SELECT TRIM(nombreyapellido) AS nombreyapellido,
                            MAX(NULLIF(TRIM(COALESCE(telefono,'')),'')) AS telefono
                        FROM usuario_apoderado
                        WHERE estado = 1 AND TRIM(nombreyapellido) <> ''
                        GROUP BY UPPER(TRIM(nombreyapellido))
                    ) AS a
                    LEFT JOIN (
                        SELECT UPPER(TRIM(nombreyapellido)) AS keyname
                        FROM usuario_docente
                        WHERE estado = 1 AND TRIM(nombreyapellido) <> ''
                        GROUP BY UPPER(TRIM(nombreyapellido))
                    ) AS dnames
                        ON UPPER(a.nombreyapellido) = dnames.keyname
                    WHERE dnames.keyname IS NULL
                    ) AS t
                    ORDER BY
                    CASE t.tipo WHEN 'docente' THEN 1 ELSE 2 END,
                    t.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
?>
