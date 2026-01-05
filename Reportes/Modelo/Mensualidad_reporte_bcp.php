<?php
require_once("../../database.php");

class Mensualidadbcp
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                ua.numerodocumento AS CODIGO,
                LEFT(
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        REPLACE(REPLACE(ua.nombreyapellido,
                        'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),
                        'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                        'ñ','n'),'Ñ','N'),
                    40
                ) AS DEPOSITANTE,
                CONCAT(
                    TRIM(
                    SUBSTRING_INDEX(
                        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                            REPLACE(REPLACE(ua.nombreyapellido,
                            'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),
                            'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                            'ñ','n'),'Ñ','N'),
                        ' ', 
                        CHAR_LENGTH(ua.nombreyapellido) - CHAR_LENGTH(REPLACE(ua.nombreyapellido, ' ', '')) - 2
                    )
                    ),
                    ' ',
                    mm.nombre
                ) AS RETORNO,
                DATE_FORMAT(CURDATE(), '%d/%m/%Y') AS FECHA_EMISION,
                DATE_FORMAT(STR_TO_DATE(mm.fechavencimiento, '%Y-%m-%d'), '%d/%m/%Y') AS FECHA_VENCIMIENTO,
                md.MONTO,
                1.50 AS MORA,
                md.monto AS MONTO_MINIMO,
                'Agregar' AS REGISTRO,
                mm.nombre AS DOCUMENTO
                FROM mensualidad_detalle md
                INNER JOIN mensualidad_mes mm ON md.id_mensualidad_mes = mm.id
                INNER JOIN matricula_detalle mdet ON md.id_matricula_detalle = mdet.id
                INNER JOIN usuario_alumno ua ON mdet.id_usuario_alumno = ua.id
                ORDER BY ua.nombreyapellido ASC, mm.fechavencimiento ASC";
        return ejecutarConsulta($sql);
    }
}
