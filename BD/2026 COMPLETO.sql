-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-01-2026 a las 22:43:42
-- Versión del servidor: 10.1.31-MariaDB
-- Versión de PHP: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `2026`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerDetallesMatricula` (`p_id` INT)  BEGIN
    -- Declaraciones para manejar el cursor y los datos
    DECLARE done INT DEFAULT FALSE;
    DECLARE documentoNombre VARCHAR(255);
    DECLARE documentosCursor CURSOR FOR SELECT nombre FROM documento;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Construcción inicial del SQL dinámico
    SET @sql = 'SELECT 
            md.id AS matricula_detalle_id,
            ins.nombre AS institucion_nombre,
            ins.telefono AS institucion_telefono,
            ins.correo AS institucion_correo,
            ins.ruc AS institucion_ruc,
            ins.razon_social AS institucion_razon_social,
            ins.direccion AS institucion_direccion,
            il.nombre AS institucion_lectivo,
            niv.nombre AS institucion_nivel, 
            ig.nombre AS institucion_grado,
            isec.nombre AS institucion_seccion,
            a.id AS apoderado_id,
            at.nombre AS apoderado_tipo,
            ad.nombre AS apoderado_documento_tipo,
            a.numerodocumento AS apoderado_numerodocumento,
            a.nombreyapellido AS apoderado_nombre,
            a.telefono AS apoderado_telefono,
            al.id AS alumno_id,
            ald.nombre AS alumno_documento_tipo,
            al.numerodocumento AS alumno_numerodocumento,
            al.nombreyapellido AS alumno_nombre';

    -- Abrir el cursor para recorrer los nombres de los documentos
    OPEN documentosCursor;

    -- Bucle para agregar las columnas dinámicas al SQL
    read_loop: LOOP
        FETCH documentosCursor INTO documentoNombre;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Añadir columnas dinámicas para los documentos
        SET @sql = CONCAT(@sql, 
            ', MAX(CASE WHEN d.nombre = ''', documentoNombre, ''' THEN 
                CASE 
                    WHEN IFNULL(dd.entregado, 0) = 1 THEN ''SI'' 
                    ELSE ''NO'' 
                END 
            ELSE ''NO'' END) AS `', documentoNombre, '`',
            ', MAX(CASE WHEN d.nombre = ''', documentoNombre, ''' THEN dd.observaciones ELSE \'\' END) AS `', documentoNombre, '_observaciones`'
        );
    END LOOP;

    -- Cerrar el cursor
    CLOSE documentosCursor;

    -- Completar el SQL dinámico con las cláusulas FROM, WHERE, GROUP BY y ORDER BY
    SET @sql = CONCAT(@sql, '
        FROM 
            matricula_detalle md
        JOIN 
            matricula m ON md.id_matricula = m.id
        JOIN 
            institucion_seccion isec ON m.id_institucion_seccion = isec.id
        JOIN 
            institucion_grado ig ON isec.id_institucion_grado = ig.id
        JOIN 
            institucion_nivel niv ON ig.id_institucion_nivel = niv.id
        JOIN 
            institucion_lectivo il ON niv.id_institucion_lectivo = il.id
        JOIN 
            institucion ins ON il.id_institucion = ins.id
        JOIN 
            usuario_apoderado a ON md.id_usuario_apoderado = a.id
        JOIN 
            usuario_apoderado_tipo at ON a.id_apoderado_tipo = at.id
        JOIN 
            usuario_documento ad ON a.id_documento = ad.id
        JOIN 
            usuario_alumno al ON md.id_usuario_alumno = al.id
        JOIN 
            usuario_documento ald ON al.id_documento = ald.id
        LEFT JOIN 
            documento_detalle dd ON md.id = dd.id_matricula_detalle
        LEFT JOIN 
            documento d ON dd.id_documento = d.id
        WHERE
            md.id = ', p_id, '
        GROUP BY 
            md.id, ins.nombre, ins.telefono, ins.correo, ins.ruc, ins.razon_social, ins.direccion, il.nombre, niv.nombre, ig.nombre, isec.nombre, 
            a.id, at.nombre, ad.nombre, a.numerodocumento, a.nombreyapellido, a.telefono, 
            al.id, ald.nombre, al.numerodocumento, al.nombreyapellido
        ORDER BY 
            ins.nombre ASC, niv.nombre ASC, ig.nombre ASC, isec.nombre ASC, al.nombreyapellido ASC
    ');

    -- Preparar y ejecutar la consulta dinámica
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerDetallesMatriculaTodos` ()  BEGIN
    -- Declaraciones para manejar el cursor y los datos
    DECLARE done INT DEFAULT FALSE;
    DECLARE documentoNombre VARCHAR(255);
    DECLARE documentosCursor CURSOR FOR SELECT nombre FROM documento;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Construcción inicial del SQL dinámico
    SET @sql = 'SELECT 
            md.id AS matricula_detalle_id,
            ins.nombre AS institucion_nombre,
            ins.telefono AS institucion_telefono,
            ins.correo AS institucion_correo,
            ins.ruc AS institucion_ruc,
            ins.razon_social AS institucion_razon_social,
            ins.direccion AS institucion_direccion,
            il.nombre AS institucion_lectivo,
            niv.nombre AS institucion_nivel, 
            ig.nombre AS institucion_grado,
            isec.nombre AS institucion_seccion,
            mc.nombre AS matricula_categoria,
            a.id AS apoderado_id,
            at.nombre AS apoderado_tipo,
            ad.nombre AS apoderado_documento_tipo,
            a.numerodocumento AS apoderado_numerodocumento,
            a.nombreyapellido AS apoderado_nombre,
            a.telefono AS apoderado_telefono,
            al.id AS alumno_id,
            ald.nombre AS alumno_documento_tipo,
            al.numerodocumento AS alumno_numerodocumento,
            al.nombreyapellido AS alumno_nombre';

    -- Abrir el cursor para recorrer los nombres de los documentos
    OPEN documentosCursor;

    -- Bucle para agregar las columnas dinámicas al SQL
    read_loop: LOOP
        FETCH documentosCursor INTO documentoNombre;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Añadir columnas dinámicas para los documentos
        SET @sql = CONCAT(@sql, 
            ', MAX(CASE WHEN d.nombre = ''', documentoNombre, ''' THEN 
                CASE 
                    WHEN IFNULL(dd.entregado, 0) = 1 THEN ''SI'' 
                    ELSE ''NO'' 
                END 
            ELSE ''NO'' END) AS `', documentoNombre, '`',
            ', MAX(CASE WHEN d.nombre = ''', documentoNombre, ''' THEN dd.observaciones ELSE \'\' END) AS `', documentoNombre, '_observaciones`'
        );
    END LOOP;

    -- Cerrar el cursor
    CLOSE documentosCursor;

    -- Completar el SQL dinámico con las cláusulas FROM, WHERE, GROUP BY y ORDER BY
    SET @sql = CONCAT(@sql, '
        FROM matricula_detalle md
        JOIN matricula m ON md.id_matricula = m.id
        JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
        JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
        JOIN institucion_nivel niv ON ig.id_institucion_nivel = niv.id
        JOIN institucion_lectivo il ON niv.id_institucion_lectivo = il.id
        JOIN institucion ins ON il.id_institucion = ins.id
        JOIN usuario_apoderado a ON md.id_usuario_apoderado = a.id
        JOIN usuario_apoderado_tipo at ON a.id_apoderado_tipo = at.id
        JOIN usuario_documento ad ON a.id_documento = ad.id
        JOIN usuario_alumno al ON md.id_usuario_alumno = al.id
        JOIN usuario_documento ald ON al.id_documento = ald.id
        JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id
        LEFT JOIN documento_detalle dd ON md.id = dd.id_matricula_detalle
        LEFT JOIN documento d ON dd.id_documento = d.id
        GROUP BY md.id, ins.nombre, ins.telefono, ins.correo, ins.ruc, ins.razon_social, ins.direccion, il.nombre, niv.nombre, ig.nombre, isec.nombre, mc.nombre,
        a.id, at.nombre, ad.nombre, a.numerodocumento, a.nombreyapellido, a.telefono, al.id, ald.nombre, al.numerodocumento, al.nombreyapellido
        ORDER BY ins.nombre ASC, niv.nombre ASC, ig.nombre ASC, isec.nombre ASC, al.nombreyapellido ASC
    ');

    -- Preparar y ejecutar la consulta dinámica
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_matricula_listar_pivot` ()  BEGIN
    DECLARE cols LONGTEXT DEFAULT '';

    SET SESSION group_concat_max_len = 1000000;

    SELECT
        GROUP_CONCAT(
            DISTINCT CONCAT(
                'SUM(CASE WHEN mc.nombre = ''',
                REPLACE(mc.nombre, '''', '\\'''),
                ''' THEN mm.monto ELSE NULL END) AS `',
                REPLACE(mc.nombre, '`', ''),
                '`'
            )
            ORDER BY mc.nombre
            SEPARATOR ', '
        )
    INTO cols
    FROM matricula_cobro mc
    WHERE mc.estado = 1;

    SET @sql = CONCAT(
    'SELECT
        m.id AS matricula_id,
        il.nombre AS lectivo,
        iniv.nombre AS nivel,
        ig.nombre AS grado,
        isec.nombre AS seccion,
        ud.nombreyapellido AS docente,
        m.aforo AS aforo,
        (SELECT COUNT(*) FROM matricula_detalle md WHERE md.id_matricula = m.id AND md.estado = 1) AS matriculados,
        m.observaciones AS observaciones,
        ', IFNULL(cols, ''), '
     FROM matricula m
     INNER JOIN institucion_seccion isec ON isec.id = m.id_institucion_seccion AND isec.estado = 1
     INNER JOIN institucion_grado ig ON ig.id = isec.id_institucion_grado AND ig.estado = 1
     INNER JOIN institucion_nivel iniv ON iniv.id = ig.id_institucion_nivel AND iniv.estado = 1
     INNER JOIN institucion_lectivo il ON il.id = iniv.id_institucion_lectivo AND il.estado = 1
     INNER JOIN usuario_docente ud ON ud.id = m.id_usuario_docente AND ud.estado = 1
     LEFT JOIN matricula_monto mm ON mm.matricula_id = m.id AND mm.estado = 1
     LEFT JOIN matricula_cobro mc ON mc.id = mm.matricula_cobro_id AND mc.estado = 1
     WHERE m.estado = 1
     GROUP BY m.id, il.nombre, iniv.nombre, ig.nombre, isec.nombre, ud.nombreyapellido, m.aforo, m.observaciones
     ORDER BY m.id'
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_pivot_matricula_mes_cobros` (IN `p_matricula_id` INT)  BEGIN
    DECLARE v_cols  LONGTEXT;
    DECLARE v_total LONGTEXT;
    DECLARE v_sql   LONGTEXT;

    SET SESSION group_concat_max_len = 1000000;

    /* =========================
       Columnas dinámicas
       ========================= */
    SELECT GROUP_CONCAT(DISTINCT
        CONCAT(
            'IFNULL(MAX(CASE ',
                'WHEN mcd.aplica = 1 ',
                ' AND mcd.estado = 1 ',
                ' AND mcd.matricula_cobro_id = ', mc.id,
                ' THEN mo.monto ',
            'END), 0) AS `', REPLACE(mc.nombre, '`', '``'), '`'
        )
        ORDER BY mc.id
        SEPARATOR ', '
    )
    INTO v_cols
    FROM matricula_cobro mc
    INNER JOIN matricula_cobro_detalle mcd
        ON mcd.matricula_cobro_id = mc.id
       AND mcd.estado = 1
       AND mcd.aplica = 1
    INNER JOIN matricula_mes mm
        ON mm.id = mcd.matricula_mes_id
       AND mm.estado = 1
       AND mm.institucion_lectivo_id = 1
    WHERE mc.estado = 1
      AND mc.apertura = 0;

    IF v_cols IS NULL OR v_cols = '' THEN
        SET v_cols = '0 AS `SIN_COBROS`';
    END IF;

    /* =========================
       TOTAL dinámico
       ========================= */
    SELECT GROUP_CONCAT(DISTINCT
        CONCAT(
            'IFNULL(MAX(CASE ',
                'WHEN mcd.aplica = 1 ',
                ' AND mcd.estado = 1 ',
                ' AND mcd.matricula_cobro_id = ', mc.id,
                ' THEN mo.monto ',
            'END), 0)'
        )
        ORDER BY mc.id
        SEPARATOR ' + '
    )
    INTO v_total
    FROM matricula_cobro mc
    INNER JOIN matricula_cobro_detalle mcd
        ON mcd.matricula_cobro_id = mc.id
       AND mcd.estado = 1
       AND mcd.aplica = 1
    INNER JOIN matricula_mes mm
        ON mm.id = mcd.matricula_mes_id
       AND mm.estado = 1
       AND mm.institucion_lectivo_id = 1
    WHERE mc.estado = 1
      AND mc.apertura = 0;

    IF v_total IS NULL OR v_total = '' THEN
        SET v_total = '0';
    END IF;

    /* =========================
       SQL final
       ========================= */
    SET v_sql = CONCAT(
        'SELECT ',
            'mm.id, ',
            'mm.institucion_lectivo_id, ',
            'il.nombre AS institucion_lectivo_nombre, ',
            'mm.nombre, ',
            'mm.fecha_vencimiento, ',
            'mm.mora, ',
            'mm.observaciones, ',
            v_cols, ', ',
            '(', v_total, ') AS `TOTAL` ',
        'FROM matricula_mes mm ',
        'INNER JOIN institucion_lectivo il ',
            'ON il.id = mm.institucion_lectivo_id ',
            'AND il.estado = 1 ',
        'LEFT JOIN matricula_cobro_detalle mcd ',
            'ON mcd.matricula_mes_id = mm.id ',
            'AND mcd.estado = 1 ',
        'LEFT JOIN matricula_monto mo ',
            'ON mo.matricula_cobro_id = mcd.matricula_cobro_id ',
            'AND mo.estado = 1 ',
            'AND mo.matricula_id = ', p_matricula_id, ' ',
        'WHERE mm.estado = 1 ',
          'AND mm.institucion_lectivo_id = 1 ',
        'GROUP BY mm.id ',
        'HAVING TOTAL > 0 ',
        'ORDER BY mm.id ASC'
    );

    SET @sql = v_sql;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_categoria`
--

CREATE TABLE `almacen_categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `observaciones` text,
  `fechacreado` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_categoria`
--

INSERT INTO `almacen_categoria` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'CITA PSICOLÓGICA', '', '2026-01-05 09:34:57', 1),
(2, 'LIBRO PERSONALIZADO INICIAL', '', '2026-01-05 12:09:49', 1),
(3, 'LIBRO PERSONALIZADO PRIMARIA', '', '2026-01-05 12:09:57', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_comprobante`
--

CREATE TABLE `almacen_comprobante` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `impuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_comprobante`
--

INSERT INTO `almacen_comprobante` (`id`, `nombre`, `impuesto`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'BOLETA', '0.00', '', '2026-01-05 05:44:12', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_ingreso`
--

CREATE TABLE `almacen_ingreso` (
  `id` int(11) NOT NULL,
  `usuario_apoderado_id` int(11) NOT NULL,
  `almacen_comprobante_id` int(11) NOT NULL,
  `numeracion` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `almacen_metodo_pago_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `observaciones` text,
  `fechacreado` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_ingreso`
--

INSERT INTO `almacen_ingreso` (`id`, `usuario_apoderado_id`, `almacen_comprobante_id`, `numeracion`, `fecha`, `almacen_metodo_pago_id`, `total`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 2, 1, '000001', '2026-01-05', 3, '0.00', '', '2026-01-05 11:55:23', 1),
(2, 2, 1, '000002', '2026-01-05', 2, '0.00', '', '2026-01-05 12:17:01', 1),
(3, 2, 1, '000003', '2026-01-05', 2, '0.00', '', '2026-01-05 13:54:26', 1);

--
-- Disparadores `almacen_ingreso`
--
DELIMITER $$
CREATE TRIGGER `manejar_stock_cambio_estado` AFTER UPDATE ON `almacen_ingreso` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_ingreso_detalle`
--

CREATE TABLE `almacen_ingreso_detalle` (
  `id` int(11) NOT NULL,
  `almacen_ingreso_id` int(11) NOT NULL,
  `almacen_producto_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `observaciones` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_ingreso_detalle`
--

INSERT INTO `almacen_ingreso_detalle` (`id`, `almacen_ingreso_id`, `almacen_producto_id`, `stock`, `precio_unitario`, `observaciones`) VALUES
(1, 1, 2, 10, '0.00', ''),
(2, 1, 1, 10, '0.00', ''),
(3, 2, 3, 15, '0.00', ''),
(4, 2, 4, 15, '0.00', ''),
(5, 2, 5, 15, '0.00', ''),
(6, 2, 6, 15, '0.00', ''),
(7, 2, 7, 15, '0.00', ''),
(8, 2, 8, 15, '0.00', ''),
(9, 2, 9, 15, '0.00', ''),
(10, 2, 10, 15, '0.00', ''),
(11, 2, 11, 15, '0.00', ''),
(12, 2, 12, 15, '0.00', ''),
(13, 3, 13, 15, '0.00', ''),
(14, 3, 14, 15, '0.00', ''),
(15, 3, 15, 15, '0.00', '');

--
-- Disparadores `almacen_ingreso_detalle`
--
DELIMITER $$
CREATE TRIGGER `actualizar_stock_ingreso` AFTER INSERT ON `almacen_ingreso_detalle` FOR EACH ROW BEGIN
    -- Incrementar el stock del producto correspondiente
    UPDATE almacen_producto
    SET stock = stock + NEW.stock
    WHERE id = NEW.almacen_producto_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_metodo_pago`
--

CREATE TABLE `almacen_metodo_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_metodo_pago`
--

INSERT INTO `almacen_metodo_pago` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'YAPE', '', '2026-01-05 05:42:51', 1),
(2, 'EFECTIVO', '', '2026-01-05 05:43:51', 1),
(3, 'TRANSFERENCIA', '', '2026-01-05 05:44:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_producto`
--

CREATE TABLE `almacen_producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `categoria_id` int(11) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT '0',
  `fechacreado` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_producto`
--

INSERT INTO `almacen_producto` (`id`, `nombre`, `descripcion`, `categoria_id`, `precio_compra`, `precio_venta`, `stock`, `fechacreado`, `estado`) VALUES
(1, 'CITA PSICOLÓGICA – PROGRAMADA', '', 1, '0.00', '50.00', 7, '2026-01-05 09:36:56', 1),
(2, 'CITA PSICOLÓGICA – INMEDIATA', '', 1, '0.00', '50.00', 10, '2026-01-05 09:36:56', 1),
(3, 'ESTIMULACIÓN TEMPRANA', '', 2, '0.00', '300.00', 15, '2026-01-05 12:12:50', 1),
(4, 'INICIAL 3 AÑOS', '', 2, '0.00', '300.00', 15, '2026-01-05 12:12:50', 1),
(5, 'INICIAL 4 AÑOS', '', 2, '0.00', '300.00', 15, '2026-01-05 12:12:50', 1),
(6, 'INICIAL 5 AÑOS', '', 2, '0.00', '300.00', 15, '2026-01-05 12:12:50', 1),
(7, 'LIBRO PRIMARIA 1 GRADO', '', 3, '0.00', '240.00', 15, '2026-01-05 12:14:57', 1),
(8, 'LIBRO PRIMARIA 2 GRADO', '', 3, '0.00', '240.00', 15, '2026-01-05 12:14:57', 1),
(9, 'LIBRO PRIMARIA 3 GRADO', '', 3, '0.00', '250.00', 13, '2026-01-05 12:14:57', 1),
(10, 'LIBRO PRIMARIA 4 GRADO', '', 3, '0.00', '250.00', 14, '2026-01-05 12:14:57', 1),
(11, 'LIBRO PRIMARIA 5 GRADO', '', 3, '0.00', '270.00', 14, '2026-01-05 12:14:57', 1),
(12, 'LIBRO PRIMARIA 6 GRADO', '', 3, '0.00', '270.00', 14, '2026-01-05 12:14:57', 1),
(13, 'LIBRO PRIMARIA COMPUTACION', '', 3, '0.00', '120.00', 13, '2026-01-05 13:54:01', 1),
(14, 'LIBRO PRIMARIA INGLES', '', 3, '0.00', '110.00', 13, '2026-01-05 13:54:01', 1),
(15, 'RAZ-KIDS', '', 3, '0.00', '100.00', 15, '2026-01-05 13:54:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_salida`
--

CREATE TABLE `almacen_salida` (
  `id` int(11) NOT NULL,
  `usuario_apoderado_id` int(11) NOT NULL,
  `almacen_comprobante_id` int(11) NOT NULL,
  `numeracion` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `almacen_metodo_pago_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `observaciones` text,
  `fechacreado` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_salida`
--

INSERT INTO `almacen_salida` (`id`, `usuario_apoderado_id`, `almacen_comprobante_id`, `numeracion`, `fecha`, `almacen_metodo_pago_id`, `total`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 1, '000001', '2025-11-21', 1, '50.00', 'CITA PSICOLÓGICA – PROGRAMADA\r\nREALIZADA', '2026-01-05 11:56:09', 1),
(2, 3, 1, '000002', '2025-12-11', 1, '80.00', '11/12/2025 ADELANTO DE PAGO\r\nPRIMARIA 3 GRADO 40 SOLES\r\nPRIMARIA 6 GRADO 40 SOLES', '2026-01-05 12:18:32', 1),
(3, 4, 1, '000003', '2025-12-12', 1, '50.00', 'ADELANTO DE PAGO DE LIBROS 12/12/2025\r\nPRIMARIA 4 GRADO 30 SOLES\r\nPRIMARIA COMPUTACION Y PRIMARIA INGLES 20 SOLES', '2026-01-05 13:56:44', 1),
(4, 5, 1, '000004', '2025-12-15', 1, '50.00', 'CITA PSICOLÓGICA – PROGRAMADA\r\nATENDIDA', '2026-01-05 14:34:22', 1),
(5, 6, 1, '000005', '2025-12-15', 2, '30.00', 'CITA PSICOLÓGICA – PROGRAMADA\r\nATENDIDA', '2026-01-05 14:43:10', 1),
(6, 6, 1, '000006', '2025-12-15', 2, '40.00', 'ADELANTO DE PAGO REUNION DE PADRES - EFECTIVO\r\nLIBRO PRIMARIA 5 GRADO 20 SOLES\r\n\r\nADELANTO DE PAGO 15/12/2025 - EFECTIVO\r\nLIBRO PRIMARIA INGLES 10 SOLES\r\nLIBRO PRIMARIA COMPUTACION 10 SOLES', '2026-01-05 14:44:39', 1),
(7, 7, 1, '000007', '2025-12-15', 1, '30.00', 'ADELANTO LIBRO PRIMARIA 3 GRADO 30 SOLES', '2026-01-05 15:05:45', 1);

--
-- Disparadores `almacen_salida`
--
DELIMITER $$
CREATE TRIGGER `manejar_stock_cambio_estado_salida` AFTER UPDATE ON `almacen_salida` FOR EACH ROW BEGIN
    -- Si el estado cambia de 1 a 0, restaurar stock
    IF OLD.estado = 1 AND NEW.estado = 0 THEN
        UPDATE almacen_producto p
        JOIN almacen_salida_detalle d ON p.id = d.almacen_producto_id
        SET p.stock = p.stock + d.stock
        WHERE d.almacen_salida_id = NEW.id;
    END IF;

    -- Si el estado cambia de 0 a 1, reducir stock
    IF OLD.estado = 0 AND NEW.estado = 1 THEN
        UPDATE almacen_producto p
        JOIN almacen_salida_detalle d ON p.id = d.almacen_producto_id
        SET p.stock = p.stock - d.stock
        WHERE d.almacen_salida_id = NEW.id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen_salida_detalle`
--

CREATE TABLE `almacen_salida_detalle` (
  `id` int(11) NOT NULL,
  `almacen_salida_id` int(11) NOT NULL,
  `almacen_producto_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `observaciones` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen_salida_detalle`
--

INSERT INTO `almacen_salida_detalle` (`id`, `almacen_salida_id`, `almacen_producto_id`, `stock`, `precio_unitario`, `observaciones`) VALUES
(1, 1, 1, 1, '50.00', ''),
(2, 2, 9, 1, '40.00', '250.00'),
(3, 2, 12, 1, '40.00', '270.00'),
(4, 3, 10, 1, '30.00', '250.00'),
(5, 3, 13, 1, '10.00', '120.00'),
(6, 3, 14, 1, '10.00', '110.00'),
(7, 4, 1, 1, '50.00', ''),
(8, 5, 1, 1, '30.00', ''),
(9, 6, 14, 1, '10.00', '110.00'),
(10, 6, 13, 1, '10.00', '120.00'),
(11, 6, 11, 1, '20.00', '270.00'),
(12, 7, 9, 1, '30.00', '250.00');

--
-- Disparadores `almacen_salida_detalle`
--
DELIMITER $$
CREATE TRIGGER `actualizar_stock_salida` AFTER INSERT ON `almacen_salida_detalle` FOR EACH ROW BEGIN
    -- Reducir el stock del producto correspondiente
    UPDATE almacen_producto
    SET stock = stock - NEW.stock
    WHERE id = NEW.almacen_producto_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca_libro`
--

CREATE TABLE `biblioteca_libro` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `observaciones` text,
  `fecha_creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `id_documento_responsable` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `documento`
--

INSERT INTO `documento` (`id`, `id_documento_responsable`, `nombre`, `obligatorio`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 'FICHA UNICA DE MATRICULA', 1, '', '2026-01-03 00:11:26', 1),
(2, 1, 'CONSTANCIA DE MATRICULA', 0, '', '2026-01-03 00:11:45', 1),
(3, 1, 'CERTIFICADO DE ESTUDIOS', 0, '', '2026-01-03 00:11:59', 1),
(4, 1, 'INFORME DE PROGRESO / LIBRETA DE NOTAS', 0, '', '2026-01-03 00:12:10', 1),
(5, 1, 'CONSTANCIA DE NO ADEUDO', 0, '', '2026-01-03 00:12:20', 1),
(6, 2, 'CARNE DE VACUNACIÓN (NIÑO SANO / COVID)', 0, '', '2026-01-03 00:12:36', 1),
(7, 2, 'PARTIDA O ACTA DE NACIMIENTO', 0, '', '2026-01-03 00:12:58', 1),
(8, 2, 'COPIA DNI ALUMNO', 0, '', '2026-01-03 00:13:28', 1),
(9, 2, 'COPIA DNI APODERADO', 0, '', '2026-01-03 00:13:40', 1),
(10, 2, '6 FOTOS (TAMAÑO CARNET)', 0, '', '2026-01-03 00:13:52', 1),
(11, 2, 'FOTO FAMILIAR (TAMAÑO JUMBO)', 0, '', '2026-01-03 00:14:03', 1),
(12, 2, 'OTROS DOCUMENTOS', 0, '', '2026-01-03 00:14:17', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_detalle`
--

CREATE TABLE `documento_detalle` (
  `id` int(11) NOT NULL,
  `id_matricula_detalle` int(11) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `entregado` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_responsable`
--

CREATE TABLE `documento_responsable` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `documento_responsable`
--

INSERT INTO `documento_responsable` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'INSTITUCION DE PROCEDENCIA', '', '2026-01-02 08:33:46', 1),
(2, 'APODERADO', '', '2026-01-02 08:33:54', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion`
--

CREATE TABLE `institucion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_usuario_docente` int(11) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `ruc` varchar(11) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `institucion`
--

INSERT INTO `institucion` (`id`, `nombre`, `id_usuario_docente`, `telefono`, `correo`, `ruc`, `razon_social`, `direccion`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'IEP EBENEZER KIDS', 1, '958197047', 'CBEBENEZER0791@GMAIL.COM', '20602116892', 'GAYCE E.I.R.L.', 'CAL.LOS PENSAMIENTOS NRO. 261 P.J. EL ERMITAÑO LIMA - LIMA - INDEPENDENCIA', '', '2025-12-27 07:19:31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion_grado`
--

CREATE TABLE `institucion_grado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_institucion_nivel` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `institucion_grado`
--

INSERT INTO `institucion_grado` (`id`, `nombre`, `id_institucion_nivel`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, '2 AÑOS', 1, '', '2025-12-27 07:24:32', 1),
(2, '3 AÑOS', 2, '', '2025-12-27 07:24:41', 1),
(3, '4 AÑOS', 2, '', '2025-12-27 07:24:50', 1),
(4, '5 AÑOS', 2, '', '2025-12-27 07:25:00', 1),
(5, '1 GRADO', 3, '', '2025-12-27 07:26:22', 1),
(6, '2 GRADO', 3, '', '2025-12-27 07:26:30', 1),
(7, '3 GRADO', 3, '', '2025-12-27 07:26:38', 1),
(8, '4 GRADO', 3, '', '2025-12-27 07:26:45', 1),
(9, '5 GRADO', 3, '', '2025-12-27 07:26:54', 1),
(10, '6 GRADO', 3, '', '2025-12-27 07:27:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion_lectivo`
--

CREATE TABLE `institucion_lectivo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nombre_lectivo` varchar(300) NOT NULL,
  `id_institucion` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `institucion_lectivo`
--

INSERT INTO `institucion_lectivo` (`id`, `nombre`, `nombre_lectivo`, `id_institucion`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, '2026', 'AÑO DEL FORTALECIMIENTO DE LA SOBERANIA NACIONAL', 1, '', '2025-12-27 07:22:03', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion_nivel`
--

CREATE TABLE `institucion_nivel` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_institucion_lectivo` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `institucion_nivel`
--

INSERT INTO `institucion_nivel` (`id`, `nombre`, `id_institucion_lectivo`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'EST. TEMPRANA', 1, '', '2025-12-27 07:23:13', 1),
(2, 'INICIAL', 1, '', '2025-12-27 07:23:26', 1),
(3, 'PRIMARIA', 1, '', '2025-12-27 07:23:33', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion_seccion`
--

CREATE TABLE `institucion_seccion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_institucion_grado` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `institucion_seccion`
--

INSERT INTO `institucion_seccion` (`id`, `nombre`, `id_institucion_grado`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'A', 1, '', '2025-12-27 07:27:16', 1),
(2, 'A', 2, '', '2025-12-27 07:27:24', 1),
(3, 'A', 3, '', '2025-12-27 07:27:31', 1),
(4, 'A', 4, '', '2025-12-27 07:27:37', 1),
(5, 'A', 5, '', '2025-12-27 07:27:45', 1),
(6, 'A', 6, '', '2025-12-27 07:27:53', 1),
(7, 'A', 7, '', '2025-12-27 07:27:59', 1),
(8, 'A', 8, '', '2025-12-27 07:28:04', 1),
(9, 'A', 9, '', '2025-12-27 07:28:09', 1),
(10, 'A', 10, '', '2025-12-27 07:28:15', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion_validacion`
--

CREATE TABLE `institucion_validacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `institucion_validacion`
--

INSERT INTO `institucion_validacion` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, '20602116892', '', '2025-12-29 06:04:46', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `id` int(11) NOT NULL,
  `id_institucion_seccion` int(11) NOT NULL,
  `id_usuario_docente` int(11) NOT NULL,
  `aforo` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula`
--

INSERT INTO `matricula` (`id`, `id_institucion_seccion`, `id_usuario_docente`, `aforo`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 1, 18, '', '2025-12-27 07:38:12', 1),
(2, 2, 1, 18, '', '2025-12-29 02:53:56', 1),
(3, 3, 1, 18, '', '2026-01-03 07:42:36', 1),
(4, 4, 1, 18, '', '2026-01-03 07:43:24', 1),
(5, 5, 1, 18, '', '2026-01-03 07:44:11', 1),
(6, 6, 1, 18, '', '2026-01-03 07:44:31', 1),
(7, 7, 1, 18, '', '2026-01-03 07:45:04', 1),
(8, 8, 1, 18, '', '2026-01-03 07:45:29', 1),
(9, 9, 1, 18, '', '2026-01-03 07:46:07', 1),
(10, 10, 1, 18, '', '2026-01-03 07:46:24', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_categoria`
--

CREATE TABLE `matricula_categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula_categoria`
--

INSERT INTO `matricula_categoria` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'RATIFICACION', '', '2025-12-27 07:30:10', 1),
(2, 'NUEVO', '', '2025-12-27 07:30:20', 1),
(3, 'LIBRE', '', '2026-01-03 07:47:56', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_cobro`
--

CREATE TABLE `matricula_cobro` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apertura` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text,
  `fechacreado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula_cobro`
--

INSERT INTO `matricula_cobro` (`id`, `nombre`, `apertura`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'MATRICULA', 1, '', '2025-12-27 02:36:28', 1),
(2, 'MENSUALIDAD', 0, '', '2025-12-27 02:36:46', 1),
(3, 'MANTENIMIENTO', 0, '', '2025-12-27 02:37:00', 1),
(4, 'IMPRESION', 0, '', '2025-12-27 02:37:16', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_cobro_detalle`
--

CREATE TABLE `matricula_cobro_detalle` (
  `id` int(11) NOT NULL,
  `matricula_cobro_id` int(11) NOT NULL,
  `matricula_mes_id` int(11) NOT NULL,
  `aplica` tinyint(1) NOT NULL,
  `observaciones` text,
  `fechacreado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `matricula_cobro_detalle`
--

INSERT INTO `matricula_cobro_detalle` (`id`, `matricula_cobro_id`, `matricula_mes_id`, `aplica`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 1, 1, '', '2025-12-27 02:36:28', 1),
(2, 1, 2, 0, '', '2025-12-27 02:36:28', 1),
(3, 1, 3, 0, '', '2025-12-27 02:36:28', 1),
(4, 1, 4, 0, '', '2025-12-27 02:36:28', 1),
(5, 1, 5, 0, '', '2025-12-27 02:36:28', 1),
(6, 1, 6, 0, '', '2025-12-27 02:36:28', 1),
(7, 1, 7, 0, '', '2025-12-27 02:36:28', 1),
(8, 1, 8, 0, '', '2025-12-27 02:36:28', 1),
(9, 1, 9, 0, '', '2025-12-27 02:36:28', 1),
(10, 1, 10, 0, '', '2025-12-27 02:36:28', 1),
(11, 1, 11, 0, '', '2025-12-27 02:36:28', 1),
(12, 2, 1, 0, '', '2025-12-27 02:36:46', 1),
(13, 2, 2, 1, '', '2025-12-27 02:36:46', 1),
(14, 2, 3, 1, '', '2025-12-27 02:36:46', 1),
(15, 2, 4, 1, '', '2025-12-27 02:36:46', 1),
(16, 2, 5, 1, '', '2025-12-27 02:36:46', 1),
(17, 2, 6, 1, '', '2025-12-27 02:36:46', 1),
(18, 2, 7, 1, '', '2025-12-27 02:36:46', 1),
(19, 2, 8, 1, '', '2025-12-27 02:36:46', 1),
(20, 2, 9, 1, '', '2025-12-27 02:36:46', 1),
(21, 2, 10, 1, '', '2025-12-27 02:36:46', 1),
(22, 2, 11, 1, '', '2025-12-27 02:36:46', 1),
(34, 4, 1, 0, '', '2025-12-27 02:37:16', 1),
(35, 4, 2, 1, '', '2025-12-27 02:37:16', 1),
(36, 4, 3, 1, '', '2025-12-27 02:37:16', 1),
(37, 4, 4, 1, '', '2025-12-27 02:37:16', 1),
(38, 4, 5, 1, '', '2025-12-27 02:37:16', 1),
(39, 4, 6, 1, '', '2025-12-27 02:37:16', 1),
(40, 4, 7, 1, '', '2025-12-27 02:37:16', 1),
(41, 4, 8, 1, '', '2025-12-27 02:37:16', 1),
(42, 4, 9, 1, '', '2025-12-27 02:37:16', 1),
(43, 4, 10, 1, '', '2025-12-27 02:37:16', 1),
(44, 4, 11, 1, '', '2025-12-27 02:37:16', 1),
(56, 3, 1, 0, '', '2026-01-05 00:26:02', 1),
(57, 3, 2, 0, '', '2026-01-05 00:26:02', 1),
(58, 3, 3, 0, '', '2026-01-05 00:26:02', 1),
(59, 3, 4, 0, '', '2026-01-05 00:26:02', 1),
(60, 3, 5, 0, '', '2026-01-05 00:26:02', 1),
(61, 3, 6, 1, '', '2026-01-05 00:26:02', 1),
(62, 3, 7, 0, '', '2026-01-05 00:26:02', 1),
(63, 3, 8, 0, '', '2026-01-05 00:26:02', 1),
(64, 3, 9, 0, '', '2026-01-05 00:26:02', 1),
(65, 3, 10, 0, '', '2026-01-05 00:26:02', 1),
(66, 3, 11, 1, '', '2026-01-05 00:26:02', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_detalle`
--

CREATE TABLE `matricula_detalle` (
  `id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `id_matricula` int(11) NOT NULL,
  `id_matricula_categoria` int(11) NOT NULL,
  `id_usuario_apoderado_referido` int(11) DEFAULT NULL,
  `id_usuario_apoderado` int(11) NOT NULL,
  `id_usuario_alumno` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula_detalle`
--

INSERT INTO `matricula_detalle` (`id`, `descripcion`, `id_matricula`, `id_matricula_categoria`, `id_usuario_apoderado_referido`, `id_usuario_apoderado`, `id_usuario_alumno`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./290.00\r\n\r\nObservaciones:', 2, 2, NULL, 1, 1, '', '2026-01-05 16:50:28', 1),
(2, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 7, 1, NULL, 3, 2, '', '2026-01-05 17:05:59', 1),
(3, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 10, 1, NULL, 3, 3, '', '2026-01-05 17:08:15', 1),
(4, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 8, 1, NULL, 4, 4, '', '2026-01-05 18:50:51', 1),
(5, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./290.00\r\n\r\nObservaciones:', 2, 2, NULL, 5, 5, '', '2026-01-05 19:32:29', 1),
(6, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./300.00\r\n\r\nObservaciones:', 3, 2, NULL, 6, 6, '', '2026-01-05 19:36:31', 1),
(7, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 10, 1, NULL, 6, 7, '', '2026-01-05 19:41:10', 1),
(8, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 7, 1, NULL, 7, 8, '', '2026-01-05 20:04:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_mes`
--

CREATE TABLE `matricula_mes` (
  `id` int(11) NOT NULL,
  `institucion_lectivo_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `mora` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observaciones` text,
  `fechacreado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `matricula_mes`
--

INSERT INTO `matricula_mes` (`id`, `institucion_lectivo_id`, `nombre`, `fecha_vencimiento`, `mora`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 'ENERO Y FEBRERO', '2026-02-28', '0.00', '', '2025-12-27 02:31:01', 1),
(2, 1, 'MARZO', '2026-04-01', '1.50', '', '2025-12-27 02:31:43', 1),
(3, 1, 'ABRIL', '2026-05-01', '1.50', '', '2025-12-27 02:32:01', 1),
(4, 1, 'MAYO', '2026-06-01', '1.50', '', '2025-12-27 02:32:21', 1),
(5, 1, 'JUNIO', '2026-07-01', '1.50', '', '2025-12-27 02:33:22', 1),
(6, 1, 'JULIO', '2026-08-01', '1.50', '', '2025-12-27 02:34:32', 1),
(7, 1, 'AGOSTO', '2026-09-01', '1.50', '', '2025-12-27 02:34:55', 1),
(8, 1, 'SEPTIEMBRE', '2026-10-01', '1.50', '', '2025-12-27 02:35:13', 1),
(9, 1, 'OCTUBRE', '2026-11-01', '1.50', '', '2025-12-27 02:35:30', 1),
(10, 1, 'NOVIEMBRE', '2026-12-01', '1.50', '', '2025-12-27 02:35:47', 1),
(11, 1, 'DICIEMBRE', '2026-12-31', '1.50', '', '2025-12-27 02:36:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_metodo_pago`
--

CREATE TABLE `matricula_metodo_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula_metodo_pago`
--

INSERT INTO `matricula_metodo_pago` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'EFECTIVO', '', '2025-12-27 07:29:31', 1),
(2, 'YAPE', '', '2025-12-27 07:29:36', 1),
(3, 'TRANSFERENCIA', '', '2025-12-27 07:29:43', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_monto`
--

CREATE TABLE `matricula_monto` (
  `id` int(11) NOT NULL,
  `matricula_id` int(11) DEFAULT NULL,
  `matricula_cobro_id` int(11) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula_monto`
--

INSERT INTO `matricula_monto` (`id`, `matricula_id`, `matricula_cobro_id`, `monto`, `observaciones`, `fechacreado`, `estado`) VALUES
(61, 1, 1, '280.00', '', '2026-01-05 16:44:38', 1),
(62, 1, 2, '290.00', '', '2026-01-05 16:44:38', 1),
(63, 1, 3, '30.00', '', '2026-01-05 16:44:38', 1),
(64, 1, 4, '15.00', '', '2026-01-05 16:44:38', 1),
(65, 2, 1, '280.00', '', '2026-01-05 16:44:47', 1),
(66, 2, 2, '290.00', '', '2026-01-05 16:44:47', 1),
(67, 2, 3, '30.00', '', '2026-01-05 16:44:47', 1),
(68, 2, 4, '15.00', '', '2026-01-05 16:44:47', 1),
(69, 3, 1, '280.00', '', '2026-01-05 16:44:52', 1),
(70, 3, 2, '300.00', '', '2026-01-05 16:44:52', 1),
(71, 3, 3, '30.00', '', '2026-01-05 16:44:52', 1),
(72, 3, 4, '15.00', '', '2026-01-05 16:44:52', 1),
(73, 4, 1, '280.00', '', '2026-01-05 16:44:58', 1),
(74, 4, 2, '300.00', '', '2026-01-05 16:44:58', 1),
(75, 4, 3, '30.00', '', '2026-01-05 16:44:58', 1),
(76, 4, 4, '15.00', '', '2026-01-05 16:44:58', 1),
(77, 5, 1, '280.00', '', '2026-01-05 16:45:03', 1),
(78, 5, 2, '320.00', '', '2026-01-05 16:45:03', 1),
(79, 5, 3, '30.00', '', '2026-01-05 16:45:03', 1),
(80, 5, 4, '10.00', '', '2026-01-05 16:45:03', 1),
(81, 6, 1, '280.00', '', '2026-01-05 16:45:07', 1),
(82, 6, 2, '320.00', '', '2026-01-05 16:45:07', 1),
(83, 6, 3, '30.00', '', '2026-01-05 16:45:07', 1),
(84, 6, 4, '10.00', '', '2026-01-05 16:45:07', 1),
(85, 7, 1, '280.00', '', '2026-01-05 16:45:13', 1),
(86, 7, 2, '320.00', '', '2026-01-05 16:45:13', 1),
(87, 7, 3, '30.00', '', '2026-01-05 16:45:13', 1),
(88, 7, 4, '10.00', '', '2026-01-05 16:45:13', 1),
(89, 8, 1, '280.00', '', '2026-01-05 16:45:18', 1),
(90, 8, 2, '320.00', '', '2026-01-05 16:45:18', 1),
(91, 8, 3, '30.00', '', '2026-01-05 16:45:18', 1),
(92, 8, 4, '10.00', '', '2026-01-05 16:45:18', 1),
(93, 9, 1, '280.00', '', '2026-01-05 16:45:23', 1),
(94, 9, 2, '320.00', '', '2026-01-05 16:45:23', 1),
(95, 9, 3, '30.00', '', '2026-01-05 16:45:23', 1),
(96, 9, 4, '10.00', '', '2026-01-05 16:45:23', 1),
(97, 10, 1, '280.00', '', '2026-01-05 16:45:28', 1),
(98, 10, 2, '320.00', '', '2026-01-05 16:45:28', 1),
(99, 10, 3, '30.00', '', '2026-01-05 16:45:28', 1),
(100, 10, 4, '10.00', '', '2026-01-05 16:45:28', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_pago`
--

CREATE TABLE `matricula_pago` (
  `id` int(11) NOT NULL,
  `id_matricula_detalle` int(11) NOT NULL,
  `numeracion` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `id_matricula_metodo_pago` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `matricula_pago`
--

INSERT INTO `matricula_pago` (`id`, `id_matricula_detalle`, `numeracion`, `fecha`, `descripcion`, `monto`, `id_matricula_metodo_pago`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, '000001', '2025-11-21', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./290.00\r\n\r\nObservaciones:', '280.00', 2, '', '2026-01-05 16:50:28', 1),
(2, 2, '000002', '2025-12-11', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-05 17:05:59', 1),
(3, 3, '000003', '2026-01-05', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-05 17:08:15', 1),
(4, 4, '000004', '2025-12-12', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-05 18:50:51', 1),
(5, 5, '000005', '2026-01-05', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./290.00\r\n\r\nObservaciones:', '200.00', 2, 'PENDIENTE PAGO DE 80 SOLES', '2026-01-05 19:32:29', 1),
(6, 6, '000006', '2025-12-15', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./300.00\r\n\r\nObservaciones:', '220.00', 1, 'EL MONTO EN EFECTIVO FUE ENTREGADO A LA DIRECTORA', '2026-01-05 19:36:31', 1),
(7, 7, '000007', '2025-12-15', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 1, 'EL MONTO EN EFECTIVO FUE ENTREGADO A LA DIRECTORA', '2026-01-05 19:41:10', 1),
(8, 8, '000008', '2025-12-15', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-05 20:04:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensualidad_detalle`
--

CREATE TABLE `mensualidad_detalle` (
  `id` int(11) NOT NULL,
  `matricula_mes_id` int(11) NOT NULL,
  `id_matricula_detalle` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `pagado` tinyint(1) NOT NULL DEFAULT '0',
  `recibo` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `mensualidad_detalle`
--

INSERT INTO `mensualidad_detalle` (`id`, `matricula_mes_id`, `id_matricula_detalle`, `monto`, `pagado`, `recibo`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 2, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(2, 3, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(3, 4, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(4, 5, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(5, 6, 1, '335.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(6, 7, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(7, 8, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(8, 9, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(9, 10, 1, '305.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(10, 11, 1, '335.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(11, 2, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(12, 3, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(13, 4, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(14, 5, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(15, 6, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(16, 7, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(17, 8, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(18, 9, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(19, 10, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(20, 11, 2, '300.00', 0, 0, '', '2026-01-05 17:05:59', 1),
(21, 2, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(22, 3, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(23, 4, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(24, 5, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(25, 6, 3, '330.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(26, 7, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(27, 8, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(28, 9, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(29, 10, 3, '300.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(30, 11, 3, '330.00', 0, 0, '', '2026-01-05 17:08:15', 1),
(31, 2, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(32, 3, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(33, 4, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(34, 5, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(35, 6, 4, '360.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(36, 7, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(37, 8, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(38, 9, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(39, 10, 4, '330.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(40, 11, 4, '360.00', 0, 0, '', '2026-01-05 18:50:51', 1),
(41, 2, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(42, 3, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(43, 4, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(44, 5, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(45, 6, 5, '335.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(46, 7, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(47, 8, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(48, 9, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(49, 10, 5, '305.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(50, 11, 5, '335.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(51, 2, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(52, 3, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(53, 4, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(54, 5, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(55, 6, 6, '345.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(56, 7, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(57, 8, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(58, 9, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(59, 10, 6, '315.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(60, 11, 6, '345.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(61, 2, 7, '320.00', 0, 0, 'DSCTO. HERMANOS - 10 SOLES', '2026-01-05 19:41:10', 1),
(62, 3, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(63, 4, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(64, 5, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(65, 6, 7, '350.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(66, 7, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(67, 8, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(68, 9, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(69, 10, 7, '320.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(70, 11, 7, '350.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(71, 2, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(72, 3, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(73, 4, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(74, 5, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(75, 6, 8, '360.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(76, 7, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(77, 8, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(78, 9, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(79, 10, 8, '330.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(80, 11, 8, '360.00', 0, 0, '', '2026-01-05 20:04:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_alumno`
--

CREATE TABLE `usuario_alumno` (
  `id` int(11) NOT NULL,
  `id_apoderado` int(11) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `numerodocumento` varchar(20) NOT NULL,
  `nombreyapellido` varchar(100) NOT NULL,
  `nacimiento` date DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_sexo` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_alumno`
--

INSERT INTO `usuario_alumno` (`id`, `id_apoderado`, `id_documento`, `numerodocumento`, `nombreyapellido`, `nacimiento`, `telefono`, `id_sexo`, `usuario`, `clave`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 1, '93320382', 'VARGAS PONTE SOFIA CATALEYA', '2023-03-27', '', 2, '93320382', '93320382', '', '2026-01-05 16:50:28', 1),
(2, 3, 1, '90178071', 'SALVADOR LOARTE ANGELY CAMILA', '2017-04-14', '', 2, '90178071', '90178071', '', '2026-01-05 17:05:59', 1),
(3, 3, 1, '78637674', 'SALVADOR LOARTE JONATHAN BAHYRÓN', '2014-06-20', '', 1, '78637674', '78637674', '', '2026-01-05 17:08:15', 1),
(4, 4, 1, '79878771', 'ESPINOZA GARCIA ANGELO CAMILO', '2016-10-01', '', 1, '79878771', '79878771', '', '2026-01-05 18:50:51', 1),
(5, 5, 1, '92986466', 'ALBARRAN JARA LEONARDO ANDREE', '2022-07-22', '', 1, '92986466', '92986466', '', '2026-01-05 19:32:29', 1),
(6, 6, 1, '92611878', 'RIOS PEREIRA BEN JUDÁ', '2021-11-04', '', 1, '92611878', '92611878', '', '2026-01-05 19:36:31', 1),
(7, 6, 1, '78915280', 'RIOS PEREIRA VICTORIA ARIANA', '2015-01-08', '', 2, '78915280', '78915280', '', '2026-01-05 19:41:10', 1),
(8, 7, 1, '90259735', 'MEDINA FUENTES BRISTAN MILER', '2017-06-09', '', 1, '90259735', '90259735', '', '2026-01-05 20:04:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_apoderado`
--

CREATE TABLE `usuario_apoderado` (
  `id` int(11) NOT NULL,
  `id_apoderado_tipo` int(11) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `numerodocumento` varchar(20) NOT NULL,
  `nombreyapellido` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_apoderado`
--

INSERT INTO `usuario_apoderado` (`id`, `id_apoderado_tipo`, `id_documento`, `numerodocumento`, `nombreyapellido`, `telefono`, `usuario`, `clave`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, 1, '46014535', 'ELSA ELVIRA PONTE SANTOS', '996568120', '46014535', '46014535', '', '2026-01-05 16:50:28', 1),
(2, 3, 1, '10509059', 'CECILIA ROSARIO MANRIQUE LOPEZ', '976300448', '10509059', '10509059', '', '2026-01-05 16:53:00', 1),
(3, 1, 1, '40865109', 'RUBEN SALVADOR MARENGO', '981959676', '40865109', '40865109', '', '2026-01-05 17:05:59', 1),
(4, 1, 1, '40148474', 'MELINDA GARCIA OLIVAS', '935928398', '40148474', '40148474', '', '2026-01-05 18:50:51', 1),
(5, 1, 1, '75962058', 'SHADDANA CANDY JARA ROSAS', '923192197', '75962058', '75962058', '985171060 PAPA', '2026-01-05 19:32:29', 1),
(6, 1, 1, '43289465', 'DAYSY BELGICA PEREIRA LOPEZ', '982525851', '43289465', '43289465', 'MIGUEL ANGEL RIOS CHIRINOS\r\nTELEF.: 992058931', '2026-01-05 19:36:31', 1),
(7, 1, 1, '75539860', 'KATIA NORCELIA FUENTES CHAVEZ', '901931165', '75539860', '75539860', '', '2026-01-05 20:04:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_apoderado_tipo`
--

CREATE TABLE `usuario_apoderado_tipo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_apoderado_tipo`
--

INSERT INTO `usuario_apoderado_tipo` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'MADRE', '', '2025-12-27 06:55:02', 1),
(2, 'PADRE', '', '2025-12-27 06:55:07', 1),
(3, 'OTRO FAMILIAR', '', '2025-12-27 06:55:31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_cargo`
--

CREATE TABLE `usuario_cargo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_cargo`
--

INSERT INTO `usuario_cargo` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'DIRECTOR', '', '2025-12-27 07:13:32', 1),
(2, 'ADMINISTRATIVO', '', '2025-12-27 07:13:41', 1),
(3, 'DOCENTE', '', '2025-12-27 07:13:49', 1),
(4, 'AUXILIAR', '', '2026-01-02 06:44:29', 1),
(5, 'APODERADO', '', '2026-01-02 06:44:40', 1),
(6, 'ALUMNO', '', '2026-01-02 06:46:44', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_docente`
--

CREATE TABLE `usuario_docente` (
  `id` int(11) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `numerodocumento` varchar(20) NOT NULL,
  `nombreyapellido` varchar(100) NOT NULL,
  `nacimiento` date NOT NULL,
  `id_estado_civil` int(11) NOT NULL,
  `id_sexo` int(11) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_cargo` int(11) NOT NULL,
  `id_tipo_contrato` int(11) DEFAULT NULL,
  `fechainicio` date NOT NULL,
  `fechafin` date DEFAULT NULL,
  `sueldo` decimal(10,2) NOT NULL,
  `cuentabancaria` varchar(20) DEFAULT NULL,
  `cuentainterbancaria` varchar(20) DEFAULT NULL,
  `sunat_ruc` varchar(11) DEFAULT NULL,
  `sunat_usuario` varchar(50) NOT NULL,
  `sunat_contraseña` varchar(255) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_docente`
--

INSERT INTO `usuario_docente` (`id`, `id_documento`, `numerodocumento`, `nombreyapellido`, `nacimiento`, `id_estado_civil`, `id_sexo`, `direccion`, `telefono`, `correo`, `id_cargo`, `id_tipo_contrato`, `fechainicio`, `fechafin`, `sueldo`, `cuentabancaria`, `cuentainterbancaria`, `sunat_ruc`, `sunat_usuario`, `sunat_contraseña`, `usuario`, `clave`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, '10509059', 'CECILIA ROSARIO MANRIQUE LOPEZ', '1977-01-16', 2, 2, 'PROLONG. LAS GLADIOLAS MZ.X LT.12 EL ERMITAÑO', '976300448', '', 1, 1, '2026-01-01', '2027-02-28', '0.00', '', '', '', '', '', '10509059', '10509059', '', '2026-01-05 16:43:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_documento`
--

CREATE TABLE `usuario_documento` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_documento`
--

INSERT INTO `usuario_documento` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'DNI', '', '2025-12-27 06:53:05', 1),
(2, 'PASAPORTE', '', '2025-12-27 06:53:15', 1),
(3, 'CEDULA', '', '2025-12-27 06:53:21', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_estado_civil`
--

CREATE TABLE `usuario_estado_civil` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_estado_civil`
--

INSERT INTO `usuario_estado_civil` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'SOLTERO', '', '2025-12-27 06:53:35', 1),
(2, 'CASADO', '', '2025-12-27 06:53:42', 1),
(3, 'DIVORCIADO', '', '2025-12-27 06:53:50', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_sexo`
--

CREATE TABLE `usuario_sexo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_sexo`
--

INSERT INTO `usuario_sexo` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'MASCULINO', '', '2025-12-27 06:54:02', 1),
(2, 'FEMENINO', '', '2025-12-27 06:54:10', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_tipo_contrato`
--

CREATE TABLE `usuario_tipo_contrato` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `observaciones` text,
  `fechacreado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_tipo_contrato`
--

INSERT INTO `usuario_tipo_contrato` (`id`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'PLANILLA', '', '2025-12-27 06:54:26', 1),
(2, 'HONORARIOS', '', '2025-12-27 06:54:37', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacen_categoria`
--
ALTER TABLE `almacen_categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `almacen_comprobante`
--
ALTER TABLE `almacen_comprobante`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `almacen_ingreso`
--
ALTER TABLE `almacen_ingreso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_apoderado_id` (`usuario_apoderado_id`),
  ADD KEY `almacen_comprobante_id` (`almacen_comprobante_id`),
  ADD KEY `almacen_metodo_pago_id` (`almacen_metodo_pago_id`);

--
-- Indices de la tabla `almacen_ingreso_detalle`
--
ALTER TABLE `almacen_ingreso_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `almacen_ingreso_id` (`almacen_ingreso_id`),
  ADD KEY `almacen_producto_id` (`almacen_producto_id`);

--
-- Indices de la tabla `almacen_metodo_pago`
--
ALTER TABLE `almacen_metodo_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `almacen_producto`
--
ALTER TABLE `almacen_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `almacen_salida`
--
ALTER TABLE `almacen_salida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_apoderado_id` (`usuario_apoderado_id`),
  ADD KEY `almacen_comprobante_id` (`almacen_comprobante_id`),
  ADD KEY `almacen_metodo_pago_id` (`almacen_metodo_pago_id`);

--
-- Indices de la tabla `almacen_salida_detalle`
--
ALTER TABLE `almacen_salida_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `almacen_salida_id` (`almacen_salida_id`),
  ADD KEY `almacen_producto_id` (`almacen_producto_id`);

--
-- Indices de la tabla `biblioteca_libro`
--
ALTER TABLE `biblioteca_libro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_documento_responsable` (`id_documento_responsable`);

--
-- Indices de la tabla `documento_detalle`
--
ALTER TABLE `documento_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_matricula_detalle` (`id_matricula_detalle`),
  ADD KEY `id_documento` (`id_documento`);

--
-- Indices de la tabla `documento_responsable`
--
ALTER TABLE `documento_responsable`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `institucion`
--
ALTER TABLE `institucion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario_docente` (`id_usuario_docente`);

--
-- Indices de la tabla `institucion_grado`
--
ALTER TABLE `institucion_grado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_institucion_nivel` (`id_institucion_nivel`);

--
-- Indices de la tabla `institucion_lectivo`
--
ALTER TABLE `institucion_lectivo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_institucion` (`id_institucion`);

--
-- Indices de la tabla `institucion_nivel`
--
ALTER TABLE `institucion_nivel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_institucion_lectivo` (`id_institucion_lectivo`);

--
-- Indices de la tabla `institucion_seccion`
--
ALTER TABLE `institucion_seccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_institucion_grado` (`id_institucion_grado`);

--
-- Indices de la tabla `institucion_validacion`
--
ALTER TABLE `institucion_validacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_institucion_seccion` (`id_institucion_seccion`),
  ADD KEY `id_usuario_docente` (`id_usuario_docente`);

--
-- Indices de la tabla `matricula_categoria`
--
ALTER TABLE `matricula_categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `matricula_cobro`
--
ALTER TABLE `matricula_cobro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `matricula_cobro_detalle`
--
ALTER TABLE `matricula_cobro_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_matricula_cobro_detalle_cobro` (`matricula_cobro_id`),
  ADD KEY `fk_matricula_cobro_detalle_mes` (`matricula_mes_id`);

--
-- Indices de la tabla `matricula_detalle`
--
ALTER TABLE `matricula_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_matricula_detalle_matricula` (`id_matricula`),
  ADD KEY `fk_matricula_detalle_matricula_categoria` (`id_matricula_categoria`),
  ADD KEY `fk_matricula_detalle_usuario_apoderado` (`id_usuario_apoderado`),
  ADD KEY `fk_matricula_detalle_usuario_alumno` (`id_usuario_alumno`),
  ADD KEY `fk_matricula_detalle_usuario_apoderado_referido` (`id_usuario_apoderado_referido`);

--
-- Indices de la tabla `matricula_mes`
--
ALTER TABLE `matricula_mes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_matricula_mes_institucion_lectivo` (`institucion_lectivo_id`);

--
-- Indices de la tabla `matricula_metodo_pago`
--
ALTER TABLE `matricula_metodo_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `matricula_monto`
--
ALTER TABLE `matricula_monto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matricula_id` (`matricula_id`),
  ADD KEY `matricula_cobro_id` (`matricula_cobro_id`);

--
-- Indices de la tabla `matricula_pago`
--
ALTER TABLE `matricula_pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_matricula_pago_matricula_detalle` (`id_matricula_detalle`),
  ADD KEY `fk_matricula_pago_metodo_pago` (`id_matricula_metodo_pago`);

--
-- Indices de la tabla `mensualidad_detalle`
--
ALTER TABLE `mensualidad_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_matricula_detalle` (`id_matricula_detalle`),
  ADD KEY `matricula_mes_id` (`matricula_mes_id`);

--
-- Indices de la tabla `usuario_alumno`
--
ALTER TABLE `usuario_alumno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_apoderado` (`id_apoderado`),
  ADD KEY `id_documento` (`id_documento`),
  ADD KEY `id_sexo` (`id_sexo`);

--
-- Indices de la tabla `usuario_apoderado`
--
ALTER TABLE `usuario_apoderado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_apoderado_tipo` (`id_apoderado_tipo`),
  ADD KEY `id_documento` (`id_documento`);

--
-- Indices de la tabla `usuario_apoderado_tipo`
--
ALTER TABLE `usuario_apoderado_tipo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_cargo`
--
ALTER TABLE `usuario_cargo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_docente`
--
ALTER TABLE `usuario_docente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_documento` (`id_documento`),
  ADD KEY `id_estado_civil` (`id_estado_civil`),
  ADD KEY `id_cargo` (`id_cargo`),
  ADD KEY `id_tipo_contrato` (`id_tipo_contrato`),
  ADD KEY `id_sexo` (`id_sexo`);

--
-- Indices de la tabla `usuario_documento`
--
ALTER TABLE `usuario_documento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_estado_civil`
--
ALTER TABLE `usuario_estado_civil`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_sexo`
--
ALTER TABLE `usuario_sexo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_tipo_contrato`
--
ALTER TABLE `usuario_tipo_contrato`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacen_categoria`
--
ALTER TABLE `almacen_categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `almacen_comprobante`
--
ALTER TABLE `almacen_comprobante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `almacen_ingreso`
--
ALTER TABLE `almacen_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `almacen_ingreso_detalle`
--
ALTER TABLE `almacen_ingreso_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `almacen_metodo_pago`
--
ALTER TABLE `almacen_metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `almacen_producto`
--
ALTER TABLE `almacen_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `almacen_salida`
--
ALTER TABLE `almacen_salida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `almacen_salida_detalle`
--
ALTER TABLE `almacen_salida_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `biblioteca_libro`
--
ALTER TABLE `biblioteca_libro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `documento_detalle`
--
ALTER TABLE `documento_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento_responsable`
--
ALTER TABLE `documento_responsable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `institucion`
--
ALTER TABLE `institucion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `institucion_grado`
--
ALTER TABLE `institucion_grado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `institucion_lectivo`
--
ALTER TABLE `institucion_lectivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `institucion_nivel`
--
ALTER TABLE `institucion_nivel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `institucion_seccion`
--
ALTER TABLE `institucion_seccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `institucion_validacion`
--
ALTER TABLE `institucion_validacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `matricula_categoria`
--
ALTER TABLE `matricula_categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `matricula_cobro`
--
ALTER TABLE `matricula_cobro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `matricula_cobro_detalle`
--
ALTER TABLE `matricula_cobro_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `matricula_detalle`
--
ALTER TABLE `matricula_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `matricula_mes`
--
ALTER TABLE `matricula_mes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `matricula_metodo_pago`
--
ALTER TABLE `matricula_metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `matricula_monto`
--
ALTER TABLE `matricula_monto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `matricula_pago`
--
ALTER TABLE `matricula_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mensualidad_detalle`
--
ALTER TABLE `mensualidad_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `usuario_alumno`
--
ALTER TABLE `usuario_alumno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuario_apoderado`
--
ALTER TABLE `usuario_apoderado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuario_apoderado_tipo`
--
ALTER TABLE `usuario_apoderado_tipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario_cargo`
--
ALTER TABLE `usuario_cargo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario_docente`
--
ALTER TABLE `usuario_docente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario_documento`
--
ALTER TABLE `usuario_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario_estado_civil`
--
ALTER TABLE `usuario_estado_civil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario_sexo`
--
ALTER TABLE `usuario_sexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario_tipo_contrato`
--
ALTER TABLE `usuario_tipo_contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `almacen_ingreso`
--
ALTER TABLE `almacen_ingreso`
  ADD CONSTRAINT `almacen_ingreso_ibfk_1` FOREIGN KEY (`usuario_apoderado_id`) REFERENCES `usuario_apoderado` (`id`),
  ADD CONSTRAINT `almacen_ingreso_ibfk_2` FOREIGN KEY (`almacen_comprobante_id`) REFERENCES `almacen_comprobante` (`id`),
  ADD CONSTRAINT `almacen_ingreso_ibfk_3` FOREIGN KEY (`almacen_metodo_pago_id`) REFERENCES `almacen_metodo_pago` (`id`);

--
-- Filtros para la tabla `almacen_ingreso_detalle`
--
ALTER TABLE `almacen_ingreso_detalle`
  ADD CONSTRAINT `almacen_ingreso_detalle_ibfk_1` FOREIGN KEY (`almacen_ingreso_id`) REFERENCES `almacen_ingreso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `almacen_ingreso_detalle_ibfk_2` FOREIGN KEY (`almacen_producto_id`) REFERENCES `almacen_producto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `almacen_producto`
--
ALTER TABLE `almacen_producto`
  ADD CONSTRAINT `almacen_producto_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `almacen_categoria` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `almacen_salida`
--
ALTER TABLE `almacen_salida`
  ADD CONSTRAINT `almacen_salida_ibfk_1` FOREIGN KEY (`usuario_apoderado_id`) REFERENCES `usuario_apoderado` (`id`),
  ADD CONSTRAINT `almacen_salida_ibfk_2` FOREIGN KEY (`almacen_comprobante_id`) REFERENCES `almacen_comprobante` (`id`),
  ADD CONSTRAINT `almacen_salida_ibfk_3` FOREIGN KEY (`almacen_metodo_pago_id`) REFERENCES `almacen_metodo_pago` (`id`);

--
-- Filtros para la tabla `almacen_salida_detalle`
--
ALTER TABLE `almacen_salida_detalle`
  ADD CONSTRAINT `almacen_salida_detalle_ibfk_1` FOREIGN KEY (`almacen_salida_id`) REFERENCES `almacen_salida` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `almacen_salida_detalle_ibfk_2` FOREIGN KEY (`almacen_producto_id`) REFERENCES `almacen_producto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`id_documento_responsable`) REFERENCES `documento_responsable` (`id`);

--
-- Filtros para la tabla `documento_detalle`
--
ALTER TABLE `documento_detalle`
  ADD CONSTRAINT `documento_detalle_ibfk_1` FOREIGN KEY (`id_matricula_detalle`) REFERENCES `matricula_detalle` (`id`),
  ADD CONSTRAINT `documento_detalle_ibfk_2` FOREIGN KEY (`id_documento`) REFERENCES `documento` (`id`);

--
-- Filtros para la tabla `institucion`
--
ALTER TABLE `institucion`
  ADD CONSTRAINT `institucion_ibfk_1` FOREIGN KEY (`id_usuario_docente`) REFERENCES `usuario_docente` (`id`);

--
-- Filtros para la tabla `institucion_grado`
--
ALTER TABLE `institucion_grado`
  ADD CONSTRAINT `institucion_grado_ibfk_1` FOREIGN KEY (`id_institucion_nivel`) REFERENCES `institucion_nivel` (`id`);

--
-- Filtros para la tabla `institucion_lectivo`
--
ALTER TABLE `institucion_lectivo`
  ADD CONSTRAINT `institucion_lectivo_ibfk_1` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id`);

--
-- Filtros para la tabla `institucion_nivel`
--
ALTER TABLE `institucion_nivel`
  ADD CONSTRAINT `institucion_nivel_ibfk_1` FOREIGN KEY (`id_institucion_lectivo`) REFERENCES `institucion_lectivo` (`id`);

--
-- Filtros para la tabla `institucion_seccion`
--
ALTER TABLE `institucion_seccion`
  ADD CONSTRAINT `institucion_seccion_ibfk_1` FOREIGN KEY (`id_institucion_grado`) REFERENCES `institucion_grado` (`id`);

--
-- Filtros para la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`id_institucion_seccion`) REFERENCES `institucion_seccion` (`id`),
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`id_usuario_docente`) REFERENCES `usuario_docente` (`id`);

--
-- Filtros para la tabla `matricula_cobro_detalle`
--
ALTER TABLE `matricula_cobro_detalle`
  ADD CONSTRAINT `fk_matricula_cobro_detalle_cobro` FOREIGN KEY (`matricula_cobro_id`) REFERENCES `matricula_cobro` (`id`),
  ADD CONSTRAINT `fk_matricula_cobro_detalle_mes` FOREIGN KEY (`matricula_mes_id`) REFERENCES `matricula_mes` (`id`);

--
-- Filtros para la tabla `matricula_detalle`
--
ALTER TABLE `matricula_detalle`
  ADD CONSTRAINT `fk_matricula_detalle_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id`),
  ADD CONSTRAINT `fk_matricula_detalle_matricula_categoria` FOREIGN KEY (`id_matricula_categoria`) REFERENCES `matricula_categoria` (`id`),
  ADD CONSTRAINT `fk_matricula_detalle_usuario_alumno` FOREIGN KEY (`id_usuario_alumno`) REFERENCES `usuario_alumno` (`id`),
  ADD CONSTRAINT `fk_matricula_detalle_usuario_apoderado` FOREIGN KEY (`id_usuario_apoderado`) REFERENCES `usuario_apoderado` (`id`),
  ADD CONSTRAINT `fk_matricula_detalle_usuario_apoderado_referido` FOREIGN KEY (`id_usuario_apoderado_referido`) REFERENCES `usuario_apoderado` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `matricula_mes`
--
ALTER TABLE `matricula_mes`
  ADD CONSTRAINT `fk_matricula_mes_institucion_lectivo` FOREIGN KEY (`institucion_lectivo_id`) REFERENCES `institucion_lectivo` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `matricula_monto`
--
ALTER TABLE `matricula_monto`
  ADD CONSTRAINT `matricula_monto_ibfk_1` FOREIGN KEY (`matricula_id`) REFERENCES `matricula` (`id`),
  ADD CONSTRAINT `matricula_monto_ibfk_2` FOREIGN KEY (`matricula_cobro_id`) REFERENCES `matricula_cobro` (`id`);

--
-- Filtros para la tabla `matricula_pago`
--
ALTER TABLE `matricula_pago`
  ADD CONSTRAINT `fk_matricula_pago_matricula_detalle` FOREIGN KEY (`id_matricula_detalle`) REFERENCES `matricula_detalle` (`id`),
  ADD CONSTRAINT `fk_matricula_pago_metodo_pago` FOREIGN KEY (`id_matricula_metodo_pago`) REFERENCES `matricula_metodo_pago` (`id`);

--
-- Filtros para la tabla `mensualidad_detalle`
--
ALTER TABLE `mensualidad_detalle`
  ADD CONSTRAINT `fk_mensualidad_detalle_matricula_mes` FOREIGN KEY (`matricula_mes_id`) REFERENCES `matricula_mes` (`id`),
  ADD CONSTRAINT `mensualidad_detalle_ibfk_2` FOREIGN KEY (`id_matricula_detalle`) REFERENCES `matricula_detalle` (`id`);

--
-- Filtros para la tabla `usuario_alumno`
--
ALTER TABLE `usuario_alumno`
  ADD CONSTRAINT `usuario_alumno_ibfk_1` FOREIGN KEY (`id_apoderado`) REFERENCES `usuario_apoderado` (`id`),
  ADD CONSTRAINT `usuario_alumno_ibfk_2` FOREIGN KEY (`id_documento`) REFERENCES `usuario_documento` (`id`),
  ADD CONSTRAINT `usuario_alumno_ibfk_3` FOREIGN KEY (`id_sexo`) REFERENCES `usuario_sexo` (`id`);

--
-- Filtros para la tabla `usuario_apoderado`
--
ALTER TABLE `usuario_apoderado`
  ADD CONSTRAINT `usuario_apoderado_ibfk_1` FOREIGN KEY (`id_apoderado_tipo`) REFERENCES `usuario_apoderado_tipo` (`id`),
  ADD CONSTRAINT `usuario_apoderado_ibfk_2` FOREIGN KEY (`id_documento`) REFERENCES `usuario_documento` (`id`);

--
-- Filtros para la tabla `usuario_docente`
--
ALTER TABLE `usuario_docente`
  ADD CONSTRAINT `usuario_docente_ibfk_1` FOREIGN KEY (`id_documento`) REFERENCES `usuario_documento` (`id`),
  ADD CONSTRAINT `usuario_docente_ibfk_2` FOREIGN KEY (`id_estado_civil`) REFERENCES `usuario_estado_civil` (`id`),
  ADD CONSTRAINT `usuario_docente_ibfk_3` FOREIGN KEY (`id_cargo`) REFERENCES `usuario_cargo` (`id`),
  ADD CONSTRAINT `usuario_docente_ibfk_4` FOREIGN KEY (`id_tipo_contrato`) REFERENCES `usuario_tipo_contrato` (`id`),
  ADD CONSTRAINT `usuario_docente_ibfk_5` FOREIGN KEY (`id_sexo`) REFERENCES `usuario_sexo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
