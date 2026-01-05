<?php
require_once("../../database.php");

class Registrodocumento
{
    public function __construct() {}

    public function guardarDocumentoDetalle($matricula_detalle_id, $documentos)
    {
        try {
            foreach ($documentos as $documento_id => $detalle) {
                $entregado = limpiarcadena($detalle['entregado']);
                $observaciones = limpiarcadena($detalle['observaciones']);
                $matricula_detalle_id = limpiarcadena($matricula_detalle_id);
                $documento_id = limpiarcadena($documento_id);

                // Verificar si el registro ya existe
                $sql_verificar = "SELECT COUNT(*) as count FROM documento_detalle WHERE id_matricula_detalle = '$matricula_detalle_id' AND id_documento = '$documento_id'";
                $resultado = ejecutarConsultaSimpleFila($sql_verificar);

                if ($resultado['count'] > 0) {
                    // Actualizar el registro existente
                    $sql_actualizar = "UPDATE documento_detalle SET entregado = '$entregado', observaciones = '$observaciones' WHERE id_matricula_detalle = '$matricula_detalle_id' AND id_documento = '$documento_id'";
                    ejecutarConsulta($sql_actualizar);
                } else {
                    // Insertar un nuevo registro
                    $sql_insertar = "INSERT INTO documento_detalle (id_matricula_detalle, id_documento, entregado, observaciones) VALUES ('$matricula_detalle_id', '$documento_id', '$entregado', '$observaciones')";
                    ejecutarConsulta($sql_insertar);
                }
            }
            return true;
        } catch (Exception $e) { return false; }
    }

    public function listar()
    {
        $sql = "SELECT
                md.id AS id_matricula_detalle,
                il.nombre AS lectivo,
                iniv.nombre AS nivel,
                igr.nombre AS grado,
                isec.nombre AS seccion,
                ua.nombreyapellido AS apoderado_nombre,
                ual.nombreyapellido AS alumno_nombre,
                mc.nombre AS categoria_matricula
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado igr ON isec.id_institucion_grado = igr.id AND igr.estado = 1
                INNER JOIN institucion_nivel iniv ON igr.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, igr.nombre ASC, isec.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    public function listar_documento()
    {
        $sql = "SELECT 
                documento.id,
                documento.id_documento_responsable,
                documento_responsable.nombre AS nombre_responsable,
                UPPER(TRIM(REPLACE(CONCAT(LEFT(documento_responsable.nombre, 1), '.',CASE WHEN LOCATE(' ', documento_responsable.nombre) > 0 THEN CONCAT('.', LEFT(SUBSTRING_INDEX(documento_responsable.nombre, ' ', -1), 1), '.') ELSE '' END), '..', '.' ))) AS iniciales_responsable,
                documento.nombre AS nombre_documento,
                documento.obligatorio,
                CASE WHEN documento.obligatorio = 1 THEN '***' ELSE '' END AS obligatorio_marcado,
                documento.observaciones,
                documento.fechacreado,
                documento.estado
                FROM documento
                LEFT JOIN documento_responsable
                ON documento.id_documento_responsable = documento_responsable.id
                WHERE documento.estado = 1 AND documento_responsable.estado = 1";
        return ejecutarConsulta($sql);
    }

    public function listar_documento_detalle($id)
    {
        $id = limpiarcadena($id);
        $sql = "SELECT id, id_matricula_detalle, id_documento, entregado, observaciones, fechacreado, estado 
                FROM documento_detalle 
                WHERE id_matricula_detalle = '$id' AND estado = '1'";
        return ejecutarConsulta($sql);
    }

    public function listar_id_matricula_detalle($id)
    {
        $id = limpiarcadena($id);
        $sql = "SELECT
                md.id AS id_matricula_detalle,
                il.nombre AS lectivo,
                iniv.nombre AS nivel,
                igr.nombre AS grado,
                isec.nombre AS seccion,
                ua.nombreyapellido AS apoderado_nombre,
                ua.telefono AS apoderado_telefono,
                ual.nombreyapellido AS alumno_nombre,
                mc.nombre AS categoria_matricula
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado igr ON isec.id_institucion_grado = igr.id AND igr.estado = 1
                INNER JOIN institucion_nivel iniv ON igr.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                WHERE md.id = '$id'
                ORDER BY il.nombre ASC, iniv.nombre ASC, igr.nombre ASC, isec.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsultaSimpleFila($sql);
    }
}
