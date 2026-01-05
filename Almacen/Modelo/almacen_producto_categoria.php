<?php
require_once("../../database.php");

class AlmacenProductoCategoria
{
    public function __construct() {}

    public function guardarEditarMasivo($detalles)
    {
        try {
            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? $detalle['id'] : null;
                $nombre = isset($detalle['nombre']) ? $detalle['nombre'] : null;
                $categoria_id = isset($detalle['categoria_id']) ? $detalle['categoria_id'] : null;
                $precio_compra = isset($detalle['precio_compra']) ? $detalle['precio_compra'] : null;
                $precio_venta = isset($detalle['precio_venta']) ? $detalle['precio_venta'] : null;
                $estado = isset($detalle['estado']) ? $detalle['estado'] : null;

                // Actualizar el registro con el ID correspondiente
                $sql = "UPDATE almacen_producto 
                            SET nombre = '$nombre', categoria_id = '$categoria_id', precio_compra = '$precio_compra', precio_venta = '$precio_venta', estado = '$estado' 
                        WHERE id = '$id'";
                if (!ejecutarConsulta($sql)) {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) { return false; }
    }


    // Método para listar
    public function listar()
    {
        $sql = "SELECT
                    p.categoria_id,
                    c.nombre AS categoria_nombre,
                    COUNT(p.id) AS cantidad_productos
                FROM almacen_producto p
                JOIN almacen_categoria c ON p.categoria_id = c.id 
                GROUP BY p.categoria_id, c.nombre";
        return ejecutarConsulta($sql);
    }

    // Método para listar
    public function listar_frm($id)
    {
        $sql = "SELECT 
                    id AS ids,
                    nombre AS nombres,
                    categoria_id AS categorias,
                    precio_compra AS precios_compra,
                    precio_venta AS precios_venta,
                    stock AS stocks,
                    estado AS estados
                FROM almacen_producto
                WHERE categoria_id = '$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar las categorías activas
    public function listarCategoriasActivas()
    {
        $sql = "SELECT id, nombre FROM almacen_categoria WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }
}
