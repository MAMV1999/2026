<?php
require_once("../../database.php");

class Perfil
{
    public function __construct()
    {
    }

    // Método para mostrar los detalles del usuario docente (por ID)
    public function mostrar($id)
    {
        $sql = "SELECT * FROM usuario_docente WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para editar los datos del usuario docente (por ID)
    public function editar(
        $id,
        $id_documento,
        $numerodocumento,
        $nombreyapellido,
        $nacimiento,
        $id_estado_civil,
        $id_sexo,
        $direccion,
        $telefono,
        $correo,
        $id_cargo,
        $id_tipo_contrato,
        $fechainicio,
        $fechafin,
        $sueldo,
        $cuentabancaria,
        $cuentainterbancaria,
        $sunat_ruc,
        $sunat_usuario,
        $sunat_contraseña,
        $usuario,
        $clave,
        $observaciones
    ) {
        $sql = "UPDATE usuario_docente SET
                    id_documento='$id_documento',
                    numerodocumento='$numerodocumento',
                    nombreyapellido='$nombreyapellido',
                    nacimiento='$nacimiento',
                    id_estado_civil='$id_estado_civil',
                    id_sexo='$id_sexo',
                    direccion='$direccion',
                    telefono='$telefono',
                    correo='$correo',
                    id_cargo='$id_cargo',
                    id_tipo_contrato='$id_tipo_contrato',
                    fechainicio='$fechainicio',
                    fechafin='$fechafin',
                    sueldo='$sueldo',
                    cuentabancaria='$cuentabancaria',
                    cuentainterbancaria='$cuentainterbancaria',
                    sunat_ruc='$sunat_ruc',
                    sunat_usuario='$sunat_usuario',
                    sunat_contraseña='$sunat_contraseña',
                    usuario='$usuario',
                    clave='$clave',
                    observaciones='$observaciones'
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar docentes (opcional: por si lo necesitas en administración)
    public function listar()
    {
        $sql = "SELECT
                    ud.id,
                    ud.numerodocumento,
                    ud.nombreyapellido,
                    udoc.nombre AS tipo_documento,
                    usc.nombre AS estado_civil,
                    usx.nombre AS sexo,
                    uc.nombre AS cargo,
                    utc.nombre AS tipo_contrato,
                    ud.telefono,
                    ud.correo,
                    ud.estado,
                    ud.fechacreado
                FROM usuario_docente ud
                LEFT JOIN usuario_documento udoc ON ud.id_documento = udoc.id
                LEFT JOIN usuario_estado_civil usc ON ud.id_estado_civil = usc.id
                LEFT JOIN usuario_sexo usx ON ud.id_sexo = usx.id
                LEFT JOIN usuario_cargo uc ON ud.id_cargo = uc.id
                LEFT JOIN usuario_tipo_contrato utc ON ud.id_tipo_contrato = utc.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un usuario docente
    public function desactivar($id)
    {
        $sql = "UPDATE usuario_docente SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un usuario docente
    public function activar($id)
    {
        $sql = "UPDATE usuario_docente SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Listados auxiliares (para combos en el perfil)
    public function listarTiposDocumentosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_documento WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarEstadosCivilesActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_estado_civil WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarSexosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_sexo WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarCargosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_cargo WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarTiposContratoActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_tipo_contrato WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
