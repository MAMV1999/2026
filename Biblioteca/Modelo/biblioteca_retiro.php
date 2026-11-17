<?php
require_once("../../database.php");

class BibliotecaRetiro
{
    public function __construct()
    {
    }

    public function guardar($usuario_docente_id, $fecha, $observaciones, $libros)
    {
        global $conectar;

        try {
            $usuario_docente_id = limpiarcadena($usuario_docente_id);
            $fecha = limpiarcadena($fecha);
            $observaciones = limpiarcadena($observaciones);

            if (empty($libros)) {
                throw new Exception("Debe agregar al menos un libro al retiro.");
            }

            $conectar->begin_transaction();

            $sqlRetiro = "INSERT INTO biblioteca_retiro (usuario_docente_id, fecha, observaciones, estado, devuelto)
                          VALUES ('$usuario_docente_id', '$fecha', '$observaciones', '1', '0')";
            $biblioteca_retiro_id = ejecutarConsulta_retornarID($sqlRetiro);

            if (!$biblioteca_retiro_id) {
                throw new Exception("No se pudo registrar la cabecera del retiro.");
            }

            $codigo_retiro = 'RET-' . str_replace('-', '', $fecha) . '-' . str_pad($biblioteca_retiro_id, 6, '0', STR_PAD_LEFT);
            if (!ejecutarConsulta("UPDATE biblioteca_retiro SET codigo_retiro='$codigo_retiro' WHERE id='$biblioteca_retiro_id'")) {
                throw new Exception("No se pudo generar el código del retiro.");
            }

            foreach ($libros as $libro) {
                $biblioteca_libro_id = limpiarcadena($libro['biblioteca_libro_id']);
                $cantidad = (int)$libro['cantidad'];
                $observaciones_libro = isset($libro['observaciones']) ? limpiarcadena($libro['observaciones']) : '';

                if ($cantidad <= 0) {
                    throw new Exception("La cantidad debe ser mayor a cero.");
                }

                $fila = ejecutarConsultaSimpleFila("SELECT stock, estado FROM biblioteca_libro WHERE id='$biblioteca_libro_id' LIMIT 1 FOR UPDATE");
                if (!$fila || $fila['estado'] != 1) {
                    throw new Exception("Uno de los libros no está disponible.");
                }

                if ((int)$fila['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente para uno de los libros.");
                }

                $sqlDetalle = "INSERT INTO biblioteca_retiro_detalle (biblioteca_retiro_id, biblioteca_libro_id, cantidad, observaciones, estado)
                               VALUES ('$biblioteca_retiro_id', '$biblioteca_libro_id', '$cantidad', '$observaciones_libro', '1')";
                if (!ejecutarConsulta($sqlDetalle)) {
                    throw new Exception("No se pudo guardar el detalle del retiro.");
                }

                $sqlStock = "UPDATE biblioteca_libro SET stock = stock - $cantidad WHERE id='$biblioteca_libro_id' AND stock >= $cantidad";
                if (!ejecutarConsulta($sqlStock) || $conectar->affected_rows == 0) {
                    throw new Exception("No se pudo actualizar el stock del libro.");
                }
            }

            $conectar->commit();
            return ["ok" => true, "msg" => "Retiro $codigo_retiro guardado correctamente"];
        } catch (Exception $e) {
            $conectar->rollback();
            return ["ok" => false, "msg" => $e->getMessage()];
        }
    }

    public function editar($id, $usuario_docente_id, $fecha, $observaciones, $libros)
    {
        global $conectar;

        try {
            $id = limpiarcadena($id);
            $usuario_docente_id = limpiarcadena($usuario_docente_id);
            $fecha = limpiarcadena($fecha);
            $observaciones = limpiarcadena($observaciones);

            if (empty($libros)) {
                throw new Exception("Debe agregar al menos un libro al retiro.");
            }

            $conectar->begin_transaction();

            $cabecera = ejecutarConsultaSimpleFila("SELECT estado, devuelto FROM biblioteca_retiro WHERE id='$id' LIMIT 1 FOR UPDATE");
            if (!$cabecera || $cabecera['estado'] != 1) {
                throw new Exception("Solo se pueden editar retiros activos.");
            }
            if ((int)$cabecera['devuelto'] == 1) {
                throw new Exception("No se puede editar un retiro que ya fue devuelto.");
            }

            $rsAnterior = ejecutarConsulta("SELECT biblioteca_libro_id, cantidad FROM biblioteca_retiro_detalle WHERE biblioteca_retiro_id='$id' AND estado=1");
            while ($anterior = $rsAnterior->fetch_object()) {
                $libro_id = (int)$anterior->biblioteca_libro_id;
                $cantidad_anterior = (int)$anterior->cantidad;
                if (!ejecutarConsulta("UPDATE biblioteca_libro SET stock = stock + $cantidad_anterior WHERE id='$libro_id'")) {
                    throw new Exception("No se pudo restaurar el stock anterior.");
                }
            }

            if (!ejecutarConsulta("DELETE FROM biblioteca_retiro_detalle WHERE biblioteca_retiro_id='$id'")) {
                throw new Exception("No se pudo actualizar el detalle del retiro.");
            }

            $sqlCabecera = "UPDATE biblioteca_retiro SET
                                usuario_docente_id='$usuario_docente_id',
                                fecha='$fecha',
                                observaciones='$observaciones'
                            WHERE id='$id'";
            if (!ejecutarConsulta($sqlCabecera)) {
                throw new Exception("No se pudo actualizar el retiro.");
            }

            foreach ($libros as $libro) {
                $biblioteca_libro_id = limpiarcadena($libro['biblioteca_libro_id']);
                $cantidad = (int)$libro['cantidad'];
                $observaciones_libro = isset($libro['observaciones']) ? limpiarcadena($libro['observaciones']) : '';

                if ($cantidad <= 0) {
                    throw new Exception("La cantidad debe ser mayor a cero.");
                }

                $fila = ejecutarConsultaSimpleFila("SELECT stock, estado FROM biblioteca_libro WHERE id='$biblioteca_libro_id' LIMIT 1 FOR UPDATE");
                if (!$fila || $fila['estado'] != 1 || (int)$fila['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente o libro no disponible.");
                }

                $sqlDetalle = "INSERT INTO biblioteca_retiro_detalle (biblioteca_retiro_id, biblioteca_libro_id, cantidad, observaciones, estado)
                               VALUES ('$id', '$biblioteca_libro_id', '$cantidad', '$observaciones_libro', '1')";
                if (!ejecutarConsulta($sqlDetalle)) {
                    throw new Exception("No se pudo guardar el nuevo detalle.");
                }

                $sqlStock = "UPDATE biblioteca_libro SET stock = stock - $cantidad WHERE id='$biblioteca_libro_id' AND stock >= $cantidad";
                if (!ejecutarConsulta($sqlStock) || $conectar->affected_rows == 0) {
                    throw new Exception("No se pudo descontar el stock del libro.");
                }
            }

            $conectar->commit();
            return ["ok" => true, "msg" => "Retiro actualizado correctamente"];
        } catch (Exception $e) {
            $conectar->rollback();
            return ["ok" => false, "msg" => $e->getMessage()];
        }
    }

    public function mostrar($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                    br.id,
                    br.codigo_retiro,
                    br.usuario_docente_id,
                    ud.nombreyapellido AS docente,
                    DATE_FORMAT(br.fecha, '%Y-%m-%d') AS fecha,
                    br.observaciones,
                    br.estado,
                    br.devuelto,
                    DATE_FORMAT(br.fecha_devolucion, '%Y-%m-%d %H:%i:%s') AS fecha_devolucion
                FROM biblioteca_retiro br
                INNER JOIN usuario_docente ud ON br.usuario_docente_id = ud.id
                WHERE br.id='$id'
                LIMIT 1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_detalle($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                    brd.id,
                    brd.biblioteca_libro_id,
                    bl.codigo,
                    bl.nombre AS libro,
                    bl.stock AS stock_actual,
                    brd.cantidad,
                    brd.observaciones
                FROM biblioteca_retiro_detalle brd
                INNER JOIN biblioteca_libro bl ON brd.biblioteca_libro_id = bl.id
                WHERE brd.biblioteca_retiro_id='$id'
                AND brd.estado=1
                ORDER BY brd.id ASC";
        return ejecutarConsulta($sql);
    }

    public function listar()
    {
        $sql = "SELECT
                    br.id,
                    br.codigo_retiro,
                    ud.nombreyapellido AS docente,
                    DATE_FORMAT(br.fecha, '%d/%m/%Y') AS fecha,
                    br.observaciones,
                    br.estado,
                    br.devuelto,
                    DATE_FORMAT(br.fecha_devolucion, '%d/%m/%Y %H:%i') AS fecha_devolucion,
                    CASE
                        WHEN br.estado=0 THEN 'ANULADO'
                        WHEN br.devuelto=1 THEN 'DEVUELTO'
                        ELSE 'PENDIENTE'
                    END AS estado_texto,
                    COALESCE(SUM(brd.cantidad), 0) AS cantidad_libros
                FROM biblioteca_retiro br
                INNER JOIN usuario_docente ud ON br.usuario_docente_id = ud.id
                LEFT JOIN biblioteca_retiro_detalle brd ON br.id = brd.biblioteca_retiro_id AND brd.estado=1
                GROUP BY br.id, br.codigo_retiro, ud.nombreyapellido, br.fecha, br.observaciones, br.estado, br.devuelto, br.fecha_devolucion
                ORDER BY br.fecha DESC, br.id DESC";
        return ejecutarConsulta($sql);
    }

    public function listar_docentes()
    {
        $sql = "SELECT id, nombreyapellido FROM usuario_docente WHERE estado=1 ORDER BY nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_libros()
    {
        $sql = "SELECT id, codigo, nombre, stock FROM biblioteca_libro WHERE estado=1 ORDER BY nombre ASC, codigo ASC";
        return ejecutarConsulta($sql);
    }

    public function mostrar_libro($id)
    {
        $id = limpiarcadena($id);
        $sql = "SELECT id, codigo, nombre, stock FROM biblioteca_libro WHERE id='$id' AND estado=1 LIMIT 1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function devolver($id)
    {
        global $conectar;

        try {
            $id = limpiarcadena($id);
            $conectar->begin_transaction();

            $cabecera = ejecutarConsultaSimpleFila("SELECT estado, devuelto FROM biblioteca_retiro WHERE id='$id' LIMIT 1 FOR UPDATE");
            if (!$cabecera) {
                throw new Exception("Retiro no encontrado.");
            }
            if ((int)$cabecera['estado'] != 1) {
                throw new Exception("No se puede registrar la devolución de un retiro anulado.");
            }
            if ((int)$cabecera['devuelto'] == 1) {
                throw new Exception("Este retiro ya fue marcado como devuelto.");
            }

            $detalle = ejecutarConsulta("SELECT biblioteca_libro_id, cantidad FROM biblioteca_retiro_detalle WHERE biblioteca_retiro_id='$id' AND estado=1");
            $cantidad_detalles = 0;
            while ($reg = $detalle->fetch_object()) {
                $libro_id = (int)$reg->biblioteca_libro_id;
                $cantidad = (int)$reg->cantidad;
                if ($cantidad <= 0) {
                    throw new Exception("Se encontró una cantidad inválida en el detalle del retiro.");
                }
                if (!ejecutarConsulta("UPDATE biblioteca_libro SET stock = stock + $cantidad WHERE id='$libro_id'")) {
                    throw new Exception("No se pudo devolver el stock de uno de los libros.");
                }
                $cantidad_detalles++;
            }

            if ($cantidad_detalles == 0) {
                throw new Exception("El retiro no tiene libros registrados.");
            }

            if (!ejecutarConsulta("UPDATE biblioteca_retiro SET devuelto=1, fecha_devolucion=NOW() WHERE id='$id' AND estado=1 AND devuelto=0") || $conectar->affected_rows == 0) {
                throw new Exception("No se pudo marcar el retiro como devuelto.");
            }

            $conectar->commit();
            return ["ok" => true, "msg" => "Devolución registrada correctamente. El stock fue actualizado."];
        } catch (Exception $e) {
            $conectar->rollback();
            return ["ok" => false, "msg" => $e->getMessage()];
        }
    }

    public function desactivar($id)
    {
        global $conectar;

        try {
            $id = limpiarcadena($id);
            $conectar->begin_transaction();

            $cabecera = ejecutarConsultaSimpleFila("SELECT estado, devuelto FROM biblioteca_retiro WHERE id='$id' LIMIT 1 FOR UPDATE");
            if (!$cabecera) {
                throw new Exception("Retiro no encontrado.");
            }
            if ((int)$cabecera['estado'] == 0) {
                $conectar->rollback();
                return ["ok" => true, "msg" => "El retiro ya estaba anulado."];
            }

            // Si aún no fue devuelto, anular el retiro devuelve los libros al stock.
            // Si ya fue devuelto, el stock se repuso al registrar la devolución y no se toca nuevamente.
            if ((int)$cabecera['devuelto'] == 0) {
                $detalle = ejecutarConsulta("SELECT biblioteca_libro_id, cantidad FROM biblioteca_retiro_detalle WHERE biblioteca_retiro_id='$id' AND estado=1");
                while ($reg = $detalle->fetch_object()) {
                    $libro_id = (int)$reg->biblioteca_libro_id;
                    $cantidad = (int)$reg->cantidad;
                    if (!ejecutarConsulta("UPDATE biblioteca_libro SET stock = stock + $cantidad WHERE id='$libro_id'")) {
                        throw new Exception("No se pudo devolver el stock al anular el retiro.");
                    }
                }
            }

            if (!ejecutarConsulta("UPDATE biblioteca_retiro SET estado=0 WHERE id='$id'")) {
                throw new Exception("No se pudo anular el retiro.");
            }

            $conectar->commit();
            return ["ok" => true, "msg" => "Retiro anulado correctamente."];
        } catch (Exception $e) {
            $conectar->rollback();
            return ["ok" => false, "msg" => $e->getMessage()];
        }
    }

    public function activar($id)
    {
        global $conectar;

        try {
            $id = limpiarcadena($id);
            $conectar->begin_transaction();

            $cabecera = ejecutarConsultaSimpleFila("SELECT estado, devuelto FROM biblioteca_retiro WHERE id='$id' LIMIT 1 FOR UPDATE");
            if (!$cabecera) {
                throw new Exception("Retiro no encontrado.");
            }
            if ((int)$cabecera['estado'] == 1) {
                $conectar->rollback();
                return ["ok" => true, "msg" => "El retiro ya estaba activo."];
            }

            // Un retiro ya devuelto puede reactivarse administrativamente sin volver a descontar stock.
            if ((int)$cabecera['devuelto'] == 0) {
                $detalle = ejecutarConsulta("SELECT biblioteca_libro_id, cantidad FROM biblioteca_retiro_detalle WHERE biblioteca_retiro_id='$id' AND estado=1");
                $movimientos = [];

                while ($reg = $detalle->fetch_object()) {
                    $libro_id = (int)$reg->biblioteca_libro_id;
                    $cantidad = (int)$reg->cantidad;
                    $fila = ejecutarConsultaSimpleFila("SELECT stock, estado FROM biblioteca_libro WHERE id='$libro_id' LIMIT 1 FOR UPDATE");

                    if (!$fila || $fila['estado'] != 1 || (int)$fila['stock'] < $cantidad) {
                        throw new Exception("No hay stock suficiente para volver a activar este retiro.");
                    }
                    $movimientos[] = ["libro_id" => $libro_id, "cantidad" => $cantidad];
                }

                foreach ($movimientos as $movimiento) {
                    $libro_id = $movimiento['libro_id'];
                    $cantidad = $movimiento['cantidad'];
                    if (!ejecutarConsulta("UPDATE biblioteca_libro SET stock = stock - $cantidad WHERE id='$libro_id' AND stock >= $cantidad") || $conectar->affected_rows == 0) {
                        throw new Exception("No se pudo descontar el stock.");
                    }
                }
            }

            if (!ejecutarConsulta("UPDATE biblioteca_retiro SET estado=1 WHERE id='$id'")) {
                throw new Exception("No se pudo activar el retiro.");
            }

            $conectar->commit();
            return ["ok" => true, "msg" => "Retiro activado correctamente."];
        } catch (Exception $e) {
            $conectar->rollback();
            return ["ok" => false, "msg" => $e->getMessage()];
        }
    }
}
?>