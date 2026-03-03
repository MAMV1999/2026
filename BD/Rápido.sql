-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-03-2026 a las 04:29:57
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
(2, 'LIBRO ESCOLAR INICIAL', '', '2026-01-05 12:09:49', 1),
(3, 'LIBRO ESCOLAR PRIMARIA', '', '2026-01-05 12:09:57', 1),
(4, 'DOCUMENTOS', '', '2026-01-06 09:44:36', 1),
(5, 'PRENDA DE VESTIR', '', '2026-01-16 10:59:01', 1),
(6, 'OTROS PAGOS', '', '2026-01-21 12:17:58', 1);

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
(3, 2, 1, '000003', '2026-01-05', 2, '0.00', '', '2026-01-05 13:54:26', 1),
(4, 2, 1, '000004', '2026-01-06', 2, '0.00', '', '2026-01-06 09:47:49', 1),
(5, 2, 1, '000005', '2026-01-16', 2, '0.00', '', '2026-01-16 10:56:51', 1),
(6, 2, 1, '000006', '2026-01-16', 2, '0.00', '', '2026-01-16 11:04:23', 1),
(7, 2, 1, '000007', '2026-01-16', 2, '0.00', '', '2026-01-16 11:32:48', 1),
(8, 2, 1, '000008', '2026-01-21', 2, '0.00', '', '2026-01-21 12:15:17', 1),
(9, 2, 1, '000009', '2026-01-21', 2, '0.00', '', '2026-01-21 12:30:20', 1),
(10, 2, 1, '000010', '2026-01-30', 2, '0.00', '', '2026-01-30 10:23:10', 1),
(11, 2, 1, '000011', '2026-02-02', 2, '0.00', '', '2026-02-02 08:22:11', 1),
(12, 2, 1, '000012', '2026-02-02', 2, '0.00', '', '2026-02-02 08:57:47', 1),
(13, 2, 1, '000013', '2026-02-04', 2, '0.00', '', '2026-02-04 16:30:07', 1),
(14, 2, 1, '000014', '2026-02-24', 2, '0.00', '', '2026-02-24 10:24:18', 1),
(15, 2, 1, '000015', '2026-03-02', 2, '0.00', '', '2026-03-02 09:17:48', 1);

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
(15, 3, 15, 15, '0.00', ''),
(16, 4, 18, 20, '0.00', ''),
(17, 4, 17, 20, '0.00', ''),
(18, 4, 16, 20, '0.00', ''),
(19, 5, 19, 15, '0.00', ''),
(20, 6, 20, 15, '0.00', ''),
(21, 6, 21, 15, '0.00', ''),
(22, 6, 22, 15, '0.00', ''),
(23, 6, 23, 15, '0.00', ''),
(24, 6, 24, 15, '0.00', ''),
(25, 6, 25, 15, '0.00', ''),
(26, 6, 26, 15, '0.00', ''),
(27, 6, 27, 15, '0.00', ''),
(28, 7, 28, 15, '0.00', ''),
(29, 7, 29, 15, '0.00', ''),
(30, 7, 30, 15, '0.00', ''),
(31, 7, 31, 15, '0.00', ''),
(32, 7, 32, 15, '0.00', ''),
(33, 7, 33, 15, '0.00', ''),
(34, 7, 34, 15, '0.00', ''),
(35, 7, 35, 15, '0.00', ''),
(36, 7, 36, 15, '0.00', ''),
(37, 7, 37, 15, '0.00', ''),
(38, 7, 38, 15, '0.00', ''),
(39, 7, 39, 15, '0.00', ''),
(40, 7, 40, 15, '0.00', ''),
(41, 7, 41, 15, '0.00', ''),
(42, 7, 42, 15, '0.00', ''),
(43, 7, 43, 15, '0.00', ''),
(44, 8, 45, 20, '0.00', ''),
(45, 8, 44, 20, '0.00', ''),
(46, 9, 48, 20, '0.00', ''),
(47, 9, 46, 20, '0.00', ''),
(48, 9, 47, 20, '0.00', ''),
(49, 10, 52, 15, '0.00', ''),
(50, 10, 53, 15, '0.00', ''),
(51, 10, 54, 15, '0.00', ''),
(52, 10, 55, 15, '0.00', ''),
(53, 10, 49, 15, '0.00', ''),
(54, 10, 50, 15, '0.00', ''),
(55, 10, 51, 15, '0.00', ''),
(56, 10, 57, 15, '0.00', ''),
(57, 10, 56, 15, '0.00', ''),
(58, 11, 61, 15, '0.00', ''),
(59, 11, 62, 15, '0.00', ''),
(60, 11, 63, 15, '0.00', ''),
(61, 11, 64, 15, '0.00', ''),
(62, 11, 58, 15, '0.00', ''),
(63, 11, 59, 15, '0.00', ''),
(64, 11, 60, 15, '0.00', ''),
(65, 11, 66, 15, '0.00', ''),
(66, 11, 65, 15, '0.00', ''),
(67, 11, 23, 15, '0.00', ''),
(68, 12, 1, 10, '0.00', ''),
(69, 13, 67, 10, '0.00', ''),
(70, 13, 68, 10, '0.00', ''),
(71, 13, 69, 10, '0.00', ''),
(72, 13, 70, 10, '0.00', ''),
(73, 14, 71, 15, '0.00', ''),
(74, 15, 72, 15, '0.00', '');

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
(1, 'CITA PSICOLÓGICA - PROGRAMADA', '', 1, '0.00', '50.00', 3, '2026-01-05 09:36:56', 1),
(2, 'CITA PSICOLÓGICA - INMEDIATA', '', 1, '0.00', '50.00', 10, '2026-01-05 09:36:56', 1),
(3, 'LIBRO INICIAL 2 AÑOS', '', 2, '0.00', '300.00', 11, '2026-01-05 12:12:50', 1),
(4, 'LIBRO INICIAL 3 AÑOS', '', 2, '0.00', '300.00', 5, '2026-01-05 12:12:50', 1),
(5, 'LIBRO INICIAL 4 AÑOS', '', 2, '0.00', '300.00', 13, '2026-01-05 12:12:50', 1),
(6, 'LIBRO INICIAL 5 AÑOS', '', 2, '0.00', '300.00', 13, '2026-01-05 12:12:50', 1),
(7, 'LIBRO PRIMARIA 1 GRADO', '', 3, '0.00', '260.00', 10, '2026-01-05 12:14:57', 1),
(8, 'LIBRO PRIMARIA 2 GRADO', '', 3, '0.00', '260.00', 12, '2026-01-05 12:14:57', 1),
(9, 'LIBRO PRIMARIA 3 GRADO', '', 3, '0.00', '270.00', 10, '2026-01-05 12:14:57', 1),
(10, 'LIBRO PRIMARIA 4 GRADO', '', 3, '0.00', '270.00', 10, '2026-01-05 12:14:57', 1),
(11, 'LIBRO PRIMARIA 5 GRADO', '', 3, '0.00', '290.00', 14, '2026-01-05 12:14:57', 1),
(12, 'LIBRO PRIMARIA 6 GRADO', '', 3, '0.00', '290.00', 6, '2026-01-05 12:14:57', 1),
(13, 'LIBRO PRIMARIA COMPUTACION', '', 3, '0.00', '120.00', 12, '2026-01-05 13:54:01', 1),
(14, 'LIBRO PRIMARIA INGLES', '', 3, '0.00', '110.00', 13, '2026-01-05 13:54:01', 1),
(15, 'RAZ-KIDS', '', 3, '0.00', '100.00', 7, '2026-01-05 13:54:01', 1),
(16, 'CONSTANCIA DE NO ADEUDO', '', 4, '0.00', '10.00', 9, '2026-01-06 09:47:18', 1),
(17, 'CONSTANCIA DE MATRICULA', '', 4, '0.00', '10.00', 6, '2026-01-06 09:47:18', 1),
(18, 'CERTIFICADO DE ESTUDIO', '', 4, '0.00', '80.00', 6, '2026-01-06 09:47:18', 1),
(19, 'PROMOCION - LIBRO PRIMARIA INGLES Y COMPUTACION', '', 3, '0.00', '220.00', 11, '2026-01-16 10:55:46', 0),
(20, 'BUZO TALLA 4', '', 5, '0.00', '76.00', 15, '2026-01-16 11:02:39', 1),
(21, 'BUZO TALLA 6', '', 5, '0.00', '76.00', 15, '2026-01-16 11:02:39', 1),
(22, 'BUZO TALLA 8', '', 5, '0.00', '76.00', 15, '2026-01-16 11:02:39', 1),
(23, 'BUZO TALLA 10', '', 5, '0.00', '78.00', 29, '2026-01-16 11:02:39', 1),
(24, 'BUZO TALLA 12', '', 5, '0.00', '80.00', 15, '2026-01-16 11:02:39', 1),
(25, 'BUZO TALLA 14', '', 5, '0.00', '82.00', 12, '2026-01-16 11:02:39', 1),
(26, 'BUZO TALLA 16', '', 5, '0.00', '83.00', 15, '2026-01-16 11:02:39', 1),
(27, 'BUZO TALLA S', '', 5, '0.00', '83.00', 14, '2026-01-16 11:02:39', 1),
(28, 'POLO BLANCO TALLA 4', '', 5, '0.00', '34.00', 15, '2026-01-16 11:25:35', 1),
(29, 'POLO BLANCO TALLA 6', '', 5, '0.00', '34.00', 15, '2026-01-16 11:25:35', 1),
(30, 'POLO BLANCO TALLA 8', '', 5, '0.00', '34.00', 15, '2026-01-16 11:25:35', 1),
(31, 'POLO BLANCO TALLA 10', '', 5, '0.00', '36.00', 13, '2026-01-16 11:25:35', 1),
(32, 'POLO BLANCO TALLA 12', '', 5, '0.00', '39.00', 15, '2026-01-16 11:25:35', 1),
(33, 'POLO BLANCO TALLA 14', '', 5, '0.00', '41.00', 11, '2026-01-16 11:25:35', 1),
(34, 'POLO BLANCO TALLA 16', '', 5, '0.00', '43.00', 14, '2026-01-16 11:25:35', 1),
(35, 'POLO BLANCO TALLA S', '', 5, '0.00', '47.00', 15, '2026-01-16 11:25:35', 1),
(36, 'POLO PLOMO TALLA 4', '', 5, '0.00', '34.00', 15, '2026-01-16 11:25:35', 1),
(37, 'POLO PLOMO TALLA 6', '', 5, '0.00', '34.00', 15, '2026-01-16 11:25:35', 1),
(38, 'POLO PLOMO TALLA 8', '', 5, '0.00', '34.00', 15, '2026-01-16 11:25:35', 1),
(39, 'POLO PLOMO TALLA 10', '', 5, '0.00', '36.00', 14, '2026-01-16 11:25:35', 1),
(40, 'POLO PLOMO TALLA 12', '', 5, '0.00', '39.00', 15, '2026-01-16 11:25:35', 1),
(41, 'POLO PLOMO TALLA 14', '', 5, '0.00', '41.00', 14, '2026-01-16 11:25:35', 1),
(42, 'POLO PLOMO TALLA 16', '', 5, '0.00', '43.00', 14, '2026-01-16 11:25:35', 1),
(43, 'POLO PLOMO TALLA S', '', 5, '0.00', '47.00', 14, '2026-01-16 11:25:35', 1),
(44, 'RESOLUCION DE TRASLADO', '', 4, '0.00', '30.00', 19, '2026-01-21 12:13:28', 1),
(45, 'CONSTANCIA DE CONDUCTA', '', 4, '0.00', '0.00', 19, '2026-01-21 12:13:28', 1),
(46, 'PROMOCIÓN IMPRESIÓN INICIAL - PAGO ANUAL', '', 6, '0.00', '80.00', 17, '2026-01-21 12:29:42', 1),
(47, 'PROMOCIÓN IMPRESIÓN PRIMARIA - PAGO ANUAL', '', 6, '0.00', '60.00', 18, '2026-01-21 12:29:42', 1),
(48, 'MANTENIMIENTO JULIO Y DICIEMBRE', '', 6, '0.00', '60.00', 19, '2026-01-21 12:29:42', 1),
(49, 'FALDA SHORT TALLA 4', '', 5, '0.00', '33.00', 15, '2026-01-30 10:22:36', 1),
(50, 'FALDA SHORT TALLA 6', '', 5, '0.00', '33.00', 15, '2026-01-30 10:22:36', 1),
(51, 'FALDA SHORT TALLA 8', '', 5, '0.00', '33.00', 15, '2026-01-30 10:22:36', 1),
(52, 'FALDA SHORT TALLA 10', '', 5, '0.00', '38.00', 14, '2026-01-30 10:22:36', 1),
(53, 'FALDA SHORT TALLA 12', '', 5, '0.00', '38.00', 15, '2026-01-30 10:22:36', 1),
(54, 'FALDA SHORT TALLA 14', '', 5, '0.00', '41.00', 14, '2026-01-30 10:22:36', 1),
(55, 'FALDA SHORT TALLA 16', '', 5, '0.00', '41.00', 15, '2026-01-30 10:22:36', 1),
(56, 'FALDA SHORT TALLA S', '', 5, '0.00', '43.00', 15, '2026-01-30 10:22:36', 1),
(57, 'FALDA SHORT TALLA M', '', 5, '0.00', '43.00', 15, '2026-01-30 10:22:36', 1),
(58, 'SHORT TALLA 4', '', 5, '0.00', '31.00', 15, '2026-02-02 08:21:11', 1),
(59, 'SHORT TALLA 6', '', 5, '0.00', '31.00', 15, '2026-02-02 08:21:11', 1),
(60, 'SHORT TALLA 8', '', 5, '0.00', '31.00', 15, '2026-02-02 08:21:11', 1),
(61, 'SHORT TALLA 10', '', 5, '0.00', '36.00', 15, '2026-02-02 08:21:11', 1),
(62, 'SHORT TALLA 12', '', 5, '0.00', '36.00', 15, '2026-02-02 08:21:11', 1),
(63, 'SHORT TALLA 14', '', 5, '0.00', '39.00', 14, '2026-02-02 08:21:11', 1),
(64, 'SHORT TALLA 16', '', 5, '0.00', '39.00', 15, '2026-02-02 08:21:11', 1),
(65, 'SHORT TALLA S', '', 5, '0.00', '41.00', 14, '2026-02-02 08:21:11', 1),
(66, 'SHORT TALLA M', '', 5, '0.00', '41.00', 15, '2026-02-02 08:21:11', 1),
(67, 'VACACIONES ÚTILES 2026 - 2 AÑOS', '', 6, '0.00', '180.00', 10, '2026-02-04 16:29:39', 1),
(68, 'VACACIONES ÚTILES 2026 - 3 AÑOS', '', 6, '0.00', '180.00', 10, '2026-02-04 16:29:39', 1),
(69, 'VACACIONES ÚTILES 2026 - 4 AÑOS', '', 6, '0.00', '180.00', 9, '2026-02-04 16:29:39', 1),
(70, 'VACACIONES ÚTILES 2026 - 5 AÑOS', '', 6, '0.00', '180.00', 10, '2026-02-04 16:29:39', 1),
(71, 'PACK EBENEZER', '', 6, '0.00', '15.00', 12, '2026-02-24 10:24:01', 1),
(72, 'CUADERNO DE CONTROL', '', 6, '0.00', '12.00', 13, '2026-03-02 09:17:30', 1);

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
(3, 4, 1, '000003', '2025-12-12', 1, '350.00', 'PAGO COMPLETO - LIBRO PRIMARIA 4 GRADO - PENDIENTE DE ENTREGA\r\nADELANTO DE PAGO - PROMOCION - LIBRO PRIMARIA INGLES Y COMPUTACION - PAGA 20 SOLES\r\nSE LE CAMBIA EL PAGO DE 20 SOLES A RAZKIDS Y AGREGA UN PAGO DE 60 SOLES - PENDIENTE 20 SOLES', '2026-01-05 13:56:44', 1),
(4, 5, 1, '000004', '2025-12-15', 1, '50.00', 'CITA PSICOLÓGICA – PROGRAMADA\r\nATENDIDA', '2026-01-05 14:34:22', 1),
(5, 6, 1, '000005', '2025-12-15', 2, '30.00', 'CITA PSICOLÓGICA – PROGRAMADA\r\nATENDIDA', '2026-01-05 14:43:10', 1),
(6, 6, 1, '000006', '2025-12-15', 2, '40.00', 'EL MONTO EN EFECTIVO SE ENTREGO A LA DIRECTORA\r\n\r\nADELANTO DE PAGO REUNION DE PADRES - EFECTIVO\r\nLIBRO PRIMARIA 5 GRADO 20 SOLES\r\n\r\nADELANTO DE PAGO 15/12/2025 - EFECTIVO\r\nPROMOCION - LIBRO PRIMARIA INGLES Y COMPUTACION - 20 SOLES', '2026-01-05 14:44:39', 1),
(7, 7, 1, '000007', '2025-12-15', 1, '250.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-01-05 15:05:45', 1),
(8, 8, 1, '000008', '2025-12-15', 1, '50.00', 'LIBRO PRIMARIA 4 GRADO - 30 SOLES\r\nPROMOCION - LIBRO PRIMARIA INGLES Y COMPUTACION - 20 SOLES', '2026-01-06 11:51:36', 1),
(9, 9, 1, '000009', '2025-12-15', 1, '60.00', 'LIBRO PRIMARIA 4 GRADO - 30 SOLES\r\nPROMOCION - LIBRO PRIMARIA INGLES Y COMPUTACION - 30 SOLES', '2026-01-06 11:55:29', 1),
(10, 12, 1, '000010', '2025-12-16', 3, '250.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-01-06 12:06:17', 1),
(11, 13, 1, '000011', '2025-12-29', 1, '30.00', 'LIBRO PRIMARIA 6 GRADO ADELANTO - 30 SOLES', '2026-01-06 12:10:08', 1),
(12, 14, 1, '000012', '2026-01-05', 2, '50.00', 'LAUANA GUANIÑO\r\n2 GRADO\r\nHORA DE EVALUACIÓN\r\nJUEVES 22 - 10 AM A 11 AM\r\nPUNTUALIDAD, SI LLEGA TARDE LA EVALUACIÓN DURARÁ SOLO EL TIEMPO RESTANTE', '2026-01-06 12:13:48', 1),
(13, 16, 1, '000013', '2025-12-15', 1, '30.00', 'ADELANTO DE PAGO - LIBRO PRIMARIA 4 GRADO\r\nMONTO TOTAL 250 SOLES', '2026-01-07 09:30:43', 1),
(14, 17, 1, '000014', '2025-12-18', 2, '10.00', 'LIBRO PRIMARIA 6 GRADO ADELANTO - 10 SOLES EFECTIVO', '2026-01-07 09:32:48', 1),
(15, 20, 1, '000015', '2026-01-07', 1, '50.00', 'CITA PENDIENTE', '2026-01-07 15:56:56', 1),
(16, 21, 1, '000016', '2026-01-08', 2, '90.00', 'DOCUMENTACION PENDIENTE.\r\nLA APODERADA MENCIONA QUE LA DOCUMENTACION LO RECOGERA SU HIJO.', '2026-01-08 09:56:50', 1),
(17, 8, 1, '000017', '2026-01-13', 1, '100.00', 'DOCUMENTOS ENTREGADOS 26/01', '2026-01-13 11:44:14', 1),
(18, 25, 1, '000018', '2026-01-16', 1, '100.00', 'DOCUMENTOS PENDIENTES', '2026-01-16 12:27:24', 1),
(19, 26, 1, '000019', '2026-01-19', 2, '100.00', 'DOCUMENTOS ENTREGADOS', '2026-01-19 10:46:08', 1),
(20, 27, 1, '000020', '2026-01-19', 1, '50.00', 'HASSAN BAZALAR\r\n2 GRADO\r\nHORA DE EVALUACIÓN\r\nJUEVES 22 - 12 PM A 1 PM\r\nPUNTUALIDAD, SI LLEGA TARDE LA EVALUACIÓN DURARÁ SOLO EL TIEMPO RESTANTE', '2026-01-20 10:05:00', 1),
(21, 28, 1, '000021', '2026-01-19', 1, '50.00', 'EYDAN VALDIVIEZO\r\n1 GRADO\r\nHORA DE EVALUACIÓN\r\nJUEVES 22 - 11 AM A 12 PM\r\nPUNTUALIDAD, SI LLEGA TARDE LA EVALUACIÓN DURARÁ SOLO EL TIEMPO RESTANTE', '2026-01-20 14:35:03', 1),
(22, 25, 1, '000022', '2026-01-20', 1, '30.00', 'DOCUMENTOS PENDIENTES', '2026-01-21 12:16:06', 1),
(23, 3, 1, '000023', '2025-12-11', 1, '180.00', '', '2026-01-21 14:23:37', 1),
(24, 31, 1, '000024', '2026-01-21', 2, '80.00', '', '2026-01-22 10:05:43', 1),
(25, 32, 1, '000025', '2026-01-21', 2, '50.00', 'CITA ATENDIDA\r\nZULEIDY CAROLINA VENTURA ZEA\r\nCHAVEZ VENTURA IVANNA DEL CARMEN\r\n910063816\r\n4 GRADO\r\nJUEVES 22 DE ENERO', '2026-01-22 10:29:44', 1),
(26, 9, 1, '000026', '2026-01-27', 1, '100.00', 'ENTREGA PENDIENTE', '2026-01-27 11:02:17', 1),
(27, 37, 1, '000027', '2026-01-30', 3, '252.00', 'PENDIENTE LIBRO PRIMARIA 6 GRADO', '2026-01-30 10:30:37', 1),
(28, 38, 1, '000028', '2026-01-30', 1, '100.00', 'PENDIENTE DE ENTREGA', '2026-01-30 11:59:37', 1),
(29, 39, 1, '000029', '2026-01-30', 1, '50.00', 'CITA ATENDIDA\r\nMARYCIELO NASHELI ESPINAL LEVANO\r\nESPINAL LEVANO JHARED DASIEL ENRIQUE\r\n968151666\r\n2 GRADO\r\nJUEVES 05 DE FEBRERO\r\n09:00 AM A 10:00 AM', '2026-01-30 12:09:05', 1),
(30, 22, 1, '000030', '2026-01-30', 3, '620.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-01-30 14:34:02', 1),
(31, 24, 1, '000031', '2026-01-30', 1, '50.00', 'CITA ATENDIDA\r\nLISBETH DAYANA FLORES TRUJILLO\r\nDIONICIO FLORES KAYLANI CATALINA\r\n993747432\r\n3 AÑOS\r\nJUEVES 05 DE FEBRERO\r\n10:00 AM A 11:00 AM', '2026-01-30 15:23:55', 1),
(32, 42, 1, '000032', '2026-02-02', 2, '50.00', 'CITA ATENDIDA\r\nANGELICA MARIELA PRADO NEYRA\r\nURBAY PRADO LUKAS ALESSANDRO\r\n924339115\r\n1 GRADO\r\nJUEVES 05 DE FEBRERO\r\n12:00 PM A 01:00 PM', '2026-02-02 09:01:21', 1),
(33, 43, 1, '000033', '2026-02-02', 1, '100.00', 'DOCUMENTOS ENTREGADOS\r\n09 DE FEBRERO DEL 2026', '2026-02-02 12:02:18', 1),
(34, 44, 1, '000034', '2026-02-02', 1, '50.00', 'CITA ATENDIDA\r\nEDITH YOLANDA LOPEZ CURE\r\nREYNA LOPEZ JOAO JOSE\r\n980602670\r\n4 AÑOS\r\nJUEVES 05 DE FEBRERO\r\n11:00 AM A 12:00 PM', '2026-02-02 14:15:29', 1),
(35, 45, 1, '000035', '2026-02-03', 1, '50.00', 'CITA ATENDIDA\r\nEMELY CERVANTES DE LA CRUZ\r\nTANTARUNA CERVANTES SANTIAGO ALESSANDRO\r\n912008608\r\n1 GRADO\r\nJUEVES 05 DE FEBRERO\r\n01:00 PM A 02:00 PM', '2026-02-03 12:00:08', 1),
(36, 11, 1, '000036', '2026-02-03', 2, '30.00', 'ADELANTO DE PAGO 30 SOLES LIBRO PRIMARIA 6 GRADO - PAGO TOTAL 290 SOLES', '2026-02-03 15:13:47', 1),
(37, 36, 1, '000037', '2026-01-01', 1, '30.00', '', '2026-02-03 23:23:27', 1),
(38, 47, 1, '000038', '2026-02-04', 2, '100.00', '', '2026-02-04 16:32:47', 1),
(39, 40, 1, '000039', '2026-02-05', 1, '30.00', 'ADELANTO DE PAGO\r\nLIBRO PRIMARIA 3 GRADO\r\nPAGO TOTAL 270 SOLES', '2026-02-05 09:14:39', 1),
(40, 1, 1, '000040', '2026-02-05', 1, '300.00', 'PAGO COMPLETO - PENDIENTE ENTREGA', '2026-02-05 09:31:06', 1),
(41, 23, 1, '000041', '2026-02-05', 1, '620.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-05 10:24:33', 1),
(42, 48, 1, '000042', '2026-02-05', 1, '100.00', 'ADELANTO DE PAGO\r\nLIBRO PRIMARIA 5 GRADO\r\nPAGO TOTAL 290 SOLES', '2026-02-05 12:04:37', 1),
(43, 31, 1, '000043', '2026-02-05', 1, '300.00', 'PAGO COMPLETO - PENDIENTE DE ENTRAGA', '2026-02-05 15:52:26', 1),
(44, 35, 1, '000044', '2026-02-05', 1, '300.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-05 16:01:56', 1),
(45, 1, 1, '000045', '2026-02-05', 1, '300.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-05 16:02:51', 0),
(46, 51, 1, '000046', '2026-02-09', 1, '100.00', 'PENDIENTE DE ENTREGA', '2026-02-09 08:35:56', 1),
(47, 41, 1, '000047', '2026-02-09', 1, '100.00', 'ADELANTO DE PAGO\r\nLIBRO PRIMARIA 6 GRADO\r\nPAGO TOTAL 290 SOLES', '2026-02-09 08:43:02', 1),
(48, 5, 1, '000048', '2026-02-09', 1, '80.00', 'ADELANTO DE PAGO - LIBRO INICIAL 3 AÑOS\r\nPAGO TOTAL 300 SOLES\r\n30 SOLES - 09 DE FEBRERO\r\n50 SOLES - 25 DE FEBRERO', '2026-02-09 08:44:22', 1),
(49, 32, 1, '000049', '2026-02-09', 1, '40.00', 'ADELANTO DE PAGO\r\nLIBRO PRIMARIA 4 GRADO\r\nPAGO TOTAL 270 SOLES', '2026-02-09 08:48:29', 1),
(50, 52, 1, '000050', '2026-02-09', 2, '200.00', 'PENDIENTE DE ENTREGA', '2026-02-09 15:13:10', 1),
(51, 54, 1, '000051', '2026-02-10', 1, '50.00', 'CITA PENDIENTE\r\nYENY MARCELA NOEL CLERQUE\r\nROSAS NOEL ANTONELA FRANCESCA\r\n976246137\r\n4 AÑOS\r\nPOR CONFIRMAR FECHA Y HORA', '2026-02-11 08:16:13', 1),
(52, 36, 1, '000052', '2026-01-01', 1, '30.00', 'ADELANTO DE PAGO\r\nPROMOCION - LIBRO PRIMARIA INGLES Y COMPUTACION\r\nPAGO TOTAL 220 SOLES', '2026-02-11 08:48:45', 0),
(53, 55, 1, '000053', '2026-02-11', 1, '50.00', 'CITA PENDIENTE\r\nWENDY JULIANA SUAREZ TERRONES\r\nMANCO SUAREZ ARIANNA MASSIEL\r\n915150897\r\n3 AÑOS\r\nPENDIENTE FECHA Y HORA', '2026-02-11 11:12:37', 1),
(54, 34, 1, '000054', '2026-02-11', 1, '300.00', 'PAGO COMPLETO - PENDINETE DE ENTREGA', '2026-02-11 11:14:27', 1),
(55, 6, 1, '000055', '2026-02-11', 2, '300.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-11 15:22:55', 1),
(56, 10, 1, '000056', '2026-02-12', 1, '100.00', '', '2026-02-12 16:54:08', 1),
(57, 50, 1, '000057', '2026-02-12', 1, '300.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-16 21:55:09', 1),
(58, 42, 1, '000058', '2026-02-16', 1, '260.00', 'PAGO COMPLETO', '2026-02-17 18:43:19', 1),
(59, 58, 1, '000059', '2026-02-19', 1, '90.00', 'DOCUMENTOS PENDIENTE DE ENTREGA\r\nFECHA JUEVES 26', '2026-02-19 11:31:13', 1),
(60, 46, 1, '000060', '2026-02-19', 1, '300.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-19 14:21:17', 1),
(61, 7, 1, '000061', '2026-02-13', 1, '120.00', 'PAGO COMPLETO - PENDIENTE DE ENTREGA', '2026-02-19 14:26:14', 1),
(62, 59, 1, '000062', '2026-02-19', 1, '300.00', '', '2026-02-19 15:17:08', 1),
(63, 59, 1, '000063', '2026-02-19', 1, '80.00', '', '2026-02-19 15:18:09', 1),
(64, 60, 1, '000064', '2026-02-19', 1, '200.00', 'FECHA DE ENTREGA 26 DE FEBRERO', '2026-02-19 15:32:20', 1),
(65, 61, 1, '000065', '2026-02-19', 1, '50.00', 'CITA PENDIENTE\r\nKARINA LICET OCHANTE HUYHUA\r\nPORTAL OCHANTE KEREN MICAELA\r\n940189176\r\nNUNCA FUE AL COLEGIO, CUMPLE 7 EN SEPTIEMBRE\r\nCAL.LOS PENSAMIENTOS NRO. 261\r\nPENDIENTE FECHA Y HORA', '2026-02-19 15:45:53', 1),
(66, 35, 1, '000066', '2026-02-19', 1, '80.00', '', '2026-02-19 15:47:50', 1),
(67, 30, 1, '000067', '2026-02-19', 1, '50.00', 'ADELANTO DE PAGO - LIBRO INICIAL 2 AÑOS\r\nMONTO TOTAL 300 SOLES', '2026-02-19 16:24:44', 1),
(68, 8, 1, '000068', '2026-02-23', 1, '50.00', 'ADELANTO DE PAGO\r\nLIBRO INICIAL 5 AÑOS\r\nCOSTO TOTAL 300 SOLES', '2026-02-23 12:00:28', 1),
(69, 63, 1, '000069', '2026-02-23', 1, '50.00', 'ADELANTO DE LIBRO\r\nLIBRO ESTIMULACIÓN TEMPRANA 2 AÑOS\r\nPAGO TOTAL 300 SOLES', '2026-02-23 12:01:33', 1),
(70, 15, 1, '000070', '2026-02-21', 1, '100.00', '', '2026-02-23 14:39:03', 1),
(71, 65, 1, '000071', '2026-02-24', 1, '50.00', 'ADELANTO DE LIBRO - LIBRO INICIAL 3 AÑOS\r\nPAGO TOTAL 300 SOLES', '2026-02-24 09:48:52', 1),
(72, 11, 1, '000072', '2026-02-24', 1, '156.00', 'PENDIENTE DE ENTREGA PACK EBENEZER\r\n100 EFECTIVO - 56 YAPE', '2026-02-24 10:26:10', 1),
(73, 50, 1, '000073', '2026-02-24', 1, '38.00', 'ENTREGADO', '2026-02-24 10:27:52', 1),
(74, 67, 1, '000074', '2026-02-24', 1, '90.00', 'FECHA DE ENTREGA 03 DE MARZO', '2026-02-24 10:35:01', 1),
(75, 3, 1, '000075', '2026-02-25', 1, '250.00', 'PENDIENTE DE ENTREGA - POLO PLOMO TALLA 14', '2026-02-25 09:53:25', 1),
(76, 24, 1, '000076', '2026-02-25', 1, '300.00', 'PAGO COMPLETO', '2026-02-25 15:16:02', 1),
(77, 29, 1, '000077', '2026-02-25', 1, '50.00', 'ADELANTO DE PAGO - LIBRO INICIAL 3 AÑOS\r\nPAGO TOTAL 300 SOLES', '2026-02-26 08:32:39', 1),
(78, 62, 1, '000078', '2026-02-26', 3, '200.00', 'ADELANTO DE PAGO - LIBRO PRIMARIA 1 GRADO Y LIBRO PRIMARIA 3 GRADO\r\nMONTO TOTAL 530 SOLES', '2026-02-26 09:30:46', 1),
(79, 55, 1, '000079', '2026-02-26', 1, '50.00', 'ADELANTO DE PAGO - LIBRO INICIAL 3 AÑOS\r\nMONTO TOTAL 300 SOLES', '2026-02-26 14:25:02', 1),
(80, 66, 1, '000080', '2026-02-27', 1, '30.00', 'ADELANTO DE PAGO - LIBRO PRIMARIA 2 GRADO\r\nMONTO TOTAL 260 SOLES', '2026-02-27 09:19:52', 1),
(81, 77, 1, '000081', '2026-02-27', 1, '452.00', 'ADELANTO DE PAGO - LIBRO PRIMARIA 1 GRADO Y LIBRO PRIMARIA 2 GRADO\r\nPAGO TOTAL 520 TOTAL', '2026-02-27 14:44:17', 1),
(82, 78, 1, '000082', '2026-02-27', 1, '50.00', 'CITA PENDIENTE\r\nNATALI STEFANI POMALAZA FERNANDEZ\r\nGOMEZ POMALAZA DOMINICK EMILIANO ALFREDO\r\n966306903\r\n3 AÑOS\r\nPOR CONFIRMAR FECHA Y HORA', '2026-02-28 09:10:49', 1),
(83, 79, 1, '000083', '2026-03-02', 2, '821.00', 'PENDIENTE DE ENTREGA\r\nBUZO TALLA 10\r\nPOLO BLANCO TALLA 10\r\n\r\nEFECTIVO 820 SOLES\r\nYAPE 1 SOLES', '2026-03-02 09:23:16', 1),
(84, 42, 1, '000084', '2026-03-02', 1, '80.00', 'ENTREGADO', '2026-03-02 09:44:40', 1),
(85, 42, 1, '000085', '2026-03-02', 1, '100.00', '', '2026-03-02 09:50:19', 1),
(86, 48, 1, '000086', '2026-03-01', 3, '100.00', '', '2026-03-02 14:38:56', 1),
(87, 45, 1, '000087', '2026-03-02', 3, '260.00', 'PAGO COMPLETO LIBRO PRIMARIA 1 GRADO', '2026-03-02 14:50:52', 1),
(88, 76, 1, '000088', '2026-03-02', 1, '300.00', 'PAGO COMPLETO LIBRO INICIAL 3 AÑOS', '2026-03-02 15:33:07', 1),
(89, 19, 1, '000089', '2026-03-02', 1, '260.00', 'PAGO COMPLETO LIBRO PRIMARIA 1 GRADO', '2026-03-02 15:41:57', 1);

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
(4, 3, 10, 1, '270.00', 'PAGO COMPLETO'),
(7, 4, 1, 1, '50.00', ''),
(8, 5, 1, 1, '30.00', ''),
(12, 7, 9, 1, '250.00', ''),
(13, 8, 10, 1, '30.00', '250.00'),
(16, 9, 10, 1, '30.00', '250.00'),
(19, 10, 9, 1, '250.00', ''),
(20, 11, 12, 1, '30.00', '270.00'),
(21, 12, 1, 1, '50.00', ''),
(22, 6, 12, 1, '20.00', '270.00'),
(23, 13, 10, 1, '30.00', '250.00'),
(24, 14, 12, 1, '10.00', '270.00'),
(25, 15, 1, 1, '50.00', ''),
(26, 16, 17, 1, '10.00', ''),
(27, 16, 18, 1, '80.00', ''),
(28, 17, 18, 1, '80.00', ''),
(29, 17, 17, 1, '10.00', ''),
(30, 17, 16, 1, '10.00', ''),
(31, 18, 18, 1, '80.00', ''),
(32, 18, 17, 1, '10.00', ''),
(33, 18, 16, 1, '10.00', ''),
(34, 19, 18, 1, '80.00', ''),
(35, 19, 17, 1, '10.00', ''),
(36, 19, 16, 1, '10.00', ''),
(37, 20, 1, 1, '50.00', ''),
(38, 21, 1, 1, '50.00', ''),
(39, 22, 44, 1, '30.00', ''),
(40, 22, 45, 1, '0.00', ''),
(41, 23, 48, 1, '60.00', ''),
(42, 23, 47, 2, '60.00', ''),
(44, 9, 19, 1, '30.00', '220.00'),
(45, 8, 19, 1, '20.00', '220.00'),
(46, 6, 19, 1, '20.00', '220.00'),
(47, 24, 46, 1, '80.00', ''),
(48, 25, 1, 1, '50.00', ''),
(49, 26, 18, 1, '80.00', ''),
(50, 26, 17, 1, '10.00', ''),
(51, 26, 16, 1, '10.00', ''),
(52, 27, 33, 2, '38.00', ''),
(53, 27, 54, 1, '41.00', ''),
(54, 27, 12, 1, '135.00', '270.00'),
(55, 28, 18, 1, '80.00', ''),
(56, 28, 17, 1, '10.00', ''),
(57, 28, 16, 1, '10.00', ''),
(58, 29, 1, 1, '50.00', ''),
(59, 30, 12, 1, '290.00', ''),
(60, 31, 1, 1, '50.00', ''),
(61, 32, 1, 1, '50.00', ''),
(62, 33, 18, 1, '80.00', ''),
(63, 33, 17, 1, '10.00', ''),
(64, 33, 16, 1, '10.00', ''),
(65, 34, 1, 1, '50.00', ''),
(66, 35, 1, 1, '50.00', ''),
(67, 36, 12, 1, '30.00', '290.00'),
(68, 37, 19, 1, '30.00', '220.00'),
(69, 38, 69, 1, '100.00', ''),
(70, 39, 9, 1, '30.00', '270.00'),
(71, 40, 4, 1, '300.00', ''),
(72, 41, 12, 1, '290.00', ''),
(73, 42, 11, 1, '100.00', '290.00'),
(74, 43, 4, 1, '300.00', ''),
(75, 44, 3, 1, '300.00', ''),
(76, 45, 4, 1, '300.00', ''),
(77, 41, 13, 1, '120.00', ''),
(78, 41, 14, 1, '110.00', ''),
(79, 41, 15, 1, '100.00', ''),
(80, 46, 16, 1, '10.00', ''),
(81, 46, 17, 1, '10.00', ''),
(82, 46, 18, 1, '80.00', ''),
(83, 47, 12, 1, '100.00', '290.00'),
(84, 48, 4, 1, '80.00', '300.00'),
(85, 49, 10, 1, '40.00', '270.00'),
(86, 50, 18, 2, '80.00', ''),
(87, 50, 17, 2, '10.00', ''),
(88, 50, 16, 2, '10.00', ''),
(89, 30, 13, 1, '120.00', ''),
(90, 30, 14, 1, '110.00', ''),
(91, 30, 15, 1, '100.00', ''),
(92, 51, 1, 1, '50.00', ''),
(93, 52, 19, 1, '30.00', '220.00'),
(94, 53, 1, 1, '50.00', ''),
(95, 54, 4, 1, '300.00', ''),
(96, 55, 5, 1, '300.00', ''),
(97, 56, 15, 1, '100.00', ''),
(98, 57, 6, 1, '300.00', ''),
(99, 58, 7, 1, '260.00', '260.00'),
(100, 3, 15, 1, '80.00', ''),
(101, 59, 18, 1, '80.00', ''),
(102, 59, 17, 1, '10.00', ''),
(104, 60, 3, 1, '300.00', ''),
(105, 61, 13, 1, '120.00', ''),
(106, 62, 5, 1, '300.00', ''),
(107, 63, 46, 1, '80.00', ''),
(108, 64, 17, 2, '10.00', ''),
(109, 64, 16, 2, '10.00', ''),
(110, 64, 18, 2, '80.00', ''),
(111, 65, 1, 1, '50.00', ''),
(112, 66, 46, 1, '80.00', ''),
(113, 67, 3, 1, '50.00', ''),
(114, 68, 6, 1, '50.00', '300.00'),
(115, 69, 3, 1, '50.00', '300.00'),
(116, 70, 15, 1, '100.00', ''),
(117, 71, 4, 1, '50.00', '300.00'),
(118, 72, 65, 1, '41.00', ''),
(119, 72, 15, 1, '100.00', ''),
(120, 72, 71, 1, '15.00', ''),
(121, 73, 52, 1, '38.00', ''),
(122, 74, 17, 1, '10.00', ''),
(123, 74, 18, 1, '80.00', ''),
(124, 75, 43, 1, '47.00', ''),
(125, 75, 25, 1, '82.00', ''),
(126, 75, 27, 1, '80.00', ''),
(127, 75, 41, 1, '41.00', ''),
(128, 76, 4, 1, '300.00', ''),
(129, 77, 4, 1, '50.00', '300.00'),
(130, 78, 7, 1, '100.00', '260.00'),
(131, 78, 9, 1, '100.00', '270.00'),
(132, 79, 4, 1, '50.00', '300.00'),
(133, 80, 8, 1, '30.00', '260.00'),
(134, 81, 25, 1, '82.00', ''),
(135, 81, 31, 1, '33.00', ''),
(136, 81, 39, 1, '33.00', ''),
(137, 81, 34, 1, '40.00', ''),
(138, 81, 42, 1, '40.00', ''),
(139, 81, 71, 2, '12.00', ''),
(140, 81, 7, 1, '100.00', '260.00'),
(141, 81, 8, 1, '100.00', '260.00'),
(142, 82, 1, 1, '50.00', ''),
(143, 83, 4, 1, '300.00', ''),
(144, 83, 8, 1, '260.00', ''),
(145, 83, 72, 2, '12.00', ''),
(146, 83, 25, 1, '82.00', ''),
(147, 83, 23, 1, '78.00', ''),
(148, 83, 33, 1, '41.00', ''),
(149, 83, 31, 1, '36.00', ''),
(150, 84, 63, 1, '39.00', ''),
(151, 84, 33, 1, '41.00', ''),
(152, 85, 15, 1, '100.00', ''),
(153, 86, 15, 1, '100.00', ''),
(154, 87, 7, 1, '260.00', ''),
(155, 88, 4, 1, '300.00', ''),
(156, 89, 7, 1, '260.00', '');

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

--
-- Volcado de datos para la tabla `documento_detalle`
--

INSERT INTO `documento_detalle` (`id`, `id_matricula_detalle`, `id_documento`, `entregado`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 22, 1, 0, 'DOC. OMITIDO', '2026-01-22 14:25:54', 1),
(2, 22, 2, 0, 'DOC. OMITIDO', '2026-01-22 14:25:54', 1),
(3, 22, 3, 0, 'DOC. OMITIDO', '2026-01-22 14:25:54', 1),
(4, 22, 4, 0, 'DOC. OMITIDO', '2026-01-22 14:25:54', 1),
(5, 22, 5, 0, 'DOC. OMITIDO', '2026-01-22 14:25:54', 1),
(6, 22, 6, 0, '', '2026-01-22 14:25:54', 1),
(7, 22, 7, 0, '', '2026-01-22 14:25:54', 1),
(8, 22, 8, 0, '', '2026-01-22 14:25:54', 1),
(9, 22, 9, 0, '', '2026-01-22 14:25:54', 1),
(10, 22, 10, 0, '', '2026-01-22 14:25:54', 1),
(11, 22, 11, 0, '', '2026-01-22 14:25:54', 1),
(12, 22, 12, 0, '', '2026-01-22 14:25:54', 1),
(13, 5, 1, 0, 'DOC. OMITIDO', '2026-01-22 14:26:11', 1),
(14, 5, 2, 0, 'DOC. OMITIDO', '2026-01-22 14:26:11', 1),
(15, 5, 3, 0, 'DOC. OMITIDO', '2026-01-22 14:26:11', 1),
(16, 5, 4, 0, 'DOC. OMITIDO', '2026-01-22 14:26:11', 1),
(17, 5, 5, 0, 'DOC. OMITIDO', '2026-01-22 14:26:11', 1),
(18, 5, 6, 0, '', '2026-01-22 14:26:11', 1),
(19, 5, 7, 0, '', '2026-01-22 14:26:11', 1),
(20, 5, 8, 0, '', '2026-01-22 14:26:11', 1),
(21, 5, 9, 0, '', '2026-01-22 14:26:11', 1),
(22, 5, 10, 0, '', '2026-01-22 14:26:11', 1),
(23, 5, 11, 0, '', '2026-01-22 14:26:11', 1),
(24, 5, 12, 0, '', '2026-01-22 14:26:11', 1),
(25, 21, 1, 0, 'DOC. OMITIDO', '2026-01-22 14:26:26', 1),
(26, 21, 2, 0, 'DOC. OMITIDO', '2026-01-22 14:26:26', 1),
(27, 21, 3, 0, 'DOC. OMITIDO', '2026-01-22 14:26:26', 1),
(28, 21, 4, 0, 'DOC. OMITIDO', '2026-01-22 14:26:26', 1),
(29, 21, 5, 0, 'DOC. OMITIDO', '2026-01-22 14:26:26', 1),
(30, 21, 6, 0, '', '2026-01-22 14:26:26', 1),
(31, 21, 7, 0, '', '2026-01-22 14:26:26', 1),
(32, 21, 8, 0, '', '2026-01-22 14:26:26', 1),
(33, 21, 9, 0, '', '2026-01-22 14:26:26', 1),
(34, 21, 10, 0, '', '2026-01-22 14:26:26', 1),
(35, 21, 11, 0, '', '2026-01-22 14:26:26', 1),
(36, 21, 12, 0, '', '2026-01-22 14:26:26', 1),
(37, 1, 1, 0, 'DOC. OMITIDO', '2026-01-22 14:29:23', 1),
(38, 1, 2, 0, 'DOC. OMITIDO', '2026-01-22 14:29:23', 1),
(39, 1, 3, 0, 'DOC. OMITIDO', '2026-01-22 14:29:23', 1),
(40, 1, 4, 0, 'DOC. OMITIDO', '2026-01-22 14:29:23', 1),
(41, 1, 5, 0, 'DOC. OMITIDO', '2026-01-22 14:29:23', 1),
(42, 1, 6, 0, '', '2026-01-22 14:29:23', 1),
(43, 1, 7, 0, '', '2026-01-22 14:29:23', 1),
(44, 1, 8, 0, '', '2026-01-22 14:29:23', 1),
(45, 1, 9, 0, '', '2026-01-22 14:29:23', 1),
(46, 1, 10, 0, '', '2026-01-22 14:29:23', 1),
(47, 1, 11, 0, '', '2026-01-22 14:29:23', 1),
(48, 1, 12, 0, '', '2026-01-22 14:29:23', 1),
(49, 20, 1, 0, 'DOC. OMITIDO', '2026-01-22 14:29:33', 1),
(50, 20, 2, 0, 'DOC. OMITIDO', '2026-01-22 14:29:33', 1),
(51, 20, 3, 0, 'DOC. OMITIDO', '2026-01-22 14:29:33', 1),
(52, 20, 4, 0, 'DOC. OMITIDO', '2026-01-22 14:29:33', 1),
(53, 20, 5, 0, 'DOC. OMITIDO', '2026-01-22 14:29:33', 1),
(54, 20, 6, 0, '', '2026-01-22 14:29:33', 1),
(55, 20, 7, 0, '', '2026-01-22 14:29:33', 1),
(56, 20, 8, 0, '', '2026-01-22 14:29:33', 1),
(57, 20, 9, 0, '', '2026-01-22 14:29:33', 1),
(58, 20, 10, 0, '', '2026-01-22 14:29:33', 1),
(59, 20, 11, 0, '', '2026-01-22 14:29:33', 1),
(60, 20, 12, 0, '', '2026-01-22 14:29:33', 1),
(61, 23, 1, 0, 'DOC. OMITIDO', '2026-01-22 14:55:04', 1),
(62, 23, 2, 0, 'DOC. OMITIDO', '2026-01-22 14:55:04', 1),
(63, 23, 3, 0, 'DOC. OMITIDO', '2026-01-22 14:55:04', 1),
(64, 23, 4, 0, 'DOC. OMITIDO', '2026-01-22 14:55:04', 1),
(65, 23, 5, 0, 'DOC. OMITIDO', '2026-01-22 14:55:04', 1),
(66, 23, 6, 0, '', '2026-01-22 14:55:04', 1),
(67, 23, 7, 0, '', '2026-01-22 14:55:04', 1),
(68, 23, 8, 1, '', '2026-01-22 14:55:04', 1),
(69, 23, 9, 1, '', '2026-01-22 14:55:05', 1),
(70, 23, 10, 0, '', '2026-01-22 14:55:05', 1),
(71, 23, 11, 0, '', '2026-01-22 14:55:05', 1),
(72, 23, 12, 0, '', '2026-01-22 14:55:05', 1),
(73, 25, 1, 0, 'DOC. OMITIDO', '2026-01-22 17:36:03', 1),
(74, 25, 2, 0, 'DOC. OMITIDO', '2026-01-22 17:36:03', 1),
(75, 25, 3, 0, 'DOC. OMITIDO', '2026-01-22 17:36:03', 1),
(76, 25, 4, 0, 'DOC. OMITIDO', '2026-01-22 17:36:03', 1),
(77, 25, 5, 0, 'DOC. OMITIDO', '2026-01-22 17:36:03', 1),
(78, 25, 6, 0, '', '2026-01-22 17:36:03', 1),
(79, 25, 7, 0, '', '2026-01-22 17:36:03', 1),
(80, 25, 8, 0, '', '2026-01-22 17:36:03', 1),
(81, 25, 9, 0, '', '2026-01-22 17:36:03', 1),
(82, 25, 10, 0, '', '2026-01-22 17:36:03', 1),
(83, 25, 11, 0, '', '2026-01-22 17:36:03', 1),
(84, 25, 12, 0, '', '2026-01-22 17:36:03', 1),
(85, 18, 1, 1, '', '2026-01-23 19:44:16', 1),
(86, 18, 2, 1, '', '2026-01-23 19:44:16', 1),
(87, 18, 3, 1, '', '2026-01-23 19:44:16', 1),
(88, 18, 4, 1, '', '2026-01-23 19:44:16', 1),
(89, 18, 5, 1, '', '2026-01-23 19:44:16', 1),
(90, 18, 6, 0, '', '2026-01-23 19:44:16', 1),
(91, 18, 7, 0, '', '2026-01-23 19:44:16', 1),
(92, 18, 8, 0, '', '2026-01-23 19:44:16', 1),
(93, 18, 9, 0, '', '2026-01-23 19:44:16', 1),
(94, 18, 10, 0, '', '2026-01-23 19:44:16', 1),
(95, 18, 11, 0, '', '2026-01-23 19:44:16', 1),
(96, 18, 12, 1, 'INFORME PSICOLOGICO', '2026-01-23 19:44:16', 1),
(97, 26, 1, 0, '', '2026-01-28 16:34:44', 1),
(98, 26, 2, 0, '', '2026-01-28 16:34:44', 1),
(99, 26, 3, 0, '', '2026-01-28 16:34:44', 1),
(100, 26, 4, 0, '', '2026-01-28 16:34:44', 1),
(101, 26, 5, 0, '', '2026-01-28 16:34:44', 1),
(102, 26, 6, 0, '', '2026-01-28 16:34:44', 1),
(103, 26, 7, 0, '', '2026-01-28 16:34:44', 1),
(104, 26, 8, 1, '', '2026-01-28 16:34:44', 1),
(105, 26, 9, 1, '', '2026-01-28 16:34:44', 1),
(106, 26, 10, 0, '', '2026-01-28 16:34:44', 1),
(107, 26, 11, 0, '', '2026-01-28 16:34:44', 1),
(108, 26, 12, 0, '', '2026-01-28 16:34:44', 1),
(109, 33, 1, 1, '', '2026-02-23 19:40:55', 1),
(110, 33, 2, 1, '', '2026-02-23 19:40:55', 1),
(111, 33, 3, 1, '', '2026-02-23 19:40:55', 1),
(112, 33, 4, 0, '', '2026-02-23 19:40:55', 1),
(113, 33, 5, 0, '', '2026-02-23 19:40:55', 1),
(114, 33, 6, 0, '', '2026-02-23 19:40:55', 1),
(115, 33, 7, 0, '', '2026-02-23 19:40:55', 1),
(116, 33, 8, 0, '', '2026-02-23 19:40:55', 1),
(117, 33, 9, 0, '', '2026-02-23 19:40:55', 1),
(118, 33, 10, 0, '', '2026-02-23 19:40:55', 1),
(119, 33, 11, 0, '', '2026-02-23 19:40:55', 1),
(120, 33, 12, 0, '', '2026-02-23 19:40:55', 1),
(121, 29, 1, 1, '', '2026-03-02 16:19:02', 1),
(122, 29, 2, 1, '', '2026-03-02 16:19:02', 1),
(123, 29, 3, 1, '', '2026-03-02 16:19:02', 1),
(124, 29, 4, 1, '', '2026-03-02 16:19:02', 1),
(125, 29, 5, 1, '', '2026-03-02 16:19:02', 1),
(126, 29, 6, 0, '', '2026-03-02 16:19:02', 1),
(127, 29, 7, 0, '', '2026-03-02 16:19:02', 1),
(128, 29, 8, 1, '', '2026-03-02 16:19:02', 1),
(129, 29, 9, 1, '', '2026-03-02 16:19:02', 1),
(130, 29, 10, 0, '', '2026-03-02 16:19:02', 1),
(131, 29, 11, 0, '', '2026-03-02 16:19:02', 1),
(132, 29, 12, 0, '', '2026-03-02 16:19:02', 1);

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
(1, 'ESTIMULACIÓN', 1, '', '2025-12-27 07:23:13', 1),
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
(8, 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 7, 1, NULL, 7, 8, '', '2026-01-05 20:04:51', 1),
(9, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 8, 1, NULL, 8, 9, '', '2026-01-06 16:46:28', 1),
(10, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 8, 1, NULL, 9, 10, '', '2026-01-06 16:53:17', 1),
(11, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 9, 1, NULL, 10, 11, '', '2026-01-06 16:57:03', 1),
(12, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 10, 1, NULL, 11, 12, '', '2026-01-06 17:03:09', 1),
(13, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 7, 1, NULL, 12, 13, '', '2026-01-06 17:04:44', 1),
(14, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 10, 1, NULL, 13, 14, '', '2026-01-06 17:08:52', 1),
(15, 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 5, 1, NULL, 15, 15, '', '2026-01-06 17:24:33', 1),
(16, 'MATRICULA 2026 - 07/01/2026\r\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./300.00\r\n\r\nObservaciones:', 3, 1, NULL, 18, 16, '', '2026-01-07 14:39:06', 1),
(17, 'MATRICULA 2026 - 07/01/2026\r\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 5, 1, NULL, 19, 17, '', '2026-01-07 15:10:34', 1),
(18, 'MATRICULA 2026 - 13/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 10, 2, NULL, 22, 18, '', '2026-01-13 14:29:48', 1),
(19, 'MATRICULA 2026 - 14/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', 10, 2, NULL, 23, 19, '', '2026-01-14 14:40:30', 1),
(20, 'MATRICULA 2026 - 15/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 24, 20, '', '2026-01-15 14:47:22', 1),
(21, 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 29, 21, '', '2026-01-22 14:00:53', 1),
(22, 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: EST. TEMPRANA - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 1, NULL, 30, 22, '', '2026-01-22 14:07:46', 1),
(23, 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 31, 23, '', '2026-01-22 14:53:30', 1),
(24, 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 33, 24, '', '2026-01-22 16:43:51', 1),
(25, 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 34, 25, '', '2026-01-22 17:28:50', 1),
(26, '', 8, 2, NULL, 32, 26, '', '2026-01-28 16:33:42', 1),
(27, 'MATRICULA 2026 - 29/01/2026\\r\\nNIVEL: EST. TEMP. - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 1, NULL, 35, 27, '', '2026-01-29 13:38:32', 1),
(28, 'MATRICULA 2026 - 29/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 5, 1, NULL, 36, 28, '', '2026-01-29 13:43:48', 1),
(29, 'MATRICULA 2026 - 30/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 10, 2, NULL, 37, 29, '', '2026-01-30 15:10:27', 1),
(30, 'MATRICULA 2026 - 31/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 10, 1, NULL, 17, 30, '', '2026-01-31 17:35:00', 1),
(31, 'MATRICULA 2026 - 31/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 7, 1, NULL, 40, 31, '', '2026-01-31 17:44:10', 1),
(32, 'MATRICULA 2026 - 31/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 10, 1, NULL, 41, 32, '', '2026-01-31 18:15:29', 1),
(33, 'MATRICULA 2026 - 02/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 6, 2, NULL, 27, 33, '', '2026-02-02 16:13:15', 1),
(34, 'MATRICULA 2026 - 03/02/2026\\r\\nNIVEL: EST. TEMP. - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 2, NULL, 46, 34, '', '2026-02-04 02:14:59', 1),
(35, 'MATRICULA 2026 - 05/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 9, 1, NULL, 48, 35, '', '2026-02-05 17:03:36', 1),
(36, 'MATRICULA 2026 - 07/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 8, 1, NULL, 49, 36, '', '2026-02-07 17:41:35', 1),
(37, 'MATRICULA 2026 - 09/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 4, 2, NULL, 50, 37, '', '2026-02-09 13:33:59', 1),
(38, 'MATRICULA 2026 - 10/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 5, 2, NULL, 45, 38, '', '2026-02-10 23:19:08', 1),
(39, 'MATRICULA 2026 - 10/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 4, 1, NULL, 53, 39, '', '2026-02-10 23:35:05', 1),
(40, 'MATRICULA 2026 - 11/02/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 55, 40, '', '2026-02-11 16:04:53', 1),
(41, 'MATRICULA 2026 - 11/02/2026\\r\\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 3, 2, NULL, 44, 41, '', '2026-02-11 20:10:39', 1),
(42, 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 5, 2, NULL, 42, 42, '', '2026-02-17 23:39:18', 1),
(43, 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 8, 1, NULL, 16, 43, '', '2026-02-17 23:41:57', 1),
(44, 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 7, 1, NULL, 56, 44, '', '2026-02-17 23:58:18', 1),
(45, 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 4, 1, NULL, 57, 45, '', '2026-02-18 00:03:09', 1),
(46, 'MATRICULA 2026 - 19/02/2026\\r\\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 3, 1, NULL, 59, 46, '', '2026-02-19 19:56:47', 1),
(47, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 5, 1, NULL, 62, 47, '', '2026-02-23 13:27:04', 1),
(48, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 7, 1, NULL, 62, 48, '', '2026-02-23 13:31:56', 1),
(49, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 4, 2, NULL, 8, 49, '', '2026-02-23 16:48:44', 1),
(50, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 2, NULL, 63, 50, '', '2026-02-23 16:56:29', 1),
(51, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 2, NULL, 64, 51, '', '2026-02-23 19:53:20', 1),
(52, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 65, 52, '', '2026-02-23 20:01:20', 1),
(53, 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 6, 1, NULL, 66, 53, '', '2026-02-24 02:09:02', 1),
(54, 'MATRICULA 2026 - 24/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 8, 2, NULL, 68, 54, '', '2026-02-24 17:53:55', 1),
(55, 'MATRICULA 2026 - 24/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 9, 2, NULL, 68, 55, '', '2026-02-24 17:57:14', 1),
(56, 'MATRICULA 2026 - 24/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 9, 2, NULL, 69, 56, '', '2026-02-24 18:42:53', 1),
(57, 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 4, 2, NULL, 70, 57, '', '2026-02-25 17:34:53', 1),
(58, 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 5, 1, NULL, 71, 58, '', '2026-02-25 18:00:46', 1),
(59, 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 2, NULL, 72, 59, '', '2026-02-25 21:43:37', 1),
(60, 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 8, 1, NULL, 73, 60, '', '2026-02-25 22:01:11', 1),
(61, 'MATRICULA 2026 - 26/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 1, 1, NULL, 74, 61, '', '2026-02-26 19:30:49', 1),
(62, 'MATRICULA 2026 - 26/02/2026\\r\\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', 3, 2, NULL, 75, 62, '', '2026-02-26 22:21:10', 1),
(63, 'MATRICULA 2026 - 27/02/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 2, NULL, 76, 63, '', '2026-02-27 19:19:07', 1),
(64, 'MATRICULA 2026 - 27/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 5, 1, NULL, 77, 64, '', '2026-02-27 19:31:36', 1),
(65, 'MATRICULA 2026 - 27/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 6, 1, NULL, 77, 65, '', '2026-02-27 19:33:01', 1),
(66, 'MATRICULA 2026 - 02/03/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', 2, 1, NULL, 79, 66, '', '2026-03-02 13:59:29', 1),
(67, 'MATRICULA 2026 - 02/03/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', 6, 1, NULL, 79, 67, '', '2026-03-02 14:01:21', 1);

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
(3, 'TRANSFERENCIA', '', '2025-12-27 07:29:43', 1),
(4, 'MATRICULA GRATIS', '', '2026-01-07 15:09:15', 1),
(5, 'PENDIENTE', '', '2026-01-22 16:41:19', 1);

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
(5, 5, '000005', '2026-01-05', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./290.00\r\n\r\nObservaciones:', '280.00', 2, '', '2026-01-05 19:32:29', 1),
(6, 6, '000006', '2025-12-15', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./300.00\r\n\r\nObservaciones:', '220.00', 1, 'EL MONTO EN EFECTIVO FUE ENTREGADO A LA DIRECTORA', '2026-01-05 19:36:31', 1),
(7, 7, '000007', '2025-12-15', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 1, 'EL MONTO EN EFECTIVO FUE ENTREGADO A LA DIRECTORA', '2026-01-05 19:41:10', 1),
(8, 8, '000008', '2025-12-15', 'MATRICULA 2026 - 05/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-05 20:04:51', 1),
(9, 9, '000009', '2025-12-15', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-06 16:46:28', 1),
(10, 10, '000010', '2025-12-15', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-06 16:53:17', 1),
(11, 11, '000011', '2025-12-15', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-06 16:57:03', 1),
(12, 12, '000012', '2025-12-16', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-06 17:03:09', 1),
(13, 13, '000013', '2025-12-16', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 3, '', '2026-01-06 17:04:44', 1),
(14, 14, '000014', '2025-12-29', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-06 17:08:53', 1),
(15, 15, '000015', '2025-01-16', 'MATRICULA 2026 - 06/01/2026\r\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-06 17:24:33', 1),
(16, 16, '000016', '2025-12-17', 'MATRICULA 2026 - 07/01/2026\r\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\r\n\r\nImpresion: S./15.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./300.00\r\n\r\nObservaciones:', '220.00', 2, '', '2026-01-07 14:39:06', 1),
(17, 17, '000017', '2025-12-12', 'MATRICULA 2026 - 12/12/2025\r\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '0.00', 4, '', '2026-01-07 15:10:34', 1),
(18, 18, '000018', '2026-01-12', 'MATRICULA 2026 - 13/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '280.00', 2, '', '2026-01-13 14:29:48', 1),
(19, 19, '000019', '2026-01-14', 'MATRICULA 2026 - 14/01/2026\r\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\r\n\r\nImpresion: S./10.00\r\nMantenimiento: S./30.00\r\nMatricula: S./280.00\r\nMensualidad: S./320.00\r\n\r\nObservaciones:', '280.00', 2, 'PAGO 100 SOLES 08/01/2026 - FALTA COMPLETAR\r\nPAGO 180 SOLES 14/01/2026 - COMPLETO', '2026-01-14 14:40:30', 1),
(20, 20, '000020', '2026-01-12', 'MATRICULA 2026 - 15/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-01-15 14:47:22', 1),
(21, 21, '000021', '2025-12-29', 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-01-22 14:00:53', 1),
(22, 22, '000022', '2025-12-30', 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: EST. TEMPRANA - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-01-22 14:07:46', 1),
(23, 23, '000023', '2026-01-21', 'MATRICULA 2026 - 22/01/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 1, '', '2026-01-22 14:53:30', 1),
(24, 24, '000024', '2026-02-12', '', '280.00', 2, '', '2026-01-22 16:43:51', 1),
(25, 25, '000025', '2026-01-23', '', '280.00', 2, '', '2026-01-22 17:28:50', 1),
(26, 26, '000026', '2026-01-28', '', '280.00', 1, '', '2026-01-28 16:33:42', 1),
(27, 27, '000027', '2026-01-01', 'MATRICULA 2026 - 29/01/2026\\r\\nNIVEL: EST. TEMP. - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-01-29 13:38:32', 1),
(28, 28, '000028', '2026-01-01', 'MATRICULA 2026 - 29/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-01-29 13:43:48', 1),
(29, 29, '000029', '2026-01-30', 'MATRICULA 2026 - 30/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 1, '', '2026-01-30 15:10:27', 1),
(30, 30, '000030', '2026-01-31', 'MATRICULA 2026 - 31/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-01-31 17:35:00', 1),
(31, 31, '000031', '2026-01-30', 'MATRICULA 2026 - 31/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-01-31 17:44:10', 1),
(32, 32, '000032', '2025-12-24', 'MATRICULA 2026 - 31/01/2026\\r\\nNIVEL: PRIMARIA - GRADO: 6 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-01-31 18:15:29', 1),
(33, 33, '000033', '2026-01-29', 'MATRICULA 2026 - 02/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-02 16:13:15', 1),
(34, 34, '000034', '2026-02-03', 'MATRICULA 2026 - 03/02/2026\\r\\nNIVEL: EST. TEMP. - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-02-04 02:14:59', 1),
(35, 35, '000035', '2026-02-05', 'MATRICULA 2026 - 05/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '200.00', 2, '', '2026-02-05 17:03:36', 1),
(36, 36, '000036', '2026-02-07', 'MATRICULA 2026 - 07/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-02-07 17:41:35', 1),
(37, 37, '000037', '2026-02-06', 'MATRICULA 2026 - 09/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-02-09 13:33:59', 1),
(38, 38, '000038', '2026-02-09', 'MATRICULA 2026 - 10/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-10 23:19:08', 1),
(39, 39, '000039', '2026-02-10', 'MATRICULA 2026 - 10/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-10 23:35:05', 1),
(40, 40, '000040', '2026-02-11', 'MATRICULA 2026 - 11/02/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-11 16:04:53', 1),
(41, 41, '000041', '2026-02-11', 'MATRICULA 2026 - 11/02/2026\\r\\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-11 20:10:39', 1),
(42, 42, '000042', '2026-02-16', 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-17 23:39:18', 1),
(43, 43, '000043', '2026-02-16', 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-02-17 23:41:57', 1),
(44, 44, '000044', '2026-02-17', 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-02-17 23:58:18', 1),
(45, 45, '000045', '2026-02-17', 'MATRICULA 2026 - 17/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-02-18 00:03:09', 1),
(46, 46, '000046', '2026-02-19', 'MATRICULA 2026 - 19/02/2026\\r\\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-19 19:56:47', 1),
(47, 47, '000047', '2026-02-21', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '220.00', 3, '', '2026-02-23 13:27:04', 1),
(48, 48, '000048', '2026-02-21', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 3 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '220.00', 3, '', '2026-02-23 13:31:56', 1),
(49, 49, '000049', '2026-02-23', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-02-23 16:48:44', 1),
(50, 50, '000050', '2026-02-23', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '250.00', 1, 'YAPE 30 SOLES\\r\\nEFECTIVO 220 SOLES', '2026-02-23 16:56:29', 1),
(51, 51, '000051', '2026-02-20', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-23 19:53:20', 1),
(52, 52, '000052', '2026-02-20', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 3, '', '2026-02-23 20:01:20', 1),
(53, 53, '000053', '2025-12-31', 'MATRICULA 2026 - 23/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-02-24 02:09:02', 1),
(54, 54, '000054', '2026-02-24', 'MATRICULA 2026 - 24/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-24 17:53:55', 1),
(55, 55, '000055', '2026-02-24', 'MATRICULA 2026 - 24/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-24 17:57:14', 1),
(56, 56, '000056', '2026-02-24', 'MATRICULA 2026 - 24/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 5 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-24 18:42:53', 1),
(57, 57, '000057', '2026-02-25', 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: INICIAL - GRADO: 5 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '220.00', 2, '', '2026-02-25 17:34:53', 1),
(58, 58, '000058', '2026-02-25', 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-25 18:00:46', 1),
(59, 59, '000059', '2026-02-25', 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-25 21:43:37', 1),
(60, 60, '000060', '2026-02-25', 'MATRICULA 2026 - 25/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 4 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-25 22:01:11', 1),
(61, 61, '000061', '2026-02-26', 'MATRICULA 2026 - 26/02/2026\\r\\nNIVEL: ESTIMULACIÓN - GRADO: 2 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 2, '', '2026-02-26 19:30:49', 1),
(62, 62, '000062', '2026-02-26', 'MATRICULA 2026 - 26/02/2026\\r\\nNIVEL: INICIAL - GRADO: 4 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./300.00\\r\\n\\r\\nObservaciones:', '200.00', 2, '', '2026-02-26 22:21:10', 1),
(63, 63, '000063', '2026-02-27', 'MATRICULA 2026 - 27/02/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '280.00', 1, '', '2026-02-27 19:19:07', 1),
(64, 64, '000064', '2026-02-27', 'MATRICULA 2026 - 27/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 1 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-02-27 19:31:36', 1),
(65, 65, '000065', '2026-02-27', 'MATRICULA 2026 - 27/02/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-02-27 19:33:01', 1),
(66, 66, '000066', '2026-03-02', 'MATRICULA 2026 - 02/03/2026\\r\\nNIVEL: INICIAL - GRADO: 3 AÑOS - SECCION: A\\r\\n\\r\\nImpresion: S./15.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./290.00\\r\\n\\r\\nObservaciones:', '250.00', 2, '', '2026-03-02 13:59:29', 1),
(67, 67, '000067', '2026-03-02', 'MATRICULA 2026 - 02/03/2026\\r\\nNIVEL: PRIMARIA - GRADO: 2 GRADO - SECCION: A\\r\\n\\r\\nImpresion: S./10.00\\r\\nMantenimiento: S./30.00\\r\\nMatricula: S./280.00\\r\\nMensualidad: S./320.00\\r\\n\\r\\nObservaciones:', '250.00', 1, '', '2026-03-02 14:01:21', 1);

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
(1, 2, 1, '310.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(2, 3, 1, '310.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(3, 4, 1, '310.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(4, 5, 1, '310.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(5, 6, 1, '320.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(6, 7, 1, '290.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(7, 8, 1, '290.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(8, 9, 1, '290.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(9, 10, 1, '290.00', 0, 0, '', '2026-01-05 16:50:28', 1),
(10, 11, 1, '320.00', 0, 0, '', '2026-01-05 16:50:28', 1),
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
(41, 2, 5, '310.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(42, 3, 5, '310.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(43, 4, 5, '310.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(44, 5, 5, '310.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(45, 6, 5, '320.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(46, 7, 5, '290.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(47, 8, 5, '290.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(48, 9, 5, '290.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(49, 10, 5, '290.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(50, 11, 5, '320.00', 0, 0, '', '2026-01-05 19:32:29', 1),
(51, 2, 6, '320.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(52, 3, 6, '320.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(53, 4, 6, '320.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(54, 5, 6, '320.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(55, 6, 6, '330.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(56, 7, 6, '300.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(57, 8, 6, '300.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(58, 9, 6, '300.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(59, 10, 6, '300.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(60, 11, 6, '330.00', 0, 0, '', '2026-01-05 19:36:31', 1),
(61, 2, 7, '330.00', 0, 0, 'DSCTO. HERMANOS - 10 SOLES', '2026-01-05 19:41:10', 1),
(62, 3, 7, '330.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(63, 4, 7, '330.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(64, 5, 7, '310.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(65, 6, 7, '340.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(66, 7, 7, '310.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(67, 8, 7, '310.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(68, 9, 7, '310.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(69, 10, 7, '310.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(70, 11, 7, '340.00', 0, 0, '', '2026-01-05 19:41:10', 1),
(71, 2, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(72, 3, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(73, 4, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(74, 5, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(75, 6, 8, '340.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(76, 7, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(77, 8, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(78, 9, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(79, 10, 8, '310.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(80, 11, 8, '340.00', 0, 0, '', '2026-01-05 20:04:51', 1),
(81, 2, 9, '220.00', 0, 0, 'MENSUALIDAD A 200 SOLES', '2026-01-06 16:46:28', 1),
(82, 3, 9, '220.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(83, 4, 9, '220.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(84, 5, 9, '200.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(85, 6, 9, '230.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(86, 7, 9, '200.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(87, 8, 9, '200.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(88, 9, 9, '200.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(89, 10, 9, '200.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(90, 11, 9, '230.00', 0, 0, '', '2026-01-06 16:46:28', 1),
(91, 2, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(92, 3, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(93, 4, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(94, 5, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(95, 6, 10, '360.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(96, 7, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(97, 8, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(98, 9, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(99, 10, 10, '330.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(100, 11, 10, '360.00', 0, 0, '', '2026-01-06 16:53:17', 1),
(101, 2, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(102, 3, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(103, 4, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(104, 5, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(105, 6, 11, '360.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(106, 7, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(107, 8, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(108, 9, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(109, 10, 11, '330.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(110, 11, 11, '360.00', 0, 0, '', '2026-01-06 16:57:03', 1),
(111, 2, 12, '320.00', 0, 0, 'DSCTO. REFERIDOS X2 -20 SOLES', '2026-01-06 17:03:09', 1),
(112, 3, 12, '320.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(113, 4, 12, '320.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(114, 5, 12, '300.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(115, 6, 12, '330.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(116, 7, 12, '300.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(117, 8, 12, '300.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(118, 9, 12, '300.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(119, 10, 12, '300.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(120, 11, 12, '330.00', 0, 0, '', '2026-01-06 17:03:09', 1),
(121, 2, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(122, 3, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(123, 4, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(124, 5, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(125, 6, 13, '360.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(126, 7, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(127, 8, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(128, 9, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(129, 10, 13, '330.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(130, 11, 13, '360.00', 0, 0, '', '2026-01-06 17:04:44', 1),
(131, 2, 14, '380.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(132, 3, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(133, 4, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(134, 5, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(135, 6, 14, '350.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(136, 7, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(137, 8, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(138, 9, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(139, 10, 14, '320.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(140, 11, 14, '350.00', 0, 0, '', '2026-01-06 17:08:53', 1),
(141, 2, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(142, 3, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(143, 4, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(144, 5, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(145, 6, 15, '360.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(146, 7, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(147, 8, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(148, 9, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(149, 10, 15, '330.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(150, 11, 15, '360.00', 0, 0, '', '2026-01-06 17:24:33', 1),
(151, 2, 16, '320.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(152, 3, 16, '320.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(153, 4, 16, '320.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(154, 5, 16, '320.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(155, 6, 16, '330.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(156, 7, 16, '300.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(157, 8, 16, '300.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(158, 9, 16, '300.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(159, 10, 16, '300.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(160, 11, 16, '330.00', 0, 0, '', '2026-01-07 14:39:06', 1),
(161, 2, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(162, 3, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(163, 4, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(164, 5, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(165, 6, 17, '360.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(166, 7, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(167, 8, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(168, 9, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(169, 10, 17, '330.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(170, 11, 17, '360.00', 0, 0, '', '2026-01-07 15:10:34', 1),
(171, 2, 18, '340.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(172, 3, 18, '340.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(173, 4, 18, '340.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(174, 5, 18, '320.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(175, 6, 18, '350.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(176, 7, 18, '320.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(177, 8, 18, '320.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(178, 9, 18, '320.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(179, 10, 18, '320.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(180, 11, 18, '350.00', 0, 0, '', '2026-01-13 14:29:48', 1),
(181, 2, 19, '340.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(182, 3, 19, '340.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(183, 4, 19, '340.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(184, 5, 19, '320.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(185, 6, 19, '350.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(186, 7, 19, '320.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(187, 8, 19, '320.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(188, 9, 19, '320.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(189, 10, 19, '320.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(190, 11, 19, '350.00', 0, 0, '', '2026-01-14 14:40:30', 1),
(191, 2, 20, '310.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(192, 3, 20, '310.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(193, 4, 20, '310.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(194, 5, 20, '310.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(195, 6, 20, '320.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(196, 7, 20, '290.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(197, 8, 20, '290.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(198, 9, 20, '290.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(199, 10, 20, '290.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(200, 11, 20, '320.00', 0, 0, '', '2026-01-15 14:47:22', 1),
(201, 2, 21, '310.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(202, 3, 21, '310.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(203, 4, 21, '310.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(204, 5, 21, '310.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(205, 6, 21, '320.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(206, 7, 21, '290.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(207, 8, 21, '290.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(208, 9, 21, '290.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(209, 10, 21, '290.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(210, 11, 21, '320.00', 0, 0, '', '2026-01-22 14:00:53', 1),
(211, 2, 22, '310.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(212, 3, 22, '310.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(213, 4, 22, '310.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(214, 5, 22, '310.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(215, 6, 22, '320.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(216, 7, 22, '290.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(217, 8, 22, '290.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(218, 9, 22, '290.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(219, 10, 22, '290.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(220, 11, 22, '320.00', 0, 0, '', '2026-01-22 14:07:46', 1),
(221, 2, 23, '290.00', 0, 0, 'PAGO APARTE RECIBO N 000024', '2026-01-22 14:53:30', 1),
(222, 3, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(223, 4, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(224, 5, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(225, 6, 23, '320.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(226, 7, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(227, 8, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(228, 9, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(229, 10, 23, '290.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(230, 11, 23, '320.00', 0, 0, '', '2026-01-22 14:53:30', 1),
(231, 2, 24, '310.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(232, 3, 24, '310.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(233, 4, 24, '310.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(234, 5, 24, '310.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(235, 6, 24, '320.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(236, 7, 24, '290.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(237, 8, 24, '290.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(238, 9, 24, '290.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(239, 10, 24, '290.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(240, 11, 24, '320.00', 0, 0, '', '2026-01-22 16:43:51', 1),
(241, 2, 25, '288.00', 0, 0, 'PAGO ANUALMENTE - PENDIENTE DE AVISO', '2026-01-22 17:28:50', 1),
(242, 3, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(243, 4, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(244, 5, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(245, 6, 25, '318.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(246, 7, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(247, 8, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(248, 9, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(249, 10, 25, '288.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(250, 11, 25, '318.00', 0, 0, '', '2026-01-22 17:28:50', 1),
(251, 2, 26, '340.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(252, 3, 26, '340.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(253, 4, 26, '340.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(254, 5, 26, '320.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(255, 6, 26, '350.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(256, 7, 26, '320.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(257, 8, 26, '320.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(258, 9, 26, '320.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(259, 10, 26, '320.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(260, 11, 26, '350.00', 0, 0, '', '2026-01-28 16:33:42', 1),
(261, 2, 27, '290.00', 0, 0, 'PAGO APARTE RECIBO N 000066', '2026-01-29 13:38:32', 1),
(262, 3, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(263, 4, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(264, 5, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(265, 6, 27, '320.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(266, 7, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(267, 8, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(268, 9, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(269, 10, 27, '290.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(270, 11, 27, '320.00', 0, 0, '', '2026-01-29 13:38:32', 1),
(271, 2, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(272, 3, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(273, 4, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(274, 5, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(275, 6, 28, '360.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(276, 7, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(277, 8, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(278, 9, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(279, 10, 28, '330.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(280, 11, 28, '360.00', 0, 0, '', '2026-01-29 13:43:48', 1),
(281, 2, 29, '340.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(282, 3, 29, '340.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(283, 4, 29, '340.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(284, 5, 29, '320.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(285, 6, 29, '350.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(286, 7, 29, '320.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(287, 8, 29, '320.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(288, 9, 29, '320.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(289, 10, 29, '320.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(290, 11, 29, '350.00', 0, 0, '', '2026-01-30 15:10:27', 1),
(291, 2, 30, '380.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(292, 3, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(293, 4, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(294, 5, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(295, 6, 30, '350.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(296, 7, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(297, 8, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(298, 9, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(299, 10, 30, '320.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(300, 11, 30, '350.00', 0, 0, '', '2026-01-31 17:35:00', 1),
(301, 2, 31, '340.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(302, 3, 31, '340.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(303, 4, 31, '340.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(304, 5, 31, '320.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(305, 6, 31, '350.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(306, 7, 31, '320.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(307, 8, 31, '320.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(308, 9, 31, '320.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(309, 10, 31, '320.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(310, 11, 31, '350.00', 0, 0, '', '2026-01-31 17:44:10', 1),
(311, 2, 32, '380.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(312, 3, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(313, 4, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(314, 5, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(315, 6, 32, '350.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(316, 7, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(317, 8, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(318, 9, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(319, 10, 32, '320.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(320, 11, 32, '350.00', 0, 0, '', '2026-01-31 18:15:29', 1),
(321, 2, 33, '340.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(322, 3, 33, '340.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(323, 4, 33, '340.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(324, 5, 33, '320.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(325, 6, 33, '350.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(326, 7, 33, '320.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(327, 8, 33, '320.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(328, 9, 33, '320.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(329, 10, 33, '320.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(330, 11, 33, '350.00', 0, 0, '', '2026-02-02 16:13:15', 1),
(331, 2, 34, '310.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(332, 3, 34, '310.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(333, 4, 34, '310.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(334, 5, 34, '310.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(335, 6, 34, '320.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(336, 7, 34, '290.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(337, 8, 34, '290.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(338, 9, 34, '290.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(339, 10, 34, '290.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(340, 11, 34, '320.00', 0, 0, '', '2026-02-04 02:14:59', 1),
(341, 2, 35, '340.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(342, 3, 35, '340.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(343, 4, 35, '340.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(344, 5, 35, '320.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(345, 6, 35, '350.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(346, 7, 35, '320.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(347, 8, 35, '320.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(348, 9, 35, '320.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(349, 10, 35, '320.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(350, 11, 35, '350.00', 0, 0, '', '2026-02-05 17:03:36', 1),
(351, 2, 36, '340.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(352, 3, 36, '340.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(353, 4, 36, '340.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(354, 5, 36, '320.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(355, 6, 36, '350.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(356, 7, 36, '320.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(357, 8, 36, '320.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(358, 9, 36, '320.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(359, 10, 36, '320.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(360, 11, 36, '350.00', 0, 0, '', '2026-02-07 17:41:35', 1),
(361, 2, 37, '320.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(362, 3, 37, '320.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(363, 4, 37, '320.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(364, 5, 37, '320.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(365, 6, 37, '330.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(366, 7, 37, '300.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(367, 8, 37, '300.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(368, 9, 37, '300.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(369, 10, 37, '300.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(370, 11, 37, '330.00', 0, 0, '', '2026-02-09 13:33:59', 1),
(371, 2, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(372, 3, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(373, 4, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(374, 5, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(375, 6, 38, '360.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(376, 7, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(377, 8, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(378, 9, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(379, 10, 38, '330.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(380, 11, 38, '360.00', 0, 0, '', '2026-02-10 23:19:08', 1),
(381, 2, 39, '320.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(382, 3, 39, '320.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(383, 4, 39, '320.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(384, 5, 39, '320.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(385, 6, 39, '330.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(386, 7, 39, '300.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(387, 8, 39, '300.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(388, 9, 39, '300.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(389, 10, 39, '300.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(390, 11, 39, '330.00', 0, 0, '', '2026-02-10 23:35:05', 1),
(391, 2, 40, '310.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(392, 3, 40, '310.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(393, 4, 40, '310.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(394, 5, 40, '310.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(395, 6, 40, '320.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(396, 7, 40, '290.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(397, 8, 40, '290.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(398, 9, 40, '290.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(399, 10, 40, '290.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(400, 11, 40, '320.00', 0, 0, '', '2026-02-11 16:04:53', 1),
(401, 2, 41, '320.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(402, 3, 41, '320.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(403, 4, 41, '320.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(404, 5, 41, '320.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(405, 6, 41, '330.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(406, 7, 41, '300.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(407, 8, 41, '300.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(408, 9, 41, '300.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(409, 10, 41, '300.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(410, 11, 41, '330.00', 0, 0, '', '2026-02-11 20:10:39', 1),
(411, 2, 42, '340.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(412, 3, 42, '340.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(413, 4, 42, '340.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(414, 5, 42, '320.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(415, 6, 42, '350.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(416, 7, 42, '320.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(417, 8, 42, '320.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(418, 9, 42, '320.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(419, 10, 42, '320.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(420, 11, 42, '350.00', 0, 0, '', '2026-02-17 23:39:18', 1),
(421, 2, 43, '230.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(422, 3, 43, '230.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(423, 4, 43, '230.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(424, 5, 43, '210.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(425, 6, 43, '240.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(426, 7, 43, '210.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(427, 8, 43, '210.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(428, 9, 43, '210.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(429, 10, 43, '210.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(430, 11, 43, '240.00', 0, 0, '', '2026-02-17 23:41:57', 1),
(431, 2, 44, '280.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(432, 3, 44, '280.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(433, 4, 44, '280.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(434, 5, 44, '260.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(435, 6, 44, '290.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(436, 7, 44, '260.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(437, 8, 44, '260.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(438, 9, 44, '260.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(439, 10, 44, '260.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(440, 11, 44, '290.00', 0, 0, '', '2026-02-17 23:58:18', 1),
(441, 2, 45, '320.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(442, 3, 45, '320.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(443, 4, 45, '320.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(444, 5, 45, '320.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(445, 6, 45, '330.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(446, 7, 45, '300.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(447, 8, 45, '300.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(448, 9, 45, '300.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(449, 10, 45, '300.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(450, 11, 45, '330.00', 0, 0, '', '2026-02-18 00:03:09', 1),
(451, 2, 46, '300.00', 0, 0, 'PAGO APARTE RECIBO N 000063', '2026-02-19 19:56:47', 1),
(452, 3, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(453, 4, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(454, 5, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(455, 6, 46, '330.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(456, 7, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(457, 8, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(458, 9, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(459, 10, 46, '300.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(460, 11, 46, '330.00', 0, 0, '', '2026-02-19 19:56:47', 1),
(461, 2, 47, '270.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(462, 3, 47, '270.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(463, 4, 47, '270.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(464, 5, 47, '250.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(465, 6, 47, '280.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(466, 7, 47, '250.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(467, 8, 47, '250.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(468, 9, 47, '250.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(469, 10, 47, '250.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(470, 11, 47, '280.00', 0, 0, '', '2026-02-23 13:27:04', 1),
(471, 2, 48, '270.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(472, 3, 48, '270.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(473, 4, 48, '270.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(474, 5, 48, '250.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(475, 6, 48, '280.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(476, 7, 48, '250.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(477, 8, 48, '250.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(478, 9, 48, '250.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(479, 10, 48, '250.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(480, 11, 48, '280.00', 0, 0, '', '2026-02-23 13:31:56', 1),
(481, 2, 49, '220.00', 0, 0, 'MENSUALIDAD A 200 SOLES', '2026-02-23 16:48:44', 1),
(482, 3, 49, '220.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(483, 4, 49, '220.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(484, 5, 49, '220.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(485, 6, 49, '230.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(486, 7, 49, '200.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(487, 8, 49, '200.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(488, 9, 49, '200.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(489, 10, 49, '200.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(490, 11, 49, '230.00', 0, 0, '', '2026-02-23 16:48:44', 1),
(491, 2, 50, '310.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(492, 3, 50, '310.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(493, 4, 50, '310.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(494, 5, 50, '310.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(495, 6, 50, '320.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(496, 7, 50, '290.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(497, 8, 50, '290.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(498, 9, 50, '290.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(499, 10, 50, '290.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(500, 11, 50, '320.00', 0, 0, '', '2026-02-23 16:56:29', 1),
(501, 2, 51, '310.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(502, 3, 51, '310.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(503, 4, 51, '310.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(504, 5, 51, '310.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(505, 6, 51, '320.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(506, 7, 51, '290.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(507, 8, 51, '290.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(508, 9, 51, '290.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(509, 10, 51, '290.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(510, 11, 51, '320.00', 0, 0, '', '2026-02-23 19:53:20', 1),
(511, 2, 52, '310.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(512, 3, 52, '310.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(513, 4, 52, '310.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(514, 5, 52, '310.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(515, 6, 52, '320.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(516, 7, 52, '290.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(517, 8, 52, '290.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(518, 9, 52, '290.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(519, 10, 52, '290.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(520, 11, 52, '320.00', 0, 0, '', '2026-02-23 20:01:20', 1),
(521, 2, 53, '340.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(522, 3, 53, '340.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(523, 4, 53, '340.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(524, 5, 53, '320.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(525, 6, 53, '350.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(526, 7, 53, '320.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(527, 8, 53, '320.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(528, 9, 53, '320.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(529, 10, 53, '320.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(530, 11, 53, '350.00', 0, 0, '', '2026-02-24 02:09:02', 1),
(531, 2, 54, '370.00', 0, 0, 'DSCTO. - 10 SOLES HERMANOS', '2026-02-24 17:53:55', 1),
(532, 3, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(533, 4, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(534, 5, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(535, 6, 54, '340.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(536, 7, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(537, 8, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(538, 9, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(539, 10, 54, '310.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(540, 11, 54, '340.00', 0, 0, '', '2026-02-24 17:53:55', 1),
(541, 2, 55, '380.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(542, 3, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(543, 4, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(544, 5, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(545, 6, 55, '350.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(546, 7, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(547, 8, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(548, 9, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(549, 10, 55, '320.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(550, 11, 55, '350.00', 0, 0, '', '2026-02-24 17:57:14', 1),
(551, 2, 56, '380.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(552, 3, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(553, 4, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(554, 5, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(555, 6, 56, '350.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(556, 7, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(557, 8, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(558, 9, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(559, 10, 56, '320.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(560, 11, 56, '350.00', 0, 0, '', '2026-02-24 18:42:53', 1),
(561, 2, 57, '320.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(562, 3, 57, '320.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(563, 4, 57, '320.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(564, 5, 57, '320.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(565, 6, 57, '330.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(566, 7, 57, '300.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(567, 8, 57, '300.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(568, 9, 57, '300.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(569, 10, 57, '300.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(570, 11, 57, '330.00', 0, 0, '', '2026-02-25 17:34:53', 1),
(571, 2, 58, '380.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(572, 3, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(573, 4, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(574, 5, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(575, 6, 58, '350.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(576, 7, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(577, 8, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(578, 9, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(579, 10, 58, '320.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(580, 11, 58, '350.00', 0, 0, '', '2026-02-25 18:00:46', 1),
(581, 2, 59, '310.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(582, 3, 59, '310.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(583, 4, 59, '310.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(584, 5, 59, '310.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(585, 6, 59, '320.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(586, 7, 59, '290.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(587, 8, 59, '290.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(588, 9, 59, '290.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(589, 10, 59, '290.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(590, 11, 59, '320.00', 0, 0, '', '2026-02-25 21:43:37', 1),
(591, 2, 60, '340.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(592, 3, 60, '340.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(593, 4, 60, '340.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(594, 5, 60, '320.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(595, 6, 60, '350.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(596, 7, 60, '320.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(597, 8, 60, '320.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(598, 9, 60, '320.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(599, 10, 60, '320.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(600, 11, 60, '350.00', 0, 0, '', '2026-02-25 22:01:11', 1),
(601, 2, 61, '310.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(602, 3, 61, '310.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(603, 4, 61, '310.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(604, 5, 61, '310.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(605, 6, 61, '320.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(606, 7, 61, '290.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(607, 8, 61, '290.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(608, 9, 61, '290.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(609, 10, 61, '290.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(610, 11, 61, '320.00', 0, 0, '', '2026-02-26 19:30:49', 1),
(611, 2, 62, '320.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(612, 3, 62, '320.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(613, 4, 62, '320.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(614, 5, 62, '320.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(615, 6, 62, '330.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(616, 7, 62, '300.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(617, 8, 62, '300.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(618, 9, 62, '300.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(619, 10, 62, '300.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(620, 11, 62, '330.00', 0, 0, '', '2026-02-26 22:21:10', 1),
(621, 2, 63, '310.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(622, 3, 63, '310.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(623, 4, 63, '310.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(624, 5, 63, '310.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(625, 6, 63, '320.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(626, 7, 63, '290.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(627, 8, 63, '290.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(628, 9, 63, '290.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(629, 10, 63, '290.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(630, 11, 63, '320.00', 0, 0, '', '2026-02-27 19:19:07', 1),
(631, 2, 64, '330.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(632, 3, 64, '330.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(633, 4, 64, '330.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(634, 5, 64, '310.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(635, 6, 64, '340.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(636, 7, 64, '310.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(637, 8, 64, '310.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(638, 9, 64, '310.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(639, 10, 64, '310.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(640, 11, 64, '340.00', 0, 0, '', '2026-02-27 19:31:36', 1),
(641, 2, 65, '330.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(642, 3, 65, '330.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(643, 4, 65, '330.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(644, 5, 65, '310.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(645, 6, 65, '340.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(646, 7, 65, '310.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(647, 8, 65, '310.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(648, 9, 65, '310.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(649, 10, 65, '310.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(650, 11, 65, '340.00', 0, 0, '', '2026-02-27 19:33:01', 1),
(651, 2, 66, '310.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(652, 3, 66, '310.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(653, 4, 66, '310.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(654, 5, 66, '310.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(655, 6, 66, '320.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(656, 7, 66, '290.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(657, 8, 66, '290.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(658, 9, 66, '290.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(659, 10, 66, '290.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(660, 11, 66, '320.00', 0, 0, '', '2026-03-02 13:59:29', 1),
(661, 2, 67, '340.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(662, 3, 67, '340.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(663, 4, 67, '340.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(664, 5, 67, '320.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(665, 6, 67, '350.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(666, 7, 67, '320.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(667, 8, 67, '320.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(668, 9, 67, '320.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(669, 10, 67, '320.00', 0, 0, '', '2026-03-02 14:01:21', 1),
(670, 11, 67, '350.00', 0, 0, '', '2026-03-02 14:01:21', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_utiles`
--

CREATE TABLE `registro_utiles` (
  `id` int(11) NOT NULL,
  `id_matricula` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `observaciones` text,
  `fechacreado` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `registro_utiles`
--

INSERT INTO `registro_utiles` (`id`, `id_matricula`, `nombre`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 1, '01 cuaderno decroly A4 forrado de color _____', '', '2026-01-14 11:52:46', 1),
(2, 1, '01 cuaderno personalizado (caratula entregara el colegio)', '', '2026-01-14 11:52:46', 1),
(3, 1, '01 folder todo papel grueso con liga', '', '2026-01-14 11:52:46', 1),
(4, 1, '01 caja de colores jumbo con su nombre', '', '2026-01-14 11:52:46', 1),
(5, 1, '02 caja de crayolas gruesas (una en su cartuchera y la otra se entrega)', '', '2026-01-14 11:52:46', 1),
(6, 1, '01 tijera punta roma', '', '2026-01-14 11:52:46', 1),
(7, 1, '01 punzón de mango de goma', '', '2026-01-14 11:52:46', 1),
(8, 1, '01 tabla para punzar', '', '2026-01-14 11:52:46', 1),
(9, 1, '400 hojas A3', '', '2026-01-14 11:52:46', 1),
(10, 1, '500 hojas A4', '', '2026-01-14 11:52:46', 1),
(11, 1, '01 block de papel arco iris', '', '2026-01-14 11:52:46', 1),
(12, 1, '20 imperdibles', '', '2026-01-14 11:52:46', 1),
(13, 1, '01 block de papel lustre grande', '', '2026-01-14 11:52:46', 1),
(14, 1, '01 block de cartulinas de colores grande Canson', '', '2026-01-14 11:52:46', 1),
(15, 1, '03 pliego de cartulina dúplex', '', '2026-01-14 11:52:46', 1),
(16, 1, '10 papelógrafos blancos', '', '2026-01-14 11:52:46', 1),
(17, 1, '02 papel crepe colores: _________', '', '2026-01-14 11:52:46', 1),
(18, 1, '02 limpiatipo', '', '2026-01-14 11:52:46', 1),
(19, 1, '02 masking tape grueso colores: ______', '', '2026-01-14 11:52:46', 1),
(20, 1, '02 cinta de embalaje transparente gruesa', '', '2026-01-14 11:52:46', 1),
(21, 1, '02 colas sintéticas con aplicador de 250 ml', '', '2026-01-14 11:52:46', 1),
(22, 1, '02 siliconas líquidas', '', '2026-01-14 11:52:46', 1),
(23, 1, '12 barras de silicona delgadas', '', '2026-01-14 11:52:46', 1),
(24, 1, '02 cajas de plastilina', '', '2026-01-14 11:52:46', 1),
(25, 1, '01 estuche de plumones delgados', '', '2026-01-14 11:52:46', 1),
(26, 1, '01 estuche de plumones gruesos x10', '', '2026-01-14 11:52:46', 1),
(27, 1, '02 pinceles N° 12', '', '2026-01-14 11:52:46', 1),
(28, 1, '01 taper #6', '', '2026-01-14 11:52:46', 1),
(29, 1, '01 témpera por 250 ml color ________', '', '2026-01-14 11:52:46', 1),
(30, 1, '05 micas', '', '2026-01-14 11:52:46', 1),
(31, 1, '1 metro de microporoso de color', '', '2026-01-14 11:52:46', 1),
(32, 1, '1 caja de chinche mariposa', '', '2026-01-14 11:52:46', 1),
(33, 1, '1/2 metro de microporoso con diseño', '', '2026-01-14 11:52:46', 1),
(34, 1, '12 chenille de colores', '', '2026-01-14 11:52:46', 1),
(35, 1, '01 sobre de lentejuelas de colores', '', '2026-01-14 11:52:46', 1),
(36, 1, '10 botones de colores medianos', '', '2026-01-14 11:52:46', 1),
(37, 1, '02 serpentinas', '', '2026-01-14 11:52:46', 1),
(38, 1, '01 bolsa de baja lengua', '', '2026-01-14 11:52:46', 1),
(39, 1, '01 bolsa de palitos de chupete', '', '2026-01-14 11:52:46', 1),
(40, 1, '01 bolsa de globos colores ________', '', '2026-01-14 11:52:46', 1),
(41, 1, '2 docenas de ganchos de ropa plástico', '', '2026-01-14 11:52:46', 1),
(42, 1, '2 paquetes de sorbetes', '', '2026-01-14 11:52:46', 1),
(43, 1, '1 hula hula', '', '2026-01-14 11:52:46', 1),
(44, 1, '1 pelota de trapo', '', '2026-01-14 11:52:46', 1),
(45, 1, '1 titere de peluche', '', '2026-01-14 11:52:46', 1),
(46, 1, '1 foto pasaporte', '', '2026-01-14 11:52:46', 1),
(47, 1, '2 ganchos percheros de plástico adhesivo', '', '2026-01-14 11:52:46', 1),
(48, 1, '1 metro de corrospun escarchado doble ancho color ____________', '', '2026-01-14 11:52:46', 1),
(49, 1, '2 Indelebles negros gruesos', '', '2026-01-14 11:52:46', 1),
(50, 1, '2 Indelebles negros delgados', '', '2026-01-14 11:52:46', 1),
(51, 1, '1 Cinta de agua gruesa color __________', '', '2026-01-14 11:52:46', 1),
(52, 1, '1 Hilo de pescar', '', '2026-01-14 11:52:46', 1),
(53, 1, '1 Ovillo de pabilo', '', '2026-01-14 11:52:46', 1),
(54, 1, '1 Tecnopor forrado', '', '2026-01-14 11:52:46', 1),
(55, 1, '1 Bolsa de Globos Nro. 9 (payaso) Color: __________', '', '2026-01-14 11:52:46', 1),
(56, 1, '1 Bolsa de Globos pencil de colores', '', '2026-01-14 11:52:46', 1),
(57, 1, '12 Paliglobos de colores', '', '2026-01-14 11:52:46', 1),
(58, 1, '12 Ojos movibles tamaño mediano', '', '2026-01-14 11:52:46', 1),
(59, 1, '1 Pincel N° 10', '', '2026-01-14 11:52:46', 1),
(60, 1, '1 Pincel N° 00', '', '2026-01-14 11:52:46', 1),
(61, 1, '1 Paleta para mezclar colores', '', '2026-01-14 11:52:46', 1),
(62, 1, '1 Envase de hisopos', '', '2026-01-14 11:52:46', 1),
(63, 1, '1 Mota para pizarra acrílica', '', '2026-01-14 11:52:46', 1),
(64, 1, '1 Fórmica tamaño A4 color blanco', '', '2026-01-14 11:52:46', 1),
(65, 1, '4 Plumones de pizarra acrílica (rojo, azul, negro y rojo)', '', '2026-01-14 11:52:46', 1),
(66, 1, 'platos 25', '', '2026-01-14 11:52:46', 1),
(67, 1, 'vasos 25', '', '2026-01-14 11:52:46', 1),
(68, 1, 'cucharitas 25', '', '2026-01-14 11:52:46', 1),
(69, 1, '01 balde de playgo con piezas grandes', '', '2026-01-14 11:52:46', 1),
(70, 1, '01 juego didáctico para su edad', '', '2026-01-14 11:52:46', 1),
(71, 1, '01 rompecabeza de encaje', '', '2026-01-14 11:52:46', 1),
(72, 1, '1 rompecabeza de 8 piezas grandes', '', '2026-01-14 11:52:46', 1),
(73, 1, '01 cuento grande plastificado', '', '2026-01-14 11:52:46', 1),
(74, 1, '01 títere de ______', '', '2026-01-14 11:52:46', 1),
(75, 1, '02 láminas de stickers motivadores', '', '2026-01-14 11:52:46', 1),
(76, 1, '10 chapas de color rojo, azul, amarillo y verde', '', '2026-01-14 11:52:46', 1),
(77, 1, '01 jabón líquido', '', '2026-01-14 11:52:46', 1),
(78, 1, '12 rollos de papel higiénico', '', '2026-01-14 11:52:46', 1),
(79, 1, '03 rollos de papel toalla JUMBO', '', '2026-01-14 11:52:46', 1),
(80, 1, '02 paquetes grandes de pañitos húmedos', '', '2026-01-14 11:52:46', 1),
(81, 1, '01 bolsa de pañitos amarillos para limpiar', '', '2026-01-14 11:52:46', 1),
(82, 1, '01 bolsa de algodón', '', '2026-01-14 11:52:46', 1),
(83, 1, '02 esponjas', '', '2026-01-14 11:52:46', 1),
(84, 1, '01 muda con su ropa con nombre', '', '2026-01-14 11:52:46', 1),
(85, 1, '(si usa pañal, traer para la semana)', '', '2026-01-14 11:52:46', 1),
(86, 1, '01 individual', '', '2026-01-14 11:52:46', 1),
(87, 1, '01 toalla de manos', '', '2026-01-14 11:52:46', 1),
(88, 2, '1 cuaderno triple max ? Comunicación', '', '2026-01-14 11:55:20', 1),
(89, 2, '1 cuaderno cuadrimax 2x2 ? Matemática', '', '2026-01-14 11:55:20', 1),
(90, 2, '1 FOLDER Raz. verbal ? Amarillo', '', '2026-01-14 11:55:20', 1),
(91, 2, '1 FOLDER Ciencia y ambiente ? verde', '', '2026-01-14 11:55:20', 1),
(92, 2, '1 FOLDER Personal social ? Azul', '', '2026-01-14 11:55:20', 1),
(93, 2, '1 FOLDER Educación cristiana ? Celeste', '', '2026-01-14 11:55:20', 1),
(94, 2, '1 millar de hojas bond A4', '', '2026-01-14 11:55:20', 1),
(95, 2, '1 block de papel lustre A3', '', '2026-01-14 11:55:20', 1),
(96, 2, '200 hojas de colores vibrantes / pasteles', '', '2026-01-14 11:55:20', 1),
(97, 2, '6 pliegos de papel crepé diferentes colores', '', '2026-01-14 11:55:20', 1),
(98, 2, '2 pliego de cartulina corrugada metálica', '', '2026-01-14 11:55:20', 1),
(99, 2, '3 pliegos de cartulina dúplex', '', '2026-01-14 11:55:20', 1),
(100, 2, '2 block de cartulina de colores A3 y blanco sin anillar y sin borde', '', '2026-01-14 11:55:20', 1),
(101, 2, '1 pliego de cartulina plastificada con diseño', '', '2026-01-14 11:55:20', 1),
(102, 2, '3 pliegos de cartulina blanca', '', '2026-01-14 11:55:20', 1),
(103, 2, 'Papelógrafos (5 blancos, 5 cuadriculados, 5 triple renglón y 5 papel kraft)', '', '2026-01-14 11:55:20', 1),
(104, 2, '3 cintas masking tape gruesa y una delgada)', '', '2026-01-14 11:55:20', 1),
(105, 2, '2 cintas embajale (una transparente y otra de color)', '', '2026-01-14 11:55:20', 1),
(106, 2, '2 cajas de plastilina (normal y fosforescente)', '', '2026-01-14 11:55:20', 1),
(107, 2, '1 goma de 250 g con aplicador', '', '2026-01-14 11:55:20', 1),
(108, 2, '1 témpera de 250 g color __________', '', '2026-01-14 11:55:20', 1),
(109, 2, '1 estuche de 12 plumones delgados', '', '2026-01-14 11:55:20', 1),
(110, 2, '1 estuche de 12 plumones gruesos', '', '2026-01-14 11:55:20', 1),
(111, 2, '1 caja de crayones gruesos', '', '2026-01-14 11:55:20', 1),
(112, 2, '3 frascos de silicona líquida 250 g c/u', '', '2026-01-14 11:55:20', 1),
(113, 2, '2 frascos de goma 250 ml', '', '2026-01-14 11:55:20', 1),
(114, 2, 'Paquete de micas A4 (10 unidades)', '', '2026-01-14 11:55:20', 1),
(115, 2, '1 juego didáctico de engranaje dentro de un taper o depósito con nombre', '', '2026-01-14 11:55:20', 1),
(116, 2, '2 pares de percheros', '', '2026-01-14 11:55:20', 1),
(117, 2, '3 paquetes de baja lengua (de colores y uno natural)', '', '2026-01-14 11:55:20', 1),
(118, 2, '1 tecnopor forrado para punzar tamaño A4', '', '2026-01-14 11:55:20', 1),
(119, 2, '1 punzón mango verde', '', '2026-01-14 11:55:20', 1),
(120, 2, '2 pinceles grueso tamaño 12 y delgado tamaño 3', '', '2026-01-14 11:55:20', 1),
(121, 2, '3 limpia tipos', '', '2026-01-14 11:55:20', 1),
(122, 2, '10 barras de silicona delgada', '', '2026-01-14 11:55:20', 1),
(123, 2, '1 paquete de serpentina', '', '2026-01-14 11:55:20', 1),
(124, 2, '1 bolsa de globos N° 9 color __________ y 12 pali globos', '', '2026-01-14 11:55:20', 1),
(125, 2, '1 pintura APU de color______ de 250', '', '2026-01-14 11:55:20', 1),
(126, 2, '2 paquetes de bolsas brillantes (20x30 y 14x20)', '', '2026-01-14 11:55:20', 1),
(127, 2, '8 plumones de pizarra de diferentes colores con punta gruesa', '', '2026-01-14 11:55:20', 1),
(128, 2, '1 bolsita de ojitos movibles medianos y grandes', '', '2026-01-14 11:55:20', 1),
(129, 2, '6 colores de cerámica granulada (20 grs)', '', '2026-01-14 11:55:20', 1),
(130, 2, '4 plumones delgados color negro (grueso y delgado)', '', '2026-01-14 11:55:20', 1),
(131, 2, '1 rompe cabeza de 20 o 30 piezas', '', '2026-01-14 11:55:20', 1),
(132, 2, '1 títeres de mano', '', '2026-01-14 11:55:20', 1),
(133, 2, '1 cuento clásico grande', '', '2026-01-14 11:55:20', 1),
(134, 2, '2 fine pen (azul, negro y verde)', '', '2026-01-14 11:55:20', 1),
(135, 2, '1 metro de microporoso escarchado ________', '', '2026-01-14 11:55:20', 1),
(136, 2, '1 metro de microporoso normal ________', '', '2026-01-14 11:55:20', 1),
(137, 2, '1 metro de corospum ________', '', '2026-01-14 11:55:20', 1),
(138, 2, '4 micas para laminar a4', '', '2026-01-14 11:55:20', 1),
(139, 2, '1 paquete de brochetas grandes y grueso', '', '2026-01-14 11:55:20', 1),
(140, 2, '50 platos descartables medianos', '', '2026-01-14 11:55:20', 1),
(141, 2, '50 cucharitas', '', '2026-01-14 11:55:20', 1),
(142, 2, '1 block stickers', '', '2026-01-14 11:55:20', 1),
(143, 2, '1 caja chinche mariposa', '', '2026-01-14 11:55:20', 1),
(144, 2, '2 mas king tape de color _________, _________', '', '2026-01-14 11:55:20', 1),
(145, 2, '1 paquete sorbete', '', '2026-01-14 11:55:20', 1),
(146, 2, '10 bolsas de papel blanco 27x15', '', '2026-01-14 11:55:20', 1),
(147, 2, '2 paquetes de chelines o limpiapipas (metálicos o simples)', '', '2026-01-14 11:55:20', 1),
(148, 2, '1 pote mediano de escarcha giratorio de varios colores', '', '2026-01-14 11:55:20', 1),
(149, 2, '1 individual', '', '2026-01-14 11:55:20', 1),
(150, 2, '1 set de sellos de esponjas para pintura', '', '2026-01-14 11:55:20', 1),
(151, 2, '1 madeja de lana grande color ________', '', '2026-01-14 11:55:20', 1),
(152, 2, '1 pote pequeño de papel contact', '', '2026-01-14 11:55:20', 1),
(153, 2, '1 ula ula', '', '2026-01-14 11:55:20', 1),
(154, 2, '1 pelota de trapo', '', '2026-01-14 11:55:20', 1),
(155, 2, '1 Biblia &quot;Amigo de Jesús&quot;', '', '2026-01-14 11:55:20', 1),
(156, 2, 'Colores Jumbo', '', '2026-01-14 11:55:20', 1),
(157, 2, '1 cartuchera (lápiz jumbo, borrador grande, tajador con depósito de orificio grande, tijera punta roma)', '', '2026-01-14 11:55:20', 1),
(158, 2, '(Los libros se adquieren en la institución)', '', '2026-01-14 11:55:20', 1),
(159, 2, 'Mandil de arte (se adquieren en la institución)', '', '2026-01-14 11:55:20', 1),
(160, 2, '4 sacos de color (8x15 cm) llenos de cereales color rojo, azul, amarillo, verde.', '', '2026-01-14 11:55:20', 1),
(161, 2, '1 poet spray', '', '2026-01-14 11:55:20', 1),
(162, 2, '2 paquetes de pañitos húmedos', '', '2026-01-14 11:55:20', 1),
(163, 2, '1 jabón líquido', '', '2026-01-14 11:55:20', 1),
(164, 2, '3 rollos de papel toalla', '', '2026-01-14 11:55:20', 1),
(165, 2, '3 paquetes de papel higiénico Noble (4 unidades)', '', '2026-01-14 11:55:20', 1),
(166, 2, '2 paños absorbentes', '', '2026-01-14 11:55:20', 1),
(167, 2, '1 limpiador de piso (líquido o spray)', '', '2026-01-14 11:55:20', 1),
(168, 2, '1 individual – bolsa de aseo con su nombre', '', '2026-01-14 11:55:20', 1),
(169, 2, 'Toalla con su sello y nombre del alumno', '', '2026-01-14 11:55:20', 1),
(170, 2, '1 tiza de colores (caja)', '', '2026-01-14 11:55:20', 1),
(171, 2, '1 pote de cerámica frío blanco', '', '2026-01-14 11:55:20', 1),
(172, 2, '2 pañuelos de 30x30 raso amarillo', '', '2026-01-14 11:55:20', 1),
(173, 2, '1 capa de alfiler', '', '2026-01-14 11:55:20', 1),
(174, 2, '6 bolsas lentejuelas grandes de colores', '', '2026-01-14 11:55:20', 1),
(175, 2, '25 botones tamaño 10 centímetros de colores diversos', '', '2026-01-14 11:55:20', 1),
(176, 3, '1 cuaderno triple max ? Comunicación', '', '2026-01-14 12:05:42', 1),
(177, 3, '1 cuaderno cuadrimax 2x2 ? Matemática', '', '2026-01-14 12:05:42', 1),
(178, 3, '1 FOLDER Raz. verbal ? Amarillo', '', '2026-01-14 12:05:42', 1),
(179, 3, '1 FOLDER Ciencia y ambiente ? verde', '', '2026-01-14 12:05:42', 1),
(180, 3, '1 FOLDER Personal social ? Azul', '', '2026-01-14 12:05:42', 1),
(181, 3, '1 FOLDER Educación cristiana ? Celeste', '', '2026-01-14 12:05:42', 1),
(182, 3, '1 millar de hojas bond A4', '', '2026-01-14 12:05:42', 1),
(183, 3, '1 block de papel lustre A3', '', '2026-01-14 12:05:42', 1),
(184, 3, '200 hojas de colores vibrantes / pasteles', '', '2026-01-14 12:05:42', 1),
(185, 3, '6 pliegos de papel crepé diferentes colores', '', '2026-01-14 12:05:42', 1),
(186, 3, '2 pliego de cartulina corrugada metálica', '', '2026-01-14 12:05:42', 1),
(187, 3, '3 pliegos de cartulina dúplex', '', '2026-01-14 12:05:42', 1),
(188, 3, '2 block de cartulina de colores A3 y blanco sin anillar y sin borde', '', '2026-01-14 12:05:42', 1),
(189, 3, '1 pliego de cartulina plastificada con diseño', '', '2026-01-14 12:05:42', 1),
(190, 3, '3 pliegos de cartulina blanca', '', '2026-01-14 12:05:42', 1),
(191, 3, 'Papelógrafos (5 blancos, 5 cuadriculados, 5 triple renglón y 5 papel kraft)', '', '2026-01-14 12:05:42', 1),
(192, 3, '3 cintas masking tape gruesa y una delgada)', '', '2026-01-14 12:05:42', 1),
(193, 3, '2 cintas embajale (una transparente y otra de color)', '', '2026-01-14 12:05:42', 1),
(194, 3, '2 cajas de plastilina (normal y fosforescente)', '', '2026-01-14 12:05:42', 1),
(195, 3, '1 goma de 250 g con aplicador', '', '2026-01-14 12:05:42', 1),
(196, 3, '1 témpera de 250 g color __________', '', '2026-01-14 12:05:42', 1),
(197, 3, '1 estuche de 12 plumones delgados', '', '2026-01-14 12:05:42', 1),
(198, 3, '1 estuche de 12 plumones gruesos', '', '2026-01-14 12:05:42', 1),
(199, 3, '1 caja de crayones gruesos', '', '2026-01-14 12:05:42', 1),
(200, 3, '3 frascos de silicona líquida 250 g c/u', '', '2026-01-14 12:05:42', 1),
(201, 3, '2 frascos de goma 250 ml', '', '2026-01-14 12:05:42', 1),
(202, 3, 'Paquete de micas A4 (10 unidades)', '', '2026-01-14 12:05:42', 1),
(203, 3, '1 juego didáctico de engranaje dentro de un taper o depósito con nombre', '', '2026-01-14 12:05:42', 1),
(204, 3, '2 pares de percheros', '', '2026-01-14 12:05:42', 1),
(205, 3, '3 paquetes de baja lengua (de colores y uno natural)', '', '2026-01-14 12:05:42', 1),
(206, 3, '1 tecnopor forrado para punzar tamaño A4', '', '2026-01-14 12:05:42', 1),
(207, 3, '1 punzón mango verde', '', '2026-01-14 12:05:42', 1),
(208, 3, '2 pinceles grueso tamaño 12 y delgado tamaño 3', '', '2026-01-14 12:05:42', 1),
(209, 3, '3 limpia tipos', '', '2026-01-14 12:05:42', 1),
(210, 3, '10 barras de silicona delgada', '', '2026-01-14 12:05:42', 1),
(211, 3, '1 paquete de serpentina', '', '2026-01-14 12:05:42', 1),
(212, 3, '1 bolsa de globos N° 9 color __________ y 12 pali globos', '', '2026-01-14 12:05:42', 1),
(213, 3, '1 pintura APU de color______ de 250', '', '2026-01-14 12:05:42', 1),
(214, 3, '2 paquetes de bolsas brillantes (20x30 y 14x20)', '', '2026-01-14 12:05:42', 1),
(215, 3, '8 plumones de pizarra de diferentes colores con punta gruesa', '', '2026-01-14 12:05:42', 1),
(216, 3, '1 bolsita de ojitos movibles medianos y grandes', '', '2026-01-14 12:05:42', 1),
(217, 3, '6 colores de cerámica granulada (20 grs)', '', '2026-01-14 12:05:42', 1),
(218, 3, '4 plumones delgados color negro (grueso y delgado)', '', '2026-01-14 12:05:42', 1),
(219, 3, '1 rompe cabeza de 20 o 30 piezas', '', '2026-01-14 12:05:42', 1),
(220, 3, '1 títeres de mano', '', '2026-01-14 12:05:42', 1),
(221, 3, '1 cuento clásico grande', '', '2026-01-14 12:05:42', 1),
(222, 3, '2 fine pen (azul, negro y verde)', '', '2026-01-14 12:05:42', 1),
(223, 3, '1 metro de microporoso escarchado ________', '', '2026-01-14 12:05:42', 1),
(224, 3, '1 metro de microporoso normal ________', '', '2026-01-14 12:05:42', 1),
(225, 3, '1 metro de corospum ________', '', '2026-01-14 12:05:42', 1),
(226, 3, '4 micas para laminar a4', '', '2026-01-14 12:05:42', 1),
(227, 3, '1 paquete de brochetas grandes y grueso', '', '2026-01-14 12:05:42', 1),
(228, 3, '50 platos descartables medianos', '', '2026-01-14 12:05:42', 1),
(229, 3, '50 cucharitas', '', '2026-01-14 12:05:42', 1),
(230, 3, '1 block stickers', '', '2026-01-14 12:05:42', 1),
(231, 3, '1 caja chinche mariposa', '', '2026-01-14 12:05:42', 1),
(232, 3, '2 mas king tape de color _________, _________', '', '2026-01-14 12:05:42', 1),
(233, 3, '1 paquete sorbete', '', '2026-01-14 12:05:42', 1),
(234, 3, '10 bolsas de papel blanco 27x15', '', '2026-01-14 12:05:42', 1),
(235, 3, '2 paquetes de chelines o limpiapipas (metálicos o simples)', '', '2026-01-14 12:05:42', 1),
(236, 3, '1 pote mediano de escarcha giratorio de varios colores', '', '2026-01-14 12:05:42', 1),
(237, 3, '1 individual', '', '2026-01-14 12:05:42', 1),
(238, 3, '1 set de sellos de esponjas para pintura', '', '2026-01-14 12:05:42', 1),
(239, 3, '1 madeja de lana grande color ________', '', '2026-01-14 12:05:42', 1),
(240, 3, '1 pote pequeño de papel contact', '', '2026-01-14 12:05:42', 1),
(241, 3, '1 ula ula', '', '2026-01-14 12:05:42', 1),
(242, 3, '1 pelota de trapo', '', '2026-01-14 12:05:42', 1),
(243, 3, '1 Biblia &quot;Amigo de Jesús&quot;', '', '2026-01-14 12:05:42', 1),
(244, 3, 'Colores Jumbo', '', '2026-01-14 12:05:42', 1),
(245, 3, '1 cartuchera (lápiz jumbo, borrador grande, tajador con depósito de orificio grande, tijera punta roma)', '', '2026-01-14 12:05:42', 1),
(246, 3, '(Los libros se adquieren en la institución)', '', '2026-01-14 12:05:42', 1),
(247, 3, 'Mandil de arte (se adquieren en la institución)', '', '2026-01-14 12:05:42', 1),
(248, 3, '4 sacos de color (8x15 cm) llenos de cereales color rojo, azul, amarillo, verde.', '', '2026-01-14 12:05:42', 1),
(249, 3, '1 poet spray', '', '2026-01-14 12:05:42', 1),
(250, 3, '2 paquetes de pañitos húmedos', '', '2026-01-14 12:05:42', 1),
(251, 3, '1 jabón líquido', '', '2026-01-14 12:05:42', 1),
(252, 3, '3 rollos de papel toalla', '', '2026-01-14 12:05:42', 1),
(253, 3, '3 paquetes de papel higiénico Noble (4 unidades)', '', '2026-01-14 12:05:42', 1),
(254, 3, '2 paños absorbentes', '', '2026-01-14 12:05:42', 1),
(255, 3, '1 limpiador de piso (líquido o spray)', '', '2026-01-14 12:05:42', 1),
(256, 3, '1 individual – bolsa de aseo con su nombre', '', '2026-01-14 12:05:42', 1),
(257, 3, 'Toalla con su sello y nombre del alumno', '', '2026-01-14 12:05:42', 1),
(258, 3, '1 tiza de colores (caja)', '', '2026-01-14 12:05:42', 1),
(259, 3, '1 pote de cerámica frío blanco', '', '2026-01-14 12:05:42', 1),
(260, 3, '2 pañuelos de 30x30 raso amarillo', '', '2026-01-14 12:05:42', 1),
(261, 3, '1 capa de alfiler', '', '2026-01-14 12:05:42', 1),
(262, 3, '6 bolsas lentejuelas grandes de colores', '', '2026-01-14 12:05:42', 1),
(263, 3, '25 botones tamaño 10 centímetros de colores diversos', '', '2026-01-14 12:05:42', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_utiles_detalle`
--

CREATE TABLE `registro_utiles_detalle` (
  `id` int(11) NOT NULL,
  `id_matricula_detalle` int(11) NOT NULL,
  `id_registro_utiles` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `observaciones` text,
  `fechacreado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(8, 7, 1, '90259735', 'MEDINA FUENTES BRISTAN MILER', '2017-06-09', '', 1, '90259735', '90259735', '', '2026-01-05 20:04:51', 1),
(9, 8, 1, '79992036', 'ORE CARHUAS KALET EMANUEL', '2016-12-09', '', 1, '79992036', '79992036', '', '2026-01-06 16:46:28', 1),
(10, 9, 1, '79928099', 'REGALADO TITO ANGELA NADESKA', '2016-11-04', '', 2, '79928099', '79928099', '', '2026-01-06 16:53:17', 1),
(11, 10, 1, '79176666', 'OBREGON RISCO MIQUEAS SANTIAGO', '2015-06-27', '', 1, '79176666', '79176666', '', '2026-01-06 16:57:03', 1),
(12, 11, 1, '79011209', 'CONDORI PALACIOS THIAGO GABRIEL', '2015-03-05', '', 1, '79011209', '79011209', '', '2026-01-06 17:03:09', 1),
(13, 12, 1, '90495561', 'QUINTANA VARGAS MARIANO ESTIVEN', '2017-11-03', '', 1, '90495561', '90495561', '', '2026-01-06 17:04:44', 1),
(14, 13, 1, '78911962', 'COTRINA TORIBIO ZOE LUCIANA', '2014-12-28', '', 2, '78911962', '78911962', '', '2026-01-06 17:08:52', 1),
(15, 15, 1, '91521536', 'PACHECO TRUJILLO LIED MATHEO', '2019-09-17', '', 1, '91521536', '91521536', '', '2026-01-06 17:24:33', 1),
(16, 18, 1, '92756809', 'ZURITA GONZALES DYLAN VALENTINO', '2022-02-14', '', 1, '92756809', '92756809', '', '2026-01-07 14:39:06', 1),
(17, 19, 1, '91339973', 'PAREDES SAYO LUCIANA ALESSANDRA', '2019-05-20', '', 2, '91339973', '91339973', '', '2026-01-07 15:10:34', 1),
(18, 22, 1, '78820597', 'RAMOS DIONICIO ANDREA SAMANTHA', '2014-10-15', '', 2, '78820597', '78820597', '', '2026-01-13 14:29:48', 1),
(19, 23, 1, '78729458', 'CHUSDEN ARANA THIAGO JOSUE', '2014-07-05', '', 1, '78729458', '78729458', '', '2026-01-14 14:40:30', 1),
(20, 24, 1, '93171250', 'DIONICIO FLORES KAYLANI CATALINA', '2022-12-08', '', 2, '93171250', '93171250', '', '2026-01-15 14:47:22', 1),
(21, 29, 1, '92886672', 'CHUECHA FLORES ANDREW JASSIR GAEL', '2022-05-11', '', 1, '92886672', '92886672', '', '2026-01-22 14:00:53', 1),
(22, 30, 1, '93369922', 'ANAYA TORRES ALESSIA', '2023-05-03', '', 2, '93369922', '93369922', '', '2026-01-22 14:07:46', 1),
(23, 31, 1, '92970628', 'MEJIA HUACACOLQUE ANTONELLA MICHELL', '2022-07-10', '', 2, '92970628', '92970628', '', '2026-01-22 14:53:30', 1),
(24, 33, 1, '93115690', 'PONCE SANCHEZ FERNANDA LUANA', '2022-10-27', '', 2, '93115690', '93115690', '', '2026-01-22 16:43:51', 1),
(25, 34, 1, '93279128', 'POMALAZA VELIZ RAFAEL ESTEFANO', '2023-02-25', '', 1, '93279128', '93279128', '', '2026-01-22 17:28:50', 1),
(26, 32, 3, '003393507', 'CHAVEZ VENTURA IVANNA DEL CARMEN', '2014-11-29', '', 2, '003393507', '003393507', '', '2026-01-28 16:33:42', 1),
(27, 35, 1, '93496995', 'PALACIOS LLEMPEN ANTONELLA MILAGROS', '2023-08-09', '', 2, '93496995', '93496995', '', '2026-01-29 13:38:32', 1),
(28, 36, 1, '91647311', 'LLEMPEN MESTANZA THAIS ALEJANDRA', '2019-12-22', '', 2, '91647311', '91647311', '', '2026-01-29 13:43:48', 1),
(29, 37, 1, '81676649', 'VALLES FLORES ARIANA ALESSANDRA', '2014-07-26', '', 2, '81676649', '81676649', '', '2026-01-30 15:10:26', 1),
(30, 17, 1, '79038891', 'TELLO TORIBIO ORLANDO SAMIR', '2015-03-26', '', 1, '79038891', '79038891', '', '2026-01-31 17:35:00', 1),
(31, 40, 1, '90721032', 'AYESTA MOLINA DANNA ISABELLA', '2018-03-17', '', 2, '90721032', '90721032', '', '2026-01-31 17:44:10', 1),
(32, 41, 1, '78905918', 'OSORIO VALDIVIA ARHIAN ALEJANDRO', '2014-12-18', '', 1, '78905918', '78905918', '', '2026-01-31 18:15:29', 1),
(33, 27, 1, '91070331', 'BAZALAR EGOAVIL HASSAN EDUARDO', '2018-10-15', '', 1, '91070331', '91070331', '', '2026-02-02 16:13:15', 1),
(34, 46, 1, '93464819', 'DELGADO PALACIOS NICOLLE ISABELLA', '2023-07-14', '', 2, '93464819', '93464819', '', '2026-02-04 02:14:59', 1),
(35, 48, 1, '79084038', 'CARRIZO GRIMALDO ALFRED LYAM EMERICK', '2015-03-19', '', 1, '79084038', '79084038', '', '2026-02-05 17:03:36', 1),
(36, 49, 1, '79999511', 'MENDO JUAPE MATTHEW BENJAMIN', '2016-11-19', '', 1, '79999511', '79999511', '', '2026-02-07 17:41:35', 1),
(37, 50, 1, '92272336', 'CASTILLO PALACIOS ALEXA LUHANA', '2021-03-15', '', 2, '92272336', '92272336', '', '2026-02-09 13:33:59', 1),
(38, 45, 1, '90726453', 'TANTARUNA CERVANTES SANTIAGO ALESSANDRO', '2018-04-03', '', 1, '90726453', '90726453', '', '2026-02-10 23:19:08', 1),
(39, 53, 1, '91968865', 'VELASCO MEZA JUAN IGNACIO', '2020-08-12', '', 1, '91968865', '91968865', '', '2026-02-10 23:35:05', 1),
(40, 55, 1, '93181643', 'MANCO SUAREZ ARIANNA MASSIEL', '2022-12-16', '', 2, '93181643', '93181643', '', '2026-02-11 16:04:53', 1),
(41, 44, 1, '92811886', 'REYNA LOPEZ JOAO JOSE', '2022-03-22', '', 1, '92811886', '92811886', '', '2026-02-11 20:10:39', 1),
(42, 42, 1, '91302412', 'URBAY PRADO LUKAS ALESSANDRO', '2019-04-27', '', 1, '91302412', '91302412', '', '2026-02-17 23:39:18', 1),
(43, 16, 1, '79738641', 'ROJAS SANCHO PABLO JEREMIAS', '2016-06-30', '', 1, '79738641', '79738641', '', '2026-02-17 23:41:57', 1),
(44, 56, 1, '90169771', 'VARGAS VARGAS IVANNA CLARISA', '2017-04-07', '', 2, '90169771', '90169771', '', '2026-02-17 23:58:18', 1),
(45, 57, 1, '91907638', 'CHAHUA CASO DANNA ROSMADILEY', '2020-06-27', '', 2, '91907638', '91907638', '', '2026-02-18 00:03:09', 1),
(46, 59, 1, '92797671', 'GARCIA PACHECO ADRIEL EZEQUIEL', '2022-03-13', '', 1, '92797671', '92797671', '', '2026-02-19 19:56:47', 1),
(47, 62, 1, '91340784', 'TORRES FERRER DAHELA RENATTA', '2019-09-03', '', 2, '91340784', '91340784', '', '2026-02-23 13:27:04', 1),
(48, 62, 1, '90702846', 'TORRES FERRER MILAN IGNACIO', '2018-03-25', '', 1, '90702846', '90702846', '', '2026-02-23 13:31:56', 1),
(49, 8, 1, '91970022', 'ORE CARHUAS MICAELA NOEMI', '2020-08-13', '', 2, '91970022', '91970022', '', '2026-02-23 16:48:44', 1),
(50, 63, 1, '93606186', 'HUIZA ORE NOAH SEBASTIAN', '2023-11-06', '', 1, '93606186', '93606186', '', '2026-02-23 16:56:29', 1),
(51, 64, 1, '93525710', 'SAAVEDRA GRANADOS ÁMBAR BELLA VICTORIA', '2023-09-02', '', 2, '93525710', '93525710', '', '2026-02-23 19:53:20', 1),
(52, 65, 1, '93230891', 'VILLACORTA TORRES LUNA BELEN MARINA', '2023-01-23', '', 2, '93230891', '93230891', '', '2026-02-23 20:01:20', 1),
(53, 66, 1, '90994341', 'QUISPE RIVAS SAMANTHA NATALIA', '2018-10-05', '', 2, '90994341', '90994341', '', '2026-02-24 02:09:02', 1),
(54, 68, 1, '90080462', 'AGUILAR HUAMANI LUIS MANOLO PELLÉ', '2017-02-07', '', 1, '90080462', '90080462', '', '2026-02-24 17:53:55', 1),
(55, 68, 1, '79216857', 'AGUILAR HUAMANI LUKA ESTEFFANO', '2015-07-18', '', 1, '79216857', '79216857', '', '2026-02-24 17:57:14', 1),
(56, 69, 1, '79602888', 'BENITES HUAMANI HAMZELL SALVADOR', '2016-03-30', '', 1, '79602888', '79602888', '', '2026-02-24 18:42:53', 1),
(57, 70, 1, '92055336', 'PORRAS ARCE DAMARIS NOEMI FRANCHESCA', '2020-10-10', '', 2, '92055336', '92055336', '', '2026-02-25 17:34:53', 1),
(58, 71, 1, '91601455', 'SOVERO PEREA NICOLAS FABRIZIO', '2019-11-20', '', 1, '91601455', '91601455', '', '2026-02-25 18:00:46', 1),
(59, 72, 1, '93479889', 'QUISPE MARQUEZ AITANA', '2023-07-26', '', 2, '93479889', '93479889', '', '2026-02-25 21:43:37', 1),
(60, 73, 1, '90076442', 'PAMPA DE LA CRUZ JHANLUCAS DAVID', '2017-02-12', '', 1, '90076442', '90076442', '', '2026-02-25 22:01:11', 1),
(61, 74, 1, '93399097', 'CHUTON GUTIERREZ MICHELLA ANNETH', '2023-05-25', '', 2, '93399097', '93399097', '', '2026-02-26 19:30:49', 1),
(62, 75, 1, '92721771', 'PALACIOS ACOSTA JEREMY ADRIEL', '2022-01-22', '', 1, '92721771', '92721771', '', '2026-02-26 22:21:10', 1),
(63, 76, 1, '92948624', 'VILLANUEVA VILLACORTA NATHAN JAFETH', '2022-06-24', '', 1, '92948624', '92948624', '', '2026-02-27 19:19:07', 1),
(64, 77, 1, '91614882', 'YACTAYO GARCIA ZITLIALI ISABEL', '2019-11-30', '', 2, '91614882', '91614882', '', '2026-02-27 19:31:36', 1),
(65, 77, 1, '90956431', 'GARCIA RAMOS ENRIQUE FRANYERI', '2018-09-09', '', 1, '90956431', '90956431', '', '2026-02-27 19:33:01', 1),
(66, 79, 1, '93100329', 'PACHAS HUAMAN LIAM EMMANUEL', '2022-10-15', '', 1, '93100329', '93100329', '', '2026-03-02 13:59:29', 1),
(67, 79, 1, '91255628', 'GOMEZ HUAMAN NAYTARA KIABETH', '2019-03-10', '', 2, '91255628', '91255628', '', '2026-03-02 14:01:21', 1);

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
(7, 1, 1, '75539860', 'KATIA NORCELIA FUENTES CHAVEZ', '901931165', '75539860', '75539860', '', '2026-01-05 20:04:51', 1),
(8, 1, 1, '41281051', 'MARIA DE LOS ANGELES CARHUAS YJUMA', '932262539', '41281051', '41281051', '', '2026-01-06 16:46:28', 1),
(9, 1, 1, '44372303', 'ELENA YSABEL TITO LUQUE', '991060291', '44372303', '44372303', '', '2026-01-06 16:53:16', 1),
(10, 3, 1, '10154401', 'MARGOT JULIANA GARCIA ALVARADO', '993983970', '10154401', '10154401', '', '2026-01-06 16:57:03', 1),
(11, 1, 1, '46936573', 'LISETH KATHERINE PALACIOS BERMUDEZ', '977164577', '46936573', '46936573', '', '2026-01-06 17:03:09', 1),
(12, 1, 1, '09616813', 'JUVENAL QUINTANA ALVAREZ', '991179293', '09616813', '09616813', '', '2026-01-06 17:04:44', 1),
(13, 1, 1, '41478915', 'DORIS TORIBIO DOMINGUEZ', '924609727', '41478915', '41478915', '', '2026-01-06 17:08:52', 1),
(14, 1, 1, '07125138', 'ELIZABETH FRANCISCA JIMENEZ IJUMA', '972255710', '07125138', '07125138', '', '2026-01-06 17:13:02', 1),
(15, 1, 1, '72979729', 'JACKELIN LISSET TRUJILLO HURTADO', '914012987', '72979729', '72979729', '', '2026-01-06 17:24:33', 1),
(16, 1, 1, '47708402', 'LAURA ANGELICA SANCHO ALCANTARA', '978673981', '47708402', '47708402', '', '2026-01-07 14:28:24', 1),
(17, 1, 1, '10508169', 'GENOVEVA TORIBIO DOMINGUEZ', '930502729', '10508169', '10508169', '', '2026-01-07 14:31:47', 1),
(18, 1, 1, '43184714', 'YURITSA ANTONIA GONZALES ALMIDON', '960623492', '43184714', '43184714', '955188598', '2026-01-07 14:39:06', 1),
(19, 1, 1, '10161788', 'INES SAYO ANAYA', '996627688', '10161788', '10161788', '', '2026-01-07 15:10:34', 1),
(20, 1, 1, '47216577', 'CARLA REYDELINDA RIOS VILLACORTA', '966948275', '47216577', '47216577', '', '2026-01-07 20:55:46', 1),
(21, 1, 1, '42817048', 'MARIA ELENA TITO QUISPE', '984034603', '42817048', '42817048', '', '2026-01-08 14:55:15', 1),
(22, 1, 1, '45337115', 'ELIZABETH ESTHER DIONICIO PONCE', '993322443', '45337115', '45337115', '', '2026-01-13 14:29:48', 1),
(23, 1, 1, '46591150', 'YANINA PAOLA ARANA NEYRA', '947046011', '46591150', '46591150', '', '2026-01-14 14:40:30', 1),
(24, 1, 1, '48865689', 'LISBETH DAYANA FLORES TRUJILLO', '993747432', '48865689', '48865689', 'PABLO CESAR DIONICIO PEREZ - 980712671', '2026-01-15 14:47:22', 1),
(25, 1, 1, '16023830', 'ROSARIO ELIZABETH HUERTA PALACIOS DE BLAS', '934293947', '16023830', '16023830', '', '2026-01-16 17:26:48', 1),
(26, 1, 1, '44636399', 'ROSA JOHANA MINA REYES', '924810456', '44636399', '44636399', '', '2026-01-19 15:45:38', 1),
(27, 1, 1, '72291208', 'SHEYLA WENDY EGOAVIL CRESPO', '917551077', '72291208', '72291208', 'PADRE : ALDO EDUARDO BAZALAR SIPAN\r\nTELEFONO : 995908255', '2026-01-20 15:02:58', 1),
(28, 1, 1, '71526094', 'KAREN KRISTEL CANO TAYPE', '935748538', '71526094', '71526094', '', '2026-01-20 19:28:45', 1),
(29, 1, 1, '46735666', 'CELIA GUILLERMINA FLORES ESPINOZA', '960615375', '46735666', '46735666', '', '2026-01-22 14:00:53', 1),
(30, 1, 1, '45492823', 'IVETTE TORRES MORALES', '940759322', '45492823', '45492823', '', '2026-01-22 14:07:46', 1),
(31, 1, 1, '44112417', 'WENDY STEFANNY HUACACOLQUE MORENO', '912648470', '44112417', '44112417', 'ALEX AMERICO HUACACOLQUE TORRES- 910084526', '2026-01-22 14:53:30', 1),
(32, 1, 3, '003968434', 'ZULEIDY CAROLINA VENTURA ZEA', '910063816', '003968434', '003968434', '', '2026-01-22 15:29:01', 1),
(33, 1, 1, '62734985', 'GREIDY NEIROBI SANCHEZ PEÑA', '984760642', '62734985', '62734985', '', '2026-01-22 16:43:51', 1),
(34, 1, 1, '46788043', 'MARJORIE VELIZ MIRANDA', '980495621', '46788043', '46788043', '', '2026-01-22 17:28:50', 1),
(35, 1, 1, '41182783', 'SINTIA MICAELA LLEMPEN OLIVERA', '991663801', '41182783', '41182783', '', '2026-01-29 13:38:32', 1),
(36, 1, 1, '41102080', 'MARIA ADELINDA MESTANZA SUAREZ', '990477169', '41102080', '41102080', '', '2026-01-29 13:43:48', 1),
(37, 1, 1, '41838172', 'ELIZABETH FLORES MELON', '970336147', '41838172', '41838172', '997236632 - ABUELITA - ENCARGADA', '2026-01-30 15:10:26', 1),
(38, 1, 1, '75679145', 'KIMBERLY NAYELLI HUAIRA PADILLA', '936065049', '75679145', '75679145', '', '2026-01-30 16:58:59', 1),
(39, 1, 1, '76217197', 'MARYCIELO NASHELI ESPINAL LEVANO', '968151666', '76217197', '76217197', '', '2026-01-30 17:08:35', 1),
(40, 1, 1, '43886356', 'GENY LAYCES MOLINA CHIRCCA', '999239205', '43886356', '43886356', '', '2026-01-31 17:44:10', 1),
(41, 1, 1, '45977821', 'ERLINDA MARIBEL VALDIVIA LOPEZ', '945535265', '45977821', '45977821', '', '2026-01-31 18:15:29', 1),
(42, 1, 1, '43629086', 'ANGELICA MARIELA PRADO NEYRA', '924339115', '43629086', '43629086', '', '2026-02-02 13:57:07', 1),
(43, 1, 1, '40908831', 'WUENDY KAREN CARDENAS MATOS', '993436180', '40908831', '40908831', '', '2026-02-02 17:00:51', 1),
(44, 1, 1, '43100208', 'EDITH YOLANDA LOPEZ CURE', '980602670', '43100208', '43100208', '', '2026-02-02 19:11:20', 1),
(45, 1, 1, '60773832', 'EMELY CERVANTES DE LA CRUZ', '912008608', '60773832', '60773832', 'Epifanía agustina de la cruz postillos 933245826 abuela\r\nCARLOS MENDOZA TORRES - 969717117- abuelo -', '2026-02-03 16:58:19', 1),
(46, 1, 1, '76643970', 'ISABEL NOEMI PALACIOS BERMUDEZ', '946656546', '76643970', '76643970', '', '2026-02-04 02:14:59', 1),
(47, 1, 1, '46409862', 'ANGIE CAROLEY TIRADO SUAREZ', '989509698', '46409862', '46409862', '', '2026-02-04 21:32:21', 1),
(48, 3, 1, '46016217', 'MILEIDY CAROL DESIREH GRIMALDO MATOS', '982913811', '46016217', '46016217', '', '2026-02-05 17:03:36', 1),
(49, 1, 1, '48085420', 'LIGUIA ELENA MENDO JUAPE', '904554626', '48085420', '48085420', '', '2026-02-07 17:41:35', 1),
(50, 1, 1, '44787577', 'EMELYN MILAGROS PALACIOS BERMUDEZ', '958156929', '44787577', '44787577', '', '2026-02-09 13:33:59', 1),
(51, 1, 1, '75656377', 'MARCIA JANELLI CHERO MONTES', '994070866', '75656377', '75656377', '', '2026-02-09 13:35:16', 1),
(52, 1, 1, '62602786', 'RUDDY MADELEY RODRIGUEZ MALPARTIDA', '968478290', '62602786', '62602786', '', '2026-02-09 20:12:34', 1),
(53, 1, 1, '41812264', 'MARILYN PAOLA MEZA VELAZCO', '967535724', '41812264', '41812264', '', '2026-02-10 23:35:05', 1),
(54, 1, 1, '46870829', 'YENY MARCELA NOEL CLERQUE', '976246137', '46870829', '46870829', '', '2026-02-11 13:14:33', 1),
(55, 1, 1, '72697045', 'WENDY JULIANA SUAREZ TERRONES', '915150897', '72697045', '72697045', '', '2026-02-11 16:04:53', 1),
(56, 1, 1, '43942807', 'NOEMI SANDRA VARGAS LOAYZA', '954780664', '43942807', '43942807', '', '2026-02-17 23:58:18', 1),
(57, 1, 1, '44173933', 'LADY YANET CASO HIDALGO', '934645705', '44173933', '44173933', '', '2026-02-18 00:03:09', 1),
(58, 1, 1, '42102415', 'DANISSE GIANCARLA SANCHEZ CORNEJO', '982750957', '42102415', '42102415', '', '2026-02-19 16:29:49', 1),
(59, 1, 1, '46623626', 'DORIS GIANINA PACHECO SIFUENTES', '978733540', '46623626', '46623626', '', '2026-02-19 19:56:47', 1),
(60, 1, 1, '44960462', 'JENNIFER ANAHY VIZCARRA FLORES DE PARI', '947278224', '44960462', '44960462', '', '2026-02-19 20:31:36', 1),
(61, 1, 1, '43656977', 'KARINA LICET OCHANTE HUYHUA', '940189176', '43656977', '43656977', '', '2026-02-19 20:39:15', 1),
(62, 1, 1, '44520410', 'EVELIN MARITZA FERRER TAFUR', '987499267', '44520410', '44520410', '', '2026-02-23 13:27:04', 1),
(63, 1, 1, '74495965', 'SANTA ESTHER ORE CARHUAS', '940943802', '74495965', '74495965', '', '2026-02-23 16:56:29', 1),
(64, 1, 1, '73253388', 'MARICIELO BELLALUZ GRANADOS MENDOZA', '922884364', '73253388', '73253388', '', '2026-02-23 19:53:20', 1),
(65, 1, 1, '72905676', 'NOEMY ESTER TORRES NORIEGA', '984370453', '72905676', '72905676', '', '2026-02-23 20:01:20', 1),
(66, 1, 1, '44345419', 'SUSANA VICTORIA RIVAS CORTEZ', '997838185', '44345419', '44345419', '', '2026-02-24 02:09:02', 1),
(67, 1, 1, '47875566', 'ERICKA EDITH CORREA ROJAS', '935168567', '47875566', '47875566', '', '2026-02-24 15:34:22', 1),
(68, 2, 1, '43142625', 'LUIS MARTIN AGUILAR LAZO', '956766743', '43142625', '43142625', '956766743 - MAMA - YOMAIRA HUAMANI\r\n+51 956 766 743', '2026-02-24 17:53:55', 1),
(69, 1, 1, '47792180', 'MARYORI MAYTE HUAMANI PAZ', '904782682', '47792180', '47792180', '', '2026-02-24 18:42:53', 1),
(70, 1, 1, '46102032', 'LESLY ESTEFANY ARCE MORENO', '991892068', '46102032', '46102032', '', '2026-02-25 17:34:53', 1),
(71, 1, 1, '46340294', 'CARMEN PEREA ALVAREZ', '997179976', '46340294', '46340294', '', '2026-02-25 18:00:46', 1),
(72, 1, 1, '76724518', 'MISHELL KARIN MARQUEZ TARAZONA', '946415968', '76724518', '76724518', '', '2026-02-25 21:43:37', 1),
(73, 1, 1, '10533208', 'BRIGIDA SABINA DE LA CRUZ VEGA', '947535752', '10533208', '10533208', '', '2026-02-25 22:01:11', 1),
(74, 1, 1, '48289080', 'BERENICE MILENA GUTIERREZ PEREDA', '936567136', '48289080', '48289080', '', '2026-02-26 19:30:49', 1),
(75, 2, 1, '42102440', 'JULIO JULIAN PALACIOS ALVARADO', '900500972', '42102440', '42102440', '', '2026-02-26 22:21:10', 1),
(76, 1, 1, '75921744', 'KEASY ISABEL VILLACORTA MALMA', '989968818', '75921744', '75921744', '', '2026-02-27 19:19:07', 1),
(77, 1, 1, '74834333', 'ANGIE FIORELLA GARCIA RAMOS', '927335199', '74834333', '74834333', '', '2026-02-27 19:31:36', 1),
(78, 1, 1, '48152891', 'NATALI STEFANI POMALAZA FERNANDEZ', '966306903', '48152891', '48152891', '', '2026-02-28 14:07:40', 1),
(79, 1, 1, '48624260', 'YAHAIRA NINOSHKA HUAMAN ROJAS', '955372719', '48624260', '48624260', '', '2026-03-02 13:59:29', 1);

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
(1, 'DIRECTOR', '', '2025-12-27 07:13:32', 0),
(2, 'SUB DIRECTOR', '', '2025-12-27 07:13:41', 0),
(3, 'SECRETARIA', '', '2025-12-27 07:13:49', 1),
(4, 'DOCENTE', '', '2026-01-02 06:44:29', 0),
(5, 'APODERADO', '', '2026-01-02 06:44:40', 0),
(6, 'ALUMNO', '', '2026-01-02 06:46:44', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_cargo_menu`
--

CREATE TABLE `usuario_cargo_menu` (
  `id` int(11) NOT NULL,
  `id_usuario_cargo` int(11) NOT NULL,
  `id_usuario_menu` int(11) NOT NULL,
  `ingreso` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` varchar(255) DEFAULT NULL,
  `fechacreado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_cargo_menu`
--

INSERT INTO `usuario_cargo_menu` (`id`, `id_usuario_cargo`, `id_usuario_menu`, `ingreso`, `observaciones`, `fechacreado`, `estado`) VALUES
(55, 1, 1, 1, '', '2026-01-06 01:30:44', 0),
(56, 1, 2, 0, '', '2026-01-06 01:30:44', 0),
(57, 1, 3, 0, '', '2026-01-06 01:30:44', 0),
(58, 1, 4, 0, '', '2026-01-06 01:30:44', 0),
(59, 1, 5, 0, '', '2026-01-06 01:30:44', 0),
(60, 1, 6, 0, '', '2026-01-06 01:30:44', 0),
(61, 1, 7, 0, '', '2026-01-06 01:30:44', 0),
(62, 1, 8, 0, '', '2026-01-06 01:30:44', 0),
(63, 1, 9, 0, '', '2026-01-06 01:30:44', 0),
(73, 2, 1, 1, '', '2026-01-06 01:34:10', 0),
(74, 2, 2, 0, '', '2026-01-06 01:34:10', 0),
(75, 2, 3, 0, '', '2026-01-06 01:34:10', 0),
(76, 2, 4, 0, '', '2026-01-06 01:34:10', 0),
(77, 2, 5, 0, '', '2026-01-06 01:34:10', 0),
(78, 2, 6, 0, '', '2026-01-06 01:34:10', 0),
(79, 2, 7, 0, '', '2026-01-06 01:34:10', 0),
(80, 2, 8, 0, '', '2026-01-06 01:34:10', 0),
(81, 2, 9, 0, '', '2026-01-06 01:34:10', 0),
(91, 4, 1, 1, '', '2026-01-06 01:34:48', 0),
(92, 4, 2, 0, '', '2026-01-06 01:34:48', 0),
(93, 4, 3, 0, '', '2026-01-06 01:34:48', 0),
(94, 4, 4, 0, '', '2026-01-06 01:34:48', 0),
(95, 4, 5, 0, '', '2026-01-06 01:34:48', 0),
(96, 4, 6, 0, '', '2026-01-06 01:34:48', 0),
(97, 4, 7, 0, '', '2026-01-06 01:34:48', 0),
(98, 4, 8, 0, '', '2026-01-06 01:34:48', 0),
(99, 4, 9, 0, '', '2026-01-06 01:34:48', 0),
(118, 3, 1, 1, '', '2026-01-14 09:52:06', 1),
(119, 3, 2, 1, '', '2026-01-14 09:52:06', 1),
(120, 3, 3, 1, '', '2026-01-14 09:52:06', 1),
(121, 3, 4, 1, '', '2026-01-14 09:52:06', 1),
(122, 3, 5, 1, '', '2026-01-14 09:52:06', 1),
(123, 3, 6, 1, '', '2026-01-14 09:52:06', 1),
(124, 3, 7, 1, '', '2026-01-14 09:52:06', 1),
(125, 3, 8, 1, '', '2026-01-14 09:52:06', 1),
(126, 3, 9, 1, '', '2026-01-14 09:52:06', 1),
(127, 3, 10, 1, '', '2026-01-14 09:52:06', 1);

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
(1, 1, '10509059', 'CECILIA ROSARIO MANRIQUE LOPEZ', '1977-01-16', 2, 2, 'PROLONG. LAS GLADIOLAS MZ.X LT.12 EL ERMITAÑO', '976300448', '', 1, 1, '2026-01-01', '2027-02-28', '0.00', '', '', '', '', '', '10509059', '10509059', '', '2026-01-05 16:43:51', 1),
(2, 1, '73937543', 'MARCO ANTONIO MANRIQUE VARILLAS', '1999-06-18', 1, 1, 'PROLONG. LAS GLADIOLAS MZ.X LT.12 EL ERMITAÑO', '994947452', '', 3, 2, '2026-01-01', '2026-03-31', '0.00', '', '', '', '', '', '73937543', '73937543', '', '2026-01-05 22:06:21', 1);

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
(3, 'CE', '', '2025-12-27 06:53:21', 1);

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
-- Estructura de tabla para la tabla `usuario_menu`
--

CREATE TABLE `usuario_menu` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `ruta` varchar(200) DEFAULT NULL,
  `observaciones` text,
  `fechacreado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_menu`
--

INSERT INTO `usuario_menu` (`id`, `nombre`, `icono`, `ruta`, `observaciones`, `fechacreado`, `estado`) VALUES
(1, 'INICIO', '', '../../Inicio/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(2, 'USUARIO', '', '../../Usuario/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(3, 'INSTITUCION', '', '../../Institucion/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(4, 'MATRICULA 2026', '', '../../Matricula/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(5, 'MENSUALIDAD', '', '../../Mensualidad/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(6, 'FACTURACION', '', '../../Facturacion/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(7, 'DOCUMENTO', '', '../../Documento/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(8, 'ALMACEN', '', '../../Almacen/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(9, 'BIBLIOTECA', '', '../../Biblioteca/Vista/Escritorio.php', '', '2026-01-05 23:53:36', 1),
(10, 'REGISTRO', '', '../../Registro/Vista/Escritorio.php', '', '2026-01-14 09:51:56', 1);

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
-- Indices de la tabla `registro_utiles`
--
ALTER TABLE `registro_utiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_registro_utiles_matricula` (`id_matricula`);

--
-- Indices de la tabla `registro_utiles_detalle`
--
ALTER TABLE `registro_utiles_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_registro_utiles_detalle_matricula_detalle` (`id_matricula_detalle`),
  ADD KEY `fk_registro_utiles_detalle_registro_utiles` (`id_registro_utiles`);

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
-- Indices de la tabla `usuario_cargo_menu`
--
ALTER TABLE `usuario_cargo_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario_cargo_menu_cargo` (`id_usuario_cargo`),
  ADD KEY `fk_usuario_cargo_menu_menu` (`id_usuario_menu`);

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
-- Indices de la tabla `usuario_menu`
--
ALTER TABLE `usuario_menu`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `almacen_comprobante`
--
ALTER TABLE `almacen_comprobante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `almacen_ingreso`
--
ALTER TABLE `almacen_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `almacen_ingreso_detalle`
--
ALTER TABLE `almacen_ingreso_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `almacen_metodo_pago`
--
ALTER TABLE `almacen_metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `almacen_producto`
--
ALTER TABLE `almacen_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `almacen_salida`
--
ALTER TABLE `almacen_salida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `almacen_salida_detalle`
--
ALTER TABLE `almacen_salida_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `matricula_mes`
--
ALTER TABLE `matricula_mes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `matricula_metodo_pago`
--
ALTER TABLE `matricula_metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `matricula_monto`
--
ALTER TABLE `matricula_monto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `matricula_pago`
--
ALTER TABLE `matricula_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `mensualidad_detalle`
--
ALTER TABLE `mensualidad_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=671;

--
-- AUTO_INCREMENT de la tabla `registro_utiles`
--
ALTER TABLE `registro_utiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT de la tabla `registro_utiles_detalle`
--
ALTER TABLE `registro_utiles_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario_alumno`
--
ALTER TABLE `usuario_alumno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `usuario_apoderado`
--
ALTER TABLE `usuario_apoderado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

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
-- AUTO_INCREMENT de la tabla `usuario_cargo_menu`
--
ALTER TABLE `usuario_cargo_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT de la tabla `usuario_docente`
--
ALTER TABLE `usuario_docente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT de la tabla `usuario_menu`
--
ALTER TABLE `usuario_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Filtros para la tabla `registro_utiles`
--
ALTER TABLE `registro_utiles`
  ADD CONSTRAINT `fk_registro_utiles_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `registro_utiles_detalle`
--
ALTER TABLE `registro_utiles_detalle`
  ADD CONSTRAINT `fk_registro_utiles_detalle_matricula_detalle` FOREIGN KEY (`id_matricula_detalle`) REFERENCES `matricula_detalle` (`id`),
  ADD CONSTRAINT `fk_registro_utiles_detalle_registro_utiles` FOREIGN KEY (`id_registro_utiles`) REFERENCES `registro_utiles` (`id`);

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
-- Filtros para la tabla `usuario_cargo_menu`
--
ALTER TABLE `usuario_cargo_menu`
  ADD CONSTRAINT `fk_usuario_cargo_menu_cargo` FOREIGN KEY (`id_usuario_cargo`) REFERENCES `usuario_cargo` (`id`),
  ADD CONSTRAINT `fk_usuario_cargo_menu_menu` FOREIGN KEY (`id_usuario_menu`) REFERENCES `usuario_menu` (`id`);

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
