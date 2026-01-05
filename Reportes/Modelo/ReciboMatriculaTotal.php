<?php
require_once("../../database.php");

class ReciboMatriculaTotal
{
    public function __construct()
    {
    }

    public function listarReciboMatriculaTotal()
    {
        $sql = "SELECT 
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    mc.nombre AS categoria_matricula,  -- Nueva columna agregada
                    ua.nombreyapellido AS nombre_alumno,
                    ud_alumno.nombre AS tipo_documento_alumno,
                    ua.numerodocumento AS numero_documento_alumno,
                    uap.nombreyapellido AS nombre_apoderado,
                    ud_apoderado.nombre AS tipo_documento_apoderado,
                    uap.numerodocumento AS numero_documento_apoderado,
                    uap.telefono AS telefono_apoderado,
                    mp.numeracion,
                    DATE_FORMAT(mp.fecha, '%d/%m/%Y') AS fecha,
                    mp.monto,
                    mmp.nombre AS metodo_pago
                FROM matricula_detalle md
                JOIN matricula m ON md.id_matricula = m.id
                JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
                JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                JOIN usuario_alumno ua ON md.id_usuario_alumno = ua.id
                JOIN usuario_documento ud_alumno ON ua.id_documento = ud_alumno.id
                JOIN usuario_apoderado uap ON md.id_usuario_apoderado = uap.id
                JOIN usuario_documento ud_apoderado ON uap.id_documento = ud_apoderado.id
                JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id  -- Nueva unión agregada
                JOIN matricula_pago mp ON mp.id_matricula_detalle = md.id
                JOIN matricula_metodo_pago mmp ON mp.id_matricula_metodo_pago = mmp.id
                WHERE md.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
?>