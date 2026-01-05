DELIMITER $$

CREATE PROCEDURE ObtenerDetallesMatricula(p_id INT)
BEGIN
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

DELIMITER ;
