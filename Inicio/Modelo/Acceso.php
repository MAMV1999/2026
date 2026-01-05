<?php
require_once("../../database.php");

class Acceso
{
    public function __construct()
    {
    }

    // Función para verificar usuario y contraseña
    public function verificar($usuario, $clave)
    {
        $sql = "SELECT 
                    usuario_docente.id AS docente_id,
                    usuario_docente.numerodocumento AS docente_documento,
                    usuario_documento.nombre AS tipo_documento,
                    usuario_docente.nombreyapellido AS docente_nombre,
                    DATE_FORMAT(usuario_docente.nacimiento, '%d/%m/%Y') AS docente_fecha_nacimiento,
                    usuario_estado_civil.nombre AS docente_estado_civil,
                    usuario_sexo.nombre AS docente_sexo,
                    usuario_docente.direccion AS docente_direccion,
                    usuario_docente.telefono AS docente_telefono,
                    usuario_docente.correo AS docente_correo,
                    usuario_cargo.nombre AS docente_cargo,
                    usuario_tipo_contrato.nombre AS docente_tipo_contrato,
                    usuario_docente.fechainicio AS docente_fecha_inicio,
                    usuario_docente.fechafin AS docente_fecha_fin,
                    usuario_docente.sueldo AS docente_sueldo,
                    usuario_docente.cuentabancaria AS docente_cuenta_bancaria,
                    usuario_docente.cuentainterbancaria AS docente_cuenta_interbancaria,
                    usuario_docente.sunat_ruc AS docente_sunat_ruc,
                    usuario_docente.sunat_usuario AS docente_sunat_usuario,
                    usuario_docente.sunat_contraseña AS docente_sunat_contraseña,
                    usuario_docente.usuario AS docente_usuario,
                    usuario_docente.observaciones AS docente_observaciones,
                    DATE_FORMAT(usuario_docente.fechacreado, '%d/%m/%Y %H:%i:%s') AS docente_fechacreado,
                    CASE 
                        WHEN usuario_docente.estado = 1 THEN 'ACTIVO'
                        WHEN usuario_docente.estado = 0 THEN 'INACTIVO'
                        ELSE 'desconocido'
                    END AS docente_estado
                FROM usuario_docente
                LEFT JOIN usuario_documento ON usuario_docente.id_documento = usuario_documento.id AND usuario_documento.estado = 1
                LEFT JOIN usuario_estado_civil ON usuario_docente.id_estado_civil = usuario_estado_civil.id AND usuario_estado_civil.estado = 1
                LEFT JOIN usuario_sexo ON usuario_docente.id_sexo = usuario_sexo.id AND usuario_sexo.estado = 1
                LEFT JOIN usuario_cargo ON usuario_docente.id_cargo = usuario_cargo.id AND usuario_cargo.estado = 1
                LEFT JOIN usuario_tipo_contrato ON usuario_docente.id_tipo_contrato = usuario_tipo_contrato.id AND usuario_tipo_contrato.estado = 1
                WHERE 
                    usuario_docente.usuario = '$usuario'
                    AND usuario_docente.clave = '$clave'
                    AND usuario_docente.fechainicio <= CURDATE()
                    AND usuario_docente.fechafin >= CURDATE()
                    AND usuario_docente.estado = 1";
        return ejecutarConsulta($sql);
    }
}
?>
