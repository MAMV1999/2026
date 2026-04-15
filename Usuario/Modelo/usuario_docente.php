<?php
require_once("../../database.php");

class UsuarioDocente
{
    public function __construct()
    {
    }

    // Guardar o editar múltiples docentes
    public function guardarEditarMasivo($detalles)
    {
        try {
            global $conectar;

            foreach ($detalles as $detalle) {

                $id = isset($detalle['id']) ? limpiarcadena($detalle['id']) : null;
                $id_documento = isset($detalle['id_documento']) ? limpiarcadena($detalle['id_documento']) : '';
                $numerodocumento = isset($detalle['numerodocumento']) ? limpiarcadena($detalle['numerodocumento']) : '';
                $nombreyapellido = isset($detalle['nombreyapellido']) ? strtoupper(limpiarcadena($detalle['nombreyapellido'])) : '';
                $nacimiento = isset($detalle['nacimiento']) ? limpiarcadena($detalle['nacimiento']) : '';
                $id_estado_civil = isset($detalle['id_estado_civil']) ? limpiarcadena($detalle['id_estado_civil']) : '';
                $id_sexo = isset($detalle['id_sexo']) ? limpiarcadena($detalle['id_sexo']) : '';
                $direccion = isset($detalle['direccion']) ? limpiarcadena($detalle['direccion']) : '';
                $telefono = isset($detalle['telefono']) ? limpiarcadena($detalle['telefono']) : '';
                $correo = isset($detalle['correo']) ? strtoupper(limpiarcadena($detalle['correo'])) : '';
                $id_cargo = isset($detalle['id_cargo']) ? limpiarcadena($detalle['id_cargo']) : '';
                $id_tipo_contrato = isset($detalle['id_tipo_contrato']) ? limpiarcadena($detalle['id_tipo_contrato']) : '';
                $fechainicio = isset($detalle['fechainicio']) ? limpiarcadena($detalle['fechainicio']) : '';
                $fechafin = isset($detalle['fechafin']) ? limpiarcadena($detalle['fechafin']) : '';
                $sueldo = isset($detalle['sueldo']) ? limpiarcadena($detalle['sueldo']) : '';
                $cuentabancaria = isset($detalle['cuentabancaria']) ? limpiarcadena($detalle['cuentabancaria']) : '';
                $cuentainterbancaria = isset($detalle['cuentainterbancaria']) ? limpiarcadena($detalle['cuentainterbancaria']) : '';
                $sunat_ruc = isset($detalle['sunat_ruc']) ? limpiarcadena($detalle['sunat_ruc']) : '';
                $sunat_usuario = isset($detalle['sunat_usuario']) ? limpiarcadena($detalle['sunat_usuario']) : '';
                $sunat_contraseña = isset($detalle['sunat_contraseña']) ? limpiarcadena($detalle['sunat_contraseña']) : '';
                $usuario = isset($detalle['usuario']) ? limpiarcadena($detalle['usuario']) : '';
                $clave = isset($detalle['clave']) ? limpiarcadena($detalle['clave']) : '';
                $observaciones = isset($detalle['observaciones']) ? limpiarcadena($detalle['observaciones']) : '';
                $estado = isset($detalle['estado']) ? limpiarcadena($detalle['estado']) : '1';

                // Validación mínima
                if ($numerodocumento == '' || $nombreyapellido == '') {
                    continue;
                }

                if ($id) {
                    // Editar
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
                                observaciones='$observaciones',
                                estado='$estado'
                            WHERE id='$id'";
                } else {
                    // Nuevo
                    if ($usuario == '') {
                        $usuario = $numerodocumento;
                    }

                    if ($clave == '') {
                        $clave = $numerodocumento;
                    }

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
                                '$sunat_contraseña', '$usuario', '$clave', '$observaciones', '$estado'
                            )";
                }

                if (!ejecutarConsulta($sql)) {
                    error_log("Error en SQL usuario_docente: " . mysqli_error($conectar));
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en guardarEditarMasivo UsuarioDocente: " . $e->getMessage());
            return false;
        }
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM usuario_docente WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsultaSimpleFila($sql);
    }

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
                LEFT JOIN usuario_tipo_contrato tc ON ud.id_tipo_contrato = tc.id
                ORDER BY ud.id ASC";
        return ejecutarConsulta($sql);
    }

    public function listarTodos()
    {
        $sql = "SELECT * FROM usuario_docente ORDER BY id ASC";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE usuario_docente SET estado='0' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE usuario_docente SET estado='1' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    public function listarTiposDocumentoActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_documento WHERE estado='1' ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listarCargosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_cargo WHERE estado='1' ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listarEstadosCivilesActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_estado_civil WHERE estado='1' ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listarTiposContratoActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_tipo_contrato WHERE estado='1' ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listarSexosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_sexo WHERE estado='1' ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }
}
?>