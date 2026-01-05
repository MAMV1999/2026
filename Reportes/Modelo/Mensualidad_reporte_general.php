<?php
require_once("../../database.php");

class Mensualidad_reporte_general
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT
                    md.id_mensualidad_mes,
                    CONCAT(mm.nombre, ' ', il.nombre) AS nombre_mes,
                    COUNT(CASE WHEN md.pagado = 0 THEN 1 END) AS deudor,
                    SUM(CASE WHEN md.pagado = 0 THEN md.monto ELSE 0 END) AS suma_deuda,
                    COUNT(CASE WHEN md.pagado = 1 THEN 1 END) AS cancelado,
                    SUM(CASE WHEN md.pagado = 1 THEN md.monto ELSE 0 END) AS suma_cancelado,
                    CASE WHEN SUM(md.pagado) = COUNT(md.id) THEN 1 ELSE 0 END AS mes_cerrado
                FROM mensualidad_detalle md
                JOIN mensualidad_mes mm ON md.id_mensualidad_mes = mm.id AND mm.estado = 1
                JOIN institucion_lectivo il ON mm.id_institucion_lectivo = il.id
                WHERE md.estado = 1
                GROUP BY md.id_mensualidad_mes, mm.nombre, il.nombre
                ORDER BY md.id_mensualidad_mes";
        return ejecutarConsulta($sql);
    }
}
