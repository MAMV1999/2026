<?php
require_once("../../database.php");

class AlmacenProducto
{
    public function __construct()
    {
    }

    // Método para guardar o editar múltiples productos a la vez
    public function guardarEditarMasivo($detalles)
    {
        try {
            global $conectar;
            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? limpiarcadena($detalle['id']) : null;
                $nombre = isset($detalle['nombre']) ? limpiarcadena($detalle['nombre']) : null;
                $categoria_id = isset($detalle['categoria_id']) ? limpiarcadena($detalle['categoria_id']) : null;
                $precio_compra = isset($detalle['precio_compra']) ? limpiarcadena($detalle['precio_compra']) : null;
                $precio_venta = isset($detalle['precio_venta']) ? limpiarcadena($detalle['precio_venta']) : null;
                $descripcion = isset($detalle['descripcion']) ? limpiarcadena($detalle['descripcion']) : '';

                if ($id) {
                    // Actualizar producto existente
                    $sql = "UPDATE almacen_producto SET nombre='$nombre', descripcion='$descripcion', categoria_id='$categoria_id', precio_compra='$precio_compra', precio_venta='$precio_venta' WHERE id='$id'";
                } else {
                    // Insertar nuevo producto
                    $sql = "INSERT INTO almacen_producto (nombre, descripcion, categoria_id, precio_compra, precio_venta, stock, estado) VALUES ('$nombre', '$descripcion', '$categoria_id', '$precio_compra', '$precio_venta', '0', '1')";
                }

                if (!ejecutarConsulta($sql)) {
                    error_log("Error en SQL: " . mysqli_error($conectar));
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error en guardarEditarMasivo: " . $e->getMessage());
            return false;
        }
    }

    // Método para mostrar los detalles de un producto específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM almacen_producto WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los productos
    public function listar()
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, c.nombre AS categoria, p.precio_compra, p.precio_venta, p.stock, p.estado FROM almacen_producto p INNER JOIN almacen_categoria c ON p.categoria_id = c.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un producto
    public function desactivar($id)
    {
        $sql = "UPDATE almacen_producto SET estado='0' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un producto
    public function activar($id)
    {
        $sql = "UPDATE almacen_producto SET estado='1' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    // Método para listar las categorías activas
    public function listarCategoriasActivas()
    {
        $sql = "SELECT id, nombre FROM almacen_categoria WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
