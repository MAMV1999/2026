<?php
require_once("../../database.php");

class Almacenproducto
{
    public function __construct()
    {
    }

    public function listar()
    {
        $sql = "SELECT 
                    p.id, 
                    p.nombre AS nombre_producto,
                    p.descripcion,
                    c.nombre AS categoria,
                    p.precio_compra,
                    p.precio_venta,
                    p.stock,
                    p.fechacreado,
                    p.estado,
                    CASE 
                        WHEN p.estado = 1 THEN 'ACTIVO'
                        WHEN p.estado = 0 THEN 'DESACTIVADO'
                        ELSE 'DESCONOCIDO' -- En caso de valores inesperados
                    END AS estado_texto
                FROM almacen_producto p
                LEFT JOIN almacen_categoria c ON p.categoria_id = c.id";
        return ejecutarConsulta($sql);
    }
}
?>
