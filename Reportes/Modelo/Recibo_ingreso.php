<?php
require_once("../../database.php");

class Recibosalida
{
    public function __construct() {}

    public function listar_institucion()
    {
        $sql = "SELECT 
                    i.id AS institucion_id,
                    i.nombre AS institucion_nombre,
                    i.id_usuario_docente AS institucion_docente_id,
                    u.nombreyapellido AS docente_nombre,
                    i.telefono AS institucion_telefono,
                    i.correo AS institucion_correo,
                    i.ruc AS institucion_ruc,
                    i.razon_social AS institucion_razon_social,
                    i.direccion AS institucion_direccion,
                    i.observaciones AS institucion_observaciones,
                    i.fechacreado AS institucion_fecha_creacion,
                    i.estado AS institucion_estado
                FROM institucion i
                LEFT JOIN usuario_docente u ON i.id_usuario_docente = u.id";
        return ejecutarConsulta($sql);
    }

    public function listar_almacen_ingreso($id)
    {
        $sql = "SELECT 
                    almacen_ingreso.id AS salida_id,
                    almacen_ingreso.usuario_apoderado_id AS apoderado_id,
                    usuario_apoderado.nombreyapellido AS apoderado_nombre,
                    usuario_apoderado.telefono AS apoderado_telefono,
                    almacen_ingreso.almacen_comprobante_id AS comprobante_id,
                    almacen_comprobante.nombre AS comprobante_nombre,
                    almacen_ingreso.numeracion AS salida_numeracion,
                    DATE_FORMAT(almacen_ingreso.fecha, '%d/%m/%Y') AS salida_fecha,
                    almacen_ingreso.almacen_metodo_pago_id AS metodo_pago_id,
                    almacen_metodo_pago.nombre AS metodo_pago_nombre,
                    almacen_ingreso.total AS salida_total,
                    almacen_ingreso.observaciones AS salida_observaciones,
                    almacen_ingreso.fechacreado AS salida_fechacreado,
                    almacen_ingreso.estado AS salida_estado,
                    CASE 
                        WHEN almacen_ingreso.estado = 1 THEN 'ACEPTADO'
                        WHEN almacen_ingreso.estado = 0 THEN 'ANULADO'
                        ELSE 'desconocido'
                    END AS salida_estado_observaciones
                FROM almacen_ingreso
                LEFT JOIN usuario_apoderado ON almacen_ingreso.usuario_apoderado_id = usuario_apoderado.id
                LEFT JOIN almacen_comprobante ON almacen_ingreso.almacen_comprobante_id = almacen_comprobante.id
                LEFT JOIN almacen_metodo_pago ON almacen_ingreso.almacen_metodo_pago_id = almacen_metodo_pago.id
                WHERE almacen_ingreso.id = '$id'";
        return ejecutarConsulta($sql);
    }

    public function listar_almacen_ingreso_detalle($id)
    {
        $sql = "SELECT 
                    almacen_ingreso_detalle.id AS detalle_id,
                    almacen_ingreso_detalle.almacen_ingreso_id AS salida_id,
                    almacen_ingreso_detalle.almacen_producto_id AS producto_id,
                    almacen_producto.nombre AS producto_nombre,
                    almacen_ingreso_detalle.stock AS detalle_stock,
                    almacen_ingreso_detalle.precio_unitario AS detalle_precio_unitario,
                    almacen_ingreso_detalle.observaciones AS detalle_observaciones
                FROM almacen_ingreso_detalle
                LEFT JOIN almacen_producto ON almacen_ingreso_detalle.almacen_producto_id = almacen_producto.id
                WHERE almacen_ingreso_detalle.almacen_ingreso_id = '$id'";
        return ejecutarConsulta($sql);
    }
}
