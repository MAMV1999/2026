<?php
require_once("../../database.php");

class AlmacenSalida
{
    public function __construct() {}

    public function guardar($usuario_apoderado_id, $almacen_comprobante_id, $numeracion, $fecha, $almacen_metodo_pago_id, $total, $observaciones, $productos)
    {
        try {
            $usuario_apoderado_id = limpiarcadena($usuario_apoderado_id);
            $almacen_comprobante_id = limpiarcadena($almacen_comprobante_id);
            $numeracion = limpiarcadena($numeracion);
            $fecha = limpiarcadena($fecha);
            $almacen_metodo_pago_id = limpiarcadena($almacen_metodo_pago_id);
            $total = limpiarcadena($total);
            $observaciones = limpiarcadena($observaciones);

            $sqlSalida = "INSERT INTO almacen_salida (usuario_apoderado_id, almacen_comprobante_id, numeracion, fecha, almacen_metodo_pago_id, total, observaciones)
                         VALUES ('$usuario_apoderado_id', '$almacen_comprobante_id', '$numeracion', '$fecha', '$almacen_metodo_pago_id', '$total', '$observaciones')";
            $almacen_salida_id = ejecutarConsulta_retornarID($sqlSalida);

            foreach ($productos as $producto) {
                $almacen_producto_id = limpiarcadena($producto['almacen_producto_id']);
                $stock = limpiarcadena($producto['stock']);
                $precio_unitario = limpiarcadena($producto['precio_unitario']);
                $observaciones_producto = limpiarcadena($producto['observaciones']);

                $sqlDetalle = "INSERT INTO almacen_salida_detalle (almacen_salida_id, almacen_producto_id, stock, precio_unitario, observaciones)
                               VALUES ('$almacen_salida_id', '$almacen_producto_id', '$stock', '$precio_unitario', '$observaciones_producto')";
                ejecutarConsulta($sqlDetalle);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // =========================
    // NUEVO: MOSTRAR CABECERA
    // =========================
    public function mostrar($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT 
                    asd.id,
                    asd.usuario_apoderado_id,
                    ua.nombreyapellido AS nombre_apoderado,
                    asd.almacen_comprobante_id,
                    ac.nombre AS nombre_comprobante,
                    asd.numeracion,
                    DATE_FORMAT(asd.fecha, '%Y-%m-%d') AS fecha,
                    asd.almacen_metodo_pago_id,
                    amp.nombre AS metodo_pago,
                    asd.total,
                    asd.observaciones,
                    asd.estado
                FROM almacen_salida asd
                LEFT JOIN usuario_apoderado ua ON asd.usuario_apoderado_id = ua.id
                LEFT JOIN almacen_comprobante ac ON asd.almacen_comprobante_id = ac.id
                LEFT JOIN almacen_metodo_pago amp ON asd.almacen_metodo_pago_id = amp.id
                WHERE asd.id = '$id'
                LIMIT 1";
        return ejecutarConsultaSimpleFila($sql);
    }

    // =========================
    // NUEVO: LISTAR DETALLE
    // =========================
    public function listar_detalle($almacen_salida_id)
    {
        $almacen_salida_id = limpiarcadena($almacen_salida_id);

        $sql = "SELECT 
                    d.id,
                    d.almacen_producto_id,
                    p.nombre AS producto,
                    p.descripcion,
                    d.stock,
                    d.precio_unitario,
                    d.observaciones
                FROM almacen_salida_detalle d
                INNER JOIN almacen_producto p ON d.almacen_producto_id = p.id
                WHERE d.almacen_salida_id = '$almacen_salida_id'
                ORDER BY d.id ASC";
        return ejecutarConsulta($sql);
    }

    // =========================
    // NUEVO: EDITAR (CABECERA + DETALLE)
    // =========================
    public function editar($id, $usuario_apoderado_id, $almacen_comprobante_id, $numeracion, $fecha, $almacen_metodo_pago_id, $total, $observaciones, $productos)
    {
        try {
            $id = limpiarcadena($id);
            $usuario_apoderado_id = limpiarcadena($usuario_apoderado_id);
            $almacen_comprobante_id = limpiarcadena($almacen_comprobante_id);
            $numeracion = limpiarcadena($numeracion);
            $fecha = limpiarcadena($fecha);
            $almacen_metodo_pago_id = limpiarcadena($almacen_metodo_pago_id);
            $total = limpiarcadena($total);
            $observaciones = limpiarcadena($observaciones);

            // 1) Actualizar cabecera
            $sqlUpdate = "UPDATE almacen_salida SET 
                            usuario_apoderado_id = '$usuario_apoderado_id',
                            almacen_comprobante_id = '$almacen_comprobante_id',
                            numeracion = '$numeracion',
                            fecha = '$fecha',
                            almacen_metodo_pago_id = '$almacen_metodo_pago_id',
                            total = '$total',
                            observaciones = '$observaciones'
                          WHERE id = '$id'";
            $okCab = ejecutarConsulta($sqlUpdate);
            if (!$okCab) return ["ok" => false, "msg" => "No se pudo actualizar la cabecera."];

            // 2) Obtener detalle actual
            $sqlOld = "SELECT id, almacen_producto_id, stock FROM almacen_salida_detalle WHERE almacen_salida_id = '$id'";
            $rsOld = ejecutarConsulta($sqlOld);

            $old = [];      // [producto_id] => stock
            $oldRowId = []; // [producto_id] => detalle_id
            while ($r = $rsOld->fetch_object()) {
                $old[(string)$r->almacen_producto_id] = (float)$r->stock;
                $oldRowId[(string)$r->almacen_producto_id] = (int)$r->id;
            }

            // 3) Normalizar nuevo detalle
            $new = []; // [producto_id] => array(data)
            foreach ($productos as $prod) {
                $pid = (string)limpiarcadena($prod['almacen_producto_id']);
                $stk = (float)limpiarcadena($prod['stock']);
                $pu  = limpiarcadena($prod['precio_unitario']);
                $obs = limpiarcadena($prod['observaciones']);

                $new[$pid] = [
                    "almacen_producto_id" => $pid,
                    "stock" => $stk,
                    "precio_unitario" => $pu,
                    "observaciones" => $obs
                ];
            }

            // 4) Eliminados: estaban antes y ya no están ahora => devolver stock + eliminar fila detalle
            foreach ($old as $pid => $oldQty) {
                if (!isset($new[$pid])) {
                    // devolver stock
                    $sqlRest = "UPDATE almacen_producto SET stock = stock + " . floatval($oldQty) . " WHERE id = '$pid'";
                    $okRest = ejecutarConsulta($sqlRest);
                    if (!$okRest) return ["ok" => false, "msg" => "No se pudo devolver stock del producto ID $pid."];

                    // eliminar detalle
                    $detalle_id = $oldRowId[$pid];
                    $sqlDel = "DELETE FROM almacen_salida_detalle WHERE id = '$detalle_id'";
                    $okDel = ejecutarConsulta($sqlDel);
                    if (!$okDel) return ["ok" => false, "msg" => "No se pudo eliminar detalle del producto ID $pid."];
                }
            }

            // 5) Actualizados: existen en ambos => ajustar stock por delta + actualizar fila detalle
            foreach ($new as $pid => $ndata) {
                if (isset($old[$pid])) {
                    $oldQty = (float)$old[$pid];
                    $newQty = (float)$ndata["stock"];
                    $delta = $newQty - $oldQty;

                    if ($delta > 0) {
                        // se vendió más => restar stock adicional
                        // validar stock disponible
                        $fila = ejecutarConsultaSimpleFila("SELECT stock FROM almacen_producto WHERE id = '$pid' LIMIT 1");
                        $disp = $fila ? (float)$fila["stock"] : 0;
                        if ($disp < $delta) {
                            return ["ok" => false, "msg" => "Stock insuficiente para el producto ID $pid. Disponible: $disp, requerido adicional: $delta."];
                        }

                        $sqlAdj = "UPDATE almacen_producto SET stock = stock - " . floatval($delta) . " WHERE id = '$pid'";
                        $okAdj = ejecutarConsulta($sqlAdj);
                        if (!$okAdj) return ["ok" => false, "msg" => "No se pudo descontar stock adicional del producto ID $pid."];
                    } elseif ($delta < 0) {
                        // se vendió menos => devolver stock
                        $sqlAdj = "UPDATE almacen_producto SET stock = stock + " . floatval(abs($delta)) . " WHERE id = '$pid'";
                        $okAdj = ejecutarConsulta($sqlAdj);
                        if (!$okAdj) return ["ok" => false, "msg" => "No se pudo devolver stock del producto ID $pid."];
                    }

                    // actualizar detalle
                    $detalle_id = $oldRowId[$pid];
                    $pu  = $ndata["precio_unitario"];
                    $obs = $ndata["observaciones"];

                    $sqlUpdDet = "UPDATE almacen_salida_detalle SET 
                                    stock = '$newQty',
                                    precio_unitario = '$pu',
                                    observaciones = '$obs'
                                  WHERE id = '$detalle_id'";
                    $okUpdDet = ejecutarConsulta($sqlUpdDet);
                    if (!$okUpdDet) return ["ok" => false, "msg" => "No se pudo actualizar detalle del producto ID $pid."];
                }
            }

            // 6) Nuevos: no estaban antes => insertar (el trigger AFTER INSERT descuenta stock)
            foreach ($new as $pid => $ndata) {
                if (!isset($old[$pid])) {
                    $newQty = (float)$ndata["stock"];

                    // validar stock disponible antes de insertar (porque el trigger restará)
                    $fila = ejecutarConsultaSimpleFila("SELECT stock FROM almacen_producto WHERE id = '$pid' LIMIT 1");
                    $disp = $fila ? (float)$fila["stock"] : 0;
                    if ($disp < $newQty) {
                        return ["ok" => false, "msg" => "Stock insuficiente para el producto ID $pid. Disponible: $disp, requerido: $newQty."];
                    }

                    $pu  = $ndata["precio_unitario"];
                    $obs = $ndata["observaciones"];

                    $sqlIns = "INSERT INTO almacen_salida_detalle (almacen_salida_id, almacen_producto_id, stock, precio_unitario, observaciones)
                               VALUES ('$id', '$pid', '$newQty', '$pu', '$obs')";
                    $okIns = ejecutarConsulta($sqlIns);
                    if (!$okIns) return ["ok" => false, "msg" => "No se pudo insertar detalle del producto ID $pid."];
                }
            }

            return ["ok" => true, "msg" => "Registro editado correctamente"];
        } catch (Exception $e) {
            return ["ok" => false, "msg" => "Error en edición"];
        }
    }

    public function listar()
    {
        $sql = "SELECT 
                    asd.id,
                    ua.nombreyapellido AS nombre_apoderado,
                    ac.nombre AS nombre_comprobante,
                    asd.numeracion,
                    DATE_FORMAT(asd.fecha, '%d/%m/%Y') AS fecha,
                    amp.nombre AS metodo_pago,
                    asd.total,
                    asd.observaciones,
                    asd.fechacreado,
                    asd.estado
                FROM almacen_salida asd
                LEFT JOIN usuario_apoderado ua ON asd.usuario_apoderado_id = ua.id
                LEFT JOIN almacen_comprobante ac ON asd.almacen_comprobante_id = ac.id
                LEFT JOIN almacen_metodo_pago amp ON asd.almacen_metodo_pago_id = amp.id
                ORDER BY asd.fecha DESC, asd.numeracion DESC";
        return ejecutarConsulta($sql);
    }

    public function listar_buscador_apoderado()
    {
        $sql = "SELECT 
                    ua.id,
                    ua.nombreyapellido AS apoderado,
                    GROUP_CONCAT(ual.nombreyapellido SEPARATOR ', ') AS alumnos
                FROM usuario_apoderado ua
                LEFT JOIN usuario_alumno ual ON ua.id = ual.id_apoderado
                WHERE ua.estado = 1
                GROUP BY ua.id, ua.nombreyapellido
                ORDER BY ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_usuario_apoderado()
    {
        $sql = "SELECT 
                    ua.id AS id_apoderado,
                    ua.id_apoderado_tipo,
                    uat.nombre AS tipo_apoderado,
                    ua.id_documento,
                    ud.nombre AS documento,
                    ua.numerodocumento,
                    ua.nombreyapellido,
                    ua.telefono,
                    ua.usuario,
                    ua.clave,
                    ua.observaciones,
                    ua.fechacreado,
                    ua.estado
                FROM usuario_apoderado ua
                INNER JOIN usuario_documento ud ON ua.id_documento = ud.id AND ud.estado = 1
                INNER JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id AND uat.estado = 1
                WHERE ua.estado = 1
                ORDER BY ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_almacen_comprobante()
    {
        $sql = "SELECT 
                    ac.id AS id_comprobante,
                    ac.nombre AS nombre_comprobante,
                    ac.observaciones,
                    ac.fechacreado,
                    ac.estado
                FROM almacen_comprobante ac
                WHERE ac.estado = 1
                ORDER BY ac.nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_almacen_metodo_pago()
    {
        $sql = "SELECT 
                    amp.id AS id_metodo_pago,
                    amp.nombre AS metodo_pago,
                    amp.observaciones,
                    amp.fechacreado,
                    amp.estado
                FROM almacen_metodo_pago amp
                WHERE amp.estado = 1
                ORDER BY amp.nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_almacen_producto()
    {
        $sql = "SELECT 
                    ap.id AS id_producto,
                    ap.nombre AS producto,
                    ap.descripcion,
                    ap.categoria_id,
                    ac.nombre AS categoria,
                    ap.precio_compra,
                    ap.precio_venta,
                    ap.stock,
                    ap.fechacreado,
                    ap.estado
                FROM almacen_producto ap
                INNER JOIN almacen_categoria ac ON ap.categoria_id = ac.id AND ac.estado = 1
                WHERE ap.estado = 1 AND ap.stock > 0
                ORDER BY ap.nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function numeracion()
    {
        $sql = "SELECT LPAD(IFNULL(MAX(CAST(numeracion AS UNSIGNED)) + 1, 1), 6, '0') AS numeracion FROM almacen_salida";
        $result = ejecutarConsultaSimpleFila($sql);
        return $result ? $result['numeracion'] : '000001';
    }

    public function activar($id)
    {
        $sql = "UPDATE almacen_salida SET estado = 1 WHERE id = '$id'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE almacen_salida SET estado = 0 WHERE id = '$id'";
        return ejecutarConsulta($sql);
    }
}
