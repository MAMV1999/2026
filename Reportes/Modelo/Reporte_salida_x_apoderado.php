<?php
require_once("../../database.php");

class Reportesalidaxapoderado
{
    public function __construct() {}

    public function listar()
    {
        $sql = "SELECT 
                    ua.id AS apoderado_id,
                    uat.nombre AS apoderado_tipo,
                    ud.nombre AS documento_tipo,
                    ua.numerodocumento,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono,
                    ass.id AS salida_id,
                    ass.numeracion AS salida_numeracion,
                    DATE_FORMAT(ass.fecha, '%d/%m/%Y') AS salida_fecha,
                    amp.nombre AS metodo_pago,
                    ass.total AS salida_total,
                    ass.estado AS salida_estado,
                    asd.id AS salida_detalle_id,
                    asd.almacen_producto_id,
                    asd.stock,
                    ap.nombre AS producto_nombre,
                    asd.precio_unitario
                FROM almacen_salida_detalle asd
                JOIN almacen_producto ap ON asd.almacen_producto_id = ap.id
                JOIN almacen_salida ass ON asd.almacen_salida_id = ass.id
                JOIN usuario_apoderado ua ON ass.usuario_apoderado_id = ua.id
                LEFT JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id
                LEFT JOIN usuario_documento ud ON ua.id_documento = ud.id
                LEFT JOIN almacen_metodo_pago amp ON ass.almacen_metodo_pago_id = amp.id
                WHERE ass.estado = 1
                ORDER BY ua.nombreyapellido ASC, ass.fecha ASC, ass.numeracion ASC";
        return ejecutarConsulta($sql);
    }

}
