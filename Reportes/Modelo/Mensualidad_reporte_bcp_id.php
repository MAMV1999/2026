<?php
require_once("../../database.php");

class Mensualidadbcp
{
    public function __construct() {}

    public function listar($id)
    {
        $sql = "SELECT 
                    md.id_matricula_detalle AS ID,
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
                        SUBSTRING_INDEX(
                            REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                            REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                                REPLACE(REPLACE(ua.nombreyapellido,
                                'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),
                                'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                'ñ','n'),'Ñ','N'),
                            ' ', 2
                        ),
                        ' ',
                        mm.nombre
                    ) AS RETORNO,
                
                    DATE_FORMAT(CURDATE(), '%d/%m/%Y') AS FECHA_EMISION,
                    DATE_FORMAT(mm.fecha_vencimiento, '%d/%m/%Y') AS FECHA_VENCIMIENTO,
                
                    md.monto AS MONTO,
                    1.50 AS MORA,
                    md.monto AS MONTO_MINIMO,
                
                    'Agregar' AS REGISTRO,
                    mm.nombre AS DOCUMENTO
                
                FROM mensualidad_detalle md
                INNER JOIN matricula_mes mm 
                    ON md.matricula_mes_id = mm.id
                INNER JOIN matricula_detalle mdet 
                    ON md.id_matricula_detalle = mdet.id
                INNER JOIN usuario_alumno ua 
                    ON mdet.id_usuario_alumno = ua.id
                
                WHERE md.id_matricula_detalle = '$id'
                AND md.monto > 0
                AND md.pagado = 0
                
                ORDER BY ua.nombreyapellido ASC, mm.fecha_vencimiento ASC";
        return ejecutarConsulta($sql);
    }
}
