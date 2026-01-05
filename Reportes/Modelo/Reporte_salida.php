<?php
require_once("../../database.php");

class Reportesalida
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    s.id AS salida_id,
                    u.nombreyapellido AS nombre_apoderado,
                    c.nombre AS nombre_comprobante,
                    s.numeracion,
                    DATE_FORMAT(s.fecha, '%d/%m/%Y') AS fecha,
                    m.nombre AS metodo_pago,
                    s.total AS monto,
                    s.observaciones,
                    s.fechacreado,
                    s.estado
                FROM almacen_salida s
                JOIN usuario_apoderado u ON s.usuario_apoderado_id = u.id
                JOIN almacen_comprobante c ON s.almacen_comprobante_id = c.id
                JOIN almacen_metodo_pago m ON s.almacen_metodo_pago_id = m.id
                WHERE s.estado = 1
                ORDER BY s.fecha DESC, s.numeracion DESC";
        return ejecutarConsulta($sql);
    }

}
