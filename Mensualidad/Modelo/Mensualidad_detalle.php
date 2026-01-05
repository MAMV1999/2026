<?php
require_once("../../database.php");

class Mensualidad_detalle
{
    public function __construct() {}

    // Método para editar un registro de mensualidad existente
    public function guardarEditarMasivo($detalles)
    {
        try {
            foreach ($detalles as $detalle) {
                $id = $detalle['id'];
                $monto = $detalle['monto'];
                $observaciones = $detalle['observaciones'];
                $pagado = $detalle['pagado'];

                // Si el ID está vacío, es un nuevo registro
                if (empty($id)) {
                    $sql = "INSERT INTO mensualidad_detalle (matricula_mes_id, id_matricula_detalle, monto, pagado, observaciones) VALUES ('$detalle[matricula_mes_id]', '$detalle[id_matricula_detalle]', '$monto', '$pagado', '$observaciones')";
                } else {
                    // Actualización de registro existente
                    $sql = "UPDATE mensualidad_detalle SET monto = '$monto', pagado = '$pagado', observaciones = '$observaciones' WHERE id = '$id'";
                }

                if (!ejecutarConsulta($sql)) {
                    // Manejo simple de error, puedes definir tu propia forma de manejar excepciones si lo deseas.
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            // Devuelve falso si hay algún error
            return false;
        }
    }

    // Método para mostrar los detalles de una mensualidad específica
    public function mostrar($id)
    {
        $sql = "SELECT 
                    md.id_matricula_detalle,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    ud_ap.nombre AS apoderado_tipo_documento,
                    uap.numerodocumento AS apoderado_numerodocumento,
                    uap.nombreyapellido AS apoderado_nombreyapellido,
                    uap.telefono AS apoderado_telefono,
                    ud_al.nombre AS alumno_tipo_documento,
                    ual.numerodocumento AS alumno_numerodocumento,
                    ual.nombreyapellido AS alumno_nombreyapellido,
                    GROUP_CONCAT(md.id) AS ids,
                    GROUP_CONCAT(md.matricula_mes_id) AS ids_mensualidad_mes,
                    GROUP_CONCAT(CONCAT(mm.nombre, ' ', il.nombre)) AS meses,
                    GROUP_CONCAT(DATE_FORMAT(mm.fecha_vencimiento, '%d/%m/%Y')) AS fechas_vencimiento,
                    GROUP_CONCAT(md.monto) AS montos,
                    GROUP_CONCAT(md.pagado) AS estados_pagado,
                    GROUP_CONCAT(md.estado) AS estados_generales,
                    GROUP_CONCAT(md.observaciones) AS observaciones
                FROM mensualidad_detalle md
                LEFT JOIN matricula_mes mm ON md.matricula_mes_id = mm.id
                LEFT JOIN matricula_detalle mdet ON md.id_matricula_detalle = mdet.id
                LEFT JOIN usuario_apoderado uap ON mdet.id_usuario_apoderado = uap.id
                LEFT JOIN usuario_documento ud_ap ON uap.id_documento = ud_ap.id
                LEFT JOIN usuario_alumno ual ON mdet.id_usuario_alumno = ual.id
                LEFT JOIN usuario_documento ud_al ON ual.id_documento = ud_al.id
                LEFT JOIN institucion_seccion isec ON mdet.id_matricula = isec.id
                LEFT JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                LEFT JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                WHERE md.id_matricula_detalle = '$id'
                GROUP BY md.id_matricula_detalle";
        return ejecutarConsultaSimpleFila($sql);
    }
    
// Método para listar todas las mensualidades
public function listar()
{
    $sql = "SELECT 
            md.id_matricula_detalle AS id, 
            il.nombre AS lectivo, 
            iniv.nombre AS nivel, 
            ig.nombre AS grado, 
            isec.nombre AS seccion, 
            mc.nombre AS categoria,
            uap.nombreyapellido AS apoderado, 
            ual.nombreyapellido AS alumno, 
            ual.numerodocumento AS codigo, 
            SUM(md.monto) AS total_monto, 
            COUNT(md.id) AS num_mensualidades,
            md.estado AS estado
            FROM mensualidad_detalle md
            LEFT JOIN matricula_mes mm ON md.matricula_mes_id = mm.id AND mm.estado = '1'
            LEFT JOIN matricula_detalle mdet ON md.id_matricula_detalle = mdet.id AND mdet.estado = '1'
            LEFT JOIN matricula_categoria mc ON mdet.id_matricula_categoria = mc.id AND mc.estado = '1'
            LEFT JOIN usuario_apoderado uap ON mdet.id_usuario_apoderado = uap.id AND uap.estado = '1'
            LEFT JOIN usuario_alumno ual ON mdet.id_usuario_alumno = ual.id AND ual.estado = '1'
            LEFT JOIN institucion_seccion isec ON mdet.id_matricula = isec.id AND isec.estado = '1'
            LEFT JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id AND ig.estado = '1'
            LEFT JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id AND iniv.estado = '1'
            LEFT JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = '1'
            WHERE md.estado = '1'
            GROUP BY mdet.id, il.nombre, iniv.nombre, ig.nombre, isec.nombre, mc.nombre, uap.nombreyapellido, ual.nombreyapellido
            ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, mc.nombre ASC, ual.nombreyapellido ASC";
    return ejecutarConsulta($sql);
}

    // Método para listar los meses activos
    public function listarMesesActivos()
    {
        $sql = "SELECT id, nombre FROM matricula_mes WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los detalles de matrícula activos
    public function listarMatriculaDetallesActivos()
    {
        $sql = "SELECT 
                    mdet.id, 
                    il.nombre AS lectivo, 
                    iniv.nombre AS nivel, 
                    ig.nombre AS grado, 
                    isec.nombre AS seccion, 
                    uap.nombreyapellido AS apoderado, 
                    ual.nombreyapellido AS alumno
                FROM matricula_detalle mdet
                LEFT JOIN usuario_apoderado uap ON mdet.id_usuario_apoderado = uap.id
                LEFT JOIN usuario_alumno ual ON mdet.id_usuario_alumno = ual.id
                LEFT JOIN institucion_seccion isec ON mdet.id_matricula = isec.id
                LEFT JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                LEFT JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                WHERE mdet.estado = '1'";
        return ejecutarConsulta($sql);
    }
}
