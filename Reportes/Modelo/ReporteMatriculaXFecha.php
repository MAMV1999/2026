<?php
require_once("../../database.php");

class ReporteMatricula
{
    public function __construct()
    {
    }

    public function matriculadospagos()
    {
        $sql = "SELECT 
                    md.id AS matricula_detalle_id,
                    il.nombre AS institucion_lectivo,
                    iniv.nombre AS institucion_nivel,
                    ig.nombre AS institucion_grado,
                    ua.nombreyapellido AS apoderado,
                    ua.numerodocumento AS apoderado_dni,
                    ua.telefono AS apoderado_telefono,
                    ual.nombreyapellido AS alumno,
                    ual.numerodocumento AS alumno_dni,
                    DATE_FORMAT(ual.nacimiento, '%d/%m/%Y') AS alumno_nacimiento,
                    FLOOR(DATEDIFF(CURDATE(), ual.nacimiento) / 365.25) AS alumno_edad,
                    mp.numeracion,
                    DATE_FORMAT(mp.fecha, '%d/%m/%Y') AS pago_fecha,
                    mp.monto AS pago_monto,
                    mm.nombre AS metodo_pago,
                    mp.observaciones AS pago_observaciones
                FROM matricula_detalle md
                JOIN matricula m ON md.id_matricula = m.id
                JOIN  institucion_seccion isec ON m.id_institucion_seccion = isec.id
                JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id
                JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id
                JOIN matricula_pago mp ON md.id = mp.id_matricula_detalle
                JOIN matricula_metodo_pago mm ON mp.id_matricula_metodo_pago = mm.id
                WHERE md.estado = '1'
                ORDER BY mp.fecha DESC, mm.nombre ASC, mp.numeracion ASC";
        return ejecutarConsulta($sql);
    }
}
