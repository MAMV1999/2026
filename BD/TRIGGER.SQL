DELIMITER $$

CREATE TRIGGER manejar_stock_cambio_estado
AFTER UPDATE ON almacen_ingreso
FOR EACH ROW
BEGIN
    -- Si el estado cambia de 1 a 0, restar stock
    IF OLD.estado = 1 AND NEW.estado = 0 THEN
        UPDATE almacen_producto p
        JOIN almacen_ingreso_detalle d ON p.id = d.almacen_producto_id
        SET p.stock = p.stock - d.stock
        WHERE d.almacen_ingreso_id = NEW.id;
    END IF;

    -- Si el estado cambia de 0 a 1, restaurar stock
    IF OLD.estado = 0 AND NEW.estado = 1 THEN
        UPDATE almacen_producto p
        JOIN almacen_ingreso_detalle d ON p.id = d.almacen_producto_id
        SET p.stock = p.stock + d.stock
        WHERE d.almacen_ingreso_id = NEW.id;
    END IF;
END$$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER actualizar_stock_ingreso
AFTER INSERT ON almacen_ingreso_detalle
FOR EACH ROW
BEGIN
    -- Incrementar el stock del producto correspondiente
    UPDATE almacen_producto
    SET stock = stock + NEW.stock
    WHERE id = NEW.almacen_producto_id;
END$$

DELIMITER ;

/*

Con esta configuración, cubres todos los escenarios relevantes:

manejar_stock_cambio_estado:

Resta el stock cuando el estado cambia de 1 a 0.
Restaura el stock cuando el estado cambia de 0 a 1.
actualizar_stock_ingreso:

Incrementa el stock cuando se insertan nuevos detalles en almacen_ingreso_detalle.

*/