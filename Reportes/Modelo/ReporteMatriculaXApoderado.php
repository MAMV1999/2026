<?php
require_once("../../database.php");

class ReporteMatricula
{
    public function __construct()
    {
    }

    public function matriculadospagos_apoderado()
    {
        $sql = "SELECT 
                    md.id AS matricula_detalle_id,
                    il.nombre AS institucion_lectivo,
                    niv.nombre AS institucion_nivel,
                    ig.nombre AS institucion_grado,
                    isec.nombre AS institucion_seccion,
                    ua.id AS apoderado_id,
                    uat.nombre AS apoderado_tipo,
                    ud.nombre AS apoderado_documento,
                    ua.numerodocumento AS apoderado_dni,
                    ua.nombreyapellido AS apoderado_nombre,
                    ua.telefono AS apoderado_telefono,
                    ua2.id AS alumno_id,
                    ua2.id_apoderado AS alumno_apoderado_id,
                    ud2.nombre AS alumno_documento,
                    ua2.numerodocumento AS alumno_dni,
                    ua2.nombreyapellido AS alumno_nombre,
                    DATE_FORMAT(ua2.nacimiento, '%d/%m/%Y') AS alumno_nacimiento,
                    FLOOR(DATEDIFF(CURDATE(), ua2.nacimiento) / 365.25) AS alumno_edad,
                    mp.numeracion AS pago_numeracion,
                    DATE_FORMAT(mp.fecha, '%d/%m/%Y') AS pago_fecha,
                    mp.monto AS pago_monto,
                    mm.nombre AS metodo_pago
                FROM matricula_detalle md
                JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id AND ig.estado = 1
                JOIN institucion_nivel niv ON ig.id_institucion_nivel = niv.id AND niv.estado = 1
                JOIN institucion_lectivo il ON niv.id_institucion_lectivo = il.id AND il.estado = 1
                JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id AND uat.estado = 1
                JOIN usuario_documento ud ON ua.id_documento = ud.id AND ud.estado = 1
                JOIN usuario_alumno ua2 ON md.id_usuario_alumno = ua2.id AND ua2.estado = 1
                JOIN usuario_documento ud2 ON ua2.id_documento = ud2.id AND ud2.estado = 1
                JOIN matricula_pago mp ON md.id = mp.id_matricula_detalle AND mp.estado = 1
                JOIN matricula_metodo_pago mm ON mp.id_matricula_metodo_pago = mm.id AND mm.estado = 1
                WHERE md.estado = 1
                ORDER BY ua.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }
}
