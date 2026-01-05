<?php
require_once("../../database.php");

class UsuarioDocente
{
    public function __construct()
    {
    }

    public function listarUsuarioDocente($id)
    {
        $sql = "SELECT 
                    ud.id AS docente_id,
                    ud.numerodocumento,
                    ud.nombreyapellido,
                    DATE_FORMAT(ud.nacimiento, '%d/%m/%Y') AS nacimiento,
                    TIMESTAMPDIFF(YEAR, ud.nacimiento, CURDATE()) AS edad,
                    doc.nombre AS tipo_documento,
                    ec.nombre AS estado_civil,
                    us.nombre AS sexo,
                    ud.direccion,
                    ud.telefono,
                    ud.correo,
                    uc.nombre AS cargo,
                    utc.nombre AS tipo_contrato,
                    DATE_FORMAT(ud.fechainicio, '%d/%m/%Y') AS fechainicio,
                    DATE_FORMAT(ud.fechafin, '%d/%m/%Y') AS fechafin,
                    ud.sueldo,
                    ud.cuentabancaria,
                    ud.cuentainterbancaria,
                    ud.sunat_ruc,
                    ud.sunat_usuario,
                    ud.usuario,
                    ud.clave,
                    ud.observaciones,
                    DATE_FORMAT(ud.fechacreado, '%d/%m/%Y') AS fechacreado,
                    ud.estado
                FROM usuario_docente ud
                LEFT JOIN usuario_documento doc ON ud.id_documento = doc.id
                LEFT JOIN usuario_estado_civil ec ON ud.id_estado_civil = ec.id
                LEFT JOIN usuario_sexo us ON ud.id_sexo = us.id
                LEFT JOIN usuario_cargo uc ON ud.id_cargo = uc.id
                LEFT JOIN usuario_tipo_contrato utc ON ud.id_tipo_contrato = utc.id
                WHERE ud.id = '$id'";
        return ejecutarConsulta($sql);
    }
}
?>
