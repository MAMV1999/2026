<?php
require_once("../../database.php");

class Reportecompleto
{
    public function __construct()
    {
    }

    public function listar($id)
    {
        $sql = "SELECT 
                    -- INSTITUCION
                    i.nombre AS institucion_nombre,
                    i.telefono AS institucion_telefono,
                    i.correo AS institucion_correo,
                    i.ruc AS institucion_ruc,
                    i.razon_social,
                    i.direccion AS institucion_direccion,
                
                    -- NIVEL / GRADO / SECCION
                    niv.nombre AS nivel,
                    gra.nombre AS grado,
                    sec.nombre AS seccion,
                    mc.nombre AS matricula_categoria,
                
                    -- DOCENTE (TUTOR)
                    ddo.nombre AS nombred_tutor,
                    doc.numerodocumento AS numerod_tutor,
                    doc.nombreyapellido AS tutor_nombre,
                    doc.telefono AS tutor_telefono,
                    doc.correo AS tutor_correo,
                    doc.direccion AS tutor_direccion,
                    DATE_FORMAT(doc.nacimiento, '%d/%m/%Y') AS tutor_nacimiento,
                
                    -- ALUMNO
                    dal.nombre AS nombred_alumno,
                    alu.numerodocumento AS numerod_alumno,
                    alu.nombreyapellido AS alumno_nombre,
                    DATE_FORMAT(alu.nacimiento, '%d/%m/%Y') AS alumno_nacimiento,
                
                    CONCAT(
                        TIMESTAMPDIFF(YEAR, alu.nacimiento, CURDATE()), ' AÑOS, ',
                        TIMESTAMPDIFF(MONTH, 
                            DATE_ADD(alu.nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, alu.nacimiento, CURDATE()) YEAR), 
                            CURDATE()
                        ), ' MESES Y ',
                        DATEDIFF(
                            CURDATE(),
                            DATE_ADD(
                                DATE_ADD(alu.nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, alu.nacimiento, CURDATE()) YEAR),
                                INTERVAL TIMESTAMPDIFF(
                                    MONTH,
                                    DATE_ADD(alu.nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, alu.nacimiento, CURDATE()) YEAR),
                                    CURDATE()
                                ) MONTH
                            )
                        ), ' DIAS'
                    ) AS alumno_edad,
                
                    -- APODERADO
                    dap.nombre AS nombred_apoderado,
                    apo.numerodocumento AS numerod_apoderado,
                    apo.nombreyapellido AS apoderado_nombre,
                    apo.telefono AS apoderado_telefono,
                
                    -- PAGOS DE MATRÍCULA
                    pagos.numeraciones_pago,
                    pagos.fechas_pago,
                    pagos.descripciones_pago,
                    pagos.montos_pago,
                    pagos.metodos_pago,
                    pagos.observaciones_pago,
                
                    -- MENSUALIDADES
                    men.meses,
                    men.montos,
                    men.estados_pago,
                    men.observaciones_mensualidad,
                
                    -- DOCUMENTOS
                    docs.documentos,
                    docs.estados_documentos,
                    docs.observaciones_documentos
                
                FROM matricula_detalle md
                
                -- RELACIONES PRINCIPALES
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion sec ON m.id_institucion_seccion = sec.id AND sec.estado = 1
                INNER JOIN institucion_grado gra ON sec.id_institucion_grado = gra.id AND gra.estado = 1
                INNER JOIN institucion_nivel niv ON gra.id_institucion_nivel = niv.id AND niv.estado = 1
                INNER JOIN institucion_lectivo il ON niv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN institucion i ON il.id_institucion = i.id AND i.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                
                -- DOCENTE
                INNER JOIN usuario_docente doc ON m.id_usuario_docente = doc.id AND doc.estado = 1
                INNER JOIN usuario_documento ddo ON doc.id_documento = ddo.id AND ddo.estado = 1
                
                -- ALUMNO
                INNER JOIN usuario_alumno alu ON md.id_usuario_alumno = alu.id AND alu.estado = 1
                INNER JOIN usuario_documento dal ON alu.id_documento = dal.id AND dal.estado = 1
                
                -- APODERADO
                INNER JOIN usuario_apoderado apo ON md.id_usuario_apoderado = apo.id AND apo.estado = 1
                INNER JOIN usuario_documento dap ON apo.id_documento = dap.id AND dap.estado = 1
                
                -- SUBCONSULTA PAGOS DE MATRÍCULA
                LEFT JOIN (
                    SELECT 
                        mp.id_matricula_detalle,
                        GROUP_CONCAT(mp.numeracion ORDER BY mp.fecha ASC SEPARATOR ', ') AS numeraciones_pago,
                        GROUP_CONCAT(DATE_FORMAT(mp.fecha, '%d/%m/%Y') ORDER BY mp.fecha ASC SEPARATOR ', ') AS fechas_pago,
                        GROUP_CONCAT(mp.descripcion ORDER BY mp.fecha ASC SEPARATOR ', ') AS descripciones_pago,
                        GROUP_CONCAT(mp.monto ORDER BY mp.fecha ASC SEPARATOR ', ') AS montos_pago,
                        GROUP_CONCAT(mmp.nombre ORDER BY mp.fecha ASC SEPARATOR ', ') AS metodos_pago,
                        GROUP_CONCAT(mp.observaciones ORDER BY mp.fecha ASC SEPARATOR ', ') AS observaciones_pago
                    FROM matricula_pago mp
                    INNER JOIN matricula_metodo_pago mmp
                        ON mp.id_matricula_metodo_pago = mmp.id
                        AND mmp.estado = 1
                    WHERE mp.estado = 1
                    GROUP BY mp.id_matricula_detalle
                ) pagos 
                    ON pagos.id_matricula_detalle = md.id
                
                -- SUBCONSULTA MENSUALIDADES
                LEFT JOIN (
                    SELECT 
                        md2.id_matricula_detalle,
                        GROUP_CONCAT(mm.nombre ORDER BY mm.id ASC SEPARATOR ', ') AS meses,
                        GROUP_CONCAT(md2.monto ORDER BY mm.id ASC SEPARATOR ', ') AS montos,
                        GROUP_CONCAT(md2.pagado ORDER BY mm.id ASC SEPARATOR ', ') AS estados_pago,
                        GROUP_CONCAT(md2.observaciones ORDER BY mm.id ASC SEPARATOR ', ') AS observaciones_mensualidad
                    FROM mensualidad_detalle md2
                    LEFT JOIN matricula_mes mm 
                        ON md2.matricula_mes_id = mm.id
                        AND mm.estado = 1
                    WHERE md2.estado = 1
                    GROUP BY md2.id_matricula_detalle
                ) men 
                    ON men.id_matricula_detalle = md.id
                
                -- SUBCONSULTA DOCUMENTOS
                LEFT JOIN (
                    SELECT 
                        dd.id_matricula_detalle,
                        GROUP_CONCAT(d.nombre ORDER BY d.id ASC SEPARATOR ', ') AS documentos,
                        GROUP_CONCAT(dd.entregado ORDER BY d.id ASC SEPARATOR ', ') AS estados_documentos,
                        GROUP_CONCAT(dd.observaciones ORDER BY d.id ASC SEPARATOR ', ') AS observaciones_documentos
                    FROM documento_detalle dd
                    LEFT JOIN documento d 
                        ON dd.id_documento = d.id
                        AND d.estado = 1
                    WHERE dd.estado = 1
                    GROUP BY dd.id_matricula_detalle
                ) docs 
                    ON docs.id_matricula_detalle = md.id
                
                WHERE md.id = '$id'
                AND md.estado = 1
                
                ORDER BY 
                    niv.nombre ASC,
                    gra.nombre ASC,
                    sec.nombre ASC,
                    alu.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
?>
