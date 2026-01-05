<?php
require_once("../../database.php");

class Usuario_alumno
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo usuario alumno
    public function guardar($id_apoderado, $id_documento, $numerodocumento, $nombreyapellido, $id_sexo, $usuario, $clave, $observaciones)
    {
        $sql = "INSERT INTO usuario_alumno (id_apoderado, id_documento, numerodocumento, nombreyapellido, id_sexo, usuario, clave, observaciones) VALUES ('$id_apoderado', '$id_documento', '$numerodocumento', '$nombreyapellido', '$id_sexo', '$numerodocumento', '$numerodocumento', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un usuario alumno existente
    public function editar($id, $id_apoderado, $id_documento, $numerodocumento, $nombreyapellido, $id_sexo, $usuario, $clave, $observaciones)
    {
        $sql = "UPDATE usuario_alumno SET id_apoderado='$id_apoderado', id_documento='$id_documento', numerodocumento='$numerodocumento', nombreyapellido='$nombreyapellido', id_sexo='$id_sexo', usuario='$usuario', clave='$clave', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un usuario alumno específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM usuario_alumno WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los usuarios alumnos
    public function listar()
    {
        $sql = "SELECT ua.id, ua.numerodocumento, ua.nombreyapellido, uap.nombreyapellido AS apoderado, ud.nombre AS tipo_documento,
                us.nombre AS sexo, ua.usuario, ua.estado, ua.fechacreado 
                FROM usuario_alumno ua
                LEFT JOIN usuario_apoderado uap ON ua.id_apoderado = uap.id
                LEFT JOIN usuario_documento ud ON ua.id_documento = ud.id
                LEFT JOIN usuario_sexo us ON ua.id_sexo = us.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un usuario alumno
    public function desactivar($id)
    {
        $sql = "UPDATE usuario_alumno SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un usuario alumno
    public function activar($id)
    {
        $sql = "UPDATE usuario_alumno SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los apoderados activos
    public function listarApoderadosActivos()
    {
        $sql = "SELECT * FROM usuario_apoderado WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los tipos de documentos activos
    public function listarTiposDocumentosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_documento WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los sexos activos
    public function listarSexosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_sexo WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }
}
?>
