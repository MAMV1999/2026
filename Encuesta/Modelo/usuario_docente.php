<?php
require_once("../../database.php");

class UsuarioDocente
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo docente
    public function guardar(
        $id_documento, $numerodocumento, $nombreyapellido, $nacimiento, 
        $id_estado_civil, $id_sexo, $direccion, $telefono, $correo, 
        $id_cargo, $id_tipo_contrato, $fechainicio, $fechafin, $sueldo, 
        $cuentabancaria, $cuentainterbancaria, $sunat_ruc, $sunat_usuario, 
        $sunat_contraseña, $usuario, $clave, $observaciones, $estado = 1
    ) {
        $sql = "INSERT INTO usuario_docente (
                    id_documento, numerodocumento, nombreyapellido, nacimiento, 
                    id_estado_civil, id_sexo, direccion, telefono, correo, 
                    id_cargo, id_tipo_contrato, fechainicio, fechafin, sueldo, 
                    cuentabancaria, cuentainterbancaria, sunat_ruc, sunat_usuario, 
                    sunat_contraseña, usuario, clave, observaciones, estado
                ) VALUES (
                    '$id_documento', '$numerodocumento', '$nombreyapellido', '$nacimiento', 
                    '$id_estado_civil', '$id_sexo', '$direccion', '$telefono', '$correo', 
                    '$id_cargo', '$id_tipo_contrato', '$fechainicio', '$fechafin', '$sueldo', 
                    '$cuentabancaria', '$cuentainterbancaria', '$sunat_ruc', '$sunat_usuario', 
                    '$sunat_contraseña', '$numerodocumento', '$numerodocumento', '$observaciones', '$estado'
                )";
        return ejecutarConsulta($sql);
    }

    // Método para editar un docente existente
    public function editar(
        $id, $id_documento, $numerodocumento, $nombreyapellido, $nacimiento, 
        $id_estado_civil, $id_sexo, $direccion, $telefono, $correo, 
        $id_cargo, $id_tipo_contrato, $fechainicio, $fechafin, $sueldo, 
        $cuentabancaria, $cuentainterbancaria, $sunat_ruc, $sunat_usuario, 
        $sunat_contraseña, $usuario, $clave, $observaciones, $estado
    ) {
        $sql = "UPDATE usuario_docente SET 
                    id_documento='$id_documento', numerodocumento='$numerodocumento', 
                    nombreyapellido='$nombreyapellido', nacimiento='$nacimiento', 
                    id_estado_civil='$id_estado_civil', id_sexo='$id_sexo', direccion='$direccion', 
                    telefono='$telefono', correo='$correo', id_cargo='$id_cargo', 
                    id_tipo_contrato='$id_tipo_contrato', fechainicio='$fechainicio', 
                    fechafin='$fechafin', sueldo='$sueldo', cuentabancaria='$cuentabancaria', 
                    cuentainterbancaria='$cuentainterbancaria', sunat_ruc='$sunat_ruc', 
                    sunat_usuario='$sunat_usuario', sunat_contraseña='$sunat_contraseña', 
                    usuario='$usuario', clave='$clave', observaciones='$observaciones', 
                    estado='$estado'
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un docente específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM usuario_docente WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los docentes
    public function listar()
    {
        $sql = "SELECT 
                ud.id,
                d.nombre AS tipo_documento,
                ud.numerodocumento,
                ud.nombreyapellido,
                YEAR(CURDATE()) - YEAR(ud.nacimiento) - 
                (DATE_FORMAT(CURDATE(), '%m-%d') < DATE_FORMAT(ud.nacimiento, '%m-%d')) AS edad,
                c.nombre AS cargo,
                tc.nombre AS tipo_contrato,
                ud.estado
            FROM usuario_docente ud
            INNER JOIN usuario_documento d ON ud.id_documento = d.id
            INNER JOIN usuario_cargo c ON ud.id_cargo = c.id
            LEFT JOIN usuario_tipo_contrato tc ON ud.id_tipo_contrato = tc.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un docente
    public function desactivar($id)
    {
        $sql = "UPDATE usuario_docente SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un docente
    public function activar($id)
    {
        $sql = "UPDATE usuario_docente SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Métodos para listar datos activos para los campos de selección en el formulario
    public function listarTiposDocumentoActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_documento WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarCargosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_cargo WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarEstadosCivilesActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_estado_civil WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarTiposContratoActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_tipo_contrato WHERE estado='1'";
        return ejecutarConsulta($sql);
    }

    public function listarSexosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_sexo WHERE estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>
